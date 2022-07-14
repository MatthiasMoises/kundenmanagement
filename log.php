<?php

include('tpl/header.tpl.php');

$get_rights = mysql_query("SELECT ist_admin FROM benutzer WHERE benutzername = '".$_SESSION['benutzername']."'");

while ($row_admin = mysql_fetch_assoc($get_rights))
{
	$ist_admin = $row_admin['ist_admin'];	
}

if (isset($_POST['delete_log']))
{
	$log_date = date("Y-m-d H:i:s");
	$log_txt = $_SESSION['benutzername']." hat das Loguch geleert.";
	
	$get_log_data = mysql_query("SELECT * FROM log WHERE 1") or die ("MySQL SELECT current log data failed. ".mysql_error());
	
	while ($row_get_log_data = mysql_fetch_assoc($get_log_data))
	{
		$db_log_date = $row_get_log_data['date'];
		$db_log_txt = $row_get_log_data['log_txt'];
		
		$create_complete_log = mysql_query("INSERT INTO log_complete (date,log_txt) VALUES ('$db_log_date','$db_log_txt')") or die("MySQL create complete log error. ".mysql_error());
		
		$insert_delete = mysql_query("INSERT INTO log_complete (date, log_txt) VALUES ('$log_date','$log_txt')") or die("MySQL insert deleter in full log error. ".mysql_error());
			
	}
	
	$clear_log = mysql_query("DELETE FROM log WHERE 1") or die ("MySQL DELETE FROM log error. ".mysql_error());
	if ($clear_log)
	{
		echo "<p>Logbuch erfolgreich geleert</p>";	
	
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());	
	}
}

$get_log = mysql_query("SELECT DATE_FORMAT (date, '%d.%m.%Y %H:%i:%s') AS date, log_txt FROM log WHERE 1") or die("MySQL SELECT FROM log error. ".mysql_error());

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Logbuch</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript">

</script>
</head>

<body>

<h2>Logbuch</h2>

<table>

<?php

if (mysql_num_rows($get_log) > 0)
{
	while ($row_log = mysql_fetch_assoc($get_log))
	{
		$log_date = $row_log['date'];
		$log_txt = $row_log['log_txt'];
		
		echo "
			<tr><td>$log_date - $log_txt</td></tr>
		";		
	}
}
else {
	echo "<p>Keine Logbucheintr&auml;ge vorhanden!</p>";	
}

?>

</table>

<?php

if ($ist_admin == 1)
{
?>

<form id="delete_form" action="" method="post">
	<p>
	<input type="submit" id="delete_log" name="delete_log" value="Logbuch leeren" />
	</p>
</form>

<?php
}
?>

</body>
</html>

<?php

include("tpl/footer.tpl.php");

?>