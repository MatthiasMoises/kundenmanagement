<?php

session_start();

if (!$_SESSION['benutzername']) {
	die("Sie haben keine Berechtigung um dieses Skript auszuführen!");
}

require('../required/config.php');
require('../classes/rechnung.class.php');
require('../functions.php');

$execute = isset($_GET['execute']) ? $_GET['execute'] : NULL;

if (!isset($execute) || $execute != "okay") {
	die("Sie haben keine Berechtigung um dieses Skript auszuführen!");	
}

$arr_pdfs = array();

$rechnung = new Rechnung();

$curr_month = date("m");

$get_data = mysql_query("SELECT id, rechnungsnr FROM rechnungen WHERE 1 AND MONTH(rechnungsdatum) = '$curr_month'") or die ("MySQL select rechnungen error. ".mysql_error());

while ($row = mysql_fetch_assoc($get_data)) {
    $rechnung->setId($row['id']);
    $rechnung->setRechnungsNr($row['rechnungsnr']);
    array_push($arr_pdfs,DOCUMENT_ROOT."save_finalized_pdfs.php?id={$rechnung->getId()}&m=F&r={$rechnung->getRechnungsNr()}&k=".PDF_KEY."");
    //print_r($arr_pdfs);
    echo "Das PDF zur Rechnung Nr. {$rechnung->getRechnungsNr()} wurde erfolgreich erstellt und gespeichert.<br/>";
}

echo "<p><b>Sie können diese Dateien über FTP nun auf ein lokales Medium übertragen.</b></p>";

echo "<p>
         <a href='../show_finalized_accounts.php?start=0'>Zurück</a>
     </p>";
     
     
echo "<br/><p><u>Details:</u></p>";

savePdfs($arr_pdfs);

?>