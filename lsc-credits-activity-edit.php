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
	<td class="lsc-active">
	  <a href="lsc-credits-activity-list.php">
	    <img src="img/lsc-bullet-red.png">&nbsp; Activities
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
$titleErr = "";
$id = $title = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $id = $_POST['id'];
  $title = test_input($_POST["title"]);
  $contrib_id = $_POST['contrib_id'];
  
  if (empty(trim($title))) {
      $titleErr = " * This is a required field";
  }
  // check if title only contains letters (including UTF-8 International Characters) and whitespace
  if (($titleErr == "") && (!preg_match("/^[0-9a-zA-Z\s,.'\-\pL]+$/u", $title))) { 
    $titleErr = "Only letters, numbers and white space allowed";
  }
}
else {
  
  if (isset($_GET['delete'])) {
    //Delete the current activity from the database.
    $query = "DELETE FROM credits_activity WHERE id = " . $_GET['delete'];
    $stmt = @PDO_Execute($query);
    echo "<script>window.location = 'lsc-credits-activity-list.php';</script>";
  }
  
  $id = $_GET['id'];
  
  //Retrieve all existing data from the current activity from the database.
  $data = PDO_FetchAll("SELECT * FROM credits_activity WHERE id = " . $id);
  
  $title = $data[0]['title'];
  $contrib_id = $data[0]['contrib_id'];
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>

<script src="./js/jquery-3.3.1.min.js"></script>
<script type="text/javascript">

function delete_contributor () {
  var conf = confirm('Are you sure that you want to delete this activity?');
  if (conf) {
    window.location='lsc-credits-activity-edit.php?delete=<?php echo $id; ?>';
  }
}

</script>

<h1>Edit Current Activity</h1>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table class="form-editting" border="1" width="100%" id="table1">
	<tr>
		<td class="td-title">Activity Title:</td>
		<td>
		  <input type="text" name="title" value="<?php echo $title;?>"><span class="error"> * <?php echo $titleErr;?></span>
	    <input type="hidden" name="id" value="<?php echo $id; ?>">
	    <input type="hidden" name="contrib_id" value="<?php echo $contrib_id; ?>">
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

if (($titleErr == "") && (trim($title) != "") && ($_SERVER["REQUEST_METHOD"] == "POST")) {
  
  $query = <<< HTML
UPDATE credits_activity SET title='{$title}', contrib_id='{$contrib_id}' WHERE id={$id};
HTML;

  print("<div style='background-color:lightgray; width:100%; padding:8px; color: darkblue; font-weight:bold; font-size:16pt;'>");
  
  $stmt = @PDO_Execute($query);
  if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
    $error = PDO_ErrorInfo();
    print_r($error[2]);
  }
  else {
    $title = trim($title);
    print "The activity <span style='color: red;'>\"{$title}\"</span> has been saved!";
  }
  
  print("</div>");
}
