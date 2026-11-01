<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_post_with_csrf();
logout();
flash_set('success', 'You have been logged out.');
redirect('login.php');
