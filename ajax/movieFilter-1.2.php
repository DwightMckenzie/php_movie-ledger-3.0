<?php
/**
 * Movie filter endpoint - server-side pagination.
 * Accepts POSTed JSON filters + page/pageSize, returns:
 *   { "total": <int>, "results": [...] }
 * where "total" is the full count of matching rows (for building
 * pagination controls) and "results" is just the current page.
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
 * Sort direction must come from a whitelist - never interpolate
 * user input directly into ORDER BY.
 */
$orderInput = $postData['movie-order'] ?? 'uo';
$movieOrder = ($orderInput === 'asc') ? 'ASC' : 'DESC'; // 'uo' (default) and anything else -> DESC

$movieTitle    = ($postData['movie-title']    ?? 'null') === 'null' ? '' : $postData['movie-title'];
$movieType     = ($postData['movie-type']     ?? 'all')  === 'all'  ? '' : $postData['movie-type'];
$movieCategory = ($postData['movie-category'] ?? 'all')  === 'all'  ? '' : $postData['movie-category'];
$movieRating   = ($postData['movie-rating']   ?? 'all')  === 'all'  ? '' : $postData['movie-rating'];

/**
 * Pagination inputs - validated and clamped so a bad/missing value
 * can't produce a negative OFFSET or an unbounded LIMIT.
 */
$page     = isset($postData['page']) ? (int) $postData['page'] : 1;
$pageSize = isset($postData['pageSize']) ? (int) $postData['pageSize'] : 10;

$page     = max(1, $page);
$pageSize = min(100, max(1, $pageSize)); // hard ceiling so a client can't request 1,000,000 rows at once

$offset = ($page - 1) * $pageSize;

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

$whereClause = "WHERE title LIKE :title ESCAPE '\\\\'
                AND type LIKE :type ESCAPE '\\\\'
                AND category LIKE :category ESCAPE '\\\\'
                AND rating LIKE :rating ESCAPE '\\\\'";

$params = [
  ':title'    => $titleParam,
  ':type'     => $typeParam,
  ':category' => $categoryParam,
  ':rating'   => $ratingParam,
];

try {
  // 1. Get the TRUE total of matching rows (ignores LIMIT/OFFSET) so the
  //    client can build accurate pagination controls without fetching
  //    every row.
  $countSql = "SELECT COUNT(*) FROM movie_list $whereClause";
  $countStmt = $conn->prepare($countSql);
  $countStmt->execute($params);
  $total = (int) $countStmt->fetchColumn();

  // 2. Get just the current page of rows.
  // LIMIT/OFFSET are validated ints above, so safe to inline directly
  // (PDO parameter binding for LIMIT/OFFSET is unreliable across drivers).
  $sql = "SELECT * FROM movie_list
          $whereClause
          ORDER BY title $movieOrder
          LIMIT $pageSize OFFSET $offset";

  $stmt = $conn->prepare($sql);
  $stmt->execute($params);
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'total'   => $total,
    'results' => $results,
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Query failed']);
  // Log the real error server-side instead of exposing $e->getMessage() to the client
  error_log($e->getMessage());
}
