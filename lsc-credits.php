<?php

//Estabilish database connection.
error_reporting(-1);
include "../_pdo.php";
$db_file = "./db/lsc-database.db";
PDO_Connect("sqlite:$db_file");

?>
<script src="./js/jquery-3.3.1.min.js"></script>
<script type="text/javascript">
<!--

$(document).ready(function() {
  
});

function ajaxCall(aid, cid) {
  xmlhttp=new XMLHttpRequest();
  xmlhttp.onreadystatechange=function() {
    if ((this.readyState==4) && (this.status==200)) {
      //document.getElementById("txtHint").innerHTML=this.responseText;
    }
  }
  xmlhttp.open("GET", "lsc-credits-update.php?aid=" + aid + "&cid=" + cid, true);
  xmlhttp.send();
  console.log("aid=" + aid + "&cid=" + cid);
}


//-->
</script>

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
	<td class="lsc-td-right">
	  <a href="lsc-credits-contrib-list.php">
	    <img src="img/lsc-bullet-blue.png">&nbsp; Contributors
	  </a>
	</td>
  </tr>
</table>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
  $production_title = $_POST['production_title'];
  $production_description = $_POST['production_description'];
  
  //Save the current preacher as active on the database.
  $query = <<< HTML
UPDATE settings SET
value = CASE name
WHEN 'production_title' THEN '{$production_title}'
WHEN 'production_description' THEN '{$production_description}'
END
WHERE name IN ('production_title','production_description');
HTML;
  
  $query = trim($query);
  $stmt = @PDO_Execute($query);
}
else {
  
  //Retrieve all the current settings from the database.
  $data = PDO_FetchAssoc("SELECT name, value FROM settings WHERE name IN ('production_title', 'production_description')");
  $production_title = $data['production_title'];
  $production_description = $data['production_description'];
}

?>

<style>

.error {color: #FF0000; font-weight: bold}

table {
  border-collapse: collapse;
  width: 100%;
}

table.lsc-frame-table td {
  width: 50%;
  padding-left: 5px;
}

table.lsc-actions td {
  width: 50%;
  vertical-align: top;
}

/*
th, td {
  padding: 8px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

input {
  font-size: 12pt;
  font-weight: bold;
  font-family: \"Lucida Console\", Monaco, \"Courier New\", Courier, monospace !IMPORTANT;
}

.td-title {text-align: right; width: 20%;}
.td-field {width: 20%;}
.td-preview {width: 15%;}
.td-error {width: 45%;}
*/
</style>

<style type="text/css">@import url("./css/dropdown.css");</style>

<h1>Credits</h1>

<?php

$activity = PDO_FetchAll("SELECT * FROM credits_activity ORDER BY title");
$contrib = PDO_FetchAll("SELECT * FROM credits_contrib ORDER BY name");
$count = ceil(count($activity) / 2);
$i = 0;
$before = "";
$html = "";
$after = "";
echo "
<table class='lsc-actions'>
 <tr>
  <td>";
foreach ($activity AS $av) {
  if ($i == $count) {
    echo "
  </td>
  <td>";
  }
  
  $nm = "o" . $av['id'];
  $title = $av['title'];
  $contrib_id = $av['contrib_id'];
  $not_checked = ' checked';
  
  foreach ($contrib AS $ck => $cv) {
    $op = $nm . $cv['id'];
    $checked = ($cv['id'] == $contrib_id)? ' checked': '';
    $img = ($cv['photo'])? $cv['photo']: 'img/000.jpg';
    $name = $cv['name'];
    $html .= "
   <div class='op'><input type='radio' name='{$nm}' value='{$cv['id']}' id='{$op}'{$checked} onClick='ajaxCall({$av['id']}, {$cv['id']})'><label for='{$op}'><img src='{$img}' alt=''> {$name}</label></div>";
    if ($checked != '') {
      $not_checked = '';
    }
  }
  
  echo "
<fieldset style='border: 1px solid #C0C0C0;'>
<legend style='font-weight: bold;'>{$title}</legend>

<div class='ctn'>
 <div class='slc'>
  <div class='ops'>
   <div class='op'><input type='radio' name='{$nm}' value=''  id='{$nm}0'{$not_checked} onClick='ajaxCall({$av['id']}, 0)'><label for='{$nm}0'>Select an option</label></div>
   {$html}
  </div>
 </div>
</div>

</fieldset>
<br>
";
  
  $html = "";
  
  $i++;
}

echo "
  </td>
 </tr>
</table>";

?>
<script type="text/javascript">

$(document).ready(function() {

  $('#upload').on('click', function() {
    var file_data = $('#sortpicture').prop('files')[0];   
    var form_data = new FormData();                  
    form_data.append('file', file_data);
    //alert(form_data);                             
    $.ajax({
      url: 'lsc-credits-upload.php', // point to server-side PHP script 
      dataType: 'text',  // what to expect back from the PHP script, if anything
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,                         
      type: 'post',
      success: function(php_script_response){
        var res = php_script_response.split(".");

        if (res[0] == "img/logo") {
          $('.lsc-upload-preview').attr('src', php_script_response + "?rnd=" + Math.random());
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

<?php

//Retrieve all the current settings from the database.
$logo_path = PDO_FetchOne("SELECT value FROM settings WHERE name = 'logo_path'");

$upl_btn = "Upload A Logo";
$img_src = 'img/temp-upload.png';

if ($logo_path != '') {
  $img_src = $logo_path;
  $upl_btn = "Change Current Logo";
}

?>
<fieldset style="border: 1px solid #C0C0C0;">
  <legend style="font-weight: bold;"><?php echo $production_title; ?></legend>

<table class="lsc-frame-table">
	<tr>
		<td>
		  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Activity Title: <input type="text" name="production_title" value="<?php echo $production_title; ?>" style='width: 277px;'><span class="error"></span>

			<textarea name="production_description" rows="4" cols="50" style='margin: 4px 0 4px 0;'><?php echo $production_description; ?></textarea><br>
			<input type="submit" name="submit" value="Submit">
</form>
		</td>
		<td class="lsc-image-upload">
		  <img class='lsc-upload-preview' src='<?php echo $img_src; ?>' style='width:auto; height: 50px; margin: 3px;'><br>
      <input id="sortpicture" type="file" name="sortpic" /><br>
			<button id="upload"><?php echo $upl_btn; ?></button><br>
			<div class="upload-error"></div>
		</td>
	</tr>
</table>

</fieldset>
<br>