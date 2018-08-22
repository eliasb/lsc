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
	<td>
	  <a href="lsc-credits-contrib-create.php">
	    <img src="img/lsc-bullet-blue.png">&nbsp; New Contributor
	  </a>
	</td>
	<td>
	  <a href="lsc-credits-activity-list.php">
	    <img src="img/lsc-bullet-blue.png">&nbsp; Activities
	  </a>
	</td>
	<td class="lsc-td-right lsc-active">
	  <a href="lsc-credits-contrib-list.php">
	    <img src="img/lsc-bullet-red.png">&nbsp; Contributors
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
$id = $temp_photo = $name = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $id = $_POST['id'];
  $temp_photo = test_input($_POST["temp_photo"]);
  $name = test_input($_POST["name"]);
  
  if (empty(trim($name))) {
    $nameErr = " * This is a required field";
  }
  
  // check if name only contains letters (including UTF-8 International Characters) and whitespace
  if (($nameErr == "") && (!preg_match("/^[0-9a-zA-Z\s,.'\-\pL]+$/u", $name))) { 
    $nameErr = "Only letters, numbers and white spaces allowed";
  }
}
else {
  
  if (isset($_GET['delete'])) {
    
    //Get the Active Preacher ID from the database.
    $img_src = PDO_FetchOne("SELECT photo FROM credits_contrib WHERE id = " . $_GET['delete']);
    
    //Delete Contributor's photo.
    unlink($img_src);
    
    //Delete the current contributor from the database.
    $query = "DELETE FROM credits_contrib WHERE id = " . $_GET['delete'];
    $stmt = @PDO_Execute($query);
    
    echo "<script>window.location = 'lsc-credits-contrib-list.php';</script>";
  }
  
  $id = $_GET['id'];
  
  //Retrieve all existing data from the current contributor from the database.
  $data = PDO_FetchAll("SELECT * FROM credits_contrib WHERE id = " . $id);
  
  $name = $data[0]['name'];
  $temp_photo = $data[0]['photo'];
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

function delete_contributor () {
  var conf = confirm('Are you sure that you want to delete this contributor?');
  if (conf) {
    window.location='lsc-credits-contrib-edit.php?delete=<?php echo $id; ?>';
  }
}

</script>

<h1>Edit Current Contributor</h1>
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
  		<td>
  		  <input type="text" name="name" value="<?php echo $name;?>"><span class="error"> * <?php echo $nameErr; ?></span>
  		  <input type="hidden" name="id" value="<?php echo $id; ?>">
  		</td>
  	</tr>
  	<tr>
  		<td colspan="2">
  		  <input type="submit" name="submit" value="Save"> 
  		  <button style="margin-left:50px;" type="button" id="delete" onClick="delete_contributor()">Delete</button>
  		</td>
  	</tr>
  	<tr>
  		<td colspan="2"><p><span class="error"> * Required field.</span></p></td>
  	</tr>
  </table>
</form>

<?php

if ((($nameErr) == "") && (trim($name) != "") && ($_SERVER["REQUEST_METHOD"] == "POST")) {
  
  $photo = '';
  if (!empty(trim($temp_photo))) {
    
    $jpg = explode('.', $temp_photo);
    
    if ($jpg[0] == 'img/tmp') {
      
      $photo = "img/cntr_" . str_pad($id, 3, '0', STR_PAD_LEFT) . "." . $jpg[1];
      rename($temp_photo, $photo);
    }
    else {
      $photo = $temp_photo;
    }
  }
  
  echo "
<script>
  $('#temp_photo').val('{$photo}');
</script>
";
  
  $query = <<< HTML
UPDATE credits_contrib SET name='{$name}', photo='{$photo}' WHERE id={$id};
HTML;
  
  print("<div style='background-color:lightgray; width:100%; padding:8px; color: darkblue; font-weight:bold; font-size:16pt;'>");
  
  $stmt = @PDO_Execute($query);
  if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
    $error = PDO_ErrorInfo();
    print_r($error[2]);
  }
  else {
    $name = trim($name);
    echo "<script>window.location = 'lsc-credits-contrib-edit.php?success=" . urlencode($name) . "&id={$id}';</script>";
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
