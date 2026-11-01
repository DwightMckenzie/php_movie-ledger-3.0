<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();
require_post_with_csrf();

$id = positive_int($_POST['id'] ?? null);
if ($id === null) {
    http_response_code(400);
    exit('Invalid movie.');
}

$movies = new Movies(db());
$existing = $movies->find($id);
if ($existing === null) {
    flash_set('error', 'That movie no longer exists.');
    redirect('../index.php');
}

try {
    $movies->delete($id);
} catch (Throwable $e) {
    error_log('movieDelete failed: ' . $e->getMessage());
    flash_set('error', 'The movie could not be deleted. Please try again.');
    redirect('../single-movie.php?id=' . $id);
}

CoverUpload::delete($existing['cover']);

flash_set('success', 'Movie deleted.');
redirect('../index.php');
