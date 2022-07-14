<?php

include('tpl/header.tpl.php');

$artikel = new Artikel();
$log = new Log();

$command = isset($_POST['cmd']) ? mysql_real_escape_string($_POST['cmd']) : "";

$artikel->setId(isset($_POST['id']) ? mysql_real_escape_string($_POST['id']) : "");
$artikel->setArtNrLieferant(isset($_POST['artnr_lieferant']) ? mysql_real_escape_string($_POST['artnr_lieferant']) : "-");
$artikel->setLieferantenNr(isset($_POST['lieferantennr']) ? mysql_real_escape_string($_POST['lieferantennr']) : "-");
$artikel->setBezeichnung(isset($_POST['bezeichnung']) ? mysql_real_escape_string($_POST['bezeichnung']) : "-");
$artikel->setKategorie(isset($_POST['kategorie']) ? mysql_real_escape_string($_POST['kategorie']) : "Elektro");
$artikel->setPreisNetto(isset($_POST['preis_netto']) ? mysql_real_escape_string($_POST['preis_netto']) : 0);
$artikel->setEinheit(isset($_POST['einheit']) ? mysql_real_escape_string($_POST['einheit']) : "-");

$artikel->setBezeichnung(stripslashes($artikel->getBezeichnung()));

$steuersatz = 100 + DEFAULT_MWST;
$artikel->setPreisBrutto($artikel->getPreisNetto() / 100 * $steuersatz); 

if (strpos($artikel->getPreisNetto(),".") === true){
	$artikel->setPreisNetto(str_replace(",",".",$artikel->getPreisNetto()));
}

$artikel->setPreisNetto(str_replace(",",".",$artikel->getPreisNetto()));

$log->setObjName("Artikel");

switch($command) {
	case 'new':
	
		$artikel->save();
		
		$log->setObjNr(mysql_insert_id());
		$log->setAction("erstellt");
		
		$log->save();
		
		break;
	case 'update':
	
		$artikel->update();
		
		$log->setObjNr($artikel->getId());
		$log->setAction("aktualisiert");
		
		$log->save();
	
		break;
	case 'delete':
		
		$artikel->delete();
		
		$log->setObjNr($artikel->getId());
		$log->setAction("gelöscht");
		
		$log->save();
	
		break;
	default:
		echo '<span class="error">unknown name</span>';
		break;
}

?>