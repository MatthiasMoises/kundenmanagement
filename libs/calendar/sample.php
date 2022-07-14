<?php
// Request selected language
$hl = (isset($_REQUEST["hl"])) ? $_REQUEST["hl"] : false;
if(!defined("L_LANG") || L_LANG == "L_LANG")
{
	if($hl) define("L_LANG", $hl);

	// You need to tell the class which language do you use.
	// L_LANG should be defined as en_US format!!! Next line is an example, just put your own language from the provided list
	else define("L_LANG", "ro_RO"); // Romanian exemple
}
// IMPORTANT: Request the selected date from the calendar
$mydate = isset($_REQUEST["date1"]) ? $_REQUEST["date1"] : "";
// Note: this sample doesn't show you how to use the $mydate variable with your database, but you can handle it as any other php variable in your script!
?>
<html>
<head>
<title>Your page title</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
// Load the calendar class
require('tc_calendar.php');
?>
</head>

<body>
[...]
<br />
Your page content to the point you want to insert the calendar form
<form name="calendar" method="post" action="">
<table>
	<tr>
		<td>
		<?php
		  // Call the calendar constructor - use the desired form and format, according to the instructions/samples provided on triconsole.com
		  $myCalendar = new tc_calendar("date1", true);
		  $myCalendar->setPicture("images/iconCalendar.gif");
		  $myCalendar->setDate(date('d'), date('m'), date('Y'));
		  $myCalendar->setPath("./");
		  $myCalendar->zindex = 150; //default 1
		  $myCalendar->setYearSelect(1960, date('Y'));
		  $myCalendar->dateAllow('1960-03-01', date('Y-m-d'));
		  //$myCalendar->autoSubmit(true, "calendar");
		  $myCalendar->setDateFormat(str_replace("%","",str_replace("B","F",str_replace("d","j",L_CAL_FORMAT))));
		  $myCalendar->disabledDay("sun");
		  $myCalendar->writeScript();
		  ?>
		</td>
	<tr/>
</table>
</form>
Rest of your page body content
<br />
[...]
<br /><br />
<a href="sample.txt" target="blank">View page source</a>
</body>
</html>