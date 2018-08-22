<?php

//Estabilish database connection.
error_reporting(-1);
include "../_pdo.php";
$db_file = "./db/lsc-database.db";
PDO_Connect("sqlite:$db_file");

?>
<script src="./js/jquery-3.3.1.min.js"></script>
<script type="text/javascript">
<!--

function settingsPreview() {
  var timezone = encodeURI(document.getElementById("timezone").value);
  var time = encodeURI(document.getElementById('time_format').value);
  var date = encodeURI(document.getElementById('date_format').value);
  
  xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == XMLHttpRequest.DONE) {
      var response = decodeURI(xmlhttp.responseText);
      var res_array = response.split("|");
      var error_html = "<span class='error'> * </span> ";
      var help_link = " - <a title='Help on how to use the format code for time/date.' style='color: red; font-style: italic; font-weight: bold;' target='_blank' href='http://php.net/manual/en/function.date.php'>Help!</a>";
      document.getElementById("time_format_preview").innerHTML = error_html + res_array[0] + help_link;
      document.getElementById("date_format_preview").innerHTML = error_html + res_array[1] + help_link;
      
      if (time_format_err != "") {
        $('tr.time-format td.td-error span.error').val(time_format_err);
      }
    }
  }
  
  xmlhttp.open("GET", "lsc-timer-preview.php?time=" + time + "&date=" + date + "&timezone=" + timezone, true);
  xmlhttp.send();
}

var countdown_duration_err = '';
var time_format_err = '';

//==============================================================
//====================== Countdown Code ========================
//==============================================================
var jqcd_start_id = 'input#jqcd_start';
var jqcd_time_id = 'input#jqcd_time';
var jqcd_count_id = 'span#jqcd_count';
var jqcd_end_message = '';

var jqcd_countdown = '';
var jqcd_status = 'stopped';
var jqcd_current = '';




//var jqcd_curTime = new Date();
//var jqcd_curSecn = jqcd_curTime.getSeconds();



function jqcd(action){
  
  if (action == 'start') {
    if (jqcd_status == 'stopped') {
      jqcd_updtv(jqcd_start_id, 'Pause');
      jqcd_status = 'running';
      jqcd_current = jqcd_countdown;
      jqcd_updtt(jqcd_count_id, jqcd_countdown);
    }
    else if (jqcd_status == 'running') {
      jqcd_updtv(jqcd_start_id, 'Resume');
      jqcd_status = 'paused';
    }
    else if (jqcd_status == 'paused') {
      jqcd_updtv(jqcd_start_id, 'Pause');
      jqcd_status = 'running';
    }
  }
  else if (action == 'stop') {
    jqcd_updtv(jqcd_start_id, 'Start');
    jqcd_status = 'stopped';
    jqcd_updtt(jqcd_count_id, jqcd_end_message);
  }
  else if (action == 'reset') {
    jqcd_updtv(jqcd_start_id, 'Start');
    jqcd_status = 'stopped';
    jqcd_updtt(jqcd_count_id, jqcd_countdown);
  }
  
  var a =  jqcd_current.split(":");
  var m = a[0];
  var s = (a[1] - 1);

  //If seconds are less than 0 ...
  if (s < 0) {
    
    //If minutes are also 0 ...
    if (parseInt(m) == 0) {
      jqcd_updtv(jqcd_start_id, 'Start');
      jqcd_status = 'stopped';
      jqcd_updtt(jqcd_count_id, jqcd_end_message);
    }
    else {
      m = m - 1;
      s = 59;
    }
  }
  
  if(s >= 0){
    setTimeout(function(){
      if (jqcd_status == 'running') {
        m = (parseInt(m) < 10)? "0" + parseInt(m): m;
        s = (parseInt(s) < 10)? "0" + parseInt(s): s;
        jqcd_updtt(jqcd_count_id, m + ":" + s);
        jqcd_current = m + ":" + s;
        jqcd('');
      }
    }, 1000);
  }
}

function jqcd_updtv(selector, value) {
  if (selector != '') {
    $(selector).val(value);
  }
}
function jqcd_updtt(selector, value) {
  if (selector != '') {
    $(selector).text(value);
    ajaxCountdown(value);
  }
}
function ajaxCountdown(countdown) {
  xmlhttp=new XMLHttpRequest();
  xmlhttp.open("GET", "lsc-countdown.php?countdown=" + countdown, true);
  xmlhttp.send();
}

$(document).ready(function() {
  jqcd_countdown = $(jqcd_time_id).val();
  
  $(jqcd_time_id).keyup(function() {
    jqcd_countdown = $(jqcd_time_id).val();
    jqcd_updtt(jqcd_count_id, jqcd_countdown);
    jqcd_updtv(jqcd_start_id, 'Start');
    jqcd_status = 'stopped';
  });
  
  settingsPreview();
});

//-->
</script>

<style type="text/css">@import url("css/style.css");</style>
<table class="top-menu" id="table3">
  <tr class="lsc-main-menu">
	<td><a href="lsc-preacher-list.php">Preachers</a></td>
	<td><a href="lsc-church-list.php">Churches</a></td>
	<td><a href="lsc-credits.php">Credits</a></td>
	<td class="active"><a href="lsc-timer.php">Timer</a></td>
  </tr>
  <tr class="lsc-sub-menu">
	<td class="lsc-td-left">&nbsp; </td>
	<td></td>
	<td></td>
	<td class="lsc-td-right"></td>
  </tr>
</table>
<?php

/**
 * @desc 
 *   Generates a multi-dimensional array with all the timezones sorted in a very user friendly way.
 * @param $timezon_option String (Optional)
 *   If this parameter is set with a correct timezone, the option with the given timezone will be checked on the generated HTML.
 * @return String
 *   HTML will be generated with a dropdwon list that should look like the following:
 *     Example:
 *       $tmzn = "
 *       <select>
 *           <option value='Pacific/Midway'   >Pacific/Midway                  10:02PM (UTC-11:00)</option>
 *           <option value='Pacific/Niue'     >Pacific/Niue                    10:02PM (UTC-11:00)</option>
 *           <option value='Pacific/Pago_Pago'>Pacific/Pago_Pago               10:02PM (UTC-11:00)</option>
 *           <option value='America/Adak'     >America/Adak                    11:02PM (UTC-10:00)</option>
 *       </select>";
 */
function generate_timezone_list($timezon_option = "America/New_York") {
    static $regions = array(
        DateTimeZone::AFRICA,
        DateTimeZone::AMERICA,
        DateTimeZone::ANTARCTICA,
        DateTimeZone::ASIA,
        DateTimeZone::ATLANTIC,
        DateTimeZone::AUSTRALIA,
        DateTimeZone::EUROPE,
        DateTimeZone::INDIAN,
        DateTimeZone::PACIFIC,
    );
    
    $length = 0;
    $timezones = array();
    foreach($regions as $region) {
        $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
    }
    
    foreach( $timezones as $timezone ) {
        $length = (strlen($timezone) > $length)? strlen($timezone): $length;
    }
    
    foreach( $timezones as $timezone ) {
        
        $t = new DateTimeZone($timezone);
        $offset = $t->getOffset(new DateTime);
        
        $timezone_spaces = str_pad($timezone, $length, " ");
        
        $c = new DateTime(null, $t);
        $current_time = str_pad($c->format('g:iA'), 8, " ", STR_PAD_LEFT);
        
        $offset_formatted = gmdate("H:i", abs($offset));
        
        $display =  str_replace(" ", "&nbsp;", "{$offset_formatted}|{$timezone}|{$timezone_spaces} {$current_time} (UTC");
        
        if ($offset == 0) {
            $tmzn_zer[] = $display . "&nbsp;{$offset_formatted})";
        }
        else if ($offset > 0) {
            $tmzn_pos[] = $display . "+{$offset_formatted})";
        }
        else {
            $tmzn_neg[] = $display . "-{$offset_formatted})";
        }
    }
    
    sort($tmzn_zer);
    sort($tmzn_pos);
    rsort($tmzn_neg);
    
    $cur_g = "";
    $new_k = 0;
    $tmzn_new = array();
    foreach($tmzn_neg as $v){
        
        $e = explode("|", $v);
        $cur_g = ($cur_g == "")? $e[0]: $cur_g;
        
        if ($cur_g != $e[0]) {
            sort($tmzn_new[$new_k]);
            $new_k++;
            $cur_g = $e[0];
        }
        
        $tmzn_new[$new_k][] = $v;
    }
    
    foreach($tmzn_new as $v){
        foreach($v as $n){
            $tmzn[] = $n;
        }
    }
    foreach($tmzn_zer as $v){
        $tmzn[] = $v;
    }
    foreach($tmzn_pos as $v){
        $tmzn[] = $v;
    }
    
    $s = ' selected="selected"';
    $html = "
<style>
span#jqcd_count,
select.timezone,
select.timezone option {
  font-family: \"Lucida Console\", Monaco, \"Courier New\", Courier, monospace !IMPORTANT;
}
span#jqcd_count { 
  font-size: 20pt;
  font-weight: bold;
}
input#jqcd_start,
input#jqcd_stop,
input#jqcd_reset {
  width: 65px;
  font-size: 10pt;
}
</style>
<select class='timezone' id='timezone' name='timezone[]'>";
    foreach($tmzn as $v){
        $e = explode("|", $v);
        $select = ($e[1] == $timezon_option)? $s: "";
        $html .= "
  <option value='{$e[1]}'{$select}>{$e[2]}</option>";
    }
    $html .= "
</select>
";
    
    return $html;
}

