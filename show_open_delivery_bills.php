<?php

include('tpl/header.tpl.php');

$lieferschein = new Lieferschein();
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
	$query = "SELECT l.id, l.lieferscheinnr, DATE_FORMAT (l.lieferscheindatum, '%d.%m.%Y') AS rech_date, l.kdnr, l.bezahlt, l.editierbar, l.hat_stunden, l.hat_artikel, DATE_FORMAT (l.bezahlt_datum, '%d.%m.%Y') AS bez_date, l.gedruckt, k.nachname, k.vorname FROM liefererscheine l, kunden k WHERE (l.lieferscheinnr = '$searchTerm' OR k.nachname LIKE '%$searchTerm%') AND l.kdnr = k.id AND bezahlt='0' ORDER BY id ASC";
}
else if (isset($_GET['start'])) {
	// max displayed per page
	$per_page = 50;

	// get start variable
	$start = $_GET['start'];

	// count records
	$record_count = mysql_num_rows(mysql_query("SELECT l.id, l.lieferscheindatum, DATE_FORMAT (l.lieferscheindatum, '%d.%m.%Y') AS rech_date, l.kdnr, l.bezahlt, l.editierbar, l.hat_stunden, l.hat_artikel, DATE_FORMAT (l.bezahlt_datum, '%d.%m.%Y') AS bez_date, l.gedruckt, k.nachname, k.vorname FROM lieferscheine l, kunden k WHERE l.kdnr = k.id AND bezahlt = '0'"));
	
	// count max pages
	$max_pages = $record_count / $per_page; // may come out as decimal
	
	if (!$start)
		$start = 0;
		
	// display data
	$query = "SELECT l.id, l.lieferscheinnr, DATE_FORMAT (l.lieferscheindatum, '%d.%m.%Y') AS rech_date, l.kdnr, l.bezahlt, l.editierbar, l.hat_stunden, l.hat_artikel, DATE_FORMAT (l.bezahlt_datum, '%d.%m.%Y') AS bez_date, l.gedruckt, k.nachname, k.vorname FROM lieferscheine l, kunden k WHERE l.kdnr = k.id AND bezahlt = '0' ORDER BY id ASC LIMIT $start, $per_page";
}
else if ($orderBy != NULL && $sort != NULL) {
	$query = "SELECT l.id, l.lieferscheinnr, DATE_FORMAT (l.lieferscheindatum, '%d.%m.%Y') AS rech_date, l.kdnr, l.bezahlt, l.editierbar, l.hat_stunden, l.hat_artikel, DATE_FORMAT (l.bezahlt_datum, '%d.%m.%Y') AS bez_date, l.gedruckt, k.nachname, k.vorname FROM lieferscheine l, kunden k WHERE l.kdnr = k.id AND bezahlt = '0' ORDER BY $orderBy $sort";	
}
else {
	$query = "SELECT l.id, l.lieferscheinnr, DATE_FORMAT (l.lieferscheindatum, '%d.%m.%Y') AS rech_date, l.kdnr, l.bezahlt, l.editierbar, l.hat_stunden, l.hat_artikel, DATE_FORMAT (l.bezahlt_datum, '%d.%m.%Y') AS bez_date, l.gedruckt, k.nachname, k.vorname FROM lieferscheine l, kunden k WHERE l.kdnr = k.id AND bezahlt = '0' ORDER BY id ASC";
}


$select = mysql_query($query) or die ("MySQL select open delivery bills error. ".mysql_error());

$row_count = mysql_num_rows($select);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Erstellte Lieferscheine</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/js/scriptaculous.js"></script>
<script>

function deleteAccount(acc_nr, artikel) {
		
		if (confirm("Wollen Sie diesen Lieferschein wirklich endgültig löschen?"))
		{
	
			new Ajax.Updater("","edit_delivery_bill_server.php", {
		
			parameters : {
			
			'cmd'			: 'delete',
			'acc_nr'		: acc_nr,
			'artikel'		: artikel
			},
			evalScripts : true,
			encoding : 'ISO-8859-1',
			
			onFailure : function() {
				alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
			},
			
			onComplete : function() {
				alert ("Lieferschein "+acc_nr+" wurde erfolgreich gelöscht!");
				location.reload(true);
			}
		});
	}
}

