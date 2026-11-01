<?php


	$pg_type = "single";


  /**
   * Single page for showing movie details
   * 
   */
  require_once('includes/config.php');  
  
  // for updating ??????
  // if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
  // }

  /**
   * retrieve the selected movie ID
   * create array for the loop
   * 
   */
  $movieId = $_GET['id'];
  $sql = "SELECT * FROM movie_list WHERE id=$movieId";
  $result = $conn->query($sql);

  // head data
  require_once("includes/header.php");

  /**
   * row data
   */
  while ($row = $result->fetch()) {

?>

<div class="container single">

	<div class="row single movie-wrapper justify-content-center">

		<main class="content col-md-4 justify-content-center">
			<picture>
				<img src="https://placehold.co/400x600" class="mx-auto d-block" alt="placholder" />
				<figcaption class="figure-caption text-center">A caption for the above image.</figcaption>
			</picture>
		</main>
		
		<aside class="sidebar col-md-3 bg-light details">
			
			<div class="mb-3">
				<h3>Movie Details:</h3>
			</div>
				
			<div class="d-flex mb-3">
				<p class="me-1"><strong>Title:</strong></p> 
				<p><?php echo $row['title']; ?></p>
			</div>
			<div class="d-flex mb-3">
				<p class="me-1"><strong>Type:</strong></p> 
				<p><?php echo $row['type']; ?></p>
			</div>
			<div class="d-flex mb-3">
				<p class="me-1"><strong>Category:</strong></p>
				<p><?php echo $row['category']; ?></p>
			</div>
			<div class="d-flex mb-3">
				<p class="me-1"><strong>Rating:</strong></p>
				<p><?php echo $row['rating']; ?></p>
			</div>
			<div class="d-flex mb-3">
				<p class="me-1"><strong>Release Date:</strong></p>
				<p><?php echo $row['release_date']; ?></p>
			</div>
			<div class="d-flex mb-3">
				<h3>Inventory Details:</strong></h3>
			</div>
			<div class="d-flex mb-3">
				<p class="me-1"><strong>Entry Date:</strong></p>
				<p><?php echo $row['entry_date']; ?></p>
			</div>
			<div class="mb-3">
				<h3>Edit Details:</h3>
			</div>
			<div class="mb-3">
				<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMovieModal">Edit Details</button>
				<!-- <a href="index.php" class="btn btn-outline-secondary btn-sm" target="_self">Return to List</a> -->
			</div>
		</aside>	

	</div>

	<!-- Add Movie Modal -->
	<div class="row">
		<div class="modal fade" id="addMovieModal" tabindex="-1" aria-labelledby="addMovieModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">	
					
					<form method="post" action="controllers/movieUpdate.php?movie-id=<?php echo $row['id']; ?>">
					
						<div class="modal-header">
							<h5 class="modal-title" id="addMovieModalLabel">Edit Details</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
					
					
						<div class="modal-body">
					
							<div class="mb-3">
								<label for="movie-title" class="form-label"><strong>Title:</strong></label> 
								<input type="text" class="form-control form-control-sm" name="movie-title" value="" placeholder="<?php echo $row['title']; ?>">
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
							
							<div class="mb-3">
								<label for="notesInput" class="form-label">Notes</label>
								<textarea class="form-control" id="notesInput" rows="2"></textarea>
							</div>
							
							<!-- <div>
							<label for="movie-title">Title:</label> 
							<input type="text" name="movie-title" value="" placeholder="<php echo $row['title']; ?>">
							</div>     
							<div>
							<label for="movie-type">Type:</label> 
							<input type="text" name="movie-type" value="" placeholder="<php echo $row['type']; ?>">
							</div>     
							<div>
							<label for="movie-category">Category:</label>
							<input type="text" name="movie-category" value="" placeholder="<php echo $row['category']; ?>">
							</div>     
							<div>
							<label for="movie-rating">Rating:</label>
							<input type="text" name="movie-rating" value="" placeholder="<php echo $row['rating']; ?>">
							</div> -->
							<!-- <div>
							<input type="hidden" name="movie-id" value="<php echo $row['id']; ?>">
							</div> -->
							
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
								<button type="submit" class="btn btn-primary btn-sm" id="update-movie" >Update</button>
								<a href="controllers/movieDelete.php?movie-id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" target="_self" role="button">Delete</a>
							</div>
							
						</div>
							
					</form>
						
				</div>
			</div>
		</div>
	</div>
	
</div>



<!-- </div> -->

<?php

  } // for the while loop

  require_once("includes/footer.php");
?>