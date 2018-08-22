<?php

//Get data being sent.
$pid = $_GET["pid"];

//Connect to the database.
error_reporting(-1);
include "../_pdo.php";
$db_file = "./db/lsc-database.db";
PDO_Connect("sqlite:$db_file");

//Retrieve data from the database.
$p = PDO_FetchAll("SELECT 
  pr.title, 
  pr.name, 
  ch.name AS church, 
  ch.city, 
  ch.state, 
  ch.country, 
  ch.comment, 
  (SELECT st.value FROM settings st WHERE st.name = 'logo_path') AS logo_path
FROM 
  preacher pr 
LEFT JOIN 
  church ch 
ON 
  ch.cid = pr.cid 
WHERE 
  pr.pid = " . $pid);

//Save information to the preacher text file.
$preacher_file = "./_txt/lsc-preacher.txt";
$pr = (strpos($p[0]['title'], 'Pastor') === false)? '': 'PR. ';
mb_internal_encoding('UTF-8');
$preacher = mb_strtoupper($pr . $p[0]['name']);
file_put_contents($preacher_file, $preacher);

//Save information to the ministry text file.
$ministry_file = "./_txt/lsc-ministry.txt";
$ministry = $p[0]['title'] . " da Igreja Maranata" . (($p[0]['church'])? " de " . $p[0]['church']: "") . (($p[0]['city'])? " em " . $p[0]['city']: "") . (($p[0]['state'])? ", " . $p[0]['state']: "") . (($p[0]['country'])? " " . $p[0]['country']: "");
file_put_contents($ministry_file, $ministry);

//Save information to the settings JavaScript file.
$preacher = htmlentities($preacher, 0, 'UTF-8');
$ministry = htmlentities($ministry, 0, 'UTF-8');
$settings_js_file = "./js/nametag.js";
$settings_js = "var preacherName = '{$preacher}';
var preacherMinistry = '{$ministry}';
var churchLogo = '../{$p[0]['logo_path']}';";
file_put_contents($settings_js_file, $settings_js);

//Get the Active Preacher ID from the database.
$active_preacher = PDO_FetchOne("SELECT value FROM settings WHERE name = 'active_preacher'");

if ($active_preacher != $pid) {
    //Save the current preacher as active on the database.
    $query = "UPDATE settings SET value = '{$pid}' WHERE name = 'active_preacher';";
    $stmt = @PDO_Execute($query);
}
