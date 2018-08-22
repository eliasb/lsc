<?php 

error_reporting(-1);
include "../_pdo.php";
$db_file = "./db/lsc-database.db";
PDO_Connect("sqlite:$db_file");

?>
<style type="text/css">@import url("css/style.css");

</style>
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

<table class="title-search" id="table4" style="width: 100%;">
  <tr>
    <td><h1>Contribitor List</h1></td>
    <td style="text-align: right;">
      <input style="font-size: large; width: 300px; background-color: lightblue;" type="text" id="searchTerm" onkeyup="doSearch()" placeholder="Search Here" />
    </td>
  </tr>
</table>
<div id="txtHint"></div>
<script type="text/javascript">
<!--

function doSearch() {
  var targetTable = document.getElementsByClassName("contrib-rows");
  var searchText = document.getElementById('searchTerm').value;
  searchText = searchText.toLowerCase();
  for (var rowIndex = 0; rowIndex < targetTable.length; rowIndex++) {
    var rowData = '';
    rowData = document.querySelector(".contrib-name-" + rowIndex).textContent;
    rowData = rowData.toLowerCase();
    console.log(rowData);
    console.log("contrib-" + rowIndex);
    var x = document.getElementsByClassName("contrib-" + rowIndex);
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
tr.contrib-rows td:first-child{
   width:100px;
}

tr.contrib-rows td:nth-child(3){
   width: 20%;
}
</style>

<?php

$data = PDO_FetchAll("SELECT * FROM credits_contrib ORDER BY name ASC");

echo "
<table class='table-list' id='table'>";
$i = 0;
foreach ($data as $p) {
  
  echo "
  <tr class='contrib-rows contrib-{$i}'>
    <td>";
  
  if ($p['photo']) {
    echo "<img src='{$p['photo']}' style='width: 100px; height: 100px;'>";
  }
  else {
	  echo "
        <img src='./img/000.jpg' style='width: 100px; height: 100px;'>";
  }
  
  echo "
    </td>
    <td class='contrib-name-{$i}'>
      <span style='text-transform: uppercase;'>{$p['name']}</span>
    </td>
    <td>
      <a href='lsc-credits-contrib-edit.php?id={$p['id']}' title='Edit Contributor'>
        Edit Contributor
      </a>
    </td>
  </tr>";
  
  $i++;
}

echo "
</table>";
