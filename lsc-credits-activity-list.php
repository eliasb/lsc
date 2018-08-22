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

<table class="title-search" id="table4" style="width: 100%;">
  <tr>
    <td><h1>Activities List</h1></td>
    <td style="text-align: right;">
      <input style="font-size: large; width: 300px; background-color: lightblue;" type="text" id="searchTerm" onkeyup="doSearch()" placeholder="Search Here" />
    </td>
  </tr>
</table>

<script type="text/javascript">
<!--

function doSearch() {
  var targetTable = document.getElementsByClassName("activity-rows");
  var searchText = document.getElementById('searchTerm').value;
  searchText = searchText.toLowerCase();
  for (var rowIndex = 0; rowIndex < targetTable.length; rowIndex++) {
    var rowData = '';
    rowData = document.querySelector(".activity-title-" + rowIndex).textContent;
    rowData = rowData.toLowerCase();
    var x = document.getElementsByClassName("activity-" + rowIndex);
    if (rowData.indexOf(searchText) == -1) {
      x[0].style.display = "none";
    } else {
      x[0].style.display = "table-row";
    }
  }
}

//-->
</script>
<style>
tr.activity-rows td:first-child{
   width:80%;
}

tr.activity-rows td:nth-child(2){
   width: 20%;
}
</style>
<?php

error_reporting(-1);
include "../_pdo.php";
$db_file = "./db/lsc-database.db";
PDO_Connect("sqlite:$db_file");

$data = PDO_FetchAll("SELECT * FROM credits_activity ORDER BY title ASC");
if (is_array($data)) {
  echo "
<table class='table-list' id='table'>";
  $i = 0;
  
  foreach ($data as $v) {
    if (is_array($v)) {
      echo "
  <tr class='activity-rows activity-{$i}'>
    <td class='activity-title-{$i}'>
      {$v['title']}
    </td>
    <td>
      <a href='lsc-credits-activity-edit.php?id={$v['id']}' title='Edit Activity'>
        Edit Activity
      </a>
    </td>
  </tr>";
      
      $i++;
    }
  }
  echo "
</table>";
}
