<?php

$countdown = urldecode($_GET['countdown']);
$countdown_file = "./_txt/lsc-countdown.txt";

file_put_contents($countdown_file, $countdown);
