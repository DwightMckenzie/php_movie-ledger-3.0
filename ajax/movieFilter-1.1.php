<?php
/**
 * Movie filter endpoint
 * Accepts POSTed JSON filters, returns the full matching movie list
 * (client handles pagination on its side).
 */

require_once "../includes/config.php";

header('Content-Type: application/json');

// Get + decode the raw POST body
$jsonData = file_get_contents("php://input");
$postData = json_decode($jsonData, true);

if (!$postData) {
  http_response_code(400);
  echo json_encode(['error' => 'No data passed into file']);
  exit;
}

/**
 * Sort direction must come from a whitelist — never interpolate
 * user input directly into ORDER BY.
 */
$orderInput = $postData['movie-order'] ?? 'uo';
$movieOrder = ($orderInput === 'asc') ? 'ASC' : 'DESC'; // 'uo' (default) and anything else -> DESC

$movieTitle    = ($postData['movie-title']    ?? 'null') === 'null' ? '' : $postData['movie-title'];
$movieType     = ($postData['movie-type']     ?? 'all')  === 'all'  ? '' : $postData['movie-type'];
$movieCategory = ($postData['movie-category'] ?? 'all')  === 'all'  ? '' : $postData['movie-category'];
$movieRating   = ($postData['movie-rating']   ?? 'all')  === 'all'  ? '' : $postData['movie-rating'];

/**
 * Escape LIKE wildcards in user input so a literal % or _ in a title
 * doesn't act as a wildcard.
 */
function escapeLike(string $value): string {
  return addcslashes($value, '%_\\');
}

$titleParam    = '%' . escapeLike($movieTitle) . '%';
$typeParam     = '%' . escapeLike($movieType) . '%';
$categoryParam = '%' . escapeLike($movieCategory) . '%';
$ratingParam   = '%' . escapeLike($movieRating) . '%';

// ORDER BY direction is whitelisted above, so it's safe to inline;
// everything else is a bound parameter.
$sql = "SELECT * FROM movie_list
        WHERE title LIKE :title ESCAPE '\\\\'
        AND type LIKE :type ESCAPE '\\\\'
        AND category LIKE :category ESCAPE '\\\\'
        AND rating LIKE :rating ESCAPE '\\\\'
        ORDER BY title $movieOrder";

try {
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':title'    => $titleParam,
    ':type'     => $typeParam,
    ':category' => $categoryParam,
    ':rating'   => $ratingParam,
  ]);

  $sqlData = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($sqlData);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Query failed']);
  // Log the real error server-side instead of exposing $e->getMessage() to the client
  error_log($e->getMessage());
}
