<?php

include('tpl/header.tpl.php');

$get_data = mysql_query("SELECT id, author, DATE_FORMAT (date, '%d.%m.%Y %H:%i:%s') AS date, short_desc, long_desc, status, current, admin_comment, time_needed FROM support WHERE 1 ORDER BY current ASC, id DESC") or die("MySQL get support data error. ".mysql_error());

$counter = mysql_num_rows($get_data);

$entryCounter = 0;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Support</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
</head>

<body>

<h1>Support</h1>

<p>
<input type="button" id="new" name="new" value="Neues Ticket anlegen" onclick="parent.location='create_ticket.php'" />
</p>

<hr/>

<br/>

<?php

if ($counter > 0) 
{

?>

<table id="customer_table" border="1">
<thead>
	<th>ID</th>
    <th>Author</th>
    <th>Datum</th>
    <th>Kurzbeschreibung (Wo?)</th>
    <th>Auf&uuml;hrliche Beschreibung</th>
    <th>Status</th>
    <th>Aktuell</th>
    <th>Administratorkommentar</th>
    <th>Zeitaufwand (Minuten)</th>
    <?php
	if ($_SESSION['benutzername'] == "admin") {
		?>
        <th>Optionen</th>
        <?php	
	}
	?>
</thead>

<?php

	while ($row = mysql_fetch_assoc($get_data)) {
		
		$entryCounter++;
		
		$id = $row['id'];
		$author = $row['author'];
		$date = $row['date'];
		$short = $row['short_desc'];
		$long = $row['long_desc'];
		$status = $row['status'];	
		$current = $row['current'];
		$admin_comment = $row['admin_comment'];
		$time_needed = $row['time_needed'];
		$time_needed = str_replace(".",",",$time_needed);
	
		if (($entryCounter % 2) == 0)
			{
				echo "<tr style='background-color:#FFF1AA;'>";
			}
			else {
				echo "<tr style='background-color:#9AE9FA;'>";	
			}
		echo "	
			<td>$id</td>
			<td>$author</td>
			<td>$date</td>
			<td>$short</td>
			<td>$long</td>
			<td>$status %</td>";
			if ($status < 100) {
				echo "<td style='color:red;'>$current</td>";	
			}
			else  {
				echo "<td style='color:green;'>$current</td>";	
			}
		echo "	
			<td>$admin_comment</td>
			<td align='center'>$time_needed</td>";
			if ($_SESSION['benutzername'] == "admin") {
				echo "	<td align='center'><a href='create_ticket.php?action=edit&ticket_nr={$id}'><img src='img/edit.gif' alt='edit' title='Ticket bearbeiten' /></a></td>";
			}
		echo "	
		</tr>
		";
	}
}
else {
	echo "<p><b>Keine Tickets vorhanden!</b></p>";	
}

?>

</table>

<br/>

<hr />

<p>
<input type="button" id="new" name="new" value="Neues Ticket anlegen" onclick="parent.location='create_ticket.php'" />
</p>

<hr />

</body>
</html>

<?php

include('tpl/footer.tpl.php');

?>