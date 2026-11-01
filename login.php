<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    require_post_with_csrf();

    if (!login_configured()) {
        $error = 'Login is not set up yet. Set APP_PASSWORD_HASH in includes/config.local.php.';
    } elseif (attempt_login(str_input($_POST, 'password'))) {
        redirect('index.php');
    } else {
        $error = 'Incorrect password.';
    }
}

$page_title = 'Log in';
$show_nav = false;
require __DIR__ . '/includes/header.php';
?>

<div class="container py-5" style="max-width: 420px;">
	<h1 class="h4 mb-4">Log in to Movie Ledger</h1>

	<?php if ($error !== ''): ?>
		<div class="alert alert-danger" role="alert"><?= e($error) ?></div>
	<?php endif; ?>

	<form method="post" action="login.php">
		<?= csrf_field() ?>
		<div class="mb-3">
			<label for="password" class="form-label">Password</label>
			<input type="password" class="form-control" id="password" name="password" required autocomplete="current-password" autofocus>
		</div>
		<button type="submit" class="btn btn-primary">Log in</button>
	</form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
