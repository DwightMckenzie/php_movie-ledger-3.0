<?php
  
	$pg_type = "archive";
  
  /**
   * this page holds the GUI for the movie list
   *  
   */

  require_once("includes/header.php");
?>

<div class="container-fluid archive">

	<div class="row">
	  <!-- <div class="movie-wrapper"> -->
		<!-- <div class="movie-filter" id="movie-filter"> -->
		<aside class="sidebar col-md-3 col-lg-2 bg-light sidebar py-3 px-3" id="movie-sidebar">
		
			<div class="">
				<div class="section-head">
					<h6 class="text-uppercase text-muted small fw-bold mb-3">Filters</h6>
				</div>
			
				<form method="post">
					<div class="mb-3">
						<label for="search-title" class="form-label small fw-semibold">Search Title:</label>
						<!-- <input type="search" id="search-list" name="search-list" value=""> -->
						<input type="search" class="form-control form-control-sm" id="search-title" name="search-title" value="" placeholder="Type three letters...">
					</div>
			  
					<div class="mb-3">
						<label for="movie-type" class="form-label small fw-semibold"><strong>Type:</strong></label>
						<select class="form-select form-select-sm" name="movie-type" id="movie-type" value="">
							<option value="all" selected>All</option>
							<option value="Movie">Movie</option>
							<option value="Series">Series</option>
							<option value="Mini Series">Mini Series</option>
						</select>
					</div>
			  
					<div class="mb-3">
						<label for="movie-category" class="form-label small fw-semibold"><strong>Category:</strong></label>
						<select class="form-select form-select-sm" name="movie-category" id="movie-category" value="">
							<option value="all" selected>All</option>
							<option value="action">Action</option>
							<option value="adventure">Adventure</option>
							<option value="classic">Classic</option>
							<option value="comedy">Comedy</option>
							<option value="crime">Crime</option>
							<option value="documentary">Documentary</option>
							<option value="drama">Drama</option>
							<option value="family">Family</option>
							<option value="fantasy">Fantasy</option>
							<option value="horror">Horror</option>
							<option value="romance">Romance</option>
							<option value="scifi">Sifi</option>
							<option value="thriller">Thriller</option>
							<option value="western">Western</option>
						</select>
					</div>
			  
					<div class="mb-3">
						<label for="movie-rating" class="form-label small fw-semibold"><strong>Rating:</strong></label>
						<select class="form-select form-select-sm" name="movie-rating" id="movie-rating" value="">
							<option value="all" selected>All</option>
							<option value="R">R</option>
							<option value="NR">NR</option>
							<option value="PG">PG</option>
							<option value="PG-13">PG-13</option>
							<option value="M">Mature</option>
						</select>
					</div>
			  
					<div class="mb-3">
						<div class="d-flex align-items-center justify-content-evenly">
							<button type="button" class="btn btn-outline-secondary btn-sm w-50 me-3" id="apply">Apply</button>
							<button type="button" class="btn btn-outline-secondary btn-sm w-50" id="reset">Reset</button>
						</div>
					</div>
				</form>
				
				<!-- toggle modal -->
				<div class="mb-3">
					<button type="button" class="btn btn-outline-primary btn-sm w-50 mb-4" data-bs-toggle="modal" data-bs-target="#addMovieModal">
						<i class="bi bi-plus-lg me-1"></i> Add Movie
					</button>
				</div>
				
			</div>	

		</aside>
		
		<main class="content col-md-9 col-lg-10 py-4 px-4" id="movie-list">
			<div class="row mb-3">
				<div class="col-md-6">
					<div id="results-found"><strong>Results:</strong> <span></span> </div>
				</div>
				<div class="col-md-6 d-flex justify-content-end">
					<div class="settings">
						<label for="rows-per-page" class="form-label small"><strong>Movies Per Page:</strong></label>
						<select class="form-select form-select-sm" id="page-size" value="">
							<option value="25" selected>25</option>
							<option value="50">50</option>
							<option value="75">75</option>
							<option value="100">100</option>
						</select>
					</div>
					<div class="">
						<label for="movie-order" class="form-label small"><strong>Order By:</strong></label>
						<select class="form-select form-select-sm" name="movie-order" id="movie-order" value="">
							<option value="uo" selected>Unordered</option>
							<option value="asc">A-Z</option>
							<option value="desc">Z-A</option>
						</select>
					</div>
				</div>
			</div>
			<div class="list" id="list">
				<table class="table table-striped table-sm table-hover">
					<thead class="table-light">
						<tr>
							<th>Title</th>
							<th>Type</th>
							<th>Category</th>
							<th>Rating</th>
							<th>Details</th>
						</tr>
					</thead>
					<tbody></tbody>
					<!-- <php
					$movieList = new Movies($conn);
					echo $movieList->movieList();
					?> -->
				</table>
			</div>
			<nav class="" aria-label="Page navigation example">
				<ul class="pagination pagination-sm justify-content-center" id="pagination"></ul>
			</nav>
		</main>
		<!-- </div> -->

	</div>

	<div class="row">

		<!-- Add Movie Modal -->
		<div class="modal fade" id="addMovieModal" tabindex="-1" aria-labelledby="addMovieModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<form method="post" action="controllers/movieCreate.php">
					
						<div class="modal-header">
							<h5 class="modal-title" id="addMovieModalLabel">Add a Movie</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>

						<div class="modal-body">
							<div class="mb-3">
								<label for="movie-title" class="form-label"><strong>Title:</strong></label> 
								<input type="text" class="form-control form-control-sm" name="movie-title" value="" required>
							</div>
							<div class="mb-3">
								<label for="movie-type" class="form-label"><strong>Type:</strong></label>
								<select class="form-select form-select-sm" name="movie-type" id="movie-type" value="">
									<option value="all" selected>All</option>
									<option value="Movie">Movie</option>
									<option value="Series">Series</option>
									<option value="Mini Series">Mini Series</option>
								</select>
							</div>
							<div class="mb-3">
								<label for="movie-category" class="form-label"><strong>Category:</strong></label>
								<select class="form-select form-select-sm" name="movie-category" id="movie-category" value="">
									<option value="all" selected>All</option>
									<option value="Action">Action</option>
									<option value="Adventure">Adventure</option>
									<option value="Classic">Classic</option>
									<option value="Comedy">Comedy</option>
									<option value="Crime">Crime</option>
									<option value="Documentary">Documentary</option>
									<option value="Drama">Drama</option>
									<option value="Family">Family</option>
									<option value="Fantasy">Fantasy</option>
									<option value="Horror">Horror</option>
									<option value="Romance">Romance</option>
									<option value="Scifi">Sci-fi</option>
									<option value="Thriller">Thriller</option>
									<option value="Western">Western</option>
								</select>
							</div>
							<div class="mb-3">
								<label for="movie-rating" class="form-label"><strong>Rating:</strong></label>
								<select class="form-select form-select-sm" name="movie-rating" id="movie-rating" value="">
									<option value="all" selected>All</option>
									<option value="R">R</option>
									<option value="NR">NR</option>
									<option value="PG">PG</option>
									<option value="PG-13">PG-13</option>
									<option value="M">Mature</option>
								</select>
							</div>
							<div class="mb-3">
								<label for="movie-release" class="form-label"><strong>Release Date:</strong></label>
								<input type="date" class="form-control form-control-sm" name="movie-release" id="movie-release" value="">
							</div>
							
							<!-- <div>
							<label for="movie-type">Type:</label> 
							<input type="text" name="movie-type" value="" required>
							</div>     
							<div>
							<label for="movie-category">Category:</label>
							<input type="text" name="movie-category" value="" required>
							</div>     
							<div>
							<label for="movie-rating">Rating:</label>
							<input type="text" name="movie-rating" value="" required>
							</div> -->
							
							<div class="mb-3">
								<label for="movie-cover" class="form-label">Cover Image</label>
								<input type="file" class="form-control form-control-sm" id="movie-cover" name="movie-cover" accept="image/jpeg,image/png,image/webp" required>
							</div>
							
							<div class="mb-3">
								<label for="movie-note" class="form-label">Notes</label>
								<textarea class="form-control" id="movie-note" name="movie-note" rows="2"></textarea>
							</div>
							
						</div>	
						
						<div class="modal-footer">	
							<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary btn-sm">Save</button>
						</div>
						
					</form>
			  
					<!-- ORIG
					<form id="addMovieForm">
					  <div class="modal-header">
						<h5 class="modal-title" id="addMovieModalLabel">Add a Movie</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					  </div>
					  <div class="modal-body">

						<div class="mb-3">
						  <label for="titleInput" class="form-label">Title <span class="text-danger">*</span></label>
						  <input type="text" class="form-control" id="titleInput" required>
						</div>

						<div class="row">
						  <div class="col-6 mb-3">
							<label for="yearInput" class="form-label">Year</label>
							<input type="number" class="form-control" id="yearInput" min="1888" max="2100" placeholder="2024">
						  </div>
						  <div class="col-6 mb-3">
							<label for="genreInput" class="form-label">Genre</label>
							<input type="text" class="form-control" id="genreInput" placeholder="Drama, Sci-Fi..." list="genreDatalist">
							<datalist id="genreDatalist"></datalist>
						  </div>
						</div>

						<div class="mb-3">
						  <label for="directorInput" class="form-label">Director</label>
						  <input type="text" class="form-control" id="directorInput">
						</div>

						<div class="row">
						  <div class="col-6 mb-3">
							<label for="statusInput" class="form-label">Status</label>
							<select class="form-select" id="statusInput">
							  <option value="Watchlist">Watchlist</option>
							  <option value="Watching">Watching</option>
							  <option value="Watched">Watched</option>
							</select>
						  </div>
						  <div class="col-6 mb-3">
							<label for="ratingInput" class="form-label">Rating (0–5)</label>
							<input type="number" class="form-control" id="ratingInput" min="0" max="5" step="1" placeholder="0">
						  </div>
						</div>

						<div class="mb-3">
						  <label for="posterInput" class="form-label">Poster image URL</label>
						  <input type="url" class="form-control" id="posterInput" placeholder="https://...">
						</div>
						
						<div class="mb-3">
						  <label for="notesInput" class="form-label">Notes</label>
						  <textarea class="form-control" id="notesInput" rows="2"></textarea>
						</div>

					  </div>
					  
					  <div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary btn-sm">Save Movie</button>
					  </div>
					</form>-->
				
				</div>
			</div>
		</div>

	</div>

</div>

<?php
  require_once("includes/footer.php");
?>
<!-- <script src="./assets/js/movieFilter-1.1.js" type="text/javascript"></script> -->
<!-- <script src="./assets/js/movieFilter-1.2.js" type="text/javascript"></script> -->
<script src="./assets/js/movieFilter-1.3.js" type="text/javascript"></script>
<!-- <script src="./assets/js/moviePaginate.js" type="text/javascript"></script> -->
 