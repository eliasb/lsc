<style type="text/css">@import url("css/style.css");</style>
<table class="top-menu" id="table3">
  <tr class="lsc-main-menu">
	<td><a href="lsc-preacher-list.php">Preachers</a></td>
	<td><a href="lsc-church-list.php">Churches</a></td>
	<td class="active"><a href="lsc-credits.php">Credits</a></td>
	<td><a href="lsc-timer.php">Timer</a></td>
  </tr>
  <tr class="lsc-sub-menu">
	<td class="lsc-td-left">
	  <a href="lsc-credits-activity-create.php">
	    <img src="img/lsc-bullet-blue.png">&nbsp; New Activity
	  </a>
    </td>
	<td class="lsc-active">
	  <a href="lsc-credits-contrib-create.php">
	    <img src="img/lsc-bullet-red.png">&nbsp; New Contributor
	  </a>
	</td>
	<td>
	  <a href="lsc-credits-activity-list.php">
	    <img src="img/lsc-bullet-blue.png">&nbsp; Activities
	  </a>
	</td>
	<td class="lsc-td-right">
	  <a href="lsc-credits-contrib-list.php">
	    <img src="img/lsc-bullet-blue.png">&nbsp; Contributors
	  </a>
	</td>
  </tr>
</table>
<?php

//Estabilish database connection.
error_reporting(-1);
include "../_pdo.php";
$db_file = "./db/lsc-database.db";
PDO_Connect("sqlite:$db_file");

// define variables and set to empty values
$nameErr = "";
$temp_photo = $name = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $temp_photo = test_input($_POST["temp_photo"]);
  $name = test_input($_POST["name"]);
  
  //Retrieve all existing names from the database.
  $data = PDO_FetchAll("SELECT name FROM credits_contrib");
  
  if (empty(trim($name))) {
    $nameErr = " * This is a required field";
  }
  else if (is_array($data)) {
    
    //Process array of names to allow it to be used by the "in_array" function.
  	$i = 0;
  	$names = array();
  	foreach ($data AS $v) {
  	  if (is_array($v)) {
  	    $names[$i] = strtolower($v['name']);
  	    $i++;
      }
    }
    
	  //Check to see if the current name already exists in the database.
    if (in_array(strtolower($name), $names)) {
      $nameErr = " * This name already exists in the database";
    }
  }
  // check if name only contains letters (including UTF-8 International Characters) and whitespace
  if (($nameErr == "") && (!preg_match("/^[0-9a-zA-Z\s,.'\-\pL]+$/u", $name))) { 
    $nameErr = "Only letters, numbers and white spaces allowed";
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// //Retrieve all the current settings from the database.
// $logo_path = PDO_FetchOne("SELECT value FROM settings WHERE name = 'logo_path'");

// $upl_btn = "Upload A Logo";
// $img_src = 'img/temp-upload.png';

// if ($logo_path != '') {
//   $img_src = $logo_path;
//   $upl_btn = "Change Current Logo";
// }


?>
<script src="./js/jquery-3.3.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {

  $('#upload').on('click', function() {
    var file_data = $('#sortpicture').prop('files')[0];   
    var form_data = new FormData();
    form_data.append('file', file_data);
//     alert(form_data);
    $.ajax({
      url: 'lsc-credits-contrib-photo.php', // point to server-side PHP script 
      dataType: 'text',  // what to expect back from the PHP script, if anything
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
      type: 'post',
      success: function(php_script_response){
        var res = php_script_response.split(".");
        if (res[0] == "img/tmp") {
          $('.lsc-upload-preview').attr('src', php_script_response + "?rnd=" + Math.random());
          $('#temp_photo').val(php_script_response);
      	  var input = $("#sortpicture");
      	  input.replaceWith(input.val('').clone(true));
      	  $("div.upload-error").html('');
        }
        else {
          $("div.upload-error").html(php_script_response);
        }
      }
    });
  });
});

</script>

<h1>Add a New Contributor</h1>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <table class="form-editting" border="1" width="100%" id="table1">
  	<tr>
  		<td class="lsc-image-upload">
  		  <img class='lsc-upload-preview' src='<?php echo (empty(trim($temp_photo)))? 'img/000.jpg': $temp_photo; ?>' onClick="$('#sortpicture').click();" style='cursor: pointer; width: 100px; height: 100px;'>
  		</td>
  		<td>
  		  Please, uploade a square JPG photo with at list 300px by 300px.<br><br>
  	    <input id="sortpicture" name="sortpic" type="file" accept=".jpg, .jpeg, .jpe, .jfif, .jif">
        <input id="temp_photo" name="temp_photo" type="hidden" value="<?php echo $temp_photo;?>">
        <button type="button" id="upload">Upload A Photo</button><br>
  			<div class="upload-error"></div>
      </td>
  	</tr>
  	<tr>
  		<td class="td-title">Name:</td>
  		<td><input type="text" name="name" value="<?php echo $name;?>"><span class="error"> * <?php echo $nameErr;?></span></td>
  	</tr>
  	<tr>
  		<td colspan="2"><input type="submit" name="submit" value="Submit"></td>
  	</tr>
  	<tr>
  		<td colspan="2"><p><span class="error"> * Required field.</span></p></td>
  	</tr>
  </table>
</form>

<?php

if ((($nameErr) == "") && (trim($name) != "")) {
  
  $query = <<< HTML
INSERT INTO credits_contrib (name) VALUES ("{$name}");
HTML;
  
  print("<div style='background-color:lightgray; width:100%; padding:8px; color: darkblue; font-weight:bold; font-size:16pt;'>");
  
  $stmt = @PDO_Execute($query);
  if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
    $error = PDO_ErrorInfo();
    print_r($error[2]);
  }
  else {
    
    $photo = '';
    if (!empty(trim($temp_photo))) {
      $id = PDO_LastInsertId();
      $jpg = explode('.', $temp_photo);
      $file_name = "img/cntr_" . str_pad($id, 3, '0', STR_PAD_LEFT) . "." . $jpg[1];
      rename($temp_photo, $file_name);
      
      //Save the current preacher as active on the database.
      $query = "UPDATE credits_contrib SET photo = '{$file_name}' WHERE id = {$id};";
      $stmt = @PDO_Execute($query);
    }
    
    $name = trim($name);
    echo "<script>window.location = 'lsc-credits-contrib-create.php?success=" . urlencode($name) . "';</script>";
    //print($query."\n");
//     $name = trim($name);
//     print "The contributor <span style='color: red;'>\"{$name}\"</span> has been saved!";
  }
  print("</div>");
}

if (isset($_GET["success"])) {
  //Get data being sent.
  $success = $_GET["success"];
  
  if (!empty(trim($success))) {
    $success = urldecode($success);
    echo "
<div style='background-color:lightgray; width:100%; padding:8px; color: darkblue; font-weight:bold; font-size:16pt;'>
  The contributor <span style='color: red;'>\"{$success}\"</span> has been saved!
</div>";
  }
}
