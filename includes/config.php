<?php
	declare(strict_types=1);

	/**
	 * Application configuration.
	 *
	 * Values come from environment variables first, then from the git-ignored
	 * includes/config.local.php file (copy config.local.example.php to start).
	 * No credentials live in this file.
	 */

	function app_config(string $key, string $default = ''): string
	{
		static $local = null;
		if ($local === null) {
			$file = __DIR__ . '/config.local.php';
			$local = is_file($file) ? (array) require $file : [];
		}

		$env = getenv($key);
		if ($env !== false && $env !== '') {
			return $env;
		}

		return isset($local[$key]) ? (string) $local[$key] : $default;
	}

	function app_debug(): bool
	{
		return app_config('APP_DEBUG', '0') === '1';
	}

	/**
	 * Shared PDO connection. Created on first use.
	 *
	 * Failures are logged server-side; callers receive a generic RuntimeException
	 * so no database details reach the browser.
	 */
	function db(): PDO
	{
		static $pdo = null;
		if ($pdo instanceof PDO) {
			return $pdo;
		}

		$dsn = sprintf(
			'mysql:host=%s;dbname=%s;charset=utf8mb4',
			app_config('DB_HOST', 'localhost'),
			app_config('DB_NAME', 'movies_db')
		);

		try {
			$pdo = new PDO($dsn, app_config('DB_USER'), app_config('DB_PASS'), [
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES   => false,
			]);
		} catch (PDOException $e) {
			error_log('Database connection failed: ' . $e->getMessage());
			throw new RuntimeException('Database unavailable.', 0, $e);
		}

		return $pdo;
	}
