<?php

include('../tpl/adminheader.tpl.php');

if (isset($_POST['unlock_offers']))
{
	$accounts_to_unlock = array();
	$accounts_to_unlock = isset($_POST['checked_offers']) ? $_POST['checked_offers'] : NULL;
	
	if (!empty($accounts_to_unlock))
	{
		foreach($accounts_to_unlock as $atu => $value){
			$set_accs = mysql_query("UPDATE angebote SET editierbar = '1' WHERE id = '$value'") or die ("MySQL Update finalized offers states error. ".mysql_error());
			
			if ($set_accs)
				echo "<p style='color:blue'>Angebot Nr. ".$value." wurde erfolgreich entsperrt!</p>";
			
		}
	}
	else {
		echo "<p style='color:red'>Sie haben keine Angebote ausgew&auml;hlt!</p>";	
	}
}

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
<title>Fertige Angebote entsperren</title>
<link rel="stylesheet" type="text/css" href="../css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."img/favicon.ico"; ?>" />
<script type="text/javascript" src="../libs/js/prototype.js"></script>
<script type="text/javascript" src="../libs/js/scriptaculous.js"></script>
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
				alert ("Angebot wurde erfolgreich gelöscht!");
				location.reload(true);
			}
		});
	}
}

</script>
</head>

<body id="admin">

<h2>Fertige Angebote entsperren</h2>

<form action="" method="get">
<label>Suche:</label>
	<input type="text" id="search" name="search" />
    <input type="submit" id="bt_search" name="bt_search" value="Angebot suchen" />
</form>

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
		<th>Bitte w&auml;hlen</th>
        <th>Angebotsnummer</th>
        <th>Angebotsdatum</th>
        <th>Kundennummer</th>
        <th>Vorname</th>
        <th>Nachname</th>
        <th>Angebotsbetrag</th>
        <th>Rabatt</th>
        <th>Skonto</th>
	</thead>
    
    	<?php
		
		$count = 0;
		
		while ($row = mysql_fetch_assoc($select))
		{
			$count++;
			$id = $row['id'];
			$rechnungsnr = $row['angebotsnr'];
			$rechnungsdatum = $row['rech_date'];
			$hat_stunden = $row['hat_stunden'];
			$hat_artikel = $row['hat_artikel'];
			$zahlungsbetrag = $row['endbetrag'];
			$rabatt_betrag = $row['rabatt_betrag'];
			$skonto_betrag = $row['skonto_betrag'];
			$rabatt_gesamt += $rabatt_betrag;
			$skonto_gesamt += $skonto_betrag;
			$rabatt_gesamt = sprintf("%.2f",$rabatt_gesamt);
			//$rabatt_gesamt = str_replace(".",",",$rabatt_gesamt);
			$rabatt_betrag = sprintf("%.2f",$rabatt_betrag);
			//$rabatt_betrag = str_replace(".",",",$rabatt_betrag);
			$skonto_betrag = sprintf("%.2f",$skonto_betrag);
			//$skonto_betrag = str_replace(".",",",$skonto_betrag);
			$skonto_gesamt = sprintf("%.2f",$skonto_gesamt);
			//$skonto_gesamt = str_replace(".",",",$skonto_gesamt);
			$umsatz +=  $zahlungsbetrag;
			$umsatz = sprintf("%.2f",$umsatz);
			//$umsatz = str_replace(".",",",$umsatz);
			$zahlungsbetrag = sprintf("%.2f",$zahlungsbetrag);
			//$zahlungsbetrag = str_replace(".",",",$zahlungsbetrag);
			$kdnr = $row['kdnr'];
			$k_vorname = $row['vorname'];
			$k_nachname = $row['nachname'];
			$bezahlt = $row['bezahlt'];
			$bezahlt_datum = $row['bez_date'];
			$editierbar = $row['editierbar'];
			
			$queryStunden = mysql_query("SELECT COUNT(*) FROM a_stunden rs, angebote r WHERE rs.angebotsnr = r.angebotsnr AND r.angebotsnr = '$rechnungsnr' LIMIT 1");
			
			$queryArtikel = mysql_query("SELECT COUNT(*) FROM a_artikel ra, angebote r WHERE ra.angebotsnr = r.angebotsnr AND r.angebotsnr = '$rechnungsnr' LIMIT 1");
			
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
				echo "<tr style='background-color:#FFF1AA'>";
			}
			else {
				echo "<tr style='background-color:#9AE9FA;'>";	
			}
			
			echo "
					<td align='center'>$id</td>
					<td align='center'><input type='checkbox' id='checked_offers[]' name='checked_offers[]' value='".$id."' /></td>
					<td align='center'>$rechnungsnr</td>
					<td align='center'>$rechnungsdatum</td>
					<td align='center'>$kdnr</td>
					<td>$k_vorname</td>
					<td>$k_nachname</td>
					<td align='center'>$zahlungsbetrag</td>
					<td align='center'>$rabatt_betrag</td>
					<td align='center'>$skonto_betrag</td>
			</tr>";
		}
		
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

<p>
<input type="submit" id="unlock_offers" name="unlock offers" value="Gew&auml;hlte Angebote entsperren" />
</p>

</form>

<hr  />

<p>
<a href="main.php">Zur&uuml;ck zur Startseite</a>
</p>

</body>
</html>

<?php

include("../tpl/footer.tpl.php");

?>