<?php

session_start();

require('required/config.php');

require('functions.php');

$cmd = isset($_POST['cmd']) ? $_POST['cmd'] : NULL;
$acc_nr = isset($_POST['acc_nr']) ? $_POST['acc_nr'] : NULL;
$stunden = isset($_POST['stunden']) ? $_POST['stunden'] : NULL;
$artikel = isset($_POST['artikel']) ? $_POST['artikel'] : NULL;

$netto = 0;
$brutto = 0;

switch($cmd) {
    case 'check_offer_nr':

	$rechnungsnr = mysql_real_escape_string($_POST['angebotsnr']);
    $query_nr = sprintf("SELECT angebotsnr FROM angebote WHERE angebotsnr = '%s' LIMIT 1",$rechnungsnr);
	$select_acc_nrs = mysql_query($query_nr);

	if (mysql_num_rows($select_acc_nrs) > 0)
	{
    	$result = "fehler";
	} 
	else {
        $result = "erfolg";
    }

	echo $result;

	break;
	
	case 'check_account_nr':

	$rechnungsnr = mysql_real_escape_string($_POST['rechnungsnr']);
  $query_nr = sprintf("SELECT rechnungsnr FROM rechnungen WHERE rechnungsnr = '%s' LIMIT 1",$rechnungsnr);
	$select_acc_nrs = mysql_query($query_nr);

	if (mysql_num_rows($select_acc_nrs) > 0)
	{
    	$result = "fehler";
	} 
	else {
        $result = "erfolg";
    }

	echo $result;

	break;

    case 'check_account_nr_update':

		$rechnungsnr = mysql_real_escape_string($_POST['angebotsnr']);
        $rechnungsnr_alt = mysql_real_escape_string($_POST['angebotsnr_alt']);

        if ($rechnungsnr == $rechnungsnr_alt) {
            $result = "update";
        }
        else {
            $query_nr = sprintf("SELECT angebotsnr FROM angebote WHERE angebotsnr = '%s'",$rechnungsnr);
            $select_acc_nrs = mysql_query($query_nr);

            if (mysql_num_rows($select_acc_nrs) > 0)
            {
                    $result = "fehler";

            } else {
                    $result = "update";
            }
        }
        
	echo $result;

	break;

	case 'save':

	// Rechnungsdaten

	$rechnungsnr = mysql_real_escape_string($_POST['angebotsnr']);
	$rechnungsdatum = $_POST['angebotsdatum'];
	$rechnungsdatum = date('Y-m-d', strtotime($rechnungsdatum)); 
	$message = mysql_real_escape_string($_POST['message']);
	$kd_nr = mysql_real_escape_string($_POST['kd_nr']);
	$rabatt_prozent = isset($_POST['rabatt_prozent']) ? $_POST['rabatt_prozent'] : 0;
	$skonto_prozent = isset($_POST['skonto_prozent']) ? $_POST['skonto_prozent'] : 0;
	$rabatt_betrag = isset($_POST['rabatt_betrag']) ? $_POST['rabatt_betrag'] : 0;
	$skonto_betrag = isset($_POST['skonto_betrag']) ? $_POST['skonto_betrag'] : 0;
	$mwst_prozent = isset($_POST['mwst_prozent']) ? $_POST['mwst_prozent'] : 0;
	$bezahlt = 0;
	$editierbar = isset($_POST['editierbar']) ? $_POST['editierbar'] : 1;
	$auftragsbestaetigung = isset($_POST['auftragsbestaetigung']) ? $_POST['auftragsbestaetigung'] : 0;
	
	$rabatt_betrag = str_replace(",",".",$rabatt_betrag);
	$skonto_betrag = str_replace(",",".",$skonto_betrag);

	$rabatt_prozent = str_replace(",",".",$rabatt_prozent);
	$skonto_prozent = str_replace(",",".",$skonto_prozent);
	$mwst_prozent = str_replace(",",".",$mwst_prozent);

	// Stundendaten

	$stundenString = $_POST['stundenString'];

	// Artikeldaten

	$artikelString = $_POST['artikelString'];

	// Rechnungsdaten speichern

	if ($stundenString != NULL) {
		$hat_stunden = 1;
	}
	else {
		$hat_stunden = 0;
	}

	if ($artikelString != NULL) {
		$hat_artikel = 1;
	}
	else {
		$hat_artikel = 0;
	}

	// Artikeldaten speichern

	foreach ($artikelString as $as)
	{
		$artikel_einzeln = array();
		$artikel_einzeln = explode(';',$as);
		
		$artikel_einzeln[3] = str_replace(",",".",$artikel_einzeln[3]);
		$artikel_einzeln[4] = str_replace(",",".",$artikel_einzeln[4]);

		$netto += $artikel_einzeln[4];

		$insert_artikel = sprintf("INSERT INTO a_artikel (artnr, art_menge, einzelpreis, gesamtpreis_artikel, angebotsnr) VALUES ('%s','%s','%s','%s','%s')",$artikel_einzeln[0],$artikel_einzeln[2],$artikel_einzeln[3],$artikel_einzeln[4],$rechnungsnr);

		$query_artikel = mysql_query($insert_artikel) or die("MySQL insert into a_artikel table error. ".mysql_error());
	}

	//  Stundendaten speichern

	foreach ($stundenString as $ss)
	{
		$stunde_einzeln = array();
		$stunde_einzeln = explode(';',$ss);
		
		$stunde_einzeln[0] = date('Y-m-d', strtotime($stunde_einzeln[0])); 
		
		$stunde_einzeln[3] = str_replace(",",".",$stunde_einzeln[3]);
		
		$stunde_einzeln[4] = str_replace(",",".",$stunde_einzeln[4]);

		$stunde_einzeln[5] = str_replace(",",".",$stunde_einzeln[5]);
		
		$netto += $stunde_einzeln[5];		
		
		$select_ma_nr = mysql_query("SELECT id FROM benutzer WHERE name = '$stunde_einzeln[1]'");

		while($ma_row = mysql_fetch_assoc($select_ma_nr))
		{
			$ma_id = $ma_row['id'];
		}

		$insert_stunden = sprintf("INSERT INTO a_stunden (st_datum, ma_nr, ma_name, arbeit_art, zeit_stunden, euro_st, euro_st_gesamt, angebotsnr) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')",$stunde_einzeln[0],$ma_id,$stunde_einzeln[1],$stunde_einzeln[2],$stunde_einzeln[3],$stunde_einzeln[4],$stunde_einzeln[5],$rechnungsnr);

		$query_stunden = mysql_query($insert_stunden) or die("MySQL insert into a_stunden table error. ".mysql_error());
	}
	
	$steuersatz = 100 + $mwst_prozent;
	
	$netto = str_replace(",",".",$netto);
		
	if ($rabatt_prozent != 0){
		$rabatt_betrag = setRabatt($netto,$rabatt_prozent);
		$rabatt_betrag = sprintf("%.2f",$rabatt_betrag);			
		$rabatt_betrag = str_replace(",",".",$rabatt_betrag);	
		//$rabatt_betrag = number_format($rabatt_betrag,2);
	}

	$netto -= $rabatt_betrag;	
	
	$brutto = $netto / 100 * $steuersatz;
	
	$brutto = str_replace(",",".",$brutto);
	
	$mwst_betrag = $brutto-$netto;
	
	$mwst_betrag = str_replace(",",".",$mwst_betrag);
		
	if ($skonto_prozent != 0){
		$skonto_betrag = setSkonto($brutto, $skonto_prozent);
		$skonto_betrag = str_replace(",",".",$skonto_betrag);	
		$skonto_betrag = sprintf("%01.2f", $skonto_betrag);
	}
	
	$zahlungsbetrag = setEndbetrag($brutto,$skonto_prozent,$skonto_betrag);
	
	$zahlungsbetrag = str_replace(",",".",$zahlungsbetrag);

	/*
	$datei = "temp.txt"; // Datei in die wir schreiben wollen
	$fp = fopen($datei,"w");  //Datei wird zum schreiben geöffnet

	fwrite($fp,"Update: ".$rechnungsnr); // Daten werden jetzt mit fwrite in die txt Datei geschrieben.
	fclose($fp);
	*/

	$insert_account = sprintf("INSERT INTO angebote (angebotsnr, angebotsdatum, kundennachricht, hat_stunden, hat_artikel, rabatt_prozent, rabatt_betrag, skonto_prozent, skonto_betrag, mwst_prozent, mwst_betrag, endbetrag, kdnr, bezahlt, bezahlt_datum, editierbar, ist_auftragsbestaetigung) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",$rechnungsnr,$rechnungsdatum,$message,$hat_stunden,$hat_artikel,$rabatt_prozent,$rabatt_betrag,$skonto_prozent,$skonto_betrag,$mwst_prozent,$mwst_betrag,$zahlungsbetrag,$kd_nr,$bezahlt,$bezahlt_datum,$editierbar,$auftragsbestaetigung);

	$query_account = mysql_query($insert_account) or die ("MySQL insert into angebote table error. ".mysql_error());
		
	$log_date = date("Y-m-d H:i:s");
	$log_txt = $_SESSION['benutzername']." hat Angebot Nr. $rechnungsnr angelegt.";
	$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());

	break;

	case 'search_user':
		$kd_nr = mysql_real_escape_string($_POST['kundennr']);
		$nachname = mysql_real_escape_string($_POST['nachname']);

		if (isset($kd_nr) && $kd_nr != "")
		{
			$query_kunde = sprintf("SELECT id, vorname, nachname FROM kunden WHERE id = '%s'",$kd_nr);
			$select_action = mysql_query($query_kunde);
		}
		else if ($kd_nr == "" && $nachname != ""){
			$query_kunde = sprintf("SELECT id, vorname, nachname FROM kunden WHERE nachname = '%s'",$nachname);
			$select_action = mysql_query($query_kunde);
		}

	$results ="";
	while ($row = mysql_fetch_assoc($select_action))
	{
		if ($results != "") {
			$results = $results."|".$row['id']."~".$row['nachname']."~".$row['vorname'];
		}
		else {
			$results = $row['id']."~".$row['nachname']."~".$row['vorname'];
		}
	}

	echo $results;

	break;

	case 'get_article':
		$artikelListe = $_POST['artikelListe'];
		$results = "";
		foreach ($artikelListe as $al)
		{
			$artikel_einzeln = array();
			$artikel_einzeln = explode(',',$al);

			$artikel_einzeln = str_replace("~",",",$artikel_einzeln);

			if (is_numeric($artikel_einzeln[0]))
				$select_artikel = sprintf("SELECT id, bezeichnung, preis_netto FROM artikel WHERE id = '%s'",$artikel_einzeln[0]);
			else
				$select_artikel = sprintf("SELECT id, bezeichnung, preis_netto FROM artikel WHERE bezeichnung = '%s'",$artikel_einzeln[0]);
			
			$artikel_query = mysql_query($select_artikel);
			
			if ($count = mysql_num_rows($artikel_query) == 0) {
				$results = ""; 
				break;
			}
	
			while ($row = mysql_fetch_assoc($artikel_query))
			{
				if ($results != "") {
					$results = $results."|".$row['id']."~".$row['bezeichnung']."~".$row['preis_netto'];
				}
				else {
					$results = $row['id']."~".$row['bezeichnung']."~".$row['preis_netto'];
				}
			}
		}

		echo $results;

	break;	

	case 'update':
		// Rechnungsdaten
	
		$rechnungs_id = mysql_real_escape_string($_POST['angebots_id']);
		$rechnungsnr = mysql_real_escape_string($_POST['angebotsnr']);
		$rechnungsdatum = $_POST['angebotsdatum'];
		$rechnungsdatum = date('Y-m-d', strtotime($rechnungsdatum)); 
		$message = mysql_real_escape_string($_POST['message']);
		$kd_nr = mysql_real_escape_string($_POST['kd_nr']);
		$rabatt_prozent = isset($_POST['rabatt_prozent']) ? $_POST['rabatt_prozent'] : 0;
		$skonto_prozent = isset($_POST['skonto_prozent']) ? $_POST['skonto_prozent'] : 0;
		$rabatt_betrag = isset($_POST['rabatt_betrag']) ? $_POST['rabatt_betrag'] : 0;
		$skonto_betrag = isset($_POST['skonto_betrag']) ? $_POST['skonto_betrag'] : 0;
		$mwst_prozent = isset($_POST['mwst_prozent']) ? $_POST['mwst_prozent'] : 0;
		$bezahlt = 0;
		$editierbar = isset($_POST['editierbar']) ? $_POST['editierbar'] : 1;
		$auftragsbestaetigung = isset($_POST['auftragsbestaetigung']) ? $_POST['auftragsbestaetigung'] : 0;
	
		$rabatt_betrag = str_replace(",",".",$rabatt_betrag);
		$skonto_betrag = str_replace(",",".",$skonto_betrag);
	
		$rabatt_prozent = str_replace(",",".",$rabatt_prozent);
		$skonto_prozent = str_replace(",",".",$skonto_prozent);
		$mwst_prozent = str_replace(",",".",$mwst_prozent);
		
		// Stundendaten
		
		$stundenString = $_POST['stundenString'];
		
		// Artikeldaten
		
		$artikelString = $_POST['artikelString'];
		
		// Rechnungsdaten speichern
		
		if ($stundenString != NULL) {
			$hat_stunden = 1;	
		}
		else {
			$hat_stunden = 0;	
		}
		
		if ($artikelString != NULL) {
			$hat_artikel = 1;	
		}
		else {
			$hat_artikel = 0;	
		}

		// Artikeldaten speichern
		
	 $first_delete_artikel = mysql_query("DELETE FROM a_artikel WHERE angebotsnr = '$rechnungsnr'") or die ("MySQL artikel first_delete error. ".mysql_error());

		foreach ($artikelString as $as)
		{
			$artikel_einzeln = array();
			$artikel_einzeln = explode(';',$as);
			
			$artikel_einzeln[3] = str_replace(",",".",$artikel_einzeln[3]);
			$artikel_einzeln[4] = str_replace(",",".",$artikel_einzeln[4]);
			
			$netto += $artikel_einzeln[4];			

		 $insert_artikel = sprintf("INSERT INTO a_artikel (artnr, art_menge, einzelpreis, gesamtpreis_artikel, angebotsnr) VALUES ('%s','%s','%s','%s','%s')",$artikel_einzeln[0],$artikel_einzeln[2],$artikel_einzeln[3],$artikel_einzeln[4],$rechnungsnr);

		 $query_artikel = mysql_query($insert_artikel) or die("MySQL insert into a_artikel table error. ".mysql_error());
		}
		
 	  // Stundendaten speichern
		
			$first_delete_stunden = mysql_query("DELETE FROM a_stunden WHERE angebotsnr = '$rechnungsnr'") or die ("MySQL stunden first_delete error. ".mysql_error());

		foreach ($stundenString as $ss)
		{
			$stunde_einzeln = array();
			$stunde_einzeln = explode(';',$ss);
			
			$stunde_einzeln[0] = date('Y-m-d', strtotime($stunde_einzeln[0])); 
			
			$stunde_einzeln[3] = str_replace(",",".",$stunde_einzeln[3]);
			
			$stunde_einzeln[4] = str_replace(",",".",$stunde_einzeln[4]);

			$stunde_einzeln[5] = str_replace(",",".",$stunde_einzeln[5]);
			
			$netto += $stunde_einzeln[5];
			
			$select_ma_nr = mysql_query("SELECT id FROM benutzer WHERE name = '$stunde_einzeln[1]'");
			
			while($ma_row = mysql_fetch_assoc($select_ma_nr))
			{
				$ma_id = $ma_row['id'];	
			}

		 $insert_stunden = sprintf("INSERT INTO a_stunden (st_datum, ma_nr, ma_name, arbeit_art, zeit_stunden, euro_st, euro_st_gesamt, angebotsnr) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')",$stunde_einzeln[0],$ma_id,$stunde_einzeln[1],$stunde_einzeln[2],$stunde_einzeln[3],$stunde_einzeln[4],$stunde_einzeln[5],$rechnungsnr);

		 $query_stunden = mysql_query($insert_stunden) or die("MySQL insert into a_stunden table error. ".mysql_error());
		}

		$steuersatz = 100 + $mwst_prozent;
		
		$netto = str_replace(",",".",$netto);
				
		if ($rabatt_prozent != 0){
			$rabatt_betrag = setRabatt($netto,$rabatt_prozent);
			$rabatt_betrag = sprintf("%.2f",$rabatt_betrag);			
			$rabatt_betrag = str_replace(",",".",$rabatt_betrag);	
			//$rabatt_betrag = number_format($rabatt_betrag,2);
		}

		$netto -= $rabatt_betrag;	
		
		$brutto = $netto / 100 * $steuersatz;
		
		$brutto = str_replace(",",".",$brutto);
		
		$mwst_betrag = $brutto-$netto;
		
		$mwst_betrag = str_replace(",",".",$mwst_betrag);
		
		if ($skonto_prozent != 0){
			$skonto_betrag = setSkonto($brutto, $skonto_prozent);
			$skonto_betrag = sprintf("%.2f",$skonto_betrag);
			$skonto_betrag = str_replace(",",".",$skonto_betrag);	
			//$skonto_betrag = number_format($skonto_betrag,2);
		}
	
		$zahlungsbetrag = setEndbetrag($brutto,$skonto_prozent,$skonto_betrag);
		
		$zahlungsbetrag = str_replace(",",".",$zahlungsbetrag);

    	/*
		$datei = "temp.txt"; // Datei in die wir schreiben wollen
		$fp = fopen($datei,"w");  //Datei wird zum schreiben geöffnet

		fwrite($fp,"Update: ".$rechnungsnr); // Daten werden jetzt mit fwrite in die txt Datei geschrieben.
		fclose($fp);
    	*/
	
		$update_account = sprintf("UPDATE angebote SET angebotsnr = '%s',angebotsdatum = '%s', kundennachricht = '%s', hat_stunden = '%s', hat_artikel = '%s', rabatt_prozent = '%s', rabatt_betrag = '%s', skonto_prozent = '%s', skonto_betrag = '%s', mwst_prozent = '%s', mwst_betrag = '%s', endbetrag = '%s', kdnr = '%s', bezahlt = '%s', bezahlt_datum = '%s', editierbar = '%s', ist_auftragsbestaetigung = '%s' WHERE id = '%s'",$rechnungsnr,$rechnungsdatum,$message,$hat_stunden,$hat_artikel,$rabatt_prozent, $rabatt_betrag,$skonto_prozent,$skonto_betrag,$mwst_prozent,$mwst_betrag,$zahlungsbetrag,$kd_nr,$bezahlt,$bezahlt_datum,$editierbar,$auftragsbestaetigung,$rechnungs_id);
		
		$query_account = mysql_query($update_account) or die ("MySQL update angebote table error. ".mysql_error());
		
		$log_date = date("Y-m-d H:i:s");
		$log_txt = $_SESSION['benutzername']." hat Angebot Nr. $rechnungsnr aktualisiert.";
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
		
		break;
	
	case 'delete':
		if ($stunden == 1 && $artikel == 1)
		{
			$delete = "DELETE an.*, ast.*, aa.* FROM angebote an, a_stunden ast, a_artikel aa WHERE an.angebotsnr = '$acc_nr' AND aa.angebotsnr = an.angebotsnr AND ast.angebotsnr = an.angebotsnr";
		}
		else if ($stunden == 1 && $artikel == 0)
		{
			$delete = "DELETE an.*, ast.* FROM angebote an, a_stunden ast WHERE an.angebotsnr = '$acc_nr' AND ast.angebotsnr = an.angebotsnr";	
		}
		else if ($stunden == 0 && $artikel == 1)
		{
			$delete = "DELETE an.*, aa.* FROM angebote an, a_artikel aa WHERE an.angebotsnr = '$acc_nr' AND aa.angebotsnr = an.angebotsnr";	
		}
		else {
			echo "error.";	
		}
		
		mysql_query($delete) or die ("MySql delete offer error. ".mysql_error());	
		
		$log_date = date("Y-m-d H:i:s");
		$log_txt = $_SESSION['benutzername']." hat Angebot Nr. $acc_nr gel&ouml;scht.";
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
			
		break;
		
	case 'copyOffer':
		
		$rNr = isset($_POST['acc_nr']) ? $_POST['acc_nr'] : NULL;
		$newNr = isset($_POST['new_nr']) ? $_POST['new_nr'] : NULL;
		
		$due_date = isset($_POST['due_date']) ? $_POST['due_date'] : "0000-00-00";
		$due_date = date('Y-m-d', strtotime($due_date)); 
		
		$rAngebot = mysql_query("SELECT id, angebotsnr, DATE_FORMAT (angebotsdatum, '%d.%m.%Y') AS rech_date, kundennachricht, hat_stunden, hat_artikel, rabatt_prozent, rabatt_betrag, skonto_prozent, skonto_betrag, mwst_prozent, mwst_betrag, endbetrag, kdnr, bezahlt, editierbar, ist_auftragsbestaetigung FROM angebote WHERE angebotsnr = '$rNr'") or die("MySQL select offer data error. ".mysql_error());
		
		$rKunde = mysql_query("SELECT k.* FROM kunden k, angebote r WHERE r.kdnr = k.id AND r.angebotsnr = '$rNr'") or die("MySQL select customer error. ".mysql_error()); 
		
		$rArtikel = mysql_query("SELECT a.id, a.bezeichnung, a.preis_netto, ra.art_menge, ra.einzelpreis, ra.gesamtpreis_artikel FROM artikel a, angebote r, a_artikel ra WHERE r.angebotsnr = '$rNr' AND r.angebotsnr = ra.angebotsnr AND ra.artnr = a.id ORDER by ra.id ASC") or die("MySQL select artikel error. ".mysql_error());
		
		$rStunden = mysql_query("SELECT * FROM a_stunden WHERE angebotsnr = '$rNr' ORDER BY id ASC") or die ("MySQL select r stunden error. ".mysql_error());
		
		if (mysql_num_rows($rStunden) > 0)
			$hat_stunden = 1;
		
		if (mysql_num_rows($rArtikel) > 0)
			$hat_artikel = 1;
		
		while ($row_rechnung = mysql_fetch_assoc($rAngebot))
		{
			$db_rechnungs_id = $row_rechnung['id'];
			$db_rechnungsnr = $row_rechnung['angebotsnr'];
			$db_rechnungsdatum = $row_rechnung['rech_date'];
			$db_kundennachricht = $row_rechnung['kundennachricht'];
			$db_message = $row_rechnung['kundennachricht'];
			$db_bezahlt = $row_rechnung['bezahlt'];	
		    $db_bezahlt_datum = $row_rechnung['bezahlt_datum'];
			$db_rabatt_prozent = $row_rechnung['rabatt_prozent'];
			$db_skonto_prozent = $row_rechnung['skonto_prozent'];
			$db_mwst_prozent = $row_rechnung['mwst_prozent'];
			$db_rabatt_betrag = $row_rechnung['rabatt_betrag'];
			$db_skonto_betrag = $row_rechnung['skonto_betrag'];
			$db_mwst_betrag = $row_rechnung['mwst_betrag'];
			$db_endbetrag = $row_rechnung['endbetrag'];
			$db_editierbar = $row_rechnung['editierbar'];
			$db_auftragsbestaetigung= $row_rechnung['ist_auftragsbestaetigung'];
		}
		
		while ($row_kunde = mysql_fetch_assoc($rKunde))
		{
			$db_kdnr = $row_kunde['id'];
			$db_vorname = $row_kunde['vorname'];
			$db_nachname = $row_kunde['nachname'];	
		}
		
		if ($hat_stunden == 1) {
			
			//$delete = mysql_query("DELETE FROM m_stunden WHERE mahnungsnr = '$rNr'");
			
			while ($row_stunden = mysql_fetch_assoc($rStunden))
			{
				$db_st_datum = $row_stunden['st_datum'];
				$db_st_ma_nr = $row_stunden['ma_nr'];
				$db_st_ma_name = $row_stunden['ma_name'];
				$db_st_arbeit_art = $row_stunden['arbeit_art'];
				$db_st_zeit_stunden = $row_stunden['zeit_stunden'];
				$db_st_euro_st = $row_stunden['euro_st'];
				$db_st_euro_gesamt = $row_stunden['euro_st_gesamt'];
				
				$insert_stunden = mysql_query("INSERT INTO a_stunden (st_datum,ma_nr,ma_name,arbeit_art,zeit_stunden,euro_st,euro_st_gesamt,angebotsnr) VALUES ('$db_st_datum','$db_st_ma_nr','$db_st_ma_name','$db_st_arbeit_art','$db_st_zeit_stunden','$db_st_euro_st','$db_st_euro_gesamt','$newNr')") or die("MySQL insert a_stunden error. ".mysql_error());
				
			}
		}
		
		if ($hat_artikel == 1) {
			
			//$kundennachricht = "Folgende Leistungen und Artikel wurden von Ihnen noch nicht bezahlt:";
			
			//$delete = mysql_query("DELETE FROM m_artikel WHERE mahnungsnr = '$rNr'");
			
			while ($row_artikel = mysql_fetch_assoc($rArtikel))
			{
				$db_artnr = $row_artikel['id'];
				$db_art_bezeichnung = $row_artikel['bezeichnung'];
				$db_art_menge = $row_artikel['art_menge'];
				$db_einzelpreis = $row_artikel['preis_netto'];
				$db_art_gesamtpreis = $row_artikel['gesamtpreis_artikel'];
				
				$insert_artikel = mysql_query("INSERT INTO a_artikel (artnr,art_menge,einzelpreis,gesamtpreis_artikel,angebotsnr) VALUES ('$db_artnr','$db_art_menge','$db_einzelpreis','$db_art_gesamtpreis','$newNr')") or die ("MySQL insert a_artikel error. ".mysql_error());
			}	
		}
		
		//$delete_demand_note = mysql_query("DELETE FROM mahnungen WHERE mahnungsnr = '$rNr'");
					
		$insert_angebot = mysql_query("INSERT INTO angebote (angebotsnr,angebotsdatum,kundennachricht,hat_stunden,hat_artikel,rabatt_prozent,rabatt_betrag,skonto_prozent,skonto_betrag,mwst_prozent,mwst_betrag,endbetrag,kdnr,bezahlt,bezahlt_datum,editierbar,ist_auftragsbestaetigung) VALUES ('$newNr','$db_rechnungsdatum','$db_kundennachricht','$hat_stunden','$hat_artikel','$db_rabatt_prozent','$db_rabatt_betrag','$db_skonto_prozent','$db_skonto_betrag','$db_mwst_prozent','$db_mwst_betrag','$db_endbetrag','$db_kdnr','$db_bezahlt','$db_bezahlt_datum','$db_editierbar','$db_auftragsbestaetigung')") or die("MySQL insert mahnungen error. ".mysql_error());
		
		$last_id = mysql_insert_id();
		
		$log_date = date("Y-m-d H:i:s");
		$log_txt = $_SESSION['benutzername']." hat Angebot Nr. $last_id angelegt. Kopieren erfolgreich!";
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
		
		break;
		
	case 'createAccount':
		$bill_nr = isset($_POST['bill_nr']) ? $_POST['bill_nr'] : "";
		$new_id = isset($_POST['id']) ? $_POST['id'] : "";
		
		$db_rechnungsdatum = date("Y-m-d");
		
		$rAngebot = mysql_query("SELECT id, angebotsnr, DATE_FORMAT (angebotsdatum, '%d.%m.%Y') AS rech_date, kundennachricht, hat_stunden, hat_artikel, rabatt_prozent, rabatt_betrag, skonto_prozent, skonto_betrag, mwst_prozent, mwst_betrag, endbetrag, kdnr, bezahlt, editierbar FROM angebote WHERE angebotsnr = '$bill_nr'") or die("MySQL select offer data error. ".mysql_error());
		
		$rKunde = mysql_query("SELECT k.* FROM kunden k, angebote r WHERE r.kdnr = k.id AND r.angebotsnr = '$bill_nr'") or die("MySQL select customer error. ".mysql_error()); 
		
		$rArtikel = mysql_query("SELECT a.id, a.bezeichnung, a.preis_netto, ra.art_menge, ra.einzelpreis, ra.gesamtpreis_artikel FROM artikel a, angebote r, a_artikel ra WHERE r.angebotsnr = '$bill_nr' AND r.angebotsnr = ra.angebotsnr AND ra.artnr = a.id ORDER by ra.id ASC") or die("MySQL select artikel error. ".mysql_error());
		
		$rStunden = mysql_query("SELECT * FROM a_stunden WHERE angebotsnr = '$bill_nr' ORDER BY id ASC") or die ("MySQL select r stunden error. ".mysql_error());
		
		if (mysql_num_rows($rStunden) > 0)
			$hat_stunden = 1;
		
		if (mysql_num_rows($rArtikel) > 0)
			$hat_artikel = 1;
		
		while ($row_rechnung = mysql_fetch_assoc($rAngebot))
		{
			$db_rechnungs_id = $row_rechnung['id'];
			$db_rechnungsnr = $row_rechnung['angebotsnr'];
			//$db_rechnungsdatum = $row_rechnung['rech_date'];
			$db_kundennachricht = $row_rechnung['kundennachricht'];
			$db_message = $row_rechnung['kundennachricht'];
			$db_bezahlt = $row_rechnung['bezahlt'];	
		    $db_bezahlt_datum = $row_rechnung['bezahlt_datum'];
			$db_rabatt_prozent = $row_rechnung['rabatt_prozent'];
			$db_skonto_prozent = $row_rechnung['skonto_prozent'];
			$db_mwst_prozent = $row_rechnung['mwst_prozent'];
			$db_rabatt_betrag = $row_rechnung['rabatt_betrag'];
			$db_skonto_betrag = $row_rechnung['skonto_betrag'];
			$db_mwst_betrag = $row_rechnung['mwst_betrag'];
			$db_endbetrag = $row_rechnung['endbetrag'];
			$db_editierbar = $row_rechnung['editierbar'];
		}
		
		while ($row_kunde = mysql_fetch_assoc($rKunde))
		{
			$db_kdnr = $row_kunde['id'];
			$db_vorname = $row_kunde['vorname'];
			$db_nachname = $row_kunde['nachname'];	
		}
		
		if ($hat_stunden == 1) {
			
			//$delete = mysql_query("DELETE FROM m_stunden WHERE mahnungsnr = '$rNr'");
			
			while ($row_stunden = mysql_fetch_assoc($rStunden))
			{
				$db_st_datum = $row_stunden['st_datum'];
				$db_st_ma_nr = $row_stunden['ma_nr'];
				$db_st_ma_name = $row_stunden['ma_name'];
				$db_st_arbeit_art = $row_stunden['arbeit_art'];
				$db_st_zeit_stunden = $row_stunden['zeit_stunden'];
				$db_st_euro_st = $row_stunden['euro_st'];
				$db_st_euro_gesamt = $row_stunden['euro_st_gesamt'];
				
				$insert_stunden = mysql_query("INSERT INTO r_stunden (st_datum,ma_nr,ma_name,arbeit_art,zeit_stunden,euro_st,euro_st_gesamt,rechnungsnr) VALUES ('$db_st_datum','$db_st_ma_nr','$db_st_ma_name','$db_st_arbeit_art','$db_st_zeit_stunden','$db_st_euro_st','$db_st_euro_gesamt','$new_id')") or die("MySQL insert r_stunden error. ".mysql_error());
				
			}
		}
		
		if ($hat_artikel == 1) {
			
			//$kundennachricht = "Folgende Leistungen und Artikel wurden von Ihnen noch nicht bezahlt:";
			
			//$delete = mysql_query("DELETE FROM m_artikel WHERE mahnungsnr = '$rNr'");
			
			while ($row_artikel = mysql_fetch_assoc($rArtikel))
			{
				$db_artnr = $row_artikel['id'];
				$db_art_bezeichnung = $row_artikel['bezeichnung'];
				$db_art_menge = $row_artikel['art_menge'];
				$db_einzelpreis = $row_artikel['preis_netto'];
				$db_art_gesamtpreis = $row_artikel['gesamtpreis_artikel'];
				
				$insert_artikel = mysql_query("INSERT INTO r_artikel (artnr,art_menge,einzelpreis,gesamtpreis_artikel,rechnungsnr) VALUES ('$db_artnr','$db_art_menge','$db_einzelpreis','$db_art_gesamtpreis','$new_id')") or die ("MySQL insert r_artikel error. ".mysql_error());
			}	
		}
		
		//$delete_demand_note = mysql_query("DELETE FROM mahnungen WHERE mahnungsnr = '$rNr'");
					
		$insert_angebot = mysql_query("INSERT INTO rechnungen (rechnungsnr,rechnungsdatum,kundennachricht,hat_stunden,hat_artikel,rabatt_prozent,rabatt_betrag,skonto_prozent,skonto_betrag,mwst_prozent,mwst_betrag,endbetrag,kdnr,bezahlt,bezahlt_datum,editierbar) VALUES ('$new_id','$db_rechnungsdatum','$db_kundennachricht','$hat_stunden','$hat_artikel','$db_rabatt_prozent','$db_rabatt_betrag','$db_skonto_prozent','$db_skonto_betrag','$db_mwst_prozent','$db_mwst_betrag','$db_endbetrag','$db_kdnr','$db_bezahlt','$db_bezahlt_datum','$db_editierbar')") or die("MySQL insert rechnung error. ".mysql_error());
		
		$last_id = mysql_insert_id();
		
		$log_date = date("Y-m-d H:i:s");
		$log_txt = $_SESSION['benutzername']." hat Rechnung Nr. $new_id angelegt.";
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
		
		break;
		
		case 'changePrintState':
			
			$acc_nr = isset($_POST['acc_nr']) ? $_POST['acc_nr'] : 0;
			$newState = isset($_POST['newState']) ? $_POST['newState'] : "-";
			
			if (isset($acc_nr) && $acc_nr != 0 && isset($newState)) {
			$update = mysql_query("UPDATE angebote SET gedruckt = '$newState' WHERE angebotsnr = '$acc_nr'") or die ("MySQL update angebote error. ".mysql_error());	
			}
			
			if ($update) {
				
				if ($newState == 1)
					$log_txt = $_SESSION['benutzername']." hat Angebot Nr. $acc_nr auf \"Gedruckt\" gesetzt.";
				else
					$log_txt = $_SESSION['benutzername']." hat Angebot Nr. $acc_nr auf \"Noch nicht gedruckt\" gesetzt.";
					
				$log_date = date("Y-m-d H:i:s");
				$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());	
			}
			
			break;			
		
	default:
		echo '<span class="error">unknown name</span>';
		break;	
}

?>