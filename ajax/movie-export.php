<?php
declare(strict_types=1);

/** Download the full movie list as CSV. Requires a logged-in session. */

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="movie-ledger-' . date('Y-m-d') . '.csv"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

try {
    $movies = new Movies(db());
    $out = fopen('php://output', 'w');
    fputcsv($out, ['id', 'title', 'type', 'category', 'rating', 'release_date', 'note', 'entry_date']);
    foreach ($movies->exportRows() as $row) {
        fputcsv($out, array_map('csv_safe', $row));
    }
    fclose($out);
} catch (Throwable $e) {
    error_log('movie-export failed: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Export failed.';
}
