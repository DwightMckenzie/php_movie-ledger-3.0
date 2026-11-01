<?php
declare(strict_types=1);

/**
 * POST JSON filters -> { total, page, pageCount, results: [...] }
 * Read-only. Requires a logged-in session.
 */

require_once __DIR__ . '/../includes/bootstrap.php';

require_login(json: true);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['error' => 'Method not allowed.'], 405);
}

$body = file_get_contents('php://input', false, null, 0, 4096);
$input = json_decode($body === false ? '' : $body, true);
if (!is_array($input)) {
    json_response(['error' => 'Invalid request.'], 400);
}

$type = str_input($input, 'type');
$category = str_input($input, 'category');
$rating = str_input($input, 'rating');

if (($type !== '' && !array_key_exists($type, Movies::TYPES))
    || ($category !== '' && !array_key_exists($category, Movies::CATEGORIES))
    || ($rating !== '' && !array_key_exists($rating, Movies::RATINGS))) {
    json_response(['error' => 'Invalid filter value.'], 400);
}

$orderInput = str_input($input, 'order');
$order = array_key_exists($orderInput, Movies::ORDERS) ? $orderInput : 'desc';

$pageSize = (int) ($input['pageSize'] ?? 25);
if (!in_array($pageSize, Movies::PAGE_SIZES, true)) {
    $pageSize = Movies::PAGE_SIZES[0];
}

$page = positive_int($input['page'] ?? 1) ?? 1;

$filters = [
    'title'    => mb_substr(trim(str_input($input, 'title')), 0, 100),
    'type'     => $type,
    'category' => $category,
    'rating'   => $rating,
];

try {
    json_response((new Movies(db()))->search($filters, $page, $pageSize, $order));
} catch (Throwable $e) {
    error_log('movie-search failed: ' . $e->getMessage());
    json_response(['error' => 'Search failed.'], 500);
}
