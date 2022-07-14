<?php

include('tpl/header.tpl.php');
require('libs/calendar/tc_calendar.php');

$kalender = new Kalender();

$action = isset($_GET['action']) ? $_GET['action'] : NULL;

switch($action){
	case 'new':
		$start_time = date("H:i");
		$end_time = date("H:i");
		
		$last_id = mysql_insert_id();
		
		$log_date = date("Y-m-d H:i:s");
		$log_txt = $_SESSION['benutzername']." hat Kalendereintrag Nr. $last_id angelegt.";
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
	
		break;
	case 'edit':
		
		$id = isset($_GET['id']) ? $_GET['id'] : NULL;
	
		$get_query = "SELECT * FROM kalender WHERE id = '$id' LIMIT 1";	
	
		$get_data = mysql_query($get_query) or die ("MySQL select kalender entry data error. ".mysql_error());
		
		while ($row = mysql_fetch_array($get_data))
		{
			$db_startdate = $row['start_date'];	
			$db_enddate = $row['end_date'];
			$db_starttime = $row['start_time'];
			$db_endtime = $row['end_time'];
			$db_description = $row['description'];
			$db_author = $row['author'];
			$db_important = $row['important'];
			$db_mail_sent = $row['mail_sent'];
		}
		
		$db_startdate_year = substr($db_startdate,0,4);
		$db_startdate_month = substr($db_startdate,5,2);
		$db_startdate_day = substr($db_startdate,8,2);
		
		$db_enddate_year = substr($db_enddate,0,4);
		$db_enddate_month = substr($db_enddate,5,2);
		$db_enddate_day = substr($db_enddate,8,2);
		
		$log_date = date("Y-m-d H:i:s");
		$log_txt = $_SESSION['benutzername']." hat Kalendereintrag Nr. $id aktualisiert.";
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
	
		break;
	case 'delete':
	
		$id = isset($_POST['id']) ? $_POST['id'] : NULL;
	
		$delete_entry = mysql_query("DELETE FROM kalender WHERE id = '$id' LIMIT 1") or die ("MySQL kalender entry delete error. ".mysql_error());
		
		$log_date = date("Y-m-d H:i:s");
		$log_txt = $_SESSION['benutzername']." hat Kalendereintrag Nr. $id gel&ouml;scht.";
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
	
		break;
	default:
		echo '<span class="error">unknown name</span>';
		break;	
}

if (isset($_POST['saveEntry'])){
	
	if (isset($_POST['description']) && $_POST['description'] != "")
	{
		// Startdatum
		
		$startdate_day = mysql_real_escape_string($_POST['startdatum_day']);	
		$startdate_month = mysql_real_escape_string($_POST['startdatum_month']);
		$startdate_year = mysql_real_escape_string($_POST['startdatum_year']);
		$startdate_complete = $startdate_year."-".$startdate_month."-".$startdate_day;
		
		$kalender->setStartDate($startdate_complete);
	
		// Enddatum
		
		$enddate_day = mysql_real_escape_string($_POST['enddatum_day']);	
		$enddate_month = mysql_real_escape_string($_POST['enddatum_month']);
		$enddate_year = mysql_real_escape_string($_POST['enddatum_year']);
		$enddate_complete = $enddate_year."-".$enddate_month."-".$enddate_day;
		
		$kalender->setEndDate($enddate_complete);
	
		// Startzeit
		
		$kalender->setStartTime($_POST['starttime']);
		
		// Endzeit
		
		$kalender->setEndTime($_POST['endtime']);
		
		// Rest
		
		$kalender->setAuthor($_SESSION['benutzername']);
		$kalender->setDescription(isset($_POST['description']) ? $_POST['description'] : "");
		$kalender->setImportant(isset($_POST['important']) ? 1 : 0);
		$kalender->setMailSent(isset($_POST['sendMail']) ? 1 : 0);
		
		// Speichern
		
		$save_query = sprintf("INSERT INTO kalender (start_date,end_date,start_time,end_time,description,author,important,mail_sent) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')",$kalender->getStartDate(),$kalender->getEndDate(),$kalender->getStartTime(),$kalender->getEndTime(),$kalender->getDescription(),$kalender->getAuthor(),$kalender->getImportant(),$kalender->getMailSent());
		
		if ($action == 'edit'){
			$id = $_GET['id'];
			$delete_entry = mysql_query("DELETE FROM kalender WHERE id = '$id' LIMIT 1") or die ("MySQL kalender entry delete error. ".mysql_error());
		}
		
		$save_entry = mysql_query($save_query) or die ("MySQL insert kalender entry error. ".mysql_error());
		define("LAST_ID",mysql_insert_id());
		
		if ($save_entry && $kalender->getMailSent() == 0)
			echo "<h3 style='color:blue'>Kalendereintrag erfolgreich gespeichert!</h3>";
		
		else if($kalender->getMailSent() != "" && $kalender->getMailSent() == 1 && $save_entry){
			include('mail.php');
			echo "<h3 style='color:green'>Kalendeintrag gespeichert und E-Mail erfolgreich verschickt!</h3>";
		}
		else {
			echo "<h3 style='color:red'>Es ist leider ein Fehler aufgetreten!</h3>";	
		}
	}
	else {
		echo "<h3 style='color:red'>F&uuml;llen Sie bitte alle notwendigen Felder aus!</h3>";		
	}
}

$myCalendar = new tc_calendar("date2");
$myCalendar->setIcon("libs/calendar/images/iconCalendar.gif");
$myCalendar->setDate(date('d'), date('m'), date('Y'));
$myCalendar->setPath("libs/calendar/");
$myCalendar->setYearInterval(2011, 2020);
$myCalendar->dateAllow('2011-01-01', '2020-12-31', false);
$myCalendar->startMonday(true);
//$myCalendar->disabledDay("Sat");
//$myCalendar->disabledDay("sun");
$myCalendar->writeScript();
	  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Terminverwaltung - Kalendereintrag</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/calendar/calendar.js"></script>
</head>

<body>

<h2>Termin bearbeiten</h2>

<?php

if ($action == 'new')
{
?>

<form id="newEntry" action="" method="post">
<table>
	<tr>
    	<td><label>Startdatum:</label></td>
        <td><label>Enddatum:</label></td>
    </tr>
    	<tr>
    	<td>
        <?php
		  $myCalendar = new tc_calendar("startdatum", true);
		  $myCalendar->setIcon("libs/calendar/images/iconCalendar.gif");
		  $myCalendar->setDate(date("d"), date("m"), date("Y"));
		  $myCalendar->setPath("libs/calendar/");
		  $myCalendar->setYearInterval(2011, 2020);
		  $myCalendar->dateAllow('2011-01-01', '2020-12-31');
		  $myCalendar->writeScript();
		?>
        </td>
        <td>
        <?php
		  $myCalendar = new tc_calendar("enddatum", true);
		  $myCalendar->setIcon("libs/calendar/images/iconCalendar.gif");
		  $myCalendar->setDate(date("d"), date("m"), date("Y"));
		  $myCalendar->setPath("libs/calendar/");
		  $myCalendar->setYearInterval(2011, 2020);
		  $myCalendar->dateAllow('2011-01-01', '2020-12-31');
		  $myCalendar->writeScript(); 
		?>
        </td>
    </tr>
    <tr>
    	<td><label>Startzeit:</label></td>
        <td><label>Endzeit:</label></td>
    </tr>
    	<tr>
    	<td><input type="text" id="starttime" name="starttime" value="<?php echo $start_time; ?>" /></td>
        <td><input type="text" id="endtime" name="endtime" value="<?php echo $end_time; ?>" /></td>
    </tr>
    <tr>
    	<td><label>Beschreibung:</label></td>
        <td><textarea id="description" name="description" style="width:500px;"></textarea></td>
    </tr>
    <tr>
    	<td><label>Wichtig?</label></td>
        <td><input type="checkbox" id="important" name="important" /></td>
    </tr>
    <tr>
    	<td><label>Info-Mail an Mitarbeiter senden?</label></td>
        <td><input type="checkbox" id="sendMail" name="sendMail" /></td>
    </tr>
</table>

<p>
<input type="submit" id="saveEntry" name="saveEntry" value="Eintrag speichern" />
</p>

</form>

<?php
}

else if ($action == 'edit')
{

?>

<form id="editEntry" action="" method="post">
<table>
	<tr>
    	<td><label>Startdatum:</label></td>
        <td><label>Enddatum:</label></td>
    </tr>
    	<tr>
    	<td>
        <?php
		  $myCalendar = new tc_calendar("startdatum", true);
		  $myCalendar->setIcon("libs/calendar/images/iconCalendar.gif");
		  $myCalendar->setDate($db_startdate_day, $db_startdate_month, $db_startdate_year);
		  $myCalendar->setPath("libs/calendar/");
		  $myCalendar->setYearInterval(2011, 2020);
		  $myCalendar->dateAllow('2011-01-01', '2020-12-31');
		  $myCalendar->writeScript();
		?>
        </td>
        <td>
        <?php 
		  $myCalendar = new tc_calendar("enddatum", true);
		  $myCalendar->setIcon("libs/calendar/images/iconCalendar.gif");
		  $myCalendar->setDate($db_enddate_day, $db_enddate_month, $db_enddate_year);
		  $myCalendar->setPath("libs/calendar/");
		  $myCalendar->setYearInterval(2011, 2020);
		  $myCalendar->dateAllow('2011-01-01', '2020-12-31');
		  $myCalendar->writeScript(); 
		?>
        </td>
    </tr>
    <tr>
    	<td><label>Startzeit:</label></td>
        <td><label>Endzeit:</label></td>
    </tr>
    	<tr>
    	<td><input type="text" id="starttime" name="starttime" value="<?php echo $db_starttime; ?>" /></td>
        <td><input type="text" id="endtime" name="endtime" value="<?php echo $db_endtime; ?>" /></td>
    </tr>
    <tr>
    	<td><label>Beschreibung:</label></td>
        <td><textarea id="description" name="description" style="width:500px;"><?php echo $db_description; ?></textarea></td>
    </tr>
    <tr>
    	<td><label>Wichtig?</label></td>
        <td><input type="checkbox" id="important" name="important" value="<?php if ($db_important == 1) echo 'checked="checked"'?>" /></td>
    </tr>
    <tr>
    	<td><label>Info-Mail an Mitarbeiter senden?</label></td>
        <td><input type="checkbox" id="sendMail" name="sendMail" /></td>
    </tr>
</table>

<p>
<input type="submit" id="saveEntry" name="saveEntry" value="Eintrag speichern" />
</p>

</form>

<?php

}

?>
<hr />

<input type="button" name="home" value="Zur&uuml;ck" onClick="parent.location='calendar.php'"  />

</body>
</html>


<?php

include('tpl/footer.tpl.php');

?>