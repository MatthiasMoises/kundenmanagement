<?php

require('required/config.php');

include("functions.php");

$sql = mysql_query("SELECT id, vorname, nachname FROM kunden ORDER BY id ASC");

$limit = 4;
$count = 0;

echo "<h3>Kundenliste:</h3><hr />";

echo "
	<table border='1'>
		<thead>
			<th>KundenNr</th>
			<th>Vorname</th>
			<th>Nachname</th>
		</thead>
";

while ($row = mysql_fetch_array($sql))
{
	$kndId = $row['id'];
	$vorname = $row['vorname'];
	$nachname = $row['nachname'];

	echo "<tr>
			<td>$kndId</td>
			<td>$vorname</td>
			<td>$nachname</td>
		</tr>";	
}

echo "</table>";

echo "<hr />";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kundenverwaltung - Kunden</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
</head>