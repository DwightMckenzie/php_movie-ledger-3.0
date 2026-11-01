<?php
/**
 * Copy this file to includes/config.local.php (git-ignored) and fill it in.
 * Environment variables with the same names take precedence.
 */
return [
    'DB_HOST'           => 'localhost',
    'DB_NAME'           => 'movies_db',
    'DB_USER'           => '',
    'DB_PASS'           => '',
    // Generate with:  php -r 'echo password_hash("your-password", PASSWORD_DEFAULT), PHP_EOL;'
    'APP_PASSWORD_HASH' => '',
    // Set to 1 only while developing. Keep 0 in production.
    'APP_DEBUG'         => '0',
];
