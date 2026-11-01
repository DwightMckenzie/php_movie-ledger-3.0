<?php

	/* keep for displaying errors */
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	// ---- connect to the DB
	require_once "../includes/config.php";

	// ---- Query the movies table ----
	$stmt = $conn->query('SELECT * FROM movie_list');
	 
	// ---- Set headers to force a CSV download ----
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=movies_ledger_export_' . date('Y-m-d') . '.csv');
	 
	// ---- Open output stream ----
	$output = fopen('php://output', 'w');
	 
	// ---- Write column headers (grab keys from the first row) ----
	$firstRow = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if ($firstRow) {
		fputcsv($output, array_keys($firstRow));
		fputcsv($output, $firstRow);
	 
		// ---- Write remaining rows ----
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			fputcsv($output, $row);
		}
	}
	 
	// ---- Cleanup ----
	fclose($output);
	$conn = null;
	exit;

?>