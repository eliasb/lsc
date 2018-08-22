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
	<td>
      <a href="lsc-preacher-photo.php?pid=<?php echo $_GET["pid"] . '&title=' . $_GET["title"] . '&name=' . $_GET["name"]; ?>">
        <img src="img/lsc-bullet-blue.png">&nbsp; Upload Photo
      </a>
    </td>
	<td class="lsc-active">
      <a href="lsc-preacher-edit.php?pid=<?php echo $_GET["pid"] . '&title=' . $_GET["title"] . '&name=' . $_GET["name"]; ?>">
        <img src="img/lsc-bullet-red.png">&nbsp; Edit Preacher
      </a>
    </td>
	<td class="lsc-td-right"></td>
  </tr>
</table>

<?php

//Estabilish database connection.
error_reporting(-1);
include "../_pdo.php";
$db_file = "./db/lsc-database.db";
PDO_Connect("sqlite:$db_file");

// define variables and set to empty values
$nameErr = $priorityErr = "";
$pid = $title = $name = $priority = $cid = "";
$s = ' selected="selected"';
$pid = (empty($_GET['pid']))? $_POST['pid']: $_GET['pid'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $title = $_POST['title'][0];
  
  if (empty($_POST["name"])) {
    $nameErr = "Name is required";
  } else {
    $name = test_input($_POST["name"]);
	
    // check if name only contains letters (including UTF-8 International Characters) and whitespace
    if (($nameErr == "") && (!preg_match("/^[a-zA-Z\s,.'\-\pL]+$/u", $name))) { 
      $nameErr = "Only letters and white space allowed";
    }
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
  
  $cid = $_POST['cid'][0];
}
else {
  //Retrieve all existing data from the current preacher from the database.
  $data = PDO_FetchAll("SELECT * FROM preacher WHERE pid = " . $pid);
  
  $title = $data[0]['title'];
  $name = $data[0]['name'];
  $priority = $data[0]['priority'];
  $cid = $data[0]['cid'];
}

//Function destined to remove any special character that may
//create a problem with the saving given data into the database.
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

//Function designed to check if the given string or value is an integer.
function whole_int($val) {
    $val = strval($val);
    $val = str_replace('-', '', $val);
    if (ctype_digit($val)) {
        if ($val === (string)0) {
            return true;
        }
        else if(ltrim($val, '0') === $val) {
            return true;
        }
    }
    return false;
}

$cid_select = "<option value=''></option>\n";
//Generate the church options.
$data = PDO_FetchAll("SELECT * FROM church ORDER BY name, city, state, country ASC");
if (is_array($data)) {
    //Process array of names to allow it to be used by the "in_array" function.
    foreach ($data AS $v) {
        if (is_array($v)) {
            $select = ($cid == $v['cid'])? $s: '';
            $chrch_nme = "Maranata " . (($v['name'])? " de " . $v['name']: "") .
            (($v['city'])? " em " . $v['city']: "") .
            (($v['state'])? ", " . $v['state']: "") .
            (($v['country'])? " " . $v['country']: "") .
            (($v['comment'])? " " . $v['comment']: "");
            $chrch_nme = (trim($v['name'] . $v['city'] . $v['state'] . $v['country']) == "")? $v['comment']: $chrch_nme;
            $cid_select .= "			<option value={$v['cid']}{$select}>" . $chrch_nme . "</option>\n";
        }
    }
}

?>

<style>
.error {color: #FF0000; font-weight: bold}

table {
    border-collapse: collapse;
}

th, td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.td-title {text-align: right; width: 20%;}

</style>

<h2>Edit Preacher</h2>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

<table border="1" width="100%" id="table1">
	<tr>
		<td class="td-title">Title:</td>
		<td>
      <input type="hidden" name="pid" value="<?php echo $pid; ?>">
		  <select name="title[]">
			<option value="Pastor">Pastor</option>
			<option value="Diacono"<?php echo (utf8_encode($title) == "Diacono")? $s: ''; ?>>Di&aacute;cono</option>
			<option value="Obreiro"<?php echo ($title == 'Obreiro')? $s: ''; ?>>Obreiro</option>
			<option value="Pastor Titular"<?php echo ($title == 'Pastor Titular')? $s: ''; ?>>Pastor Titular</option>
			<option value="Pastor Auxiliar"<?php echo ($title == 'Pastor Auxiliar')? $s: ''; ?>>Pastor Auxiliar</option>
			<option value="Pastor Evangelista"<?php echo ($title == 'Pastor Evangelista')? $s: ''; ?>>Pastor Evangelista</option>
			<option value="Ungido"<?php echo ($title == 'Ungido')? $s: ''; ?>>Ungido</option>
		  </select>
        </td>
	</tr>
	<tr>
		<td class="td-title">Name:</td>
		<td><input type="text" name="name" value="<?php echo $name; ?>"><span class="error"> * <?php echo $nameErr;?></span></td>
	</tr>
	<tr>
		<td class="td-title">Priority:</td>
		<td><input type="text" name="priority" value="<?php echo ($priority == '')? 1: $priority;?>" style="width:30px;"><span class="error"> * <?php echo $priorityErr;?></span></td>
	</tr>
	<tr>
		<td class="td-title">Church:</td>
		<td>
		  <select name="cid[]">
			<?php echo $cid_select; ?>
		  </select>
        </td>
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

if ((($nameErr . $priorityErr) == "") && ($name != "") && ($_SERVER["REQUEST_METHOD"] == "POST")) {
  
  $query = <<< HTML
UPDATE preacher SET title="{$title}", name="{$name}", priority={$priority}, cid={$cid} WHERE pid = {$pid};
HTML;
  
  $query = trim($query);
  $stmt = @PDO_Execute($query);
  if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
    print("<pre>");
    $error = PDO_ErrorInfo();
    print_r($error[2]);
    print("</pre>");
  }
  //If successfull, go back to Preacher List page.
  else {
    echo "<script>window.location = 'lsc-preacher-list.php'</script>";
  }
}
