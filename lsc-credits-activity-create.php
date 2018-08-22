<style type="text/css">@import url("css/style.css");</style>
<table class="top-menu" id="table3">
  <tr class="lsc-main-menu">
	<td><a href="lsc-preacher-list.php">Preachers</a></td>
	<td><a href="lsc-church-list.php">Churches</a></td>
	<td class="active"><a href="lsc-credits.php">Credits</a></td>
	<td><a href="lsc-timer.php">Timer</a></td>
  </tr>
  <tr class="lsc-sub-menu">
	<td class="lsc-td-left lsc-active">
	  <a href="lsc-credits-activity-create.php">
	    <img src="img/lsc-bullet-red.png">&nbsp; New Activity
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
$title = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $title = test_input($_POST["title"]);
  
  //Retrieve all existing names from the database.
  $data = PDO_FetchAll("SELECT title FROM credits_activity");
  
  if (empty(trim($title))) {
      $titleErr = " * This is a required field";
  }
  else if (is_array($data)) {
    
    //Process array of names to allow it to be used by the "in_array" function.
    $i = 0;
    $titles = array();
    foreach ($data AS $v) {
      if (is_array($v)) {
        $titles[$i] = strtolower($v['title']);
        $i++;
      }
    }
    
    //Check to see if the current name already exists on the database.
    if (in_array(strtolower($title), $titles)) {
      $titleErr = " * This activity already exists";
    }
  }
  // check if title only contains letters (including UTF-8 International Characters) and whitespace
  if (($titleErr == "") && (!preg_match("/^[0-9a-zA-Z\s,.'\-\pL]+$/u", $title))) { 
    $titleErr = "Only letters, numbers and white space allowed";
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>

<h1>Add a New Activity</h1>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table class="form-editting" border="1" width="100%" id="table1">
	<tr>
		<td class="td-title">Activity Title:</td>
		<td><input type="text" name="title" value="<?php echo $title;?>"><span class="error"> * <?php echo $titleErr;?></span></td>
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

if (($titleErr == "") && (trim($title) != "")) {
  
  $query = <<< HTML
INSERT INTO credits_activity (title) VALUES ("{$title}");
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
