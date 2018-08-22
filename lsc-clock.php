<?php

$timezone = urldecode($_GET['timezone']);
$timezone = ($timezone == "")? 'America/New_York' : $timezone;

$timeformat = urldecode($_GET['timeformat']);
$timeformat = ($timeformat == "")? 'g:i:s A' : $timeformat;

date_default_timezone_set($timezone);

$clock_file = "./_txt/lsc-clock.txt";
$time = date($timeformat);
file_put_contents($clock_file, $time);
