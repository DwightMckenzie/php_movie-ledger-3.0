<?php
declare(strict_types=1);

/**
 * Single-user login. The password hash comes from APP_PASSWORD_HASH.
 * If no hash is configured, login is refused (fails closed).
 */

function start_session(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'secure'   => !empty($_SERVER['HTTPS']),
        'samesite' => 'Lax',
    ]);
    session_start();
}

function is_logged_in(): bool
{
    return !empty($_SESSION['auth_ok']);
}

/** Send visitors to the login page, or return 401 JSON for AJAX calls. */
function require_login(bool $json = false): void
{
    if (is_logged_in()) {
        return;
    }
    if ($json) {
        json_response(['error' => 'Not logged in.'], 401);
    }
    redirect(app_base_url() . 'login.php');
}

function attempt_login(string $password): bool
{
    $hash = app_config('APP_PASSWORD_HASH');
    if ($hash === '' || !password_verify($password, $hash)) {
        return false;
    }
    session_regenerate_id(true);
    $_SESSION['auth_ok'] = true;
    return true;
}

function logout(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}

function login_configured(): bool
{
    return app_config('APP_PASSWORD_HASH') !== '';
}

/**
 * Relative prefix from the calling script back to the project root.
 * Pages in the root return './', controllers/ and ajax/ return '../'.
 * Measured from the project root, so it also works in a subfolder.
 */
function app_base_url(): string
{
    $root = str_replace('\\', '/', dirname(__DIR__));
    $script = str_replace('\\', '/', realpath($_SERVER['SCRIPT_FILENAME'] ?? '') ?: '');
    $dir = dirname($script);
    $relative = trim(substr($dir, strlen($root)), '/');
    return $relative === '' ? './' : str_repeat('../', substr_count($relative, '/') + 1);
}
