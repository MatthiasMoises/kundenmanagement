<?php

session_start();

require('../required/config.php');
require('../classes/benutzer.class.php');
require('../functions.php');

$command = isset($_POST['cmd']) ? $_POST['cmd'] : "";

$benutzer = new Benutzer();

$benutzer->setId(isset($_POST['id']) ? mysql_real_escape_string($_POST['id']) : "");
$benutzer->setBenutzername(isset($_POST['username']) ? mysql_real_escape_string($_POST['username']) : "-");
$benutzer->setPasswort(isset($_POST['passwort']) ? mysql_real_escape_string($_POST['passwort']) : "-");
$benutzer->setKuerzel(isset($_POST['kuerzel']) ? mysql_real_escape_string($_POST['kuerzel']) : "-");
$benutzer->setNachname(isset($_POST['name']) ? mysql_real_escape_string($_POST['name']) : "-");
$benutzer->setVorname(isset($_POST['vorname']) ? mysql_real_escape_string($_POST['vorname']) : "-");
$benutzer->setStrasse(isset($_POST['strasse']) ? mysql_real_escape_string($_POST['strasse']) : "-");
$benutzer->setHausnummer(isset($_POST['hausnummer']) ? mysql_real_escape_string($_POST['hausnummer']) : "-");
$benutzer->setPlz(isset($_POST['plz']) ? mysql_real_escape_string($_POST['plz']) : "-");
$benutzer->setOrt(isset($_POST['ort']) ? mysql_real_escape_string($_POST['ort']) : "-");
$benutzer->setTelefon(isset($_POST['telefon']) ? mysql_real_escape_string($_POST['telefon']) : "-");
$benutzer->setEmail(isset($_POST['email']) ? mysql_real_escape_string($_POST['email']) : "-");
$benutzer->setStundensatz(isset($_POST['stundensatz']) ? mysql_real_escape_string($_POST['stundensatz']) : 0);
$benutzer->setAdmin(isset($_POST['ist_admin']) ? mysql_real_escape_string($_POST['ist_admin']) : 0);
$benutzer->setGesperrt(isset($_POST['gesperrt']) ? mysql_real_escape_string($_POST['gesperrt']) : 0);

$benutzer->setStundensatz(str_replace(",",".",$benutzer->getStundensatz()));

switch($command) {
  
   case 'validate_password':

   if (preg_match('#^[a-zäöü]+$#i', $benutzer->getPasswort())) {
      $result = "fehler";
   }
   else {
      $result = "erfolg";
   }

	echo $result;

	break;

	case 'new':
		$get_names = mysql_query("SELECT benutzername FROM benutzer WHERE 1");
		
		while ($row_all_names = mysql_fetch_assoc($get_names))
		{
			$check_user = $row_all_names['benutzername'];
			if ($benutzer->getBenutzername() == $check_user)
				die("Benutzername bereits vorhanden! <a href='edit_user.php'>zur&uuml;ck</a>");	
		}
	
		$benutzer->save();
				
	break;			
	case 'update':
	
		$benutzer->update();
	
		break;
	case 'delete':
	
		$benutzer->delete();
	
		break;
	default:
		echo '<span class="error">unknown name</span>';
		break;
}

?>