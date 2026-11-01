<?php
declare(strict_types=1);

/**
 * Page header. Expects these variables from the calling page:
 *   $page_title (string), $show_nav (bool, default true), $page_scripts (list of paths).
 * Also pulls any pending flash messages.
 */

$page_title = $page_title ?? 'Movie Ledger';
$show_nav = $show_nav ?? true;
$base = app_base_url();
$flashes = flash_pull();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<title><?= e($page_title) ?> · Movie Ledger</title>
	<link rel="icon" type="image/png" href="<?= e($base) ?>assets/imgs/dm_favicon.png">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
		  integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?= e($base) ?>assets/styles/style.css">
</head>
<body>
<nav class="navbar navbar-light bg-light sticky-top shadow-sm">
	<div class="container-fluid">
		<a class="navbar-brand mb-0 h1" href="<?= e($base) ?>index.php">
			<i class="bi bi-film me-2"></i>Movie Ledger
		</a>
		<?php if ($show_nav && is_logged_in()): ?>
			<ul class="nav align-items-center">
				<li class="nav-item"><a class="nav-link" href="<?= e($base) ?>index.php">Movies</a></li>
				<li class="nav-item"><a class="nav-link" href="<?= e($base) ?>ajax/movie-export.php">Export CSV</a></li>
				<li class="nav-item">
					<form method="post" action="<?= e($base) ?>logout.php" class="m-0">
						<?= csrf_field() ?>
						<button type="submit" class="btn btn-link nav-link">Log out</button>
					</form>
				</li>
			</ul>
		<?php endif; ?>
	</div>
</nav>

<?php foreach ($flashes as $flash): ?>
	<div class="container-fluid px-3 pt-3">
		<div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible mb-0" role="alert">
			<?php foreach ($flash['messages'] as $message): ?>
				<div><?= e($message) ?></div>
			<?php endforeach; ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	</div>
<?php endforeach; ?>
