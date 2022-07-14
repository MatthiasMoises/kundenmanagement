<?php

include('tpl/header.tpl.php');

$month = date("F");
$year = date("Y");

// check for failed logins first

$failure_count = 0;

$last_login = sprintf("SELECT letzter_login FROM benutzer WHERE benutzername = '%s'",$_SESSION['benutzername']);

$get_last_login = mysql_query($last_login);

while ($row_last_login = mysql_fetch_assoc($get_last_login))
{
	$last_login_date = $row_last_login['letzter_login'];	
}

$failed_log = sprintf("SELECT * FROM login_stats WHERE login_name = '%s' AND date > '%s'",$_SESSION['benutzername'],$last_login_date);

$get_failed_log = mysql_query($failed_log);

while ($row_failed_log = mysql_fetch_assoc($get_failed_log))
{
	$failure_count++;
	$failed_log_id = $row_failed_log['id'];
	$failed_log_date = $row_failed_log['date'];
	$failed_log_name = $row_failed_log['login_name'];	
}

// set user data

$date = date("Y-m-d H:i:s");

$update_login_time = sprintf("UPDATE benutzer SET letzter_login = '$date' WHERE benutzername = '%s'",$_SESSION['benutzername']);

$get_admin = mysql_query("SELECT ist_admin FROM benutzer WHERE benutzername = '".$_SESSION['benutzername']."'");

while ($admin_row = mysql_fetch_assoc($get_admin))
{
	$user_is_admin = $admin_row['ist_admin'];	
}

$update_date = mysql_query($update_login_time);

$date = date("Y-m-d H:i:s");

if (isset($_POST['saveNews']) && isset($_POST['newsTxt']) && $_POST['newsTxt'] != ""){
	
	$message = isset($_POST['newsTxt']) ? $_POST['newsTxt'] : "";
	$recipient = isset($_POST['users']) ? $_POST['users'] : "";
	
	if (isset($recipient) && $recipient != "") {
		$txt = "@$recipient: ".$message;	
	}
	else {
		$txt = $message;	
	}
	
	$save_news = mysql_query(sprintf("INSERT INTO news (author,text,date) VALUES ('%s','%s','%s')",$_SESSION['benutzername'],$txt,$date)) or die ("Save news error. ".mysql_error());
	
	$log_date = date("Y-m-d H:i:s");
	$log_txt = $_SESSION['benutzername']." hat eine neue Nachricht angelegt.";
	$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
	
}

// get all users for messaging

$all_users = mysql_query("SELECT benutzername FROM benutzer WHERE 1");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Hauptbereich</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
</head>

<h1>Herzlich Willkommen!</h1>

<?php

if (isset($failed_log_id)){
	if ($failure_count == 1)
		echo "<p style='color:red;'>$failure_count fehlgeschlagener Loginversuch seit dem letzten erfolgreichen Login!</p>";
	else if ($failure_count > 1)
		echo "<p style='color:red;'>$failure_count fehlgeschlagene Loginversuche seit dem letzten erfolgreichen Login!</p>";
}

// check for user notifications after login

if (isset($_GET['notification']) && $_GET['notification'] == true) {
   echo "<p style='color:red; font-size: 30px; font-wight: bold;'>HINWEIS: Ihr Passwort ist nicht sicher! Bitte &auml;ndern Sie Ihr Passwort entsprechend so, dass es Sonderzeichen enth&auml;lt!</p>";
}

?>

<h3>Bitte w&auml;hlen Sie einen Bereich aus dem Hauptmen&uuml;!</h3>

<?php

//if ($user_is_admin == 1){
?>
<hr />
<div id="newsInput">
<form id="newsForm" action="" method="post"> 
<label><b>Geben Sie hier f&uuml;r Mitarbeiter wichtige und interessante Neuigkeiten oder Hinweise an!</b></label><br /><br/>
Sie k&ouml;nnen einen allgemeinen Text angeben oder hier einen Benutzer als Adressaten festlegen: 
<select id="users" name="users">
<option></option>
<option>alle</option>
<?php
while ($row_all_users = mysql_fetch_assoc($all_users))
{
	$users = $row_all_users['benutzername'];
	if ($users != "admin")
		echo "<option>$users</option>";	
}
?>
</select>
<br/>
<small>(<b>Hinweis:</b> Dient nur der Vereinfachung, die Nachricht wird trotzdem allen angezeigt!)</small>
<br/>
<textarea style="width:800px;" id="newsTxt" name="newsTxt" cols="1" rows="2"></textarea><br />	
<input type="submit" name="saveNews" id="saveNews" value="Nachricht speichern" />
</form>
</div>    
<?php    
//}

?>

<h3 style="color:#FFF;">Aktuelle Neuigkeiten und Hinweise</h3>
<iframe class="newsFrame" width="1200" height="350" src="news.php" scrolling="yes"></iframe>

<h3 style="color:#FFF;">Heutige Termine</h3>
<iframe class="newsFrame" width="1200" height="200" src="calendar_today.php" scrolling="yes"></iframe>

<h3 style="color:#FFF;">Aktuelle Termine f&uuml;r <?php echo $month." ".$year; ?></h3>
<iframe class="newsFrame" width="1200" height="200" src="calendar_month.php" scrolling="yes"></iframe>

<h3 style="color:#FFF;">Systemneuigkeiten- und informationen</h3>
<iframe class="newsFrame" width="1200" height="150" src="admin_news.php" scrolling="yes"></iframe>

</body>
</html>

<?php

include('tpl/footer.tpl.php');

?>
