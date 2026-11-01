<?php

  require_once "includes/config.php";
  require_once "includes/classes/Movies.php";

  // start session
  // session_start();

  // if (!isset($_SESSION["userLoggedIn"])) {
  //   header("Location: register.php");
  // }

  // $userLoggedIn = $_SESSION['userLoggedIn'];

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title>PHP - Movie Ledger - 2.0</title>
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<link rel="icon" type="image/png" href="./assets/imgs/dm_favicon.png">
		
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		
		<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
		
		
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link rel="stylesheet" type="text/css" href="./assets/styles/style.css">
	</head>
	<body>
		<!-- Top navbar -->
		<!-- <nav class="navbar navbar-dark bg-dark"> -->
		<nav class="navbar navbar-light bg-light sticky-top shadow-sm <?php echo $pg_type; ?>">
			<div class="container-fluid">
		
				<span class="navbar-brand mb-0 h1">
					<i class="bi bi-film me-2"></i>Movie Ledger
				</span>
				
				
				<div class="navbar" id="navbarNavDropdown">
									
					<?php
						// $result = ($condition) ? $value_if_true : $value_if_false;
							
						if ($pg_type == "archive") {
					?>  <!-- archive -->
							<ul class="nav justify-content-end">
								<!-- <div class="nav justify-content-end"> -->
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
									Dropdown link</a>
									<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
										<li><a class="dropdown-item" href="#">Action</a></li>
										<li><a class="dropdown-item" href="#">Another action</a></li>
										<li><a href="./ajax/movieExportCsv-1.0.php" class="dropdown-item">Export CSV</a></li>
									</ul>
								</li>
								
								<!-- <a href="#" class="btn btn-outline-secondary btn-sm" onclick="exportCsv()">Export CSV</a> -->
								
								<!-- <a href="./ajax/movieExportCsv-1.0.php" class="btn btn-outline-secondary btn-sm">Export CSV</a>  -->
								
								<!-- <li class="nav-item">
								// <a class="nav-link active" aria-current="page" href="#">Export CSV</a>
								// </li> -->
								  
							</ul>
					<?php
						} else {
					?> <!-- single -->
							<ul class="nav justify-content-end">
								<!-- <div class="nav justify-content-end"> -->
								<!--	<li class="nav-item dropdown">
										<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
										Dropdown link</a>
										<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
											<li><a class="dropdown-item" href="#">Action</a></li>
											<li><a class="dropdown-item" href="#">Another action</a></li>
											<li><a href="./ajax/movieExportCsv-1.0.php" class="dropdown-item">Export CSV</a></li>
										</ul>
									</li>
								-->
																
								<li clas="nav-item">
									<!-- <a href="index.php" class="btn btn-outline-secondary btn-sm" target="_self">Return to List</a> -->
									<a href="index.php" class="btn btn-secondary btn-sm" target="_self">Return to List</a>
								</li>
								
								
									<!-- <a href="#" class="btn btn-outline-secondary btn-sm" onclick="exportCsv()">Export CSV</a> -->
									
									<!-- <a href="./ajax/movieExportCsv-1.0.php" class="btn btn-outline-secondary btn-sm">Export CSV</a>  -->
									
									<!-- <li class="nav-item">
									// <a class="nav-link active" aria-current="page" href="#">Export CSV</a>
									// </li> -->
								  
							</ul>
					<?php	
						}
					?>
  
				</div>
			
			</div>
		
		</nav>

		<!-- <div class="container-fluid"> -->