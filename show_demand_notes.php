<?php

include('tpl/header.tpl.php');

$mahnung = new Mahnung();
$kunde = new Kunde();
$benutzer = new Benutzer();

$get_rights = mysql_query("SELECT ist_admin FROM benutzer WHERE benutzername = '".$_SESSION['benutzername']."'") or die ("MySQL get rights error. ".mysql_error());

while ($row_get_rights = mysql_fetch_assoc($get_rights))
{
	$benutzer->setAdmin($row_get_rights['ist_admin']);	
}

$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : NULL;
$sort = isset($_GET['sort']) ? $_GET['sort'] : NULL;

if (isset($_GET['bt_search']))
{
	$searchTerm = $_GET['search'];
	$query = "SELECT m.id, m.mahnungsnr, DATE_FORMAT (m.mahnungsdatum, '%d.%m.%Y') AS rech_date, m.kdnr, m.bezahlt, m.editierbar, m.hat_stunden, m.hat_artikel, DATE_FORMAT (m.faelligkeitsdatum, '%d.%m.%Y') AS bez_date, m.gedruckt, k.nachname, k.vorname FROM mahnungen m, kunden k WHERE (m.mahnungsnr = '$searchTerm' OR k.nachname LIKE '%$searchTerm%') AND m.kdnr = k.id ORDER BY m.id ASC";	  
}
else if (isset($_GET['start'])) {
	// max displayed per page
	$per_page = 50;

	// get start variable
	$start = $_GET['start'];

	// count records
	$record_count = mysql_num_rows(mysql_query("SELECT m.id, m.mahnungsnr, DATE_FORMAT (m.mahnungsdatum, '%d.%m.%Y') AS rech_date, m.kdnr, m.bezahlt, m.editierbar, m.hat_stunden, m.hat_artikel, DATE_FORMAT (m.faelligkeitsdatum, '%d.%m.%Y') AS bez_date, m.gedruckt, k.nachname, k.vorname FROM mahnungen m, kunden k WHERE m.kdnr = k.id"));
	
	// count max pages
	$max_pages = $record_count / $per_page; // may come out as decimal
	
	if (!$start)
		$start = 0;
		
	// display data
	$query = "SELECT m.id, m.mahnungsnr, DATE_FORMAT (m.mahnungsdatum, '%d.%m.%Y') AS rech_date, m.kdnr, m.bezahlt, m.editierbar, m.hat_stunden, m.hat_artikel, DATE_FORMAT (m.faelligkeitsdatum, '%d.%m.%Y') AS bez_date, m.gedruckt, k.nachname, k.vorname FROM mahnungen m, kunden k WHERE m.kdnr = k.id ORDER BY m.id ASC LIMIT $start, $per_page";
}
else if ($orderBy != NULL && $sort != NULL) {
	$query = "SELECT m.id, m.mahnungsnr, DATE_FORMAT (m.mahnungsdatum, '%d.%m.%Y') AS rech_date, m.kdnr, m.bezahlt, m.editierbar, m.hat_stunden, m.hat_artikel, DATE_FORMAT (m.faelligkeitsdatum, '%d.%m.%Y') AS bez_date, m.gedruckt, k.nachname, k.vorname FROM mahnungen m, kunden k WHERE m.kdnr = k.id ORDER BY $orderBy $sort";		
}
else {
	$query = "SELECT m.id, m.mahnungsnr, DATE_FORMAT (m.mahnungsdatum, '%d.%m.%Y') AS rech_date, m.kdnr, m.bezahlt, m.editierbar, m.hat_stunden, m.hat_artikel, DATE_FORMAT (m.faelligkeitsdatum, '%d.%m.%Y') AS bez_date, m.gedruckt, k.nachname, k.vorname FROM mahnungen m, kunden k WHERE m.kdnr = k.id ORDER BY m.id ASC";	
}

$select = mysql_query($query) or die ("MySQL select all demand notes error. ".mysql_error());

$row_count = mysql_num_rows($select);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Alle Mahnungen</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/js/scriptaculous.js"></script>
<script>

function setDemandPaid(acc_nr) {
		
		if (confirm("Wollen Sie diese Mahnung und die zugehörige Rechnung wirklich auf bezahlt setzen?"))
		{
	
			new Ajax.Updater("","edit_demand_note_server.php", {
		
			parameters : {
			
			'cmd'			: 'setDemandPaid',
			'acc_nr'		: acc_nr
			},
			evalScripts : true,
			encoding : 'ISO-8859-1',
			
			onFailure : function() {
				alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
			},
			
			onComplete : function() {
				alert ("Mahnung "+acc_nr+" wurde erfolgreich auf bezahlt gesetzt!");
				location.reload(true);
			}
		});
	}
}

function deleteDemandNote(acc_nr, stunden, artikel) {
		
		if (confirm("Wollen Sie diese Mahnung wirklich endgültig löschen?"))
		{
	
			new Ajax.Updater("","edit_demand_note_server.php", {
		
			parameters : {
			
			'cmd'			: 'deleteDemandNote',
			'acc_nr'		: acc_nr,
			'stunden'		: stunden,
			'artikel'		: artikel
			},
			evalScripts : true,
			encoding : 'ISO-8859-1',
			
			onFailure : function() {
				alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
			},
			
			onComplete : function() {
				alert ("Mahnung wurde erfolgreich gelöscht!");
				location.reload(true);
			}
		});
	}
}

function setPrintState(currentState,acc_nr) {
	if (currentState == 1)  {
		newState = 0;
	}
	else if (currentState == 0) {
		newState = 1;
	}
	else {
		alert("Es ist leider ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");	
	}
	
	if (newState == 1 || newState == 0) 
	{
		if (confirm("Wollen Sie den Druckstatus wirklich ändern?"))
		{
				new Ajax.Updater("","edit_demand_note_server.php", {
			
				parameters : {
				
					'cmd'			: 'changePrintState',
					'acc_nr'		: acc_nr,
					'newState'      : newState
				},
				evalScripts : true,
				encoding : 'ISO-8859-1',
				
				onFailure : function() {
					alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
				},
				
				onComplete : function() {
					if (newState == 1) {
						alert ("Mahnung wurde erfolgreich auf 'Gedruckt' gesetzt!");	
					}
					else if (newState == 0) {
						alert ("Mahnung wurde erfolgreich auf 'Nicht gedruckt' gesetzt!");
					}
					location.reload(true);
				}
			});
		}
	}
	else {
		alert("Es ist leider ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");	
	}
}

</script>
</head>

<body>

<h2>Alle Mahnungen</h2>

<div id="searchField">
	<form action="" method="get">
    	<label>Suche:</label>
        <input type="text" id="search" name="search" />
        <input type="submit" id="bt_search" name="bt_search" value="Mahnung suchen" />
    </form>
</div>

<hr />

<form id="account_form" action="" method="post">

<?php

if (mysql_num_rows($select) > 0)
{

?>

<?php

if (isset($_GET['start']))
{
	// setup previous and next variables
	$prev = $start - $per_page;
	$next = $start + $per_page;
	
	// show previous button
	if (!($start <= 0)) {
		echo "<div style='float: left;'>";
		echo "<a href='show_demand_notes.php?start=$prev'>Vorherige</a> ";
		echo "</div>";
	}
	
	// show page numbers
	
	// set variable for first page
	$i = 1;
	
	for ($x = 0; $x < $record_count; $x = $x + $per_page)
	{
		if ($start != $x) {
			echo "<div style='float: left;'>";
			echo " <a href='show_demand_notes.php?start=$x'>$i</a> ";
			echo "</div>";
		}
		else { 
			echo "<div style='float: left;'>";
			echo " <a href='show_demand_notes.php?start=$x'><b style='color: orange'>$i</b></a> ";
			echo "</div>";
		}
		$i++;
	}
	
	// show next button
	if (!($start >= $record_count - $per_page))	{
		echo "<div style='float: left;'>";
		echo "<a href='show_demand_notes.php?start=$next'>N&auml;chste</a>";
		echo "</div>";	
	}
	
	echo "<div align='right'>";
	echo "<p><b>Zeige Eintr&auml;ge $start bis $next</b></p>";	
	echo "</div>";
}
else {
	echo "<b>Zeige $row_count Eintr&auml;ge</b><br/>";	
}

?>

<hr/>

<table id="account_table" border="1">
	<thead>
		<th>ID</th>
        <th>Mahnungsnummer <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=mahnungsnr&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=mahnungsnr&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <th>Mahnungsdatum</th>
        <th>Kundennummer</th>
        <th>Vorname</th>
        <th>Nachname <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=nachname&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=nachname&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <th>Positionen Stunden</th>
        <th>Positionen Artikel</th>
        <th>Gedruckt</th>
        <th>Bezahlt</th>
        <th>F&auml;lligkeitsdatum</th>
        <th colspan="4">Optionen</th>
	</thead>
    
    	<?php
		
		$count = 0;
		
		while ($row = mysql_fetch_assoc($select))
		{
			$count++;
			$mahnung->setId($row['id']);
			$mahnung->setMahnungsNr($row['mahnungsnr']);
			$mahnung->setMahnungsDatum($row['rech_date']);
			$mahnung->setHatStunden($row['hat_stunden']);
			$mahnung->setHatArtikel($row['hat_artikel']);
			$mahnung->setKdNr($row['kdnr']);
			$kunde->setVorname($row['vorname']);
			$kunde->setNachname($row['nachname']);
			$mahnung->setBezahlt($row['bezahlt']);
			$mahnung->setFaelligkeitsDatum($row['bez_date']);
			$mahnung->setEditierbar($row['editierbar']);
			$mahnung->setGedruckt($row['gedruckt']);
			
			$queryStunden = mysql_query("SELECT COUNT(*) FROM m_stunden ms, mahnungen m WHERE ms.mahnungsnr = m.mahnungsnr AND m.mahnungsnr = '{$mahnung->getMahnungsNr()}' LIMIT 1");
			
			$queryArtikel = mysql_query("SELECT COUNT(*) FROM m_artikel ma, mahnungen m WHERE ma.mahnungsnr = m.mahnungsnr AND m.mahnungsnr = '{$mahnung->getMahnungsNr()}' LIMIT 1");
			
			while ($stundenResult = mysql_fetch_array($queryStunden))
			{
				$anzahl_stunden = $stundenResult[0];	
			}
			
			while ($artikelResult = mysql_fetch_array($queryArtikel))
			{
				$anzahl_artikel = $artikelResult[0];	
			}
			
			if (($count % 2) == 0)
			{
				echo "<tr style='background-color:#FFF1AA;'>";
			}
			else {
				echo "<tr style='background-color:#9AE9FA;'>";	
			}
			
			echo "
					<td align='center'>{$mahnung->getId()}</td>
					<td align='center'>{$mahnung->getMahnungsNr()}</td>
					<td align='center'>{$mahnung->getMahnungsDatum()}</td>
					<td align='center'>{$mahnung->getKdNr()}</td>
					<td>{$kunde->getVorname()}</td>
					<td>{$kunde->getNachname()}</td>
					<td align='center'>$anzahl_stunden</td>
					<td align='center'>$anzahl_artikel</td>	
					<td align='center'>";
					if ($mahnung->getGedruckt() == "1")
						echo "<a href='javascript:setPrintState({$mahnung->getGedruckt()},{$mahnung->getMahnungsNr()})' title='Mahnung auf \"Noch nicht gedruckt\" setzen'><img src='img/printer_ok.png' alt='gedruckt' /></a>";
					else
						echo "<a href='javascript:setPrintState({$mahnung->getGedruckt()},{$mahnung->getMahnungsNr()})' title='Mahnung auf \"Gedruckt\" setzen'><img src='img/printer_error.png' alt='nicht gedruckt' /></a>";
			echo "	</td>		
					<td align='center'>";
					if ($mahnung->getBezahlt() == "1")
						echo "<img src='img/is_admin.gif' alt='bezahlt' />";
			echo "	</td>
					<td align='center'>{$mahnung->getFaelligkeitsDatum()}</td>";
					if ($mahnung->getBezahlt() == "0")
					{			
			echo "	<td align='center'><a href='javascript:setDemandPaid({$mahnung->getMahnungsNr()})'><img src='img/error.gif' alt='set paid' title='Mahnung auf bezahlt setzen' /></a></td>";
					}
					else {
			echo "	<td align='center'><img src='img/totranslate_done_t.gif' alt='was paid' title='Mahnung wurde bezahlt' /></td>";
					}
			echo "	<td align='center'><a href='javascript:deleteDemandNote({$mahnung->getMahnungsNr()}, {$mahnung->getHatStunden()}, {$mahnung->getHatArtikel()})'><img src='img/cross.gif' alt='delete' title='Mahnung l&ouml;schen' /></a></td>";
			if (PDF_KEY != "" && PDF_KEY != NULL && $benutzer->getAdmin() == 1)
			{
			echo "	<td align='center'><a href='create_demand_note_pdf.php?id={$mahnung->getId()}&m=I&r={$mahnung->getMahnungsNr()}&k=".PDF_KEY."' target='_blank'><img src='img/acrobat.gif' alt='acrobat' title='PDF anzeigen' /></a></td>
				<td align='center'><a href='create_demand_note_pdf.php?id={$mahnung->getId()}&m=D&r={$mahnung->getMahnungsNr()}&f=open&k=".PDF_KEY."'><img src='img/database_save_on.gif' alt='acrobat' title='PDF auf Festplatte speichern' /></a></td>
				</tr>
			";
			}
			else {
				echo "<td align='center'><img src='img/acrobat_gray.gif' alt='no acrobat' title='nicht erlaubt' /></td>";
			}
		}
		
    	?>
    
</table>

<?php

}
else {
	echo "<b>Keine Datensätze gefunden!</b>";	
}

?>

</form>

<hr/>

<?php

if (isset($_GET['start']))
{
	// setup previous and next variables
	$prev = $start - $per_page;
	$next = $start + $per_page;
	
	// show previous button
	if (!($start <= 0)) {
		echo "<div style='float: left;'>";
		echo "<a href='show_demand_notes?start=$prev'>Vorherige</a> ";
		echo "</div>";
	}
	
	// show page numbers
	
	// set variable for first page
	$i = 1;
	
	for ($x = 0; $x < $record_count; $x = $x + $per_page)
	{
		if ($start != $x) {
			echo "<div style='float: left;'>";
			echo " <a href='show_demand_notes.php?start=$x'>$i</a> ";
			echo "</div>";
		}
		else { 
			echo "<div style='float: left;'>";
			echo " <a href='show_demand_notes.php?start=$x'><b style='color: orange'>$i</b></a> ";
			echo "</div>";
		}
		$i++;
	}
	
	// show next button
	if (!($start >= $record_count - $per_page))	{
		echo "<div style='float: left;'>";
		echo "<a href='show_demand_notes.php?start=$next'>N&auml;chste</a>";
		echo "</div>";	
	}
	
	echo "<div align='right'>";
	echo "<p><b>Zeige Eintr&auml;ge $start bis $next</b></p>";	
	echo "</div>";
}
else {
	echo "<b>Zeige $row_count Eintr&auml;ge</b><br/>";	
}

?>

<hr  />

<p>
<a href="main.php">Zur&uuml;ck zur Startseite</a>
</p>

</body>
</html>

<?php

include("tpl/footer.tpl.php");

?>