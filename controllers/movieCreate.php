<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();
require_post_with_csrf();

[$clean, $errors] = Movies::validate($_POST);

$cover = null;
try {
    $cover = CoverUpload::fromRequest('movie-cover');
} catch (InvalidArgumentException $e) {
    $errors[] = $e->getMessage();
}

if ($errors) {
    flash_set('error', $errors);
    redirect('../index.php');
}

try {
    $id = (new Movies(db()))->create($clean, $cover);
} catch (Throwable $e) {
    error_log('movieCreate failed: ' . $e->getMessage());
    CoverUpload::delete($cover);
    flash_set('error', 'The movie could not be saved. Please try again.');
    redirect('../index.php');
}

flash_set('success', 'Movie added.');
redirect('../single-movie.php?id=' . $id);