//Define variables and set to empty values
$time_format_err = $date_format_err = $countdown_duration_err = "";
$s = ' selected="selected"';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $timezone = $_POST['timezone'][0];
    $active_preacher = $_POST['active_preacher'];
    $time_format = $_POST['time_format'];
    
    if ($time_format == "") {
        $time_format_err = "Wrong formatting.  - <a target='_blank' href='http://php.net/manual/en/function.date.php'>HELP?</a>";
        echo "<script>var time_format_err = ' * This is a required field.';</script>";
    }
    
    $date_format = $_POST['date_format'];
    $countdown_duration = $_POST['countdown_duration'];
    
    if (!preg_match('/^([0-5]?[0-9]):([0-5][0-9])$/', $countdown_duration)) {
        $countdown_duration_err = "Wrong formatting. Ex. 5:00 / Max 59:59.";
        echo "<script>var countdown_duration_err = 'Wrong formatting. Ex. 5:00 / Max 59:59.';</script>";
    }
}
else {
    
    //Retrieve all the current settings from the database.
    $data = PDO_FetchAssoc("SELECT name, value FROM settings");
    $timezone = $data['timezone'];
    $active_preacher = $data['active_preacher'];
    $time_format = $data['time_format'];
    $date_format = $data['date_format'];
    $countdown_duration = $data['countdown_duration'];
}

echo "<script>var countdown = '{$countdown_duration}';</script>";

?>

<style>

.error {color: #FF0000; font-weight: bold}

table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  padding: 8px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

input {
  font-size: 12pt;
  font-weight: bold;
  font-family: \"Lucida Console\", Monaco, \"Courier New\", Courier, monospace !IMPORTANT;
}

.td-title {text-align: right; width: 20%;}
.td-field {width: 20%;}
.td-preview {width: 15%;}
.td-error {width: 45%;}

</style>

<h1>App Settings</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table border="1" id="table1">
  <tr>
    <td class="td-title">Timezone:</td>
    <td colspan="2">
      <input type="hidden" name="active_preacher" value="<?php echo $active_preacher; ?>">
      <?php echo generate_timezone_list($timezone); ?>
   </td>
  </tr>
  <tr class="time-format">
    <td class="td-title">Time Format:</td>
    <td class="td-field">
      <input type="text" id="time_format" name="time_format" value="<?php echo ($time_format == "")? 'g:i:s A': $time_format; ?>" onkeyup="settingsPreview()">
    </td>
    <td id="time_format_preview" class="td-error">
      <span class="error"> * <?php echo $time_format_err; ?></span>
    </td>
  </tr>
  <tr>
    <td class="td-title">Date Format:</td>
    <td class="td-field">
      <input type="text" id="date_format" name="date_format" value="<?php echo ($date_format == "")? 'l, F j, Y': $date_format; ?>" onkeyup="settingsPreview()">
    </td>
    <td id="date_format_preview" class="td-error">
      <span class="error"> * <?php echo $date_format_err; ?></span>
    </td>
  </tr>
  <tr class="countdown">
    <td class="td-title">Countdown Duration:</td>
    <td class="td-field">
      <input id="jqcd_time" type="text" name="countdown_duration" value="<?php echo ($countdown_duration == "")? '05:00': $countdown_duration; ?>">
    </td>
    <td class="td-error">
      <span class="error"> * <?php echo $countdown_duration_err; ?></span>
    </td>
  </tr>
  <tr class="countdown">
    <td class="td-title"></td>
    <td class="td-field">
      <input id="jqcd_start" onclick="jqcd('start');" type="button" class="Start" value="Start" name="start">
      <input id="jqcd_stop" onclick="jqcd('stop')" type="button" class="Stop" value="Stop" name="stop">
      <input id="jqcd_reset" onclick="jqcd('reset')" type="button" class="Reset" value="Reset" name="reset">
    </td>
    <td class="td-error">
      <span id="jqcd_count"><?php echo $countdown_duration; ?></span>
    </td>
  </tr>
  <tr>
    <td colspan="3">
      <input type="submit" name="submit" value="Save">
    </td>
  </tr>
  <tr>
    <td colspan="3">
      <p><span class="error"> * Required field.</span></p>
    </td>
  </tr>
</table>
</form>
<?php

//Set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('UTC');

if ((($time_format_err . $date_format_err . $countdown_duration_err) == "") && ($timezone != "") && ($_SERVER["REQUEST_METHOD"] == "POST")) {
  
  $query = <<< HTML
UPDATE settings SET
value = CASE name
WHEN 'active_preacher' THEN '{$active_preacher}'
WHEN 'timezone' THEN '{$timezone}'
WHEN 'time_format' THEN '{$time_format}'
WHEN 'date_format' THEN '{$date_format}'
WHEN 'countdown_duration' THEN '{$countdown_duration}'
END
WHERE name IN ('active_preacher','timezone','time_format','date_format','countdown_duration');
HTML;
  
  $query = trim($query);
  $stmt = @PDO_Execute($query);
  if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
    print("<pre>");
    $error = PDO_ErrorInfo();
    print_r($error[2]);
    print("</pre>");
  }
}
