<?php

include('tpl/header.tpl.php');

$rechnung = new Rechnung();
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
	$query = "SELECT r.id, r.rechnungsnr, DATE_FORMAT (r.rechnungsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, r.gedruckt, k.nachname, k.vorname FROM rechnungen r, kunden k WHERE (r.rechnungsnr = '$searchTerm' OR k.nachname LIKE '%$searchTerm%') AND r.kdnr = k.id AND r.im_papierkorb = 'y' ORDER BY id ASC";	  
}
else if (isset($_GET['start'])) {
	// max displayed per page
	$per_page = 50;

	// get start variable
	$start = $_GET['start'];

	// count records
	$record_count = mysql_num_rows(mysql_query("SELECT r.id, r.rechnungsnr, DATE_FORMAT (r.rechnungsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, r.gedruckt, k.nachname, k.vorname FROM rechnungen r, kunden k WHERE r.kdnr = k.id AND r.im_papierkorb = 'y'"));
	
	// count max pages
	$max_pages = $record_count / $per_page; // may come out as decimal
	
	if (!$start)
		$start = 0;
		
	// display data
	$query = "SELECT r.id, r.rechnungsnr, DATE_FORMAT (r.rechnungsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, r.gedruckt, k.nachname, k.vorname FROM rechnungen r, kunden k WHERE r.kdnr = k.id AND r.im_papierkorb = 'y' ORDER BY id ASC LIMIT $start, $per_page";
}
else if ($orderBy != NULL && $sort != NULL) {
	$query = "SELECT r.id, r.rechnungsnr, DATE_FORMAT (r.rechnungsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, r.gedruckt, k.nachname, k.vorname FROM rechnungen r, kunden k WHERE r.kdnr = k.id AND r.im_papierkorb = 'y' ORDER BY $orderBy $sort";	
}
else {
	$query = "SELECT r.id, r.rechnungsnr, DATE_FORMAT (r.rechnungsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, r.gedruckt, k.nachname, k.vorname FROM rechnungen r, kunden k WHERE r.kdnr = k.id AND r.im_papierkorb = 'y' ORDER BY id ASC";	
}

$select = mysql_query($query) or die ("MySQL select all accounts error. ".mysql_error());

$row_count = mysql_num_rows($select);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Alle Rechnungen</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/js/scriptaculous.js"></script>
<script>

function restoreAccount(acc_nr) {
	
		if (confirm("Wollen Sie diese Rechnung wirklich wiederherstellen?"))
		{
	
			new Ajax.Updater("","edit_account_server.php", {
		
			parameters : {
			
			'cmd'			: 'restoreAccount',
			'acc_nr'		: acc_nr
			},
			evalScripts : true,
			encoding : 'ISO-8859-1',
			
			onFailure : function() {
				alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
			},
			
			onComplete : function() {
				alert ("Rechnung "+acc_nr+" wurde erfolgreich wiederhergestellt!");
				location.reload(true);
			}
		});
	}	
}

function deleteAccount(acc_nr, stunden, artikel) {
		
		if (confirm("Wollen Sie diese Rechnung wirklich endgültig löschen?"))
		{
	
			new Ajax.Updater("","edit_account_server.php", {
		
			parameters : {
			
			'cmd'			: 'delete',
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
				alert ("Rechnung "+acc_nr+" wurde erfolgreich gelöscht!");
				location.reload(true);
			}
		});
	}
}

function clearTrashcan() {
		
		if (confirm("Wollen Sie alle Rechnungen im Papierkorb wirklich endgültig löschen?"))
		{
	
			new Ajax.Updater("","edit_account_server.php", {
		
			parameters : {
			
			'cmd'			: 'clearTrashcan'
			},
			evalScripts : true,
			encoding : 'ISO-8859-1',
			
			onFailure : function() {
				alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
			},
			
			onComplete : function() {
				alert ("Der Papierkorb wurde erfolgreich geleert!");
				location.reload(true);
			}
		});
	}
}

function setPaidState(currentState,acc_nr) {
	alert("Innerhalb des Papierkorbs leider nicht möglich!");
}

function setPrintState(currentState,acc_nr) {
	alert("Innerhalb des Papierkorbs leider nicht möglich!");
}

</script>
</head>

<body>

<h2>Rechnungen im Papierkorb</h2>

<div id="searchField_trash">
	<form action="" method="get">
    	<label>Suche:</label>
        <input type="text" id="search" name="search" />
        <input type="submit" id="bt_search" name="bt_search" value="Rechnung suchen" />
    </form>
</div>

<div id="clear_trashcan">
	<?php
	echo "
		<input style='color: red' type='button' name='bt_clear_trashcan' value='Papierkorb leeren' onclick='javascript:clearTrashcan()' />
	";
	?>
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
		echo "<a href='show_all_accounts.php?start=$prev'>Vorherige</a> ";
		echo "</div>";
	}
	
	// show page numbers
	
	// set variable for first page
	$i = 1;
	
	for ($x = 0; $x < $record_count; $x = $x + $per_page)
	{
		if ($start != $x) {
			echo "<div style='float: left;'>";
			echo " <a href='show_all_accounts.php?start=$x'>$i</a> ";
			echo "</div>";
		}
		else { 
			echo "<div style='float: left;'>";
			echo " <a href='show_all_accounts.php?start=$x'><b style='color: orange'>$i</b></a> ";
			echo "</div>";
		}
		$i++;
	}
	
	// show next button
	if (!($start >= $record_count - $per_page))	{
		echo "<div style='float: left;'>";
		echo "<a href='show_all_accounts.php?start=$next'>N&auml;chste</a>";
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
        <th>Rechnungsnummer <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=rechnungsnr&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=rechnungsnr&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <th>Rechnungsdatum</th>
        <th>Kundennummer</th>
        <th>Vorname</th>
        <th>Nachname <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=nachname&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=nachname&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <th>Positionen Stunden</th>
        <th>Positionen Artikel</th>
        <th>Gedruckt</th>
        <th>Bezahlt</th>
        <th>Datum der Zahlung</th>
        <th colspan="4">Optionen</th>
	</thead>
    
    	<?php
		
		$count = 0;
		
		while ($row = mysql_fetch_assoc($select))
		{
			$count++;
			$rechnung->setId($row['id']);
			$rechnung->setRechnungsNr($row['rechnungsnr']);
			$rechnung->setRechnungsDatum($row['rech_date']);
			$rechnung->setHatStunden($row['hat_stunden']);
			$rechnung->setHatArtikel($row['hat_artikel']);
			$rechnung->setKdNr($row['kdnr']);
			$kunde->setVorname($row['vorname']);
			$kunde->setNachname($row['nachname']);
			$rechnung->setBezahlt($row['bezahlt']);
			$rechnung->setBezahltDatum($row['bez_date']);
			$rechnung->setEditierbar($row['editierbar']);
			$rechnung->setGedruckt($row['gedruckt']);
			
			$queryStunden = mysql_query("SELECT COUNT(*) FROM r_stunden rs, rechnungen r WHERE rs.rechnungsnr = r.rechnungsnr AND r.rechnungsnr = '{$rechnung->getRechnungsNr()}' LIMIT 1");
			
			$queryArtikel = mysql_query("SELECT COUNT(*) FROM r_artikel ra, rechnungen r WHERE ra.rechnungsnr = r.rechnungsnr AND r.rechnungsnr = '{$rechnung->getRechnungsNr()}' LIMIT 1");
			
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
					<td align='center'>{$rechnung->getId()}</td>
					<td align='center'>{$rechnung->getRechnungsNr()}</td>
					<td align='center'>{$rechnung->getRechnungsDatum()}</td>
					<td align='center'>{$rechnung->getKdNr()}</td>
					<td>{$kunde->getVorname()}</td>
					<td>{$kunde->getNachname()}</td>
					<td align='center'>$anzahl_stunden</td>
					<td align='center'>$anzahl_artikel</td>	
					<td align='center'>";
					if ($rechnung->getGedruckt() == "1")
						echo "<a href='javascript:setPrintState({$rechnung->getGedruckt()},{$rechnung->getRechnungsNr()})' title='Rechnung auf \"Noch nicht gedruckt\" setzen'><img src='img/printer_ok.png' alt='gedruckt' /></a>";
					else
						echo "<a href='javascript:setPrintState({$rechnung->getGedruckt()},{$rechnung->getRechnungsNr()})' title='Rechnung auf \"Gedruckt\" setzen'><img src='img/printer_error.png' alt='nicht gedruckt' /></a>";
			echo "	</td>
				 	<td align='center'>";
					if ($rechnung->getBezahlt() == "1")
						echo "<a href='javascript:setPaidState({$rechnung->getBezahlt()},{$rechnung->getRechnungsNr()})' title='Rechnung auf \"Noch nicht bezahlt\" setzen'><img src='img/is_admin.gif' alt='bezahlt' /></a>";
					else
						echo "<a href='javascript:setPaidState({$rechnung->getBezahlt()},{$rechnung->getRechnungsNr()})' title='Rechnung auf \"Bezahlt\" setzen'><img src='img/exclamation_red.gif' alt='nicht bezahlt' /></a>";
			echo "	</td>
					<td align='center'>";
					if ($rechnung->getBezahlt() == "1" && $rechnung->getBezahltDatum() != "0000-00-00 00:00:00")
						echo $rechnung->getBezahltDatum();
					else 
						echo "Noch nicht bezahlt";
			echo "	</td>
					<td align='center'><a href='javascript:restoreAccount({$rechnung->getRechnungsNr()})'><img src='img/undo.png' alt='undo' title='Rechnung wiederherstellen' width='16' height='16' /></a></td>
					<td align='center'><a href='javascript:deleteAccount({$rechnung->getRechnungsNr()}, {$rechnung->getHatStunden()}, {$rechnung->getHatArtikel()})'><img src='img/cross.gif' alt='delete' title='Rechnung l&ouml;schen' /></a></td>
			";
			echo "</tr>";
		}
		
    	?>
    
</table>

<?php

}
else {
	echo "<b>Der Papierkorb ist leer!</b>";	
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
		echo "<a href='show_all_accounts.php?start=$prev'>Vorherige</a> ";
		echo "</div>";
	}
	
	// show page numbers
	
	// set variable for first page
	$i = 1;
	
	for ($x = 0; $x < $record_count; $x = $x + $per_page)
	{
		if ($start != $x) {
			echo "<div style='float: left;'>";
			echo " <a href='show_all_accounts.php?start=$x'>$i</a> ";
			echo "</div>";
		}
		else { 
			echo "<div style='float: left;'>";
			echo " <a href='show_all_accounts.php?start=$x'><b style='color: orange'>$i</b></a> ";
			echo "</div>";
		}
		$i++;
	}
	
	// show next button
	if (!($start >= $record_count - $per_page))	{
		echo "<div style='float: left;'>";
		echo "<a href='show_all_accounts.php?start=$next'>N&auml;chste</a>";
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