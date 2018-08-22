<?php

if (isset($_FILES['file'])) {
  if ( 0 < $_FILES['file']['error'] ) {
    echo 'Error: ' . $_FILES['file']['error'] . '<br>';
  }
  else {
    
    $file_name = explode('.', $_FILES['file']['name']);
    $jpg = end($file_name);
    //   move_uploaded_file($_FILES['file']['tmp_name'], 'img/' . $_FILES['file']['name']);
    move_uploaded_file($_FILES['file']['tmp_name'], 'img/tmp.' . $jpg);
    
    //Connect to the database.
//     error_reporting(-1);
//     include "../_pdo.php";
//     $db_file = "./db/lsc-database.db";
//     PDO_Connect("sqlite:$db_file");
    
//     //Save the current preacher as active on the database.
//     $query = "UPDATE settings SET value = 'img/logo.{$file_name[1]}' WHERE name = 'logo_path';";
//     $stmt = @PDO_Execute($query);
    
    echo 'img/tmp.' . $jpg;
  }
}
else {
  echo 'You need to, first, click on the "Choose File" button and select a valid image.';
}
