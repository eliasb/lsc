<style type="text/css">@import url("css/style.css");</style>
<table class="top-menu" id="table3">
  <tr class="lsc-main-menu">
	<td><a href="lsc-preacher-list.php">Preachers</a></td>
	<td class="active"><a href="lsc-church-list.php">Churches</a></td>
	<td><a href="lsc-credits.php">Credits</a></td>
	<td><a href="lsc-timer.php">Timer</a></td>
  </tr>
  <tr class="lsc-sub-menu">
	<td class="lsc-td-left">
	  <a href="lsc-church-create.php">
	    <img src="img/lsc-bullet-blue.png">&nbsp; New Church
	  </a>
    </td>
	<td></td>
	<td></td>
	<td class="lsc-td-right"></td>
  </tr>
</table>

<table class="title-search" id="table4" style="width: 100%;">
  <tr>
    <td><h1>Church List</h1></td>
    <td style="text-align: right;">
      <input style="font-size: large; width: 300px; background-color: lightblue;" type="text" id="searchTerm" onkeyup="doSearch()" placeholder="Search Here" />
    </td>
  </tr>
</table>

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
    } else {
      x[0].style.display = "table-row";
    }
  }
}

//-->
</script>

<?php


error_reporting(-1);
include "../_pdo.php";
$db_file = "./db/lsc-database.db";
PDO_Connect("sqlite:$db_file");

$data = PDO_FetchAll("SELECT * FROM church ORDER BY priority, name, city, state, country ASC");
if (is_array($data)) {
  echo "
<table class='table-list' id='table'>";
  $i = 0;
  
  foreach ($data as $v) {
    if (is_array($v)) {
      echo "
  <tr class='preacher-rows preacher-{$i}'>
    <td class='preacher-content-{$i}'>";
      
      $chrch_nme = "Maranata " . (($v['name'])? " de " . $v['name']: "") .
      (($v['city'])? " em " . $v['city']: "") .
      (($v['state'])? ", " . $v['state']: "") .
      (($v['country'])? " " . $v['country']: "") .
      (($v['comment'])? " " . $v['comment']: "");
      $chrch_nme = (trim($v['name'] . $v['city'] . $v['state'] . $v['country']) == "")? $v['comment']: $chrch_nme;
      
      echo $chrch_nme . "
    </td>
    <td>
      <a href='lsc-church-edit.php?cid={$v['cid']}' title='Edit Church'>
        Edit Church
      </a>
    </td>
  </tr>";
      
      $i++;
    }
  }
  echo "
</table>";
}
