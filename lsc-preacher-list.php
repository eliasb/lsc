<?php 

error_reporting(-1);
include "../_pdo.php";
$db_file = "./db/lsc-database.db";
PDO_Connect("sqlite:$db_file");
$data = PDO_FetchAssoc("SELECT name, value FROM settings");
$timezone = urlencode($data['timezone']);
$time_format = $data['time_format'];
$active_preacher = $data['active_preacher'];

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
      </a></td>
	<td></td>
	<td></td>
	<td class="lsc-td-right"></td>
  </tr>
</table>

<table class="title-search" id="table4" style="width: 100%;">
  <tr>
    <td><h1>Preacher List</h1></td>
    <td style="text-align: right;">
      <input style="font-size: large; width: 300px; background-color: lightblue;" type="text" id="searchTerm" onkeyup="doSearch()" placeholder="Search Here" />
    </td>
  </tr>
</table>
<div id="txtHint"></div>
<script type="text/javascript">
<!--

function doSearch() {
  var targetTable = document.getElementsByClassName("preacher-rows");
  var searchText = document.getElementById('searchTerm').value;
  searchText = searchText.toLowerCase();
  for (var rowIndex = 0; rowIndex < targetTable.length; rowIndex++) {
    var rowData = '';
    rowData = document.querySelector(".preacher-content-" + rowIndex).textContent;
    rowData = rowData.toLowerCase();
    var x = document.getElementsByClassName("preacher-" + rowIndex);
    if (rowData.indexOf(searchText) == -1) {
      x[0].style.display = "none";
      x[1].style.display = "none";
    } else {
      x[0].style.display = "table-row";
      x[1].style.display = "table-row";
    }
  }
}

function ajaxCall(pid) {
  xmlhttp=new XMLHttpRequest();
  xmlhttp.onreadystatechange=function() {
    if ((this.readyState==4) && (this.status==200)) {
      //document.getElementById("txtHint").innerHTML=this.responseText;
    }
  }
  xmlhttp.open("GET", "lsc-ajax.php?pid=" + pid, true);
  xmlhttp.send();
}

function changeColor(activeRrow) {
 var targetTable = document.getElementsByClassName("preacher-rows");
  for (var rowIndex = 0; rowIndex < targetTable.length; rowIndex++) {
    var x = document.getElementsByClassName("preacher-" + rowIndex);
    x[0].style.backgroundColor = "";
    x[1].style.backgroundColor = "";
  }
  var x = document.getElementsByClassName("preacher-" + activeRrow);
  x[0].style.backgroundColor = "LightPink";
  x[1].style.backgroundColor = "LightPink";
}

function ajaxClock() {
  var timezone = encodeURI("<?php echo $timezone; ?>");
  var timeformat = encodeURI("<?php echo $time_format; ?>");
  xmlhttp=new XMLHttpRequest();
  xmlhttp.open("GET", "lsc-clock.php?timezone=" + timezone + "&timeformat=" + timeformat, true);
  xmlhttp.send();
}

var t=setInterval(ajaxClock, 1000);

function ajaxDate() {
  xmlhttp=new XMLHttpRequest();
  xmlhttp.open("GET", "lsc-date.php", true);
  xmlhttp.send();
}

ajaxDate();

//-->
</script>
<?php

$data = PDO_FetchAll("SELECT pr.pid, pr.photo, pr.title, pr.name, ch.name AS church, ch.city, ch.state, ch.country, ch.comment, pr.priority FROM preacher pr LEFT JOIN church ch ON ch.cid = pr.cid ORDER BY ch.priority, ch.country, ch.state, ch.city, ch.name, ch.comment, pr.priority, pr.title, pr.name ASC");
$active_css = " style='background-color: LightPink;'";

echo "
<table class='table-list' id='table'>";
$i = 0;
foreach ($data as $p) {
  
  $actv = ($p['pid'] == $active_preacher)? $active_css: '';
  $alt = ($p['photo'])? "Edit Photo": "Add Photo";
  echo "
  <tr class='preacher-rows preacher-{$i}'{$actv}>
    <td rowspan='2'>";
  
  if ($p['photo']) {
    echo "<img src='." . $p['photo'] . "' style='cursor: pointer; width: 100px; height: 100px;' onclick='ajaxCall({$p['pid']}); changeColor({$i})' />";
  }
  else {
	echo "
        <img src='./img/000.jpg' style='cursor: pointer; width: 100px; height: 100px;' onclick='ajaxCall({$p['pid']}); changeColor({$i})' />";
  }
  
  echo "
    </td>
    <td class='preacher-content-{$i}' rowspan='2'>";
  
  $pr = (strpos($p['title'], 'Pastor') === false)? '': 'PR. ';
  echo $pr . "<span style='text-transform: uppercase;'>" . $p['name'] . "</span><br>";
  echo $p['title'] . 
" da Igreja Maranata" . 
(($p['church'])? " de " . $p['church']: "") . 
(($p['city'])? " em " . $p['city']: "") . 
(($p['state'])? ", " . $p['state']: "") . 
(($p['country'])? " " . $p['country']: "") . 
"<br>";
  
  echo "
    </td>
    <td>
      <a href='lsc-preacher-photo.php?pid={$p['pid']}&title={$p['title']}&name={$p['name']}' title='{$alt}'>
        {$alt}
      </a>
    </td>
  </tr>
  <tr class='preacher-{$i}'{$actv}>
    <td>
      <a href='lsc-preacher-edit.php?pid={$p['pid']}&title={$p['title']}&name={$p['name']}' title='Edit Preacher'>
        Edit Profile
      </a>
    </td>
  </tr>";
  
  $i++;
}

echo "
</table>";