function check_account_nr(bill_nr){
	
	id = prompt("Bitte geben Sie die neue Rechnungsnummer an!","");
	
	if (id != "")
	{	
		new Ajax.Request("edit_delivery_bill_server.php",
		{
			parameters: {
				'cmd'		     : 'check_account_nr',
				'rechnungsnr'	 : id
			},
			onSuccess : function(result){
				var response = result.responseText;
				
				if (response == "fehler") {
					alert("Rechnungsnummer bereits vergeben!");
				}
				else if (response == "erfolg"){
					createAccount(bill_nr, id);
				}	
			}
		});
	}	
	else {
		$('waiting').hide();
		alert("Sie haben keine oder ungültige Rechnungsdaten eingegeben!");
	}
}

function createAccount(bill_nr, id) {
			
	new Ajax.Updater("","edit_delivery_bill_server.php", {
		
		parameters : {
				
			'cmd'			: 'createAccount',
			'bill_nr'		: bill_nr,
			'id'		    : id
		},
		evalScripts : true,
		encoding : 'ISO-8859-1',
				
		onFailure : function() {
			alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
		},
				
		onComplete : function() {
			alert ("Rechnung Nr. "+id+" wurde erfolgreich angelegt!");
			//location.reload(true);
		}
	});
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
				new Ajax.Updater("","edit_delivery_bill_server.php", {
			
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
						alert ("Lieferschein wurde erfolgreich auf 'Gedruckt' gesetzt!");	
					}
					else if (newState == 0) {
						alert ("Lieferschein wurde erfolgreich auf 'Nicht gedruckt' gesetzt!");
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

<h2>Offene Lieferscheine</h2>

<div style="float:left;">
	<input type="button" name="home" value="Neuen Lieferschein anlegen" onClick="parent.location='new_delivery_bill.php'" />
</div>

<div id="searchField">
	<form action="" method="get">
    	<label>Suche:</label>
        <input type="text" id="search" name="search" />
        <input type="submit" id="bt_search" name="bt_search" value="Lieferschein suchen" />
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
		echo "<a href='show_open_delivery_bills.php?start=$prev'>Vorherige</a> ";
		echo "</div>";
	}
	
	// show page numbers
	
	// set variable for first page
	$i = 1;
	
	for ($x = 0; $x < $record_count; $x = $x + $per_page)
	{
		if ($start != $x) {
			echo "<div style='float: left;'>";
			echo " <a href='show_open_delivery_bills.php?start=$x'>$i</a> ";
			echo "</div>";
		}
		else { 
			echo "<div style='float: left;'>";
			echo " <a href='show_open_delivery_bills.php?start=$x'><b style='color: orange'>$i</b></a> ";
			echo "</div>";
		}
		$i++;
	}
	
	// show next button
	if (!($start >= $record_count - $per_page))	{
		echo "<div style='float: left;'>";
		echo "<a href='show_open_delivery_bills.php?start=$next'>N&auml;chste</a>";
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
        <th>Lieferscheinnummer <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=lieferscheinnr&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=lieferscheinnr&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <th>Lieferscheindatum</th>
        <th>Kundennummer</th>
        <th>Vorname</th>
        <th>Nachname <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=nachname&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=nachname&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <th>Positionen Artikel</th>
        <th>Gedruckt</th>
        <th colspan="5">Optionen</th>
	</thead>
    
    	<?php
		
		$count = 0;
		
		while ($row = mysql_fetch_assoc($select))
		{
			$count++;
			$lieferschein->setId($row['id']);
			$lieferschein->setLieferscheinNr($row['lieferscheinnr']);
			$lieferschein->setLieferscheinDatum($row['rech_date']);
			$lieferschein->setHatArtikel($row['hat_artikel']);
			$lieferschein->setKdNr($row['kdnr']);
			$kunde->setVorname($row['vorname']);
			$kunde->setNachname($row['nachname']);
			$lieferschein->setBezahlt($row['bezahlt']);
			$lieferschein->setBezahltDatum($row['bez_date']);
			$lieferschein->setEditierbar($row['editierbar']);
			$lieferschein->setGedruckt($row['gedruckt']);
			
			$queryArtikel = mysql_query("SELECT COUNT(*) FROM l_artikel la, lieferscheine l WHERE la.lieferscheinnr = l.lieferscheinnr AND l.lieferscheinnr = '{$lieferschein->getLieferscheinNr()}' LIMIT 1");
			
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
					<td align='center'>{$lieferschein->getId()}</td>
					<td align='center'>{$lieferschein->getLieferscheinNr()}</td>
					<td align='center'>{$lieferschein->getLieferscheinDatum()}</td>
					<td align='center'>{$lieferschein->getKdNr()}</td>
					<td>{$kunde->getVorname()}</td>
					<td>{$kunde->getNachname()}</td>
					<td align='center'>$anzahl_artikel</td>
					<td align='center'>";
					if ($lieferschein->getGedruckt() == "1")
						echo "<a href='javascript:setPrintState({$lieferschein->getGedruckt()},{$lieferschein->getLieferscheinNr()})' title='Lieferschein auf \"Noch nicht gedruckt\" setzen'><img src='img/printer_ok.png' alt='gedruckt' /></a>";
					else
						echo "<a href='javascript:setPrintState({$lieferschein->getGedruckt()},{$lieferschein->getLieferscheinNr()})' title='Lieferschein auf \"Gedruckt\" setzen'><img src='img/printer_error.png' alt='nicht gedruckt' /></a>";
			echo "	</td>";
					if ($lieferschein->getEditierbar() == "1")
					{			
			echo "	<td align='center'><a href='edit_delivery_bill.php?action=edit&acc_nr={$lieferschein->getLieferscheinNr()}'><img src='img/edit.gif' alt='edit' title='Lieferschein bearbeiten' /></a></td>";
					}
					else {
			echo "	<td align='center'><img src='img/no_edit.gif' alt='no_edit' title='Nicht editierbar' /></td>";
					};
			echo "	<td align='center'><a href='javascript:check_account_nr({$lieferschein->getLieferscheinNr()})'><img src='img/page_white_go.gif' alt='create demand note' title='Rechnung zum Lieferschein anlegen' /></a></td>";	
			echo "	<td align='center'><a href='javascript:deleteAccount({$lieferschein->getLieferscheinNr()}, {$lieferschein->getHatArtikel()})'><img src='img/cross.gif' alt='delete' title='Lieferschein l&ouml;schen' /></a></td>";
			if (PDF_KEY != "" && PDF_KEY != NULL && $benutzer->getAdmin() == 1)
			{
			echo "	<td align='center'><a href='create_delivery_bill_pdf.php?id={$lieferschein->getId()}&m=I&r={$lieferschein->getLieferscheinNr()}&k=".PDF_KEY."' target='_blank'><img src='img/acrobat.gif' alt='acrobat' title='PDF anzeigen' /></a></td>
				<td align='center'><a href='create_account_pdf.php?id={$lieferschein->getId()}&m=D&r={$lieferschein->getLieferscheinNr()}&f=open&k=".PDF_KEY."'><img src='img/database_save_on.gif' alt='acrobat' title='PDF auf Festplatte speichern' /></a></td>
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
		echo "<a href='show_open_delivery_bills.php?start=$prev'>Vorherige</a> ";
		echo "</div>";
	}
	
	// show page numbers
	
	// set variable for first page
	$i = 1;
	
	for ($x = 0; $x < $record_count; $x = $x + $per_page)
	{
		if ($start != $x) {
			echo "<div style='float: left;'>";
			echo " <a href='show_open_delivery_bills.php?start=$x'>$i</a> ";
			echo "</div>";
		}
		else { 
			echo "<div style='float: left;'>";
			echo " <a href='show_open_delivery_bills.php?start=$x'><b style='color: orange'>$i</b></a> ";
			echo "</div>";
		}
		$i++;
	}
	
	// show next button
	if (!($start >= $record_count - $per_page))	{
		echo "<div style='float: left;'>";
		echo "<a href='show_open_delivery_bills.php?start=$next'>N&auml;chste</a>";
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