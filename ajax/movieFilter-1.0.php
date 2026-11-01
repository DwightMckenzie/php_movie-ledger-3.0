<?php
	/**
	* Add duration of current video
	* provided from ajax
	* 
	*/
	require_once "../includes/config.php";

	// Get the raw POST data
	$jsonData = file_get_contents("php://input");

	// Decode the JSON data into a PHP associative array
	$postData = json_decode($jsonData, true);

	// if data passed
	if( $postData ) {

		/**
		* set the variable values
		* 
		* ternay operater, if 'all' is posted, then value is empty
		* 
		*/
		$movieOrder = $postData['movie-order'] == 'uo' ? 'desc' : $postData['movie-order']; 
		$movieTitle = $postData['movie-title'] == 'null' ? '' : $postData['movie-title'];
		$movieType = $postData['movie-type'] == 'all' ? '' : $postData['movie-type'];
		$movieCategory = $postData['movie-category'] == 'all' ? '' : $postData['movie-category'];
		$movieRating = $postData['movie-rating'] == 'all' ? '' : $postData['movie-rating'];

		// initiate array
		$sqlData = array();

		// Using LIKE clause to search the value in the data column
		$sql = "SELECT * FROM movie_list
				WHERE title LIKE '%$movieTitle%'
				AND type LIKE '%$movieType%'
				AND category LIKE '%$movieCategory%'
				AND rating LIKE '%$movieRating%'
				ORDER BY title $movieOrder";

		// run query
		$query = $conn->query($sql);

		// loop through array 
		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$sqlData[] = $row;
		}

		echo json_encode($sqlData);

	} else {
		
		echo "No data passed into file";
	
	}

?>