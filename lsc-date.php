<?php

//Connect to the database.
error_reporting(-1);
include "../_pdo.php";
$db_file = "./db/lsc-database.db";
PDO_Connect("sqlite:$db_file");

//Retrieve all the current settings from the database.
$data = PDO_FetchAssoc("SELECT name, value FROM settings");
$timezone = $data['timezone'];
$date_format = $data['date_format'];

$timezone = ($timezone == "")? 'America/New_York' : $timezone;

date_default_timezone_set($timezone);
$date = date($date_format);

$date_file = "./_txt/lsc-date.txt";
file_put_contents($date_file, $date);
