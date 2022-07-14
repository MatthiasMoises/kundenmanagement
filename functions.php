<?php

$rabatt = 0;
$skonto = 0;
$endbetrag = 0;
$rabattbetrag = 0;
$skontobetrag = 0;

function check_authentication(){
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
		header('WWW-Authenticate: Basic realm="FormerCompany Kundenmanagement"');
		header('HTTP/1.0 401 Unauthorized');
		echo '<h1>Zugriff verweigert!</h1>';
		exit();
	} 
	else if (($_SERVER['PHP_AUTH_USER'] == AUTH_USER) && ($_SERVER['PHP_AUTH_PW'] == AUTH_PASS))
	{
		return true;
	} 
	else {
		echo '<h1>Zugriff verweigert!</h1>';
		echo "The username and/or password you have entered is incorrect!";
		exit();
	}
}

function maintenance() {
	if (MAINTENANCE == true)
		die("<h1>Wartungsmodus aktiv. Bitte schauen Sie sp&auml;ter nochmal vorbei!</h1>");	
}

function security($v) {

	strip_tags($v);
	stripslashes($v);
	htmlentities($v);
	
	return $v;
}

function logout() {
	session_destroy();	
}

function setRabatt($nettobetrag, $rabatt_prozent){
	if ($rabatt_prozent != 0)
	{
		$rabattbetrag = $nettobetrag / 100 * $rabatt_prozent;
	}
	return $rabattbetrag;
}

function setSkonto($bruttobetrag, $skonto_prozent){
	if ($skonto_prozent != 0)
	{
		$skontobetrag = $bruttobetrag / 100 * $skonto_prozent;
	}
	return $skontobetrag;
}

function setEndbetrag($bruttobetrag, $skonto_prozent, $skonto_betrag){	
	if ($skonto_betrag != "0" && $skonto_prozent != "0"){
		$endbetrag = $bruttobetrag - $skonto_betrag;
	}
	
	else if ($skonto_prozent != "0" && $skonto_betrag == 0){
		$skonto = $bruttobetrag / 100 * $skonto_prozent;
		$endbetrag = $bruttobetrag - $skonto;
	}
	
	else if ($skonto_betrag != "0" && $skonto_prozent == 0){
		$endbetrag = $bruttobetrag - $skonto_betrag;
	}
	
	else if ($skonto_prozent == 0 && $skonto_betrag == 0){
		$endbetrag = $bruttobetrag;	
	}
	
	return $endbetrag;
}

// funtion to save all account pdfs at once

function savePdfs($array) {
	foreach ($array as $a) {
		$handle = fopen($a, "r");

		// If there is something, read and return
		if ($handle) {
			while (!feof($handle)) {
				$buffer = fgets($handle, 4096);
				echo $buffer;
			}
		fclose($handle);
		}
	}
}

?>