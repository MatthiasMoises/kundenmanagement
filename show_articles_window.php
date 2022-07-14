<?php

require('required/config.php');

include("functions.php");

$sql = mysql_query("SELECT id, bezeichnung FROM artikel ORDER BY bezeichnung ASC");

$limit = 4;
$count = 0;

echo "<h3>Artikelliste:</h3><hr />";

echo "
	<table border='1'>
		<thead>
			<th>ArtikelNr</th>
			<th>Bezeichnung</th>
		</thead>
";

while ($row = mysql_fetch_array($sql))
{
	$artId = $row['id'];
	$artBez = $row['bezeichnung'];

	echo "<tr>
			<td>$artId</td>
			<td>$artBez</td>
		</tr>";	
}

echo "</table>";

echo "<hr />";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kundenverwaltung - Artikel</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
</head>