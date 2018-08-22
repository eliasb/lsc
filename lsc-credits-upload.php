<?php

if (isset($_FILES['file'])) {
  if ( 0 < $_FILES['file']['error'] ) {
    echo 'Error: ' . $_FILES['file']['error'] . '<br>';
  }
  else {
    
    $file_name = explode('.', $_FILES['file']['name']);
    $jpg = end($file_name);
    //   move_uploaded_file($_FILES['file']['tmp_name'], 'img/' . $_FILES['file']['name']);
    move_uploaded_file($_FILES['file']['tmp_name'], 'img/logo.' . $jpg);
    
    //Connect to the database.
    error_reporting(-1);
    include "../_pdo.php";
    $db_file = "./db/lsc-database.db";
    PDO_Connect("sqlite:$db_file");
    
    //Save the current preacher as active on the database.
    $query = "UPDATE settings SET value = 'img/logo.{$jpg}' WHERE name = 'logo_path';";
    $stmt = @PDO_Execute($query);
    
    echo "img/logo.{$jpg}";
  }
}
else {
  echo 'You need to, first, click on the "Choose File" button and select a valid image.';
}

// if ($_POST["label"]) {
//   $label = $_POST["label"];
// }

// $allowedExts = array(
//   "gif",
//   "jpeg",
//   "jpg",
//   "png"
// );

// $temp = explode(".", $_FILES["file"]["name"]);
// $extension = end($temp);
// if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png")) && ($_FILES["file"]["size"] < 200000) && in_array($extension, $allowedExts)) {
  
//   if ($_FILES["file"]["error"] > 0) {
//     echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
//   } else {
//     $filename = $label . $_FILES["file"]["name"];
//     echo "Upload: " . $_FILES["file"]["name"] . "<br>";
//     echo "Type: " . $_FILES["file"]["type"] . "<br>";
//     echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
//     echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
    
//     if (file_exists("img/" . $filename)) {
//       echo $filename . " already exists. ";
//     } else {
//       move_uploaded_file($_FILES["file"]["tmp_name"], "img/" . $filename);
//       echo "Stored in: " . "img/" . $filename;
//     }
//   }
// } else {
//   echo "Invalid file";
// }