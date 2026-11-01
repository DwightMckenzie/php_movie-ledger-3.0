<?php
  /**
   * Add duration of current video
   * provided from ajax
   * 
   */
  require_once "../includes/config.php";

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movieId = $_GET['movie-id'];
    // $movieTitle = $_POST['movie-title'];
    // $movieType = $_POST['movie-type'];
    // $movieCategory = $_POST['movie-category'];
    // $movieRating = $_POST['movie-rating'];

    $movieTitle = "";
    $movieType = "";
    $movieCategory = "";
    $movieRating = "";
    $movieRelease = "";

    // get current data for fields not being altered
    $sql_db = "SELECT * FROM movie_list WHERE id='$movieId'";
    $res_db = $conn->query($sql_db);
    $row = $res_db->fetch(PDO::FETCH_ASSOC);

    // if variable not empty use form value, else use DB Value
    if (!empty($_POST['movie-title'])) {
      $movieTitle = $_POST['movie-title'];
    } else {
      $movieTitle = $row['title'];
    }
    
    if ($_POST['movie-type'] != 'all') {
      $movieType = $_POST['movie-type'];
    } else {
      $movieType = $row['type'];
    }
    
    if ($_POST['movie-category'] != 'all') {
      $movieCategory = $_POST['movie-category'];
    } else {
      $movieCategory = $row['category'];
    }
    
    if ($_POST['movie-rating'] != 'all') {
      $movieRating = $_POST['movie-rating'];
    } else {
      $movieRating = $row['rating'];
    }

    if ($_POST['movie-release'] != '') {
      $movieRelease = $_POST['movie-release'];
    } else {
      $movieRelease = $row['release-date'];
    }

    // var_dump($movieTitle, $movieType, $movieCategory, $movieRating);

    // Validate required fields
    // if (!isset($data['id'], $data['name'], $data['email'], $data['age'])) {
    //     die("Missing required fields.");
    // }

    $sql = "UPDATE movie_list
            SET title=:title, type=:type, category=:category, rating=:rating, release_date=:release_date
            WHERE id=:id";
    $query = $conn->prepare($sql);
    $query->bindParam(':id', $movieId, PDO::PARAM_INT);
    $query->bindParam(':title', $movieTitle, PDO::PARAM_STR);
    $query->bindParam(':type', $movieType, PDO::PARAM_STR);
    $query->bindParam(':category', $movieCategory, PDO::PARAM_STR);
    $query->bindParam(':rating', $movieRating, PDO::PARAM_STR);
    $query->bindParam(':release_date', $movieRelease, PDO::PARAM_STR);

    if ($query->execute()) {
    
      echo "Record updated successfully.";

      // 
      header("Location: ../single-movie.php?id=" . $movieId );
      exit; // Always exit after a header redirect
    
    } else {
    
      echo "something went wrong";
    
    } 

    // $sql = "INSERT INTO movie_list (title, type, category, rating)
    //         VALUES ('$movieTitle', '$movieType', '$movieCategory', '$movieRating')";

    // if ($conn->exec($sql)) {
    //   echo "Success!";

    //   header("Location: ../index.php");
    //   exit;

    // } else {
    //   echo "something went wrong";
    // }

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