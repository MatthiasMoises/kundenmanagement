<?php

include('tpl/header.tpl.php');

if (isset($_POST['saveNews']))
{
	$txt = isset($_POST['newsTxt']) ? $_POST['newsTxt'] : "";
	$date = date("Y-m-d H:i:s");
	$save_news = mysql_query(sprintf("INSERT INTO admin_news (author,text,date) VALUES ('%s','%s','%s')","admin",$txt,$date)) or die ("Save admin news error. ".mysql_error());	
	
	echo "<p>Nachricht wurde gespeichert!</p>";
	
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin - Neuigkeiten anlegen</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/js/scriptaculous.js"></script>
<script>

</script>
</head>

<body>

<h1>Admin - Neuigkeiten anlegen</h1>

<hr/>

<div id="newsInput">
<form id="newsForm" action="" method="post"> 
<label><b>Systemneuigkeiten und Informationen angeben</b></label><br /><br/>

<textarea style="width:800px;" id="newsTxt" name="newsTxt" cols="1" rows="2"></textarea><br />	
<input type="submit" name="saveNews" id="saveNews" value="Nachricht speichern" />
</form>
</div> 

<hr/>

<?php

echo "<h2>Systeminfo</h2>";

echo "<b>Server-Adresse:</b> ".$_SERVER['SERVER_ADDR']."<br/>";
echo "<b>Server-Name:</b> ".$_SERVER['SERVER_NAME']."<br/>";
echo "<b>Server-Admin:</b> ".$_SERVER['SERVER_ADMIN']."<br/>";
echo "<b>Server-Port:</b> ".$_SERVER['SERVER_PORT']."<br/>";
echo "<b>Server-Protocol:</b> ".$_SERVER['SERVER_PROTOCOL']."<br/>";
echo "<b>Server-Signatur:</b> ".$_SERVER['SERVER_SIGNATURE']."<br/>";
echo "<b>Server-Software:</b> ".$_SERVER['SERVER_SOFTWARE']."<br/><br/>";
echo "<b>Http-User-Agent:</b> ".$_SERVER['HTTP_USER_AGENT']."<br/>";

echo "<hr/>";

$get_login_stats = mysql_query("SELECT id, DATE_FORMAT (date, '%d.%m.%Y %H:%i:%s') AS date, login_name, tried_password, login_ip, failure_type, page_name FROM login_stats WHERE 1 ORDER BY id");

echo "<h2>Loginstats</h2>";

echo "<table>
		<thead>
			<th>ID</th>
			<th>Datum</th>
			<th>Login Name</th>
			<th>Versuchtes Passwort</th>
			<th>IP-Adresse</th>
			<th>Fehler-Typ</th>
			<th>Seitenname</th>
		</thead>";	

while ($row_login_stats = mysql_fetch_assoc($get_login_stats))
{
	$stats_id = $row_login_stats['id'];
	$stats_date = $row_login_stats['date'];
	$stats_login_name = $row_login_stats['login_name'];
	$stats_tried_password = $row_login_stats['tried_password'];
	$stats_ip = $row_login_stats['login_ip'];	
	$stats_failure_type = $row_login_stats['failure_type'];
	$stats_page_name = $row_login_stats['page_name'];
	
	echo "<tr>
			<td>$stats_id</td>
			<td>$stats_date</td>
			<td>$stats_login_name</td>
			<td>$stats_tried_password</td>
			<td>$stats_ip</td>
			<td>$stats_failure_type</td>
			<td>$stats_page_name</td>
		</tr>";	
}

echo "</table>";

echo "<hr/>";

echo "<h2>Show complete log</h2>";

$get_full_log = mysql_query("SELECT DATE_FORMAT(date, '%d.%m.%Y') AS date, log_txt FROM log_complete WHERE 1") or die ("MySQL select full log data failed. ".mysql_error());

echo "<table>";

while ($row_get_full_log = mysql_fetch_assoc($get_full_log))
{
	$log_date = $row_get_full_log['date'];
	$log_txt = $row_get_full_log['log_txt'];
	
	echo "<tr><td>".$log_date." - ".$log_txt."</td></tr>";
}

echo "</table>";

?>

<hr  />

<p>
<a href="main.php">Zur&uuml;ck zur Startseite</a>
</p>

</body>
</html>

<?php

include('tpl/footer.tpl.php');

?>