<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/movie-fields.php';

require_login();

$page_title = 'Movie details';
$movie = null;
$loadError = false;

$id = positive_int($_GET['id'] ?? null);
if ($id !== null) {
    try {
        $movie = (new Movies(db()))->find($id);
    } catch (Throwable $e) {
        error_log('single-movie failed: ' . $e->getMessage());
        $loadError = true;
    }
}

if ($movie === null) {
    http_response_code($loadError ? 503 : 404);
    $page_title = $loadError ? 'Unavailable' : 'Not found';
    require __DIR__ . '/includes/header.php';
    ?>
    <div class="container py-5">
		<h1 class="h4"><?= $loadError ? 'The movie list is unavailable right now.' : 'Movie not found.' ?></h1>
		<a href="index.php" class="btn btn-secondary btn-sm mt-3">Return to list</a>
	</div>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $movie['title'];
$page_scripts = [];
require __DIR__ . '/includes/header.php';

$dash = static fn (?string $value): string => ($value === null || $value === '') ? '—' : $value;
?>

<div class="container single">
	<div class="row single movie-wrapper justify-content-center py-4 g-4">

		<main class="content col-md-4">
			<?php if (!empty($movie['cover'])): ?>
				<img src="<?= e($movie['cover']) ?>" class="img-fluid d-block mx-auto rounded"
					 alt="Cover of <?= e($movie['title']) ?>">
			<?php else: ?>
				<div class="cover-placeholder d-flex align-items-center justify-content-center text-muted bg-light border rounded mx-auto"
					 style="aspect-ratio: 2 / 3; max-width: 400px;" role="img" aria-label="No cover image">
					No cover
				</div>
			<?php endif; ?>
		</main>

		<aside class="sidebar col-md-4 col-lg-3 details">
			<h3 class="h4 mb-3"><?= e($movie['title']) ?></h3>

			<dl class="row mb-4">
				<dt class="col-5 text-muted">Type</dt>
				<dd class="col-7"><?= e($movie['type']) ?></dd>
				<dt class="col-5 text-muted">Category</dt>
				<dd class="col-7"><?= e(Movies::CATEGORIES[$movie['category']] ?? $movie['category']) ?></dd>
				<dt class="col-5 text-muted">Rating</dt>
				<dd class="col-7"><?= e(Movies::RATINGS[$movie['rating']] ?? $movie['rating']) ?></dd>
				<dt class="col-5 text-muted">Release date</dt>
				<dd class="col-7"><?= e($dash($movie['release_date'])) ?></dd>
				<dt class="col-5 text-muted">Entry date</dt>
				<dd class="col-7"><?= e($dash($movie['entry_date'])) ?></dd>
			</dl>

			<h4 class="h6 text-uppercase text-muted fw-bold">Notes</h4>
			<p class="mb-4"><?= $movie['note'] ? nl2br(e($movie['note'])) : '<span class="text-muted">No notes.</span>' ?></p>

			<div class="d-flex flex-wrap gap-2">
				<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editMovieModal">Edit details</button>
				<a href="index.php" class="btn btn-secondary btn-sm">Return to list</a>
			</div>

			<form method="post" action="controllers/movieDelete.php" class="mt-4"
				  data-confirm="Delete “<?= e($movie['title']) ?>”? This cannot be undone.">
				<?= csrf_field() ?>
				<input type="hidden" name="id" value="<?= (int) $movie['id'] ?>">
				<button type="submit" class="btn btn-outline-danger btn-sm">Delete movie</button>
			</form>
		</aside>
	</div>
</div>

<!-- Edit movie -->
<div class="modal fade" id="editMovieModal" tabindex="-1" aria-labelledby="editMovieModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable">
		<div class="modal-content">
			<form method="post" action="controllers/movieUpdate.php" enctype="multipart/form-data">
				<?= csrf_field() ?>
				<input type="hidden" name="id" value="<?= (int) $movie['id'] ?>">
				<div class="modal-header">
					<h5 class="modal-title" id="editMovieModalLabel">Edit details</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<?= render_movie_fields('edit-', $movie) ?>
					<?php if (!empty($movie['cover'])): ?>
						<p class="small text-muted mb-0">Uploading a new image replaces the current cover.</p>
					<?php endif; ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary btn-sm">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
