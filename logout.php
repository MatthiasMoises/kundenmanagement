<?php

require('required/config.php');

session_start();

$logout = mysql_query("UPDATE benutzer SET status = 'OFF' WHERE benutzername = '".$_SESSION['benutzername']."'") or die (mysql_error());

$date = date("Y-m-d H:i:s");
$log_txt = $_SESSION['benutzername']." hat sich ausgeloggt.";

$insert_log = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$date','$log_txt')") or die("MySQL INSER INTO log error. ".mysql_error());

session_destroy();

setcookie('username');

if (isset($_GET['session_timeout']) && $_GET['session_timeout'] == 'true')
{
	header("Location: index.php?session_timeout=true");		
}
else {
	header("Location: index.php");	
}

?>