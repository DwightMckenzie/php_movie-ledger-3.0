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
    http_response_code(404);
    exit('Movie not found.');
}

[$clean, $errors] = Movies::validate($_POST);

$newCover = null;
try {
    $newCover = CoverUpload::fromRequest('movie-cover');
} catch (InvalidArgumentException $e) {
    $errors[] = $e->getMessage();
}

$back = '../single-movie.php?id=' . $id;
if ($errors) {
    flash_set('error', $errors);
    redirect($back);
}

try {
    $movies->update($id, $clean, $newCover);
} catch (Throwable $e) {
    error_log('movieUpdate failed: ' . $e->getMessage());
    CoverUpload::delete($newCover);
    flash_set('error', 'The movie could not be saved. Please try again.');
    redirect($back);
}

// Replaced cover: remove the old file now that the row points at the new one.
if ($newCover !== null) {
    CoverUpload::delete($existing['cover']);
}

flash_set('success', 'Movie updated.');
redirect($back);
