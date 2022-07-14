<?php

include('tpl/header.tpl.php');

$kunde = new Kunde();
$log = new Log();

$command = isset($_POST['cmd']) ? mysql_real_escape_string($_POST['cmd']) : "error";

$kunde->setId(isset($_POST['id']) ? mysql_real_escape_string($_POST['id']) : "");
$kunde->setAnrede(isset($_POST['anrede']) ? mysql_real_escape_string($_POST['anrede']) : "Herr");
$kunde->setVorname(isset($_POST['vorname']) ? mysql_real_escape_string($_POST['vorname']) : "-");
$kunde->setNachname(isset($_POST['nachname']) ? mysql_real_escape_string($_POST['nachname']) : "-");
$kunde->setKontennummer(isset($_POST['kontennummer']) ? mysql_real_escape_string($_POST['kontennummer']) : 0);
$kunde->setStrasse(isset($_POST['strasse']) ? mysql_real_escape_string($_POST['strasse']) : "-");
$kunde->setHausnummer(isset($_POST['hausnummer']) ? mysql_real_escape_string($_POST['hausnummer']) : "-");
$kunde->setAdressZusatz1(isset($_POST['adresszusatz_1']) ? mysql_real_escape_string($_POST['adresszusatz_1']) : "-");
$kunde->setAdressZusatz2(isset($_POST['adresszusatz_2']) ? mysql_real_escape_string($_POST['adresszusatz_2']) : "-");
$kunde->setPlz(isset($_POST['plz']) ? mysql_real_escape_string($_POST['plz']) : "-");
$kunde->setOrt(isset($_POST['ort']) ? mysql_real_escape_string($_POST['ort']) : "-");
$kunde->setEmail(isset($_POST['email']) ? mysql_real_escape_string($_POST['email']) : "-");
$kunde->setTelefon(isset($_POST['telefon']) ? mysql_real_escape_string($_POST['telefon']) : "-");

$log->setObjName("Kunde");

switch($command) {
	case 'new':
		
		$kunde->save();
		
		$log->setObjNr(mysql_insert_id());
		$log->setAction("erstellt");
		
		$log->save();
	
		break;
	case 'update':
			   
		$kunde->update();
		
		$log->setObjNr($kunde->getId());
		$log->setAction("aktualisiert");
		
		$log->save();
	
	break;
	case 'delete':
	
		$kunde->delete();
		
		$log->setObjNr($kunde->getId());
		$log->setAction("gelöscht");
		
		$log->save();
	
	break;
	default:
		echo '<span class="error">unknown name</span>';
		break;
}

?>