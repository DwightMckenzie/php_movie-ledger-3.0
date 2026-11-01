<?php
  /**
   * Add duration of current video
   * provided from ajax
   * 
   */
  require_once "../includes/config.php";

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $movieId = $_GET['movie-id'];

    // sql to delete a record
    $sql = "DELETE FROM movie_list WHERE id='$movieId'";

    // use exec() because no results are returned
    if ($conn->exec($sql)) {
      echo "Record deleted successfully";

      header("Location: ../index.php");
      exit;

    } else {
      echo "something went wrong";
    }

  }




































    // /**
    //  * set the variable values
    //  * 
    //  * ternay operater, if 'all' is posted, then value is empty
    //  * 
    //  */
    // $movieTitle = $postData['movie-title'] == 'null' ? '' : $postData['movie-title'];
    // $movieType = $postData['movie-type'] == 'all' ? '' : $postData['movie-type'];
    // $movieCategory = $postData['movie-category'] == 'all' ? '' : $postData['movie-category'];
    // $movieRating = $postData['movie-rating'] == 'all' ? '' : $postData['movie-rating'];

    // // initiate array
    // $sqlData = array();

    // // Using LIKE clause to search the value in the data column
    // $sql = "SELECT * FROM movie_list
    //         WHERE title LIKE '%$movieTitle%'
    //         AND type LIKE '%$movieType%'
    //         AND category LIKE '%$movieCategory%'
    //         AND rating LIKE '%$movieRating%'
    //         ORDER BY rating DESC";

    // // run query
    // $query = $conn->query($sql);

    // // loop through array 
    // while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    //   $sqlData[] = $row;
    // }
    
    // echo json_encode($sqlData);










  // } else {
  //   echo "No data passed into file";
  // }

?>