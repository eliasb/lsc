<style type="text/css">@import url("css/style.css");</style>
<table class="top-menu" id="table3">
  <tr class="lsc-main-menu">
	<td><a href="lsc-preacher-list.php">Preachers</a></td>
	<td class="active"><a href="lsc-church-list.php">Churches</a></td>
	<td><a href="lsc-credits.php">Credits</a></td>
	<td><a href="lsc-timer.php">Timer</a></td>
  </tr>
  <tr class="lsc-sub-menu">
	<td class="lsc-td-left lsc-active">
	  <a href="lsc-church-create.php">
	    <img src="img/lsc-bullet-red.png">&nbsp; New Church
	  </a>
    </td>
	<td></td>
	<td></td>
	<td class="lsc-td-right"></td>
  </tr>
</table>
<br>
<?php

//Estabilish database connection.
error_reporting(-1);
include "../_pdo.php";
$db_file = "./db/lsc-database.db";
PDO_Connect("sqlite:$db_file");

// define variables and set to empty values
$nameErr = $priorityErr = "";
$title = $name = $church = $city = $state = $country = $comment = $priority = $church_name = "";
$s = ' selected="selected"';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $church = test_input($_POST["church"]);
  $city = test_input($_POST["city"]);
  $state = test_input($_POST["state"]);
  $country = test_input($_POST["country"]);
  $comment = test_input($_POST["comment"]);
  
  $church_name = "{$church} {$city} {$state} {$country} {$comment}";
  
  //Retrieve all existing names from the database.
  $data = PDO_FetchAll("SELECT * FROM church");
  
  if (empty(trim($church_name))) {
      $nameErr = " * Fill, at list, one field";
  }
  else if (is_array($data)) {
    
    //Process array of names to allow it to be used by the "in_array" function.
  	$i = 0;
  	$church_names = array();
  	foreach ($data AS $v) {
  	  if (is_array($v)) {
  	    $church_names[$i] = strtolower("{$v['name']} {$v['city']} {$v['state']} {$v['country']} {$v['comment']}");
  	    $i++;
      }
    }
    
  	//Check to see if the current name already exists on the database.
    if (in_array(strtolower($church_name), $church_names)) {
      $nameErr = " * This church already exists";
    }
  }
  // check if name only contains letters (including UTF-8 International Characters) and whitespace
  if (($nameErr == "") && (!preg_match("/^[a-zA-Z\s,.'\-\pL]+$/u", $church_name))) { 
    $nameErr = "Only letters and white space allowed";
  }
  
  if (($_POST["priority"] !== '0') && (empty($_POST["priority"]))) {
    $priorityErr = "Priority is required";
  } else {
    $priority = $_POST["priority"];
    // check if priority is an integer
    if (!whole_int($priority)) {
      $priorityErr = "The priority must be an integer";
    }
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function whole_int($val) {
    $val = strval($val);
    $val = str_replace('-', '', $val);
    if (ctype_digit($val))     {
        if ($val === (string)0)
            return true;
        elseif(ltrim($val, '0') === $val)
            return true;
    }
    return false;
}

?>

<h2>Add a New Church</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table class="form-editting" border="1" width="100%" id="table1">
	<tr>
		<td class="td-title">Church Name:</td>
		<td><input type="text" name="church" value="<?php echo $church;?>"><span class="error"><?php echo $nameErr;?></span></td>
	</tr>
	<tr>
		<td class="td-title">City:</td>
		<td><input type="text" name="city" value="<?php echo $city;?>"></td>
	</tr>
	<tr>
		<td class="td-title">State:</td>
		<td><input type="text" name="state" value="<?php echo $state;?>"></td>
	</tr>
	<tr>
		<td class="td-title">Country:</td>
		<td><input type="text" name="country" value="<?php echo $country;?>"></td>
	</tr>
	<tr>
		<td class="td-title">Comment:</td>
		<td><input type="text" name="comment" value="<?php echo $comment;?>"></td>
	</tr>
	<tr>
		<td class="td-title">Priority:</td>
		<td><input type="text" name="priority" value="<?php echo ($priority == '')? 1: $priority;?>" style="width:30px;"><span class="error"> * <?php echo $priorityErr;?></span></td>
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

if ((($nameErr . $priorityErr) == "") && (trim($church_name) != "")) {
  
  $queries = <<< HTML
INSERT INTO church (name, city, state, country, comment, priority) 
VALUES ("{$church}", "{$city}", "{$state}", "{$country}", "{$comment}", {$priority});
HTML;

  print("<div style='background-color:lightgray; width:100%; padding:8px; color: darkblue; font-weight:bold; font-size:16pt;'>");
  $queries = explode(";", $queries);
  foreach ($queries as $query) {
    $query = trim($query);
    if (!$query) continue;
    $stmt = @PDO_Execute($query);
    if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
      $error = PDO_ErrorInfo();
      print_r($error[2]);
      break;
    }
    //print($query."\n");
    $church_name = trim($church_name);
    print "The church <span style='color: red;'>\"{$church_name}\"</span> has been saved!";
  }
  print("</div>");
}
