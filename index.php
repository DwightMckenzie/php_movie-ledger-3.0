<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/movie-fields.php';

require_login();

$page_title = 'Movies';
$page_scripts = ['assets/js/movie-filter.js'];
require __DIR__ . '/includes/header.php';
?>

<div class="container-fluid archive">
	<div class="row">
		<aside class="sidebar col-md-3 col-lg-2 bg-light py-3 px-3" id="movie-sidebar">
			<h6 class="section-head text-uppercase text-muted small fw-bold mb-3">Filters</h6>

			<form id="filter-form" novalidate>
				<div class="mb-3">
					<label for="search-title" class="form-label small fw-semibold">Search title</label>
					<input type="search" class="form-control form-control-sm" id="search-title" maxlength="100"
						   placeholder="Start typing a title…" autocomplete="off">
				</div>

				<div class="mb-3">
					<label for="movie-type" class="form-label small fw-semibold">Type</label>
					<select class="form-select form-select-sm" id="movie-type">
						<?= select_options(Movies::TYPES, '', 'All') ?>
					</select>
				</div>

				<div class="mb-3">
					<label for="movie-category" class="form-label small fw-semibold">Category</label>
					<select class="form-select form-select-sm" id="movie-category">
						<?= select_options(Movies::CATEGORIES, '', 'All') ?>
					</select>
				</div>

				<div class="mb-3">
					<label for="movie-rating" class="form-label small fw-semibold">Rating</label>
					<select class="form-select form-select-sm" id="movie-rating">
						<?= select_options(Movies::RATINGS, '', 'All') ?>
					</select>
				</div>

				<div class="d-flex gap-2 mb-3">
					<button type="submit" class="btn btn-outline-secondary btn-sm flex-fill" id="apply">Apply</button>
					<button type="button" class="btn btn-outline-secondary btn-sm flex-fill" id="reset">Reset</button>
				</div>
			</form>

			<button type="button" class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#addMovieModal">
				<i class="bi bi-plus-lg me-1"></i>Add movie
			</button>
		</aside>

		<main class="content col-md-9 col-lg-10 py-4 px-4" id="movie-list">
			<div class="row mb-3 align-items-end">
				<div class="col-md-6">
					<div id="results-found"><strong>Results:</strong> <span id="results-count">0</span></div>
				</div>
				<div class="col-md-6 d-flex justify-content-md-end gap-3 mt-2 mt-md-0">
					<div>
						<label for="page-size" class="form-label small fw-semibold mb-1">Per page</label>
						<select class="form-select form-select-sm" id="page-size">
							<?php foreach (Movies::PAGE_SIZES as $size): ?>
								<option value="<?= $size ?>"<?= $size === 25 ? ' selected' : '' ?>><?= $size ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div>
						<label for="movie-order" class="form-label small fw-semibold mb-1">Order</label>
						<select class="form-select form-select-sm" id="movie-order">
							<option value="desc" selected>Z-A (default)</option>
							<option value="asc">A-Z</option>
						</select>
					</div>
				</div>
			</div>

			<div id="list-error" class="alert alert-danger d-none" role="alert"></div>

			<div class="list table-responsive">
				<table class="table table-striped table-sm table-hover align-middle" id="movie-table">
					<thead class="table-light">
						<tr>
							<th scope="col">Title</th>
							<th scope="col">Type</th>
							<th scope="col">Category</th>
							<th scope="col">Rating</th>
							<th scope="col">Details</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>

			<nav aria-label="Movie list pages">
				<ul class="pagination pagination-sm justify-content-center flex-wrap" id="pagination"></ul>
			</nav>
		</main>
	</div>
</div>

<!-- Add movie -->
<div class="modal fade" id="addMovieModal" tabindex="-1" aria-labelledby="addMovieModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable">
		<div class="modal-content">
			<form method="post" action="controllers/movieCreate.php" enctype="multipart/form-data">
				<?= csrf_field() ?>
				<div class="modal-header">
					<h5 class="modal-title" id="addMovieModalLabel">Add a movie</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<?= render_movie_fields('add-') ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary btn-sm">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
