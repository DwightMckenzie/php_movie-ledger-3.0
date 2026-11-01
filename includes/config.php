<?php

  /**
   * connection to the database
   * 
   * 
   * 
   */

  $servername = "localhost";
  $dbname = "movies_db";
  $username = "dwight1980";
  $password = "Mckenzie1980%"; 

  try {
    // create query
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // echo "Connected successfully";
  } catch(PDOException $e) {
    echo "Sorry! Connection failed: " . $e->getMessage();
  }

?>