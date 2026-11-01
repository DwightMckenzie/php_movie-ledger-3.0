<?php


  /**
   * Add duration of current video
   * provided from ajax
   * 
   */
   
   
   

  require_once "../includes/config.php";

  
  
  
	/* keep for displaying errors */
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movieTitle = $_POST['movie-title'];
    $movieType = $_POST['movie-type'];
    $movieCategory = $_POST['movie-category'];
    $movieRating = $_POST['movie-rating'];
    $movieRelease = "";
	$movieNote = $_POST["movie-note"];
	$movieCover = $_POST["movie-cover"];
	$movieEntry = date("Y-m-d");


    // echo "<pre>";
    // print_r($movieEntry);
    // echo "</pre>";
	// die();

    // default value if date is empty
    if (empty($_POST['movie-release'])) {
      $movieRelease = '0000-00-00';
    } else {
      $movieRelease = $_POST['movie-release'];
    }




// ---- Configuration ------------------------------------------------------
// const TARGET_WIDTH   = 400;
// const TARGET_HEIGHT  = 600;
// const MAX_FILE_BYTES = 5 * 1024 * 1024; // 5MB
// const UPLOAD_SUBDIR  = 'uploads/moves'; // relative to web root
// const ALLOWED_MIME   = [
	// 'image/jpeg' => 'jpg',
	// 'image/png'  => 'png',
	// 'image/webp' => 'webp',
// ];

// header('Content-Type: application/json');

// function respond(bool $success, string $message, array $extra = []): void
// {
	// echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
	// exit;
// }






// ---- Basic request validation -------------------------------------------
// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // respond(false, 'Only POST requests are allowed.');
// }

// $moveId = filter_input(INPUT_POST, 'move_id', FILTER_VALIDATE_INT);
// if (!$moveId) {
    // respond(false, 'A valid move_id is required.');
// }

// if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
    // respond(false, 'No image file was uploaded.');
// }

// $file = $_FILES['image'];

// if ($file['error'] !== UPLOAD_ERR_OK) {
    // respond(false, 'Upload error code: ' . $file['error']);
// }

// if ($file['size'] > MAX_FILE_BYTES) {
    // respond(false, 'File is too large. Max size is 5MB.');
// }






// ---- Verify it's really an image (don't trust the client's mime string) --
// $imageInfo = getimagesize($file['tmp_name']);
// if ($imageInfo === false) {
    // respond(false, 'Uploaded file is not a valid image.');
// }

// $actualMime = $imageInfo['mime'];
// if (!array_key_exists($actualMime, ALLOWED_MIME)) {
    // respond(false, 'Unsupported image type. Allowed: JPEG, PNG, WEBP.');
// }







// ---- Confirm the move record exists before touching the filesystem -----
// $check = $pdo->prepare('SELECT id FROM move WHERE id = :id');
// $check->execute(['id' => $moveId]);
// if (!$check->fetch()) {
    // respond(false, 'No move record found for that id.');
// }





// ---- Prepare destination path -------------------------------------------
// $webRoot   = $_SERVER['DOCUMENT_ROOT']; // e.g. /var/www/html
// $uploadDir = $webRoot . '/' . UPLOAD_SUBDIR;

// if (!is_dir($uploadDir)) {
    // if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
        // respond(false, 'Could not create upload directory.');
    // }
// }

// $extension = ALLOWED_MIME[$actualMime];
// $filename  = 'move_' . $moveId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
// $destPath  = $uploadDir . '/' . $filename;
// $publicUrl = '/' . UPLOAD_SUBDIR . '/' . $filename;




// ---- Resize (crop-to-fill) to exactly 400 x 600 -------------------------
// try {
    // resizeAndCropImage($file['tmp_name'], $actualMime, $destPath, TARGET_WIDTH, TARGET_HEIGHT);
// } catch (Throwable $e) {
    // error_log('Image resize failed: ' . $e->getMessage());
    // respond(false, 'Failed to process image.');
// }






// ---- Save URL to the database --------------------------------------------
// try {
    // $stmt = $pdo->prepare('UPDATE move SET image_url = :url WHERE id = :id');
    // $stmt->execute([
        // 'url' => $publicUrl,
        // 'id'  => $moveId,
    // ]);
// } catch (PDOException $e) {
    // // Roll back the file write if the DB update fails, so we don't leave orphans.
    // if (file_exists($destPath)) {
        // unlink($destPath);
    // }
    // error_log('DB update failed: ' . $e->getMessage());
    // respond(false, 'Failed to save image URL to database.');
// }

// respond(true, 'Image uploaded successfully.', ['url' => $publicUrl]);











// ===========================================================================
// Helper: resize + center-crop an image to exact target dimensions
// ===========================================================================

/**
 * Resizes the source image so it fully covers a $targetWidth x $targetHeight
 * canvas, then center-crops the overflow. This avoids distortion/stretching
 * (unlike a plain imagecopyresampled to the target box).
 *
 * If you'd rather force-stretch to exactly 400x600 regardless of aspect
 * ratio, replace the body of this function with a single
 * imagecopyresampled() call targeting the full canvas -- see the commented
 * alternative at the bottom of this function.
 */
// function resizeAndCropImage(string $srcPath, string $mime, string $destPath, int $targetWidth, int $targetHeight): void
// {
    // switch ($mime) {
        // case 'image/jpeg':
            // $srcImage = imagecreatefromjpeg($srcPath);
            // break;
        // case 'image/png':
            // $srcImage = imagecreatefrompng($srcPath);
            // break;
        // case 'image/webp':
            // $srcImage = imagecreatefromwebp($srcPath);
            // break;
        // default:
            // throw new RuntimeException('Unsupported mime type for resize: ' . $mime);
    // }

    // if (!$srcImage) {
        // throw new RuntimeException('Could not read source image.');
    // }

    // $srcWidth  = imagesx($srcImage);
    // $srcHeight = imagesy($srcImage);

    // // Scale so the image fully covers the target box (cover-fit).
    // $scale = max($targetWidth / $srcWidth, $targetHeight / $srcHeight);
    // $scaledWidth  = (int) ceil($srcWidth * $scale);
    // $scaledHeight = (int) ceil($srcHeight * $scale);

    // $scaledImage = imagecreatetruecolor($scaledWidth, $scaledHeight);

    // // Preserve transparency for PNG/WEBP.
    // if ($mime === 'image/png' || $mime === 'image/webp') {
        // imagealphablending($scaledImage, false);
        // imagesavealpha($scaledImage, true);
    // }

    // imagecopyresampled(
        // $scaledImage, $srcImage,
        // 0, 0, 0, 0,
        // $scaledWidth, $scaledHeight,
        // $srcWidth, $srcHeight
    // );

    // // Center-crop to the exact target dimensions.
    // $cropX = (int) (($scaledWidth - $targetWidth) / 2);
    // $cropY = (int) (($scaledHeight - $targetHeight) / 2);

    // $finalImage = imagecreatetruecolor($targetWidth, $targetHeight);

    // if ($mime === 'image/png' || $mime === 'image/webp') {
        // imagealphablending($finalImage, false);
        // imagesavealpha($finalImage, true);
    // }

    // imagecopy(
        // $finalImage, $scaledImage,
        // 0, 0, $cropX, $cropY,
        // $targetWidth, $targetHeight
    // );

    // switch ($mime) {
        // case 'image/jpeg':
            // imagejpeg($finalImage, $destPath, 85);
            // break;
        // case 'image/png':
            // imagepng($finalImage, $destPath, 6);
            // break;
        // case 'image/webp':
            // imagewebp($finalImage, $destPath, 85);
            // break;
    // }

    // imagedestroy($srcImage);
    // imagedestroy($scaledImage);
    // imagedestroy($finalImage);

    // // --- Alternative: force-stretch to exact dimensions (distorts aspect ratio) ---
    // // $finalImage = imagecreatetruecolor($targetWidth, $targetHeight);
    // // imagecopyresampled($finalImage, $srcImage, 0, 0, 0, 0,
    // //     $targetWidth, $targetHeight, $srcWidth, $srcHeight);
// }













	

    // var_dump($movieTitle); // TEST

    $sql = "INSERT INTO movie_list (title, type, category, rating, release_date, note, cover, entry_date)
             VALUES (:title, :type, :category, :rating, :release_date, :note, :cover, :entry_date)";
    $query = $conn->prepare($sql);
    $query->bindParam(':title', $movieTitle, PDO::PARAM_STR);
    $query->bindParam(':type', $movieType, PDO::PARAM_STR);
    $query->bindParam(':category', $movieCategory, PDO::PARAM_STR);
    $query->bindParam(':rating', $movieRating, PDO::PARAM_STR);
    $query->bindParam(':release_date', $movieRelease, PDO::PARAM_STR);
    $query->bindParam(':note', $movieNote, PDO::PARAM_STR);
    $query->bindParam(':cover', $movieCover, PDO::PARAM_STR);
	$query->bindParam(':entry_date', $movieEntry, PDO::PARAM_STR);

    // echo "<pre>";
    // var_dump($query);
    // echo "</pre>";
    
    if ($query->execute()) {
      echo "Success!";

      header("Location: ../index.php");
      exit;
    } else {
      echo "something went wrong";
    }

  }

?>