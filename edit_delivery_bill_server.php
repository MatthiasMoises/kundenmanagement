<?php

session_start();

require('required/config.php');

require('functions.php');

$cmd = isset($_POST['cmd']) ? $_POST['cmd'] : NULL;
$acc_nr = isset($_POST['acc_nr']) ? $_POST['acc_nr'] : NULL;
$artikel = isset($_POST['artikel']) ? $_POST['artikel'] : NULL;

$netto = 0;
$brutto = 0;

switch($cmd) {
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

		$rechnungsnr = mysql_real_escape_string($_POST['rechnungsnr']);
        $rechnungsnr_alt = mysql_real_escape_string($_POST['rechnungsnr_alt']);

        if ($rechnungsnr == $rechnungsnr_alt) {
            $result = "update";
        }
        else {
            $query_nr = sprintf("SELECT lieferscheinnr FROM lieferscheine WHERE lieferscheinnr = '%s'",$rechnungsnr);
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

	$rechnungsnr = mysql_real_escape_string($_POST['rechnungsnr']);
	$rechnungsdatum = $_POST['rechnungsdatum'];
	$rechnungsdatum = date('Y-m-d', strtotime($rechnungsdatum)); 
	$kd_nr = mysql_real_escape_string($_POST['kd_nr']);
	$message = mysql_real_escape_string($_POST['message']);
	$rabatt_prozent = isset($_POST['rabatt_prozent']) ? $_POST['rabatt_prozent'] : 0;
	$skonto_prozent = isset($_POST['skonto_prozent']) ? $_POST['skonto_prozent'] : 0;
	$rabatt_betrag = isset($_POST['rabatt_betrag']) ? $_POST['rabatt_betrag'] : 0;
	$skonto_betrag = isset($_POST['skonto_betrag']) ? $_POST['skonto_betrag'] : 0;
	$mwst_prozent = isset($_POST['mwst_prozent']) ? $_POST['mwst_prozent'] : 0;
	$bezahlt = $_POST['bezahlt'];
	$editierbar = $_POST['editierbar'];
	
	$rabatt_betrag = str_replace(",",".",$rabatt_betrag);
	$skonto_betrag = str_replace(",",".",$skonto_betrag);	
	
	$rabatt_prozent = str_replace(",",".",$rabatt_prozent);
	$skonto_prozent = str_replace(",",".",$skonto_prozent);
	$mwst_prozent = str_replace(",",".",$mwst_prozent);

	if ($bezahlt == 1) {
		$bezahlt_datum = date("Y.m.d H:i:s");
	}
	else {
		$bezahlt_datum = "0000-00-00 00:00:00";
	}

	// Artikeldaten

	$artikelString = $_POST['artikelString'];

	// Rechnungsdaten speichern

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

		$insert_artikel = sprintf("INSERT INTO l_artikel (artnr, art_menge, einzelpreis, gesamtpreis_artikel, lieferscheinnr) VALUES ('%s','%s','%s','%s','%s')",$artikel_einzeln[0],$artikel_einzeln[2],$artikel_einzeln[3],$artikel_einzeln[4],$rechnungsnr);

		$query_artikel = mysql_query($insert_artikel) or die("MySQL insert into l_artikel table error. ".mysql_error());
	}
	
	$steuersatz = 100 + $mwst_prozent;
	
	$netto = str_replace(",",".",$netto);
	
	$brutto = $netto / 100 * $steuersatz;
	
	$brutto = str_replace(",",".",$brutto);
	
	$mwst_betrag = $brutto-$netto;
	
	$mwst_betrag = str_replace(",",".",$mwst_betrag);
	
	if ($rabatt_prozent != 0){
		$rabatt_betrag = setRabatt($brutto,$rabatt_prozent);
		$rabatt_betrag = sprintf("%.2f",$rabatt_betrag);			
		$rabatt_betrag = str_replace(",",".",$rabatt_betrag);	
		//$rabatt_betrag = number_format($rabatt_betrag,2);
	}
		
	if ($skonto_prozent != 0){
		$skonto_betrag = setSkonto($brutto, $skonto_prozent);
		$skonto_betrag = sprintf("%.2f",$skonto_betrag);		
		$skonto_betrag = str_replace(",",".",$skonto_betrag);	
		//$skonto_betrag = number_format($skonto_betrag,2);
	}
	
	$zahlungsbetrag = setEndbetrag($brutto,$rabatt_prozent,$skonto_prozent,$rabatt_betrag,$skonto_betrag);
	
	$zahlungsbetrag = str_replace(",",".",$zahlungsbetrag);
	
	$insert_account = sprintf("INSERT INTO lieferscheine (lieferscheinnr, lieferscheindatum, kundennachricht, hat_stunden, hat_artikel, rabatt_prozent, rabatt_betrag, skonto_prozent, skonto_betrag, mwst_prozent, mwst_betrag, endbetrag, kdnr, bezahlt, bezahlt_datum, editierbar) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",$rechnungsnr,$rechnungsdatum,$message,0,$hat_artikel,$rabatt_prozent,$rabatt_betrag,$skonto_prozent,$skonto_betrag,$mwst_prozent,$mwst_betrag,$zahlungsbetrag,$kd_nr,$bezahlt,$bezahlt_datum,$editierbar);

	$query_account = mysql_query($insert_account) or die ("MySQL insert into lieferscheine table error. ".mysql_error());
		
	$log_date = date("Y-m-d H:i:s");
	$log_txt = $_SESSION['benutzername']." hat Lieferschein Nr. $rechnungsnr angelegt.";
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
	
		$rechnungs_id = mysql_real_escape_string($_POST['rechnungs_id']);
		$rechnungsnr = mysql_real_escape_string($_POST['rechnungsnr']);
		$rechnungsdatum = $_POST['rechnungsdatum'];
		$rechnungsdatum = date('Y-m-d', strtotime($rechnungsdatum)); 
		$message = mysql_real_escape_string($_POST['message']);
		$kd_nr = mysql_real_escape_string($_POST['kd_nr']);
		$rabatt_prozent = isset($_POST['rabatt_prozent']) ? $_POST['rabatt_prozent'] : 0;
		$skonto_prozent = isset($_POST['skonto_prozent']) ? $_POST['skonto_prozent'] : 0;
		$rabatt_betrag = isset($_POST['rabatt_betrag']) ? $_POST['rabatt_betrag'] : 0;
		$skonto_betrag = isset($_POST['skonto_betrag']) ? $_POST['skonto_betrag'] : 0;
		$mwst_prozent = isset($_POST['mwst_prozent']) ? $_POST['mwst_prozent'] : 0;
		$bezahlt = $_POST['bezahlt'];
		$editierbar = $_POST['editierbar'];
		
		$rabatt_betrag = str_replace(",",".",$rabatt_betrag);
		$skonto_betrag = str_replace(",",".",$skonto_betrag);		
		
		$rabatt_prozent = str_replace(",",".",$rabatt_prozent);
		$skonto_prozent = str_replace(",",".",$skonto_prozent);
		$mwst_prozent = str_replace(",",".",$mwst_prozent);
		
		if ($bezahlt == 1) {
			$bezahlt_datum = date("Y.m.d H:i:s");	
		}
		else {
			$bezahlt_datum = "0000-00-00 00:00:00";	
		}
		
		// Artikeldaten
		
		$artikelString = $_POST['artikelString'];
		
		// Rechnungsdaten speichern
		
		if ($artikelString != NULL) {
			$hat_artikel = 1;	
		}
		else {
			$hat_artikel = 0;	
		}

		// Artikeldaten speichern
		
	 $first_delete_artikel = mysql_query("DELETE FROM l_artikel WHERE lieferscheinnr = '$rechnungsnr'") or die ("MySQL l_artikel first_delete error. ".mysql_error());

		foreach ($artikelString as $as)
		{
			$artikel_einzeln = array();
			$artikel_einzeln = explode(';',$as);
			
			$artikel_einzeln[3] = str_replace(",",".",$artikel_einzeln[3]);
			$artikel_einzeln[4] = str_replace(",",".",$artikel_einzeln[4]);
			
			$netto += $artikel_einzeln[4];			

		 	$insert_artikel = sprintf("INSERT INTO l_artikel (artnr, art_menge, einzelpreis, gesamtpreis_artikel, lieferscheinnr) VALUES ('%s','%s','%s','%s','%s')",$artikel_einzeln[0],$artikel_einzeln[2],$artikel_einzeln[3],$artikel_einzeln[4],$rechnungsnr);

		 $query_artikel = mysql_query($insert_artikel) or die("MySQL insert into l_artikel table error. ".mysql_error());
		}
		
		$steuersatz = 100 + $mwst_prozent;
		
		$netto = str_replace(",",".",$netto);
		
		$brutto = $netto / 100 * $steuersatz;
		
		$brutto = str_replace(",",".",$brutto);
		
		$mwst_betrag = $brutto-$netto;
		
		$mwst_betrag = str_replace(",",".",$mwst_betrag);
		
		if ($rabatt_prozent != 0){
			$rabatt_betrag = setRabatt($brutto,$rabatt_prozent);
			$rabatt_betrag = sprintf("%.2f",$rabatt_betrag);			
			$rabatt_betrag = str_replace(",",".",$rabatt_betrag);	
			//$rabatt_betrag = number_format($rabatt_betrag,2);
		}
		
		if ($skonto_prozent != 0){
			$skonto_betrag = setSkonto($brutto, $skonto_prozent);
			$skonto_betrag = sprintf("%.2f",$skonto_betrag);		
			$skonto_betrag = str_replace(",",".",$skonto_betrag);	
			//$skonto_betrag = number_format($skonto_betrag,2);
		}
	
		$zahlungsbetrag = setEndbetrag($brutto,$rabatt_prozent,$skonto_prozent,$rabatt_betrag,$skonto_betrag);
		
		$update_account = sprintf("UPDATE lieferscheine SET lieferscheinnr = '%s',lieferscheindatum = '%s', kundennachricht = '%s', hat_stunden = '%s', hat_artikel = '%s', endbetrag = '%s', rabatt_prozent = '%s', rabatt_betrag = '%s', skonto_prozent = '%s', skonto_betrag = '%s', mwst_prozent = '%s', mwst_betrag = '%s', kdnr = '%s', bezahlt = '%s', bezahlt_datum = '%s', editierbar = '%s' WHERE id = '%s'",$rechnungsnr,$rechnungsdatum,$message,0,$hat_artikel,$zahlungsbetrag,$rabatt_prozent, $rabatt_betrag,$skonto_prozent,$skonto_betrag,$mwst_prozent,$mwst_betrag,$kd_nr,$bezahlt,$bezahlt_datum,$editierbar,$rechnungs_id);
		
		$query_account = mysql_query($update_account) or die ("MySQL update lieferscheine table error. ".mysql_error());
		
		$log_date = date("Y-m-d H:i:s");
		$log_txt = $_SESSION['benutzername']." hat Lieferschein Nr. $rechnungsnr aktualisiert.";
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
		
		break;
	
	case 'delete':
		$delete = "DELETE l.*, la.* FROM lieferscheine l, l_artikel la WHERE l.lieferscheinnr = '$acc_nr' AND la.lieferscheinnr = l.lieferscheinnr";	
		
		mysql_query($delete) or die ("MySql delete delivery bill error. ".mysql_error());	
		
		$log_date = date("Y-m-d H:i:s");
		$log_txt = $_SESSION['benutzername']." hat Lieferschein Nr. $acc_nr gel&ouml;scht.";
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
			
		break;
		
	case 'createAccount':
		$bill_nr = isset($_POST['bill_nr']) ? $_POST['bill_nr'] : "";
		$new_id = isset($_POST['id']) ? $_POST['id'] : "";
		
		$db_rechnungsdatum = date("Y-m-d");
		
		$rRechnung = mysql_query("SELECT id, lieferscheinnr, DATE_FORMAT (lieferscheindatum, '%d.%m.%Y') AS rech_date, kundennachricht, hat_stunden, hat_artikel, rabatt_prozent, rabatt_betrag, skonto_prozent, skonto_betrag, mwst_prozent, mwst_betrag, endbetrag, kdnr, bezahlt, editierbar FROM lieferscheine WHERE lieferscheinnr = '$bill_nr'") or die("MySQL select lieferschein data error. ".mysql_error());
		
		$rKunde = mysql_query("SELECT k.* FROM kunden k, lieferscheine l WHERE l.kdnr = k.id AND l.lieferscheinnr = '$bill_nr'") or die("MySQL select customer error. ".mysql_error()); 
		
		$rArtikel = mysql_query("SELECT a.id, a.bezeichnung, a.preis_netto, la.art_menge, la.einzelpreis, la.gesamtpreis_artikel FROM artikel a, lieferscheine l, l_artikel la WHERE l.lieferscheinnr = '$bill_nr' AND l.lieferscheinnr = la.lieferscheinnr AND la.artnr = a.id ORDER by la.id ASC") or die("MySQL select artikel error. ".mysql_error());
		
		if (mysql_num_rows($rArtikel) > 0)
			$hat_artikel = 1;
		
		while ($row_rechnung = mysql_fetch_assoc($rRechnung))
		{
			$db_rechnungs_id = $row_rechnung['id'];
			$db_rechnungsnr = $row_rechnung['lieferscheinnr'];
			$db_message = $row_rechnung['kundennachricht'];
			$db_bezahlt = $row_rechnung['bezahlt'];	
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
		
		if ($hat_artikel == 1) {
			
			//$delete = mysql_query("DELETE FROM l_artikel WHERE lieferscheinnr = '$bill_nr'");
			
			while ($row_artikel = mysql_fetch_assoc($rArtikel))
			{
				$db_artnr = $row_artikel['id'];
				$db_art_bezeichnung = $row_artikel['bezeichnung'];
				$db_art_menge = $row_artikel['art_menge'];
				$db_einzelpreis = $row_artikel['preis_netto'];
				$db_art_gesamtpreis = $row_artikel['gesamtpreis_artikel'];
				
				$insert_artikel = mysql_query("INSERT INTO r_artikel (artnr,art_menge,einzelpreis,gesamtpreis_artikel,rechnungsnr) VALUES ('$db_artnr','$db_art_menge','$db_einzelpreis','$db_art_gesamtpreis','$new_id')") or die ("MySQL insert m_artikel error. ".mysql_error());
			}	
		}
		
		//$delete_demand_note = mysql_query("DELETE FROM lieferscheine WHERE lieferscheinnr = '$bill_nr'");
					
		$insert_mahnung = mysql_query("INSERT INTO rechnungen (rechnungsnr,rechnungsdatum,kundennachricht,hat_stunden,hat_artikel,rabatt_prozent,rabatt_betrag,skonto_prozent,skonto_betrag,mwst_prozent,mwst_betrag,endbetrag,kdnr,bezahlt,bezahlt_datum,editierbar) VALUES ('$new_id','$db_rechnungsdatum','$kundennachricht','$hat_stunden','$hat_artikel','$db_rabatt_prozent','$db_rabatt_betrag','$db_skonto_prozent','$db_skonto_betrag','$db_mwst_prozent','$db_mwst_betrag','$db_endbetrag','$db_kdnr','0','','1')") or die("MySQL insert rechnungen error. ".mysql_error());
		
		//$last_id = mysql_insert_id();
		
		$log_date = date("Y-m-d H:i:s");
		$log_txt = $_SESSION['benutzername']." hat Rechnung Nr. $new_id angelegt.";
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
		
		break;
		
		case 'changePrintState':
			
			$acc_nr = isset($_POST['acc_nr']) ? $_POST['acc_nr'] : 0;
			$newState = isset($_POST['newState']) ? $_POST['newState'] : "-";
			
			if (isset($acc_nr) && $acc_nr != 0 && isset($newState)) {
			$update = mysql_query("UPDATE lieferscheine SET gedruckt = '$newState' WHERE lieferscheinnr = '$acc_nr'") or die ("MySQL update lieferscheine error. ".mysql_error());	
			}
			
			if ($update) {
				
				if ($newState == 1)
					$log_txt = $_SESSION['benutzername']." hat Lieferschein Nr. $acc_nr auf \"Gedruckt\" gesetzt.";
				else
					$log_txt = $_SESSION['benutzername']." hat Lieferschein Nr. $acc_nr auf \"Noch nicht gedruckt\" gesetzt.";
					
				$log_date = date("Y-m-d H:i:s");
				$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());	
			}
			
			break;				
		
	default:
		echo '<span class="error">unknown name</span>';
		break;	
}

?>