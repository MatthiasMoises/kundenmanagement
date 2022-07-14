<?php

session_start();

require('required/config.php');
include('classes/kalender.class.php');

$kalender = new Kalender();

$query = "SELECT DATE_FORMAT (start_date, '%d.%m.%Y') AS start_date, DATE_FORMAT (end_date, '%d.%m.%Y') AS end_date, DATE_FORMAT (start_time, '%H:%i') AS start_time, DATE_FORMAT (end_time, '%H:%i') AS end_time, description, author, important FROM kalender WHERE start_date = CURDATE() OR end_date = CURDATE() ORDER BY start_date ASC";	

$select = mysql_query($query) or die ("MySQL select termine error. ".mysql_error());

$row_count = mysql_num_rows($select);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/js/scriptaculous.js"></script>
</head>

<body>

<?php

if ($row_count > 0)
{
		?>
		
		<table border="1" width="1150">
			<thead>
				<th>Startdatum</th>
				<th>Enddatum</th>
				<th>Startzeit</th>
				<th>Endzeit</th>
				<th>Beschreibung</th>
				<th>Autor</th>
                <th>Status</th>
			</thead>
			
				<?php
				
				while ($row = mysql_fetch_assoc($select))
				{
					$kalender->setStartDate($row['start_date']);
					$kalender->setEndDate($row['end_date']);
					$kalender->setStartTime($row['start_time']);
					$kalender->setEndTime($row['end_time']);
					$kalender->setDescription($row['description']);
					$kalender->setAuthor($row['author']);
					$kalender->setImportant($row['important']);
					
					echo "<tr style='background-color:#FFF1AA;'>
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
					echo "</tr>";
				}
			}
else {
	echo "<b>Keine Termine f&uuml;r heute vorhanden!</b>";	
}
		
    	?>
    
</table>

</body>
</html>