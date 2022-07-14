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
	$query = "SELECT r.id, r.rechnungsnr, DATE_FORMAT (r.rechnungsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, r.gedruckt, k.nachname, k.vorname FROM rechnungen r, kunden k WHERE (r.rechnungsnr = '$searchTerm' OR k.nachname LIKE '%$searchTerm%') AND r.kdnr = k.id AND r.bezahlt = '0' AND r.im_papierkorb = 'n' ORDER BY id ASC";	  
}
else if (isset($_GET['start'])) {
	// max displayed per page
	$per_page = 50;

	// get start variable
	$start = $_GET['start'];

	// count records
	$record_count = mysql_num_rows(mysql_query("SELECT r.id, r.rechnungsnr, DATE_FORMAT (r.rechnungsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, r.gedruckt, k.nachname, k.vorname FROM rechnungen r, kunden k WHERE r.kdnr = k.id AND r.bezahlt = '0' AND r.im_papierkorb = 'n'"));
	
	// count max pages
	$max_pages = $record_count / $per_page; // may come out as decimal
	
	if (!$start)
		$start = 0;
		
	// display data
	$query = "SELECT r.id, r.rechnungsnr, DATE_FORMAT (r.rechnungsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, r.gedruckt, k.nachname, k.vorname FROM rechnungen r, kunden k WHERE r.kdnr = k.id AND r.bezahlt = '0' AND r.im_papierkorb = 'n' ORDER BY id ASC LIMIT $start, $per_page";
}
else if ($orderBy != NULL && $sort != NULL) {
	$query = "SELECT r.id, r.rechnungsnr, DATE_FORMAT (r.rechnungsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, r.gedruckt, k.nachname, k.vorname FROM rechnungen r, kunden k WHERE r.kdnr = k.id AND r.bezahlt = '0' AND r.im_papierkorb = 'n' ORDER BY $orderBy $sort";	
}
else {
	$query = "SELECT r.id, r.rechnungsnr, DATE_FORMAT (r.rechnungsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, r.gedruckt, k.nachname, k.vorname FROM rechnungen r, kunden k WHERE r.kdnr = k.id AND r.bezahlt = '0' AND r.im_papierkorb = 'n' ORDER BY id ASC";	
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

function createDemandNote(acc_nr) {
	var due_date = prompt("Bitte geben Sie das Fälligkeitsdatum an!","");
	
	if (due_date != "") {
		new Ajax.Updater("","edit_account_server.php", {
			
			parameters : {
				'cmd'		: 'createDemandNote',
				'acc_nr'	: acc_nr,
				'due_date'  : due_date	
			},
			evalScripts : true,
			encoding : 'ISO-8859-1',
			
			onFailure : function() {
				alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
			},
			
			onComplete : function() {
				alert ("Mahnung "+acc_nr+" wurde erfolgreich angelegt!");
				location.reload(true);
			}
		});
	}	
}

function toTrashcan(acc_nr) {
	
		if (confirm("Wollen Sie diese Rechnung wirklich in den Papierkorb verschieben?"))
		{
	
			new Ajax.Updater("","edit_account_server.php", {
		
			parameters : {
			
			'cmd'			: 'toTrashcan',
			'acc_nr'		: acc_nr
			},
			evalScripts : true,
			encoding : 'ISO-8859-1',
			
			onFailure : function() {
				alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
			},
			
			onComplete : function() {
				alert ("Rechnung "+acc_nr+" wurde erfolgreich in den Papierkorb verschoben!");
				location.reload(true);
			}
		});
	}	
}

function deleteAccount(acc_nr, stunden, artikel) {
		
		if (confirm("Wollen Sie diesen Artikel wirklich endgültig löschen?"))
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
				alert ("Rechnung wurde erfolgreich gelöscht!");
				location.reload(true);
			}
		});
	}
}

function setPaidState(currentState,acc_nr) {
	if (currentState == 1)  {
		newState = 0;
		Datum = "Noch nicht bezahlt";
	}
	else if (currentState == 0) {
		newState = 1;

  		var date=new Date(); 
  		var dd=date.getDate(); 
  		var mm=date.getMonth() + 1; 
  		var yy=date.getYear(); 
		
		if (mm < 10)
			mm = "0"+mm;
		
		// Probleme mit 2-stelligen Jahreszahlen in einigen Browsern umgehen
        if ((yy > 99) && (yy < 1900)) yy += 1900;

  		Datum = yy+"-"+mm+"-"+dd;

	}
	else {
		alert("Es ist leider ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");	
	}
	
	if (newState == 1 || newState == 0) 
	{
		if (confirm("Wollen Sie den Zahlungsstatus wirklich ändern?"))
		{
				new Ajax.Updater("","edit_account_server.php", {
			
				parameters : {
				
					'cmd'			: 'changePaidState',
					'acc_nr'		: acc_nr,
					'newState'      : newState,
					'datum'			: Datum
				},
				evalScripts : true,
				encoding : 'ISO-8859-1',
				
				onFailure : function() {
					alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
				},
				
				onComplete : function() {
					if (newState == 1) {
						alert ("Rechnung wurde erfolgreich auf 'Bezahlt' gesetzt!");	
					}
					else if (newState == 0) {
						alert ("Rechnung wurde erfolgreich auf 'Nicht bezahlt' gesetzt!");
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
				new Ajax.Updater("","edit_account_server.php", {
			
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
						alert ("Rechnung wurde erfolgreich auf 'Gedruckt' gesetzt!");	
					}
					else if (newState == 0) {
						alert ("Rechnung wurde erfolgreich auf 'Nicht gedruckt' gesetzt!");
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

<h2>Offene Rechnungen</h2>

<div style="float:left; margin-right: 40px;">
	<input type="button" name="home" value="Neue Rechnung anlegen" onClick="parent.location='new_account.php'" />
</div>

<div style="float:left; margin-right: 40px;">
	<input type="button" name="trashcan" value="Papierkorb" onClick="parent.location='trashcan_account.php'" />
</div>

<div id="searchField">
	<form action="" method="get">
    	<label>Suche:</label>
        <input type="text" id="search" name="search" />
        <input type="submit" id="bt_search" name="bt_search" value="Rechnung suchen" />
    </form>
</div>

<div style="clear:both;"></div>

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
        <th colspan="5">Optionen</th>
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
			echo "	</td>";
					if ($rechnung->getEditierbar() == "1")
					{			
			echo "	<td align='center'><a href='edit_account.php?action=edit&acc_nr={$rechnung->getRechnungsNr()}'><img src='img/edit.gif' alt='edit' title='Rechnung bearbeiten' /></a></td>";
					}
					else {
			echo "	<td align='center'><img src='img/no_edit.gif' alt='no_edit' title='Nicht editierbar' /></td>";
					}
			echo "	<td align='center'><a href='javascript:createDemandNote({$rechnung->getRechnungsNr()})'><img src='img/page_white_go.gif' alt='create demand note' title='Mahnung zur Rechnung anlegen' /></a></td>
					<td align='center'><a href='javascript:toTrashcan({$rechnung->getRechnungsNr()})'><img src='img/attach_delete.gif' alt='trashcan' title='Rechnung in den Papierkorb verschieben' /></a></td>";
			if (PDF_KEY != "" && PDF_KEY != NULL && $benutzer->getAdmin() == 1)
			{
			echo "	<td align='center'><a href='create_account_pdf.php?id={$rechnung->getId()}&m=I&r={$rechnung->getRechnungsNr()}&k=".PDF_KEY."' target='_blank'><img src='img/acrobat.gif' alt='acrobat' title='PDF anzeigen' /></a></td>
				<td align='center'><a href='create_account_pdf.php?id={$rechnung->getId()}&m=D&r={$rechnung->getRechnungsNr()}&f=open&k=".PDF_KEY."'><img src='img/database_save_on.gif' alt='acrobat' title='PDF auf Festplatte speichern' /></a></td>
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