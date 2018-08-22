<?php
error_reporting(-1);
?>
<style type="text/css">@import url("css/style.css");</style>
<table class="top-menu" id="table3">
  <tr class="lsc-main-menu">
	<td class="active"><a href="lsc-preacher-list.php">Preachers</a></td>
	<td><a href="lsc-church-list.php">Churches</a></td>
	<td><a href="lsc-credits.php">Credits</a></td>
	<td><a href="lsc-timer.php">Timer</a></td>
  </tr>
  <tr class="lsc-sub-menu">
	<td class="lsc-td-left">
      <a href="lsc-preacher-create.php">
        <img src="img/lsc-bullet-blue.png">&nbsp; New Preacher
      </a>
    </td>
	<td class="lsc-active">
      <a href="lsc-preacher-photo.php?pid=<?php echo $_GET["pid"] . '&title=' . $_GET["title"] . '&name=' . $_GET["name"]; ?>">
        <img src="img/lsc-bullet-red.png">&nbsp; Upload Photo
      </a>
    </td>
	<td>
      <a href="lsc-preacher-edit.php?pid=<?php echo $_GET["pid"] . '&title=' . $_GET["title"] . '&name=' . $_GET["name"]; ?>">
        <img src="img/lsc-bullet-blue.png">&nbsp; Edit Preacher
      </a>
    </td>
	<td class="lsc-td-right"></td>
  </tr>
</table>

<title>Upload</title>
<br>
<table style="width: 100%;">
  <tr>
    <td style="text-align: center;">
      <h1 style="font-size:18pt;">Upload picture for <span style="font-weight:bold; color:red;"><?php echo $_GET["title"] . ' ' . $_GET["name"]; ?></span></h1>
<form enctype="multipart/form-data" action="lsc-preacher-photo.php?pid=<?php echo $_GET["pid"] . '&title=' . $_GET["title"] . '&name=' . $_GET["name"]; ?>" method="POST">
    <span style="font-size:12pt; font-weight:bold; color:blue;">Please, uploade a square JPG photo with at list 300px by 300px.</span><br><br>
	<input name="myfile" type="file" accept=".jpg, .jpeg, .jpe, .jfif, .jif" />
    <input type="submit" value="Save Photo" />
</form>
    </td>
  </tr>
</table>

<?php 

if (count($_FILES)) {
  
  $myfile = $_FILES["myfile"]["tmp_name"];
  
  //====================================================
  //============== Move the uploaded file ==============
  //====================================================
  
  $photo_path = "img/" . str_pad($_GET['pid'], 3, '0', STR_PAD_LEFT) . ".jpg";
  $success = move_uploaded_file($myfile, __DIR__ . $photo_path);
  
  if ($success) {
    
    //====================================================
    //========= Save image path on the database ==========
    //====================================================
    //Estabilish database connection.
    error_reporting(-1);
    include "../_pdo.php";
    $db_file = "./db/lsc-database.db";
    PDO_Connect("sqlite:$db_file");
    //Create the Update query with the Preacher's ID number and the relative path of the Photo.
    $query = "UPDATE preacher SET photo = '{$photo_path}' WHERE pid = {$_GET["pid"]}";
    //Execute query!
    $stmt = @PDO_Execute($query);
    //Check for errors.
    if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
      $error = PDO_ErrorInfo();
      //print_r("<pre>{$error[2]}</pre>");
    }
	//If successfull, go back to Preacher List page.
	else {
	  echo "<script>window.location = '/lsc-preacher-list.php'</script>";
	}
  }
  else {
	echo '<p style="color: red;">move_uploaded_file() failed.</p>';
  }
}