<?php

include('tpl/header.tpl.php');

$kalender = new Kalender();

if (isset($_GET['bt_search']))
{
	$searchTerm = $_GET['search'];
	$query = "SELECT id, DATE_FORMAT (start_date, '%d.%m.%Y') AS start_date, DATE_FORMAT (end_date, '%d.%m.%Y') AS end_date, DATE_FORMAT (start_time, '%H:%i') AS start_time, DATE_FORMAT (end_time, '%H:%i') AS end_time, description, author, important, mail_sent FROM kalender WHERE description LIKE '%$searchTerm%' AND end_date >= CURDATE() ORDER BY start_date ASC";  
}
else {
	$query = "SELECT id, DATE_FORMAT (start_date, '%d.%m.%Y') AS start_date, DATE_FORMAT (end_date, '%d.%m.%Y') AS end_date, DATE_FORMAT (start_time, '%H:%i') AS start_time, DATE_FORMAT (end_time, '%H:%i') AS end_time, description, author, important, mail_sent FROM kalender WHERE end_date >= CURDATE() ORDER BY start_date ASC";	
}

$select = mysql_query($query) or die ("MySQL select termine error. ".mysql_error());

$row_count = mysql_num_rows($select);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Terminverwaltung</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/js/scriptaculous.js"></script>
<script>

function del(id) {
		
		if (confirm("Wollen Sie diesen Termin wirklich endgültig löschen?"))
		{
	
			new Ajax.Updater("","calendar_entry.php?action=delete", {
		
			parameters : {
			
			'action'		: 'delete',
			'id'			: id
			},
			evalScripts : true,
			encoding : 'ISO-8859-1',
			
			onFailure : function() {
				alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
			},
			
			onComplete : function() {
				alert ("Termin wurde erfolgreich gelöscht!");
				location.reload(true);
			}
		});
	}
}

</script>
</head>

<body>

<h2>Terminverwaltung</h2>

<div style="float:left;">
	<input type="button" name="home" value="Neuen Termin anlegen" onClick="parent.location='calendar_entry.php?action=new'" />
</div>

<div id="searchField">
	<form action="" method="get">
    	<label>Suche:</label>
        <input type="text" id="search" name="search" />
        <input type="submit" id="bt_search" name="bt_search" value="Termin suchen" />
    </form>
</div>

<div style="clear:both;"></div>

<hr />

<?php

echo "<b>Zeige $row_count Eintr&auml;ge</b><br/><hr/>";

if ($row_count > 0)
{

		?>
		
		<form id="kalender_table" action="" method="post">
		
		<table border="1">
			<thead>
				<th>ID</th>
				<th>Startdatum</th>
				<th>Enddatum</th>
				<th>Startzeit</th>
				<th>Endzeit</th>
				<th>Beschreibung</th>
				<th>Autor</th>
				<th>Status</th>
                <th>Info-Mail</th>
				<th colspan="2">Optionen</th>
			</thead>
			
				<?php
				
				$count = 0;
				
		
				while ($row = mysql_fetch_assoc($select))
				{
					$count++;
					
					$kalender->setId($row['id']);
					$kalender->setStartDate($row['start_date']);
					$kalender->setEndDate($row['end_date']);
					$kalender->setStartTime($row['start_time']);
					$kalender->setEndTime($row['end_time']);
					$kalender->setDescription($row['description']);
					$kalender->setAuthor($row['author']);
					$kalender->setImportant($row['important']);
					$kalender->setMailSent($row['mail_sent']);
					
					if ($kalender->getMailSent() == 1)
						$mail = "<img src='img/email_go.gif' title='Mail erfolgreich verschickt' alt='mail verschickt' />";
					else if ($kalender->getMailSent() == 0)
						$mail = "-";
					
					if ($kalender->getImportant() == 1)
					{
						echo "<tr style='background-color:#FF0000; color:#ffffff;'>";	
					}
					else if (($count % 2) == 0)
					{
						echo "<tr style='background-color:#FFF1AA;'>";
					}
					else {
						echo "<tr style='background-color:#9AE9FA;'>";	
					}
					
					echo "
							<td align='center'>{$kalender->getId()}</td>
							<td align='center'>{$kalender->getStartDate()}</td>
							<td align='center'>{$kalender->getEndDate()}</td>
							<td align='center'>{$kalender->getStartTime()}</td>
							<td align='center'>{$kalender->getEndTime()}</td>
							<td>{$kalender->getDescription()}</td>
							<td align='center'>{$kalender->getAuthor()}</td>";
					if ($kalender->getImportant() == 1)
						echo "<td align='center'>Dringend</td>";
					else 
						echo "<td align='center'>Neutral</td>";	 
					echo "	<td align='center'>$mail</td>
							<td align='center'><a href='calendar_entry.php?action=edit&id={$kalender->getId()}'><img src='img/edit.gif' alt='edit' title='Termin bearbeiten' /></a></td>
							<td align='center'><a href='javascript:del({$kalender->getId()})'><img src='img/cross.gif' alt='delete' title='Termin l&ouml;schen' /></a></td>
						</tr>
						";
				}
			}
else {
	echo "<b>Keine aktuellen Termine vorhanden!</b>";	
}
		
    	?>
    
</table>

</form>

<hr />

<div>
	<input type="button" name="home" value="Neuen Termin anlegen" onClick="parent.location='calendar_entry.php?action=new'" />
</div>

<hr  />

<p>
<a href="main.php">Zur&uuml;ck zur Startseite</a>
</p>

</body>
</html>

<?php

include("tpl/footer.tpl.php");

?>