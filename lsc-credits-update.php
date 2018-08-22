<?php

//Get data being sent.
$aid = intval($_GET["aid"]);
$cid = intval($_GET["cid"]);

//Connect to the database.
error_reporting(-1);
include "../_pdo.php";
$db_file = "./db/lsc-database.db";
PDO_Connect("sqlite:$db_file");

if ($aid != 0) {
  $cid = ($cid == 0)? '': $cid;
  //Save the current preacher as active on the database.
  $query = "UPDATE credits_activity SET contrib_id = '{$cid}' WHERE id = '{$aid}';";
  $stmt = @PDO_Execute($query);
  
  //Retrieve data from the database.
  $a = PDO_FetchAll("SELECT * FROM credits_activity ORDER BY title ASC");
  $c = PDO_FetchAssoc("SELECT id, name FROM credits_contrib");
  
  mb_internal_encoding('UTF-8');
  
  $html = "
<style type='text/css'>@import url('../css/credits.css');</style>
<div class='wrapper'>";
  
  $txt = "";
  foreach ($a AS $v) {
    
    if (trim($c[$v['contrib_id']]) != '') {
      $v['title'] = mb_strtoupper($v['title']);
      $txt .= "{$v['title']}\n  {$c[$v['contrib_id']]}\n\n";
      
      $html .= "
  <div class='activity'>{$v['title']}</div>
  <div class='contrib'>{$c[$v['contrib_id']]}</div>
";
    }
  }
  
  $p = PDO_FetchAssoc("SELECT name, value FROM settings WHERE name IN ('production_title', 'production_description', 'logo_path')");
  
  if ((trim($p['production_title']) != '') && (trim($p['production_description']) != '')) {
    $p['production_title'] = mb_strtoupper($p['production_title']);
    $txt .= "{$p['production_title']}\n{$p['production_description']}\n\n";
    
    $p['production_description'] = nl2br($p['production_description']);
    $html .= "
  <div class='activity'></div>
  <div class='activity'>{$p['production_title']}</div>
  <div class='contrib'>{$p['production_description']}</div>
";
  }
  
  if (trim($p['logo_path']) != '') {
    
    $html .= "
  <div class='activity'></div>
  <div class='contrib'><img  style='width:auto; height: 100px; margin: 3px;' src='../{$p['logo_path']}'></div>
";
  }
  
  $html .= "
</div>";
  
  //Save information to the preacher text file.
  $credits_file = "./_txt/lsc-credits.txt";
  file_put_contents($credits_file, $txt);
  
  //Save information to the preacher text file.
  $credits_file = "./_html/credits.htm";
  file_put_contents($credits_file, $html);
  
}


// //Save information to the ministry text file.
// $ministry_file = "./_txt/lsc-ministry.txt";
// $ministry = $p[0]['title'] . " da Igreja Maranata" . (($p[0]['church'])? " de " . $p[0]['church']: "") . (($p[0]['city'])? " em " . $p[0]['city']: "") . (($p[0]['state'])? ", " . $p[0]['state']: "") . (($p[0]['country'])? " " . $p[0]['country']: "");
// file_put_contents($ministry_file, $ministry);

// //Save information to the settings JavaScript file.
// $preacher = htmlentities($preacher, 0, 'UTF-8');
// $ministry = htmlentities($ministry, 0, 'UTF-8');
// $settings_js_file = "./js/nametag.js";
// $settings_js = "var preacherName = '{$preacher}';
// var preacherMinistry = '{$ministry}';
// var churchLogo = '../img/church-logo.png';";
// file_put_contents($settings_js_file, $settings_js);

// //Get the Active Preacher ID from the database.
// $active_preacher = PDO_FetchOne("SELECT value FROM settings WHERE name = 'active_preacher'");


