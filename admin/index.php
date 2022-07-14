<?php

ob_start();

session_start();

require_once('../required/config.php');
require('../functions.php');

// Check for maintenance

maintenance();

//check_authentication();

if (isset($_POST['login']))
{
	$benutzername = mysql_real_escape_string($_POST['benutzername']);	
	$passwort = mysql_real_escape_string($_POST['passwort']);
	
	$date = date("Y-m-d H:i:s");
	$failed_passwort = $passwort;
	$failure_type = "Failed login";
	$page_name = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	
	if ($benutzername != "" && $passwort != "")
	{
		security($passwort);
		$passwort = md5($passwort);
		
		$query = sprintf("SELECT passwort, ist_admin, gesperrt FROM benutzer WHERE benutzername = '%s'",$benutzername);
		$select = mysql_query($query) or die ("Get data from db error. ".mysql_error());
		
		if ($count = mysql_num_rows($select) > 0)
		{
			while ($row = mysql_fetch_assoc($select))
			{
				$db_pass = $row['passwort'];	
				$db_admin = $row['ist_admin'];
				$db_gesperrt = $row['gesperrt'];
			}	
			
			if (($passwort === $db_pass) && $db_admin == 1 && $db_gesperrt == 0)
			{
				setcookie('username',$benutzername,time()+3600*24);
				$_SESSION['benutzername'] = $benutzername;
				$_SESSION['logged_ip'] = $_SERVER['REMOTE_ADDR'];
				
				// Update session time
				
				$timestamp = date("Y-m-d H:i:s");
				mysql_query("UPDATE benutzer SET session_time = '".$timestamp."', login_ip = '".$_SESSION['logged_ip']."' WHERE benutzername = '".$_SESSION['benutzername']."'");
				
				// write to log

				$log_date = date("Y-m-d H:i:s");
				$log_txt = $_SESSION['benutzername']." hat sich eingeloggt.";
				$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error()) or die ("MySQL write log error. ".mysql_error());
				
				header("Location: admin.php");	
			}
			else {
				// Save failed loggins to db
				
				$failed_log = sprintf("INSERT INTO login_stats (date, login_name, tried_password, login_ip, failure_type, page_name) VALUES ('%s','%s','%s','%s','%s','%s')",$date,$benutzername,$failed_passwort,$_SERVER['REMOTE_ADDR'],$failure_type,$page_name);
				
				$insert_log = mysql_query($failed_log) or die ("MySQL Write log error. ".mysql_error());
				
				echo "<p style='color:red;'>Benutzername/Passwort-Kombination leider falsch oder kein Zugriffsrecht!</p>";	
			}
		}
		else {
			
			$failed_log = sprintf("INSERT INTO login_stats (date, login_name, tried_password, login_ip, failure_type, page_name) VALUES ('%s','%s','%s','%s','%s','%s')",$date,$benutzername,$failed_passwort,$_SERVER['REMOTE_ADDR'],$failure_type,$page_name);
				
			$insert_log = mysql_query($failed_log) or die ("MySQL Write log error. ".mysql_error());
			
			echo "<p style='color:red;'>Benutzername/Passwort-Kombination leider falsch oder kein Zugriffsrecht!</p>";	
		}
	}	
	else {
		
		$failed_log = sprintf("INSERT INTO login_stats (date, login_name, tried_password, login_ip, failure_type, page_name) VALUES ('%s','%s','%s','%s','%s','%s')",$date,$benutzername,$failed_passwort,$_SERVER['REMOTE_ADDR'],$failure_type,$page_name);
				
		$insert_log = mysql_query($failed_log);
		
		echo "<p style='color:red;'>Bitte mit Benutername UND Passwort einloggen oder kein Zugriffsrecht!</p>";	
	}	
}

if (isset($_GET['session_timeout']) && $_GET['session_timeout'] == 'true')
{
	echo "<p style='color:red;'>Session Timeout - Ihre Sitzung wurde aus sicherheitsgründen beendet. Bitte erneut einloggen!</p>";	
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>FormerCompany Kundenmanagement - Administratorenlogin</title>
<link rel="stylesheet" type="text/css" href="../css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="../libs/js/prototype.js"></script>
<script type="text/javascript">

</script>
</head>

<body id="admin">

<div id="headLogo" style="float:left;">
	<img src="../img/logo_web.png" alt="FormerCompany" />
</div>

<div style="margin-left:600px;">
	<iframe src="http://www.weather365.net/foreign/city6a.php?cityid=34128" width="99%" height="112" align="left" scrolling="no" frameborder="0" name="Weather365"> <p>Ihr Browser kann leider keine eingebetteten Frames anzeigen: Sie können die eingebettete Seite über den folgenden Verweis aufrufen: <a href="http://www.weather365.net"> WEATHER365.net </a></p> </iframe>
</div>

<div style="clear:both;"></div>

<h1>Administratorenlogin</h1>

<noscript><h1 style="color: red;">Diese Anwendung erfordert zwingend JavaScript zur korrekten Ausf&uuml;hrung!</h1></noscript>

<form id="login_form" action="index.php" method="post">

<div id="loginForm">

<table border="0">
<tr>
	<td><label>Benutzername:</label></td>
    <td><input type="text" name="benutzername" id="benutzername" /></td>
</tr>
<tr>
	<td><label>Passwort:</label></td>
    <td><input type="password" name="passwort" id="passwort" /></td>
</tr>
</table>
</div>
<p>
<input type="submit" name="login" id="login" value="Login" />
</p>
</form>

<hr />

<a href="../index.php">Zum Benutzerbereich</a>

</body>
</html>