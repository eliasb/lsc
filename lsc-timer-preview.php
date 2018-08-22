<?php

//Get data being sent.
$time = trim(urldecode($_GET["time"]));
$date = trim(urldecode($_GET["date"]));
$timezone = urldecode($_GET['timezone']);

$timezone = ($timezone == "")? 'America/New_York' : $timezone;
date_default_timezone_set($timezone);
echo date($time) . "|" . date($date);
