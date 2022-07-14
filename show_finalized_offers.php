<?php

include('tpl/header.tpl.php');

$angebot = new Angebot();
$kunde = new Kunde();
$benutzer = new Benutzer();

$get_rights = mysql_query("SELECT ist_admin FROM benutzer WHERE benutzername = '".$_SESSION['benutzername']."'") or die ("MySQL get rights error. ".mysql_error());

while ($row_get_rights = mysql_fetch_assoc($get_rights))
{
	$benutzer->setAdmin($row_get_rights['ist_admin']);	
}

$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : NULL;
$sort = isset($_GET['sort']) ? $_GET['sort'] : NULL;

$selected_month = isset($_GET['month']) ? $_GET['month'] : "";
$selected_year = isset($_GET['year']) ? $_GET['year'] : "";

$umsatz = 0;
$rabatt_gesamt = 0;
$skonto_gesamt = 0;

$month = array(
	"1" => "Januar",
	"2" => "Februar",
	"3" => "M&auml;rz",
	"4" => "April",
	"5" => "Mai",
	"6" => "Juni",
	"7" => "Juli",
	"8" => "August",
	"9" => "September",
	"10" => "Oktober",
	"11" => "November",
	"12" => "Dezember"
);

$year = array("2011","2012","2013","2014","2015","2016","2017","2018","2019","2020");

if (isset($_GET['bt_search']))
{
	$searchTerm = $_GET['search'];
	$query = "SELECT r.id, r.angebotsnr, DATE_FORMAT (r.angebotsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, r.rabatt_betrag, r.skonto_betrag, r.endbetrag, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, k.nachname, k.vorname FROM angebote r, kunden k WHERE (r.angebotsnr = '$searchTerm' OR k.nachname LIKE '%$searchTerm%') AND r.kdnr = k.id AND r.editierbar = '0' ORDER BY id ASC";
}
else if (isset($selected_month) && isset($selected_year) && isset($_GET['filter']) && $selected_month != "" && $selected_year != ""){
	$query = "SELECT r.id, r.angebotsnr, DATE_FORMAT (r.angebotsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, r.rabatt_betrag, r.skonto_betrag, r.endbetrag, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, k.nachname, k.vorname FROM angebote r, kunden k WHERE r.kdnr = k.id AND r.editierbar = '0' AND MONTH(r.angebotsdatum) = '$selected_month' AND YEAR(r.angebotsdatum) = '$selected_year' ORDER BY r.angebotsdatum ASC";	
}
else if (isset($selected_year) && isset($_GET['filter']) && $selected_month == "" && $selected_year != ""){
	$query = "SELECT r.id, r.angebotsnr, DATE_FORMAT (r.angebotsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, r.rabatt_betrag, r.skonto_betrag, r.endbetrag, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, k.nachname, k.vorname FROM angebote r, kunden k WHERE r.kdnr = k.id AND r.editierbar = '0' AND YEAR(r.angebotsdatum) = '$selected_year' ORDER BY r.angebotsdatum ASC";	
}
else if (isset($_GET['start'])) {
	// max displayed per page
	$per_page = 50;

	// get start variable
	$start = $_GET['start'];

	// count records
	$record_count = mysql_num_rows(mysql_query("SELECT r.id, r.angebotsnr, DATE_FORMAT (r.angebotsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, r.rabatt_betrag, r.skonto_betrag, r.endbetrag, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, k.nachname, k.vorname FROM angebote r, kunden k WHERE r.kdnr = k.id AND r.editierbar = '0'"));
	
	// count max pages
	$max_pages = $record_count / $per_page; // may come out as decimal
	
	if (!$start)
		$start = 0;
		
	// display data
	$query = "SELECT r.id, r.angebotsnr, DATE_FORMAT (r.angebotsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, r.rabatt_betrag, r.skonto_betrag, r.endbetrag, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, k.nachname, k.vorname FROM angebote r, kunden k WHERE r.kdnr = k.id AND r.editierbar = '0' ORDER BY id ASC LIMIT $start, $per_page";
}
else if ($orderBy != NULL && $sort != NULL) {
	$query = "SELECT r.id, r.angebotsnr, DATE_FORMAT (r.angebotsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, r.rabatt_betrag, r.skonto_betrag, r.endbetrag, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, k.nachname, k.vorname FROM angebote r, kunden k WHERE r.kdnr = k.id AND r.editierbar = '0' ORDER BY $orderBy $sort";	
}
else {
	$query = "SELECT r.id, r.angebotsnr, DATE_FORMAT (r.angebotsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.bezahlt, r.editierbar, r.hat_stunden, r.hat_artikel, r.rabatt_betrag, r.skonto_betrag, r.endbetrag, DATE_FORMAT (r.bezahlt_datum, '%d.%m.%Y') AS bez_date, k.nachname, k.vorname FROM angebote r, kunden k WHERE r.kdnr = k.id AND r.editierbar = '0' ORDER BY id ASC";
}


$select = mysql_query($query) or die ("MySQL select all offers error. ".mysql_error());

$row_count = mysql_num_rows($select);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Fertige Angebote</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/js/scriptaculous.js"></script>
<script>

function deleteOffer(acc_nr, stunden, artikel) {
		
		if (confirm("Wollen Sie dieses Angebot wirklich endgültig löschen?"))
		{
	
			new Ajax.Updater("","edit_offer_server.php", {
		
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
				alert ("Angebot "+acc_nr+" wurde erfolgreich gelöscht!");
				location.reload(true);
			}
		});
	}
}

</script>
</head>

<body>

<h2>Fertige Angebote</h2>

<div style="float:left;">
	<input type="button" name="home" value="Neues Angebot anlegen" onClick="parent.location='new_offer.php'" />
</div>

<div id="searchField">
	<form action="" method="get">
    	<label>Suche:</label>
        <input type="text" id="search" name="search" />
        <input type="submit" id="bt_search" name="bt_search" value="Angebot suchen" />
    </form>
</div>

<div style="clear:both;"></div>

<hr />

<form id="filter_form" action="" method="get">

<?php

if (isset($_GET['filter']) && isset($selected_month) && isset($selected_year) && $selected_month != "" && $selected_year != "")
	echo "<p style='color:orange'>Filter: $selected_month - $selected_year</p>";
else if (isset($_GET['filter']) && ($selected_month == "" && $selected_year != ""))
	echo "<p style='color:orange'>Filter: $selected_year</p>";
else if (isset($_GET['filter']) && ($selected_month != "" && $selected_year == ""))
	echo "<p style='color:red'>Zum Filtern bitte Jahr (und optional Monat) ausw&auml;hlen!</p>";
else if (isset($_GET['filter']) && ($selected_month == "" && $selected_year == ""))
	echo "<p style='color:red'>Zum Filtern bitte Jahr (und optional Monat) ausw&auml;hlen!</p>";

echo '<select id="year" name="year">';
echo '<option value=""></option>';
foreach ($year as $y) {
	//if ($y >= date("Y"))
		echo '<option value="'.$y.'">'.$y.'</option>';  	
}
echo '</select>';

echo '<select style="margin-left: 10px;" id="month" name="month">';
echo '<option value=""></option>';
foreach ($month as $m => $key) {
	echo '<option value="'.$m.'">'.$key.'</option>';
}
echo '</select>';

?>

<input style="margin-left:20px;" type="submit" id="filter" name="filter" value="Zeitraum filtern" />
<input style="margin-left:20px;" type="button" name="reset_filter" value="Filter zur&uuml;cksetzen" onClick="parent.location='show_finalized_offers.php'"  />

