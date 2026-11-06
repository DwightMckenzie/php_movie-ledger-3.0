<?php
	declare(strict_types=1);

	/** HTML-escape any value for output. */
	function e(mixed $value): string
	{
		return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	}

	/** Read a string field from request data; arrays and missing keys become ''. */
	function str_input(array $data, string $key): string
	{
		$value = $data[$key] ?? '';
		return is_string($value) ? $value : '';
	}

	/** Return a positive integer or null. */
	function positive_int(mixed $value): ?int
	{
		$int = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		return $int === false ? null : $int;
	}

	/**
	 * Render <option> tags. $options maps value => label.
	 * An optional placeholder adds a leading option with an empty value.
	 */
	function select_options(array $options, string $selected = '', ?string $placeholder = null): string
	{
		$html = '';
		if ($placeholder !== null) {
			$html .= '<option value="">' . e($placeholder) . '</option>';
		}
		foreach ($options as $value => $label) {
			$isSelected = (string) $value === $selected ? ' selected' : '';
			$html .= '<option value="' . e($value) . '"' . $isSelected . '>' . e($label) . '</option>';
		}
		return $html;
	}

	/** Neutralise spreadsheet formula injection in a CSV cell. */
	function csv_safe(mixed $value): mixed
	{
		if (is_string($value) && $value !== '' && strpos('=+-@' . "\t\r", $value[0]) !== false) {
			return "'" . $value;
		}
		return $value;
	}

	function json_response(array $data, int $status = 200): never
	{
		http_response_code($status);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data, JSON_THROW_ON_ERROR);
		exit;
	}

	function redirect(string $location): never
	{
		header('Location: ' . $location);
		exit;
	}

	/* ---- Flash messages (shown once on the next page) ---- */

	function flash_set(string $type, string|array $messages): void
	{
		$_SESSION['flash'][] = ['type' => $type, 'messages' => (array) $messages];
	}

	/** @return list<array{type: string, messages: list<string>}> */
	function flash_pull(): array
	{
		$items = $_SESSION['flash'] ?? [];
		unset($_SESSION['flash']);
		return $items;
	}

	/* ---- CSRF protection ---- */

	function csrf_token(): string
	{
		if (empty($_SESSION['csrf'])) {
			$_SESSION['csrf'] = bin2hex(random_bytes(32));
		}
		return $_SESSION['csrf'];
	}

	function csrf_field(): string
	{
		return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
	}

	/** Stop the request unless it is a POST carrying a valid CSRF token. */
	function require_post_with_csrf(): void
	{
		if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
			http_response_code(405);
			exit('Method not allowed.');
		}
		$sent = str_input($_POST, 'csrf_token');
		$expected = $_SESSION['csrf'] ?? '';
		if ($expected === '' || !hash_equals($expected, $sent)) {
			http_response_code(403);
			exit('Your session has expired. Go back, reload the page and try again.');
		}
	}
