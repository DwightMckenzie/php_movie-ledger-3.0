<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/classes/Movies.php';
require_once __DIR__ . '/classes/CoverUpload.php';

ini_set('display_errors', app_debug() ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

start_session();