</form>

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
		echo "<a href='show_finalized_offers.php?start=$prev'>Vorherige</a> ";
		echo "</div>";
	}
	
	// show page numbers
	
	// set variable for first page
	$i = 1;
	
	for ($x = 0; $x < $record_count; $x = $x + $per_page)
	{
		if ($start != $x) {
			echo "<div style='float: left;'>";
			echo " <a href='show_finalized_offers.php?start=$x'>$i</a> ";
			echo "</div>";
		}
		else { 
			echo "<div style='float: left;'>";
			echo " <a href='show_finalized_offers.php?start=$x'><b style='color: orange'>$i</b></a> ";
			echo "</div>";
		}
		$i++;
	}
	
	// show next button
	if (!($start >= $record_count - $per_page))	{
		echo "<div style='float: left;'>";
		echo "<a href='show_finalized_offers.php?start=$next'>N&auml;chste</a>";
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
        <th>Angebotsnummer <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=angebotsnr&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=angebotsnr&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <th>Angebotsdatum</th>
        <th>Kundennummer</th>
        <th>Vorname</th>
        <th>Nachname</th>
        <th>Angebotsbetrag</th>
        <th>Rabatt</th>
        <th>Skonto</th>
        <th colspan="4">Optionen</th>
	</thead>
    
    	<?php
		
		$count = 0;
		
		while ($row = mysql_fetch_assoc($select))
		{
			$count++;
			
			$angebot->setId($row['id']);
			$angebot->setAngebotsNr($row['angebotsnr']);
			$angebot->setAngebotsDatum($row['rech_date']);
			$angebot->setHatStunden($row['hat_stunden']);
			$angebot->setHatArtikel($row['hat_artikel']);
			$angebot->setEndBetrag($row['endbetrag']);
			$angebot->setRabattBetrag($row['rabatt_betrag']);
			$angebot->setSkontoBetrag($row['skonto_betrag']);
			$angebot->setKdNr($row['kdnr']);
			$kunde->setVorname($row['vorname']);
			$kunde->setNachname($row['nachname']);
			$angebot->setBezahlt($row['bezahlt']);
			$angebot->setBezahltDatum($row['bez_date']);
			$angebot->setEditierbar($row['editierbar']);
			
			$rabatt_gesamt += $angebot->getRabattBetrag();
			$skonto_gesamt += $angebot->getSkontoBetrag();
			$rabatt_gesamt = sprintf("%.2f",$rabatt_gesamt);
			//$rabatt_gesamt = str_replace(".",",",$rabatt_gesamt);
			$angebot->setRabattBetrag(sprintf("%.2f",$angebot->getRabattBetrag()));
			//$rabatt_betrag = str_replace(".",",",$rabatt_betrag);
			$angebot->setSkontoBetrag(sprintf("%.2f",$angebot->getSkontoBetrag()));
			//$skonto_betrag = str_replace(".",",",$skonto_betrag);
			$skonto_gesamt = sprintf("%.2f",$skonto_gesamt);
			//$skonto_gesamt = str_replace(".",",",$skonto_gesamt);
			$umsatz +=  $angebot->getEndBetrag();
			$umsatz = sprintf("%.2f",$umsatz);
			//$umsatz = str_replace(".",",",$umsatz);
			$angebot->setEndBetrag(sprintf("%.2f",$angebot->getEndBetrag()));
			//$zahlungsbetrag = str_replace(".",",",$zahlungsbetrag);
			
			$queryStunden = mysql_query("SELECT COUNT(*) FROM a_stunden rs, angebote r WHERE rs.angebotsnr = r.angebotsnr AND r.angebotsnr = '{$angebot->getAngebotsNr()}' LIMIT 1");
			
			$queryArtikel = mysql_query("SELECT COUNT(*) FROM a_artikel ra, angebote r WHERE ra.angebotsnr = r.angebotsnr AND r.angebotsnr = '{$angebot->getAngebotsNr()}' LIMIT 1");
			
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
					<td align='center'>{$angebot->getId()}</td>
					<td align='center'>{$angebot->getAngebotsNr()}</td>
					<td align='center'>{$angebot->getAngebotsDatum()}</td>
					<td align='center'>{$angebot->getKdNr()}</td>
					<td>{$kunde->getVorname()}</td>
					<td>{$kunde->getNachname()}</td>
					<td align='center'>{$angebot->getEndBetrag()}</td>
					<td align='center'>{$angebot->getRabattBetrag()}</td>
					<td align='center'>{$angebot->getSkontoBetrag()}</td>";
					if ($angebot->getEditierbar() == 1)
					{			
			echo "	<td align='center'><a href='edit_offer.php?action=edit&acc_nr={$angebot->getAngebotsNr()}'><img src='img/edit.gif' alt='edit' title='Angebot bearbeiten' /></a></td>";
					}
					else {
			echo "	<td align='center'><img src='img/no_edit.gif' alt='no_edit' title='Nicht editierbar' /></td>";
					}
			echo "	<td align='center'><a href='javascript:deleteOffer({$angebot->getAngebotsNr()}, {$angebot->getHatStunden()}, {$angebot->getHatArtikel()})'><img src='img/cross.gif' alt='delete' title='Angebot l&ouml;schen' /></a></td>";
			if (PDF_KEY != "" && PDF_KEY != NULL && $benutzer->getAdmin() == 1)
			{
			echo "	<td align='center'><a href='create_offer_pdf.php?id={$angebot->getId()}&m=I&r={$angebot->getAngebotsNr()}&k=".PDF_KEY."' target='_blank'><img src='img/acrobat.gif' alt='acrobat' title='PDF anzeigen' /></a></td>
				<td align='center'><a href='create_offer_pdf.php?id={$angebot->getId()}&m=D&r={$angebot->getAngebotsNr()}&f=finalized&k=".PDF_KEY."'><img src='img/database_save_on.gif' alt='acrobat' title='PDF auf Festplatte speichern' /></a></td>
				</tr>
			";
			}
			else {
				echo "<td align='center'><img src='img/acrobat_gray.gif' alt='no acrobat' title='nicht erlaubt' /></td>";
			}
		}
		
		echo "<tr style='background-color:green; color:white'><td colspan='6'>Gesamt:</td>
		      <td align='center'>".str_replace(".",",",$umsatz)."</td>
			  <td align='center'>".str_replace(".",",",$rabatt_gesamt)."</td>
			  <td align='center'>".str_replace(".",",",$skonto_gesamt)."</td>
		</tr>";
		
    	?>
    
</table>

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
		echo "<a href='show_finalized_offers.php?start=$prev'>Vorherige</a> ";
		echo "</div>";
	}
	
	// show page numbers
	
	// set variable for first page
	$i = 1;
	
	for ($x = 0; $x < $record_count; $x = $x + $per_page)
	{
		if ($start != $x) {
			echo "<div style='float: left;'>";
			echo " <a href='show_finalized_offers.php?start=$x'>$i</a> ";
			echo "</div>";
		}
		else { 
			echo "<div style='float: left;'>";
			echo " <a href='show_finalized_offers.php?start=$x'><b style='color: orange'>$i</b></a> ";
			echo "</div>";
		}
		$i++;
	}
	
	// show next button
	if (!($start >= $record_count - $per_page))	{
		echo "<div style='float: left;'>";
		echo "<a href='show_finalized_offers.php?start=$next'>N&auml;chste</a>";
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

<?php

}
else {
	echo "<b>Keine Datensätze gefunden!</b>";	
}

?>

</form>

<hr  />

<p>
<a href="main.php">Zur&uuml;ck zur Startseite</a>
</p>

</body>
</html>

<?php

include("tpl/footer.tpl.php");

?>