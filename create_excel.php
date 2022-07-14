<?php

require('required/config.php');

include('libs/PHPExcel/Classes/PHPExcel.php');
include('libs/PHPExcel/Classes/PHPExcel/Calculation.php');
include('libs/PHPExcel/Classes/PHPExcel/Cell.php');

$action = isset($_GET['action']) ? $_GET['action'] : NULL;
$date = date("d-m-Y");

if (EXCEL_VERSION == 'Excel_2003') {
	define("VERSION","Excel2003");
	$file_ending = '.xls';
}
else if (EXCEL_VERSION == 'Excel_2007') {
	define("VERSION","Excel2007");
	$file_ending = '.xlsx';
}
else if (EXCEL_VERSION == 'Excel_2010') {
	define("VERSION","Excel2010");
	$file_ending = '.xlsx';
}
else {
	define("VERSION","Excel2003");
	$file_ending = '.xls';
}

$objPHPExcel = new PHPExcel();

$validLocale = PHPExcel_Settings::setLocale(LOCALE);
if (!$validLocale) {
	echo 'Unable to set locale to '.$locale." - reverting to en_us<br />\n";
}

// Add some data

// Artikel

if ($action == 'article_xls') {
	
	$objPHPExcel->getProperties()
            ->setCreator($_SESSION['benutzername'])
            ->setLastModifiedBy($_SESSION['benutzername'])
            ->setTitle("Artikelübersicht")
            ->setSubject("Artikelübersicht")
            ->setDescription("Alle Artikel von FormerCompany")
            ->setKeywords("Artikel, FormerCompany")
            ->setCategory("Artikel");

	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ArtNr')
            ->setCellValue('B1', 'ArtikelNr Lieferant')
            ->setCellValue('C1', 'LieferantenNr')
            ->setCellValue('D1', 'Bezeichnung')
			->setCellValue('E1', 'Kategorie')
            ->setCellValue('F1', 'Preis (netto)')
			->setCellValue('G1', 'Steuersatz')
			->setCellValue('H1', 'Preis (brutto)')
            ->setCellValue('I1', 'Einheit');

	// Miscellaneous glyphs, UTF-8
	
	$get_article_data = mysql_query("SELECT * FROM artikel WHERE 1") or die("MySQL get Artikel data failed. ".mysql_error());
	
	$x = 2;
	
	while ($row_art_data = mysql_fetch_assoc($get_article_data))
	{
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$x, $row_art_data['id'])
				->setCellValue('B'.$x, $row_art_data['artnr_lieferant'])
				->setCellValue('C'.$x, $row_art_data['lieferantennr'])
				->setCellValue('D'.$x, $row_art_data['bezeichnung'])
				->setCellValue('E'.$x, $row_art_data['kategorie'])
				->setCellValue('F'.$x, $row_art_data['preis_netto'])
				->setCellValue('G'.$x, $row_art_data['steuersatz'])
				->setCellValue('H'.$x, $row_art_data['preis_brutto'])
				->setCellValue('I'.$x, $row_art_data['einheit']);
				$x++;	
	}

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Artikel');
	
	$save_path = 'excel/artikel/';
	$save_file = 'Artikel';
	$filename = $save_file."_".$date.$file_ending;

}

// Kunden

else if ($action == 'customer_xls') {
	
	$objPHPExcel->getProperties()
            ->setCreator($_SESSION['benutzername'])
            ->setLastModifiedBy($_SESSION['benutzername'])
            ->setTitle("Kundenübersicht")
            ->setSubject("Kundenübersicht")
            ->setDescription("Alle Kunden von FormerCompany")
            ->setKeywords("Kunden, FormerCompany")
            ->setCategory("Kunden");
		
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'KdNr')
            ->setCellValue('B1', 'Anrede')
            ->setCellValue('C1', 'Vorname')
            ->setCellValue('D1', 'Nachname')
			->setCellValue('E1', 'Kontennr')
            ->setCellValue('F1', 'Strasse')
			->setCellValue('G1', 'Hausnr')
			->setCellValue('H1', 'PLZ')
			->setCellValue('G1', 'Ort')
			->setCellValue('H1', 'Email')
            ->setCellValue('I1', 'Telefon');

	// Miscellaneous glyphs, UTF-8
	
	$get_article_data = mysql_query("SELECT * FROM kunden WHERE 1") or die("MySQL get Kunden data failed. ".mysql_error());
	
	$x = 2;
	
	while ($row_kd_data = mysql_fetch_assoc($get_article_data))
	{
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$x, $row_kd_data['id'])
				->setCellValue('B'.$x, $row_kd_data['anrede'])
				->setCellValue('C'.$x, $row_kd_data['vorname'])
				->setCellValue('D'.$x, $row_kd_data['nachname'])
				->setCellValue('E'.$x, $row_kd_data['kontennummer'])
				->setCellValue('F'.$x, $row_kd_data['strasse'])
				->setCellValue('G'.$x, $row_kd_data['hausnummer'])
				->setCellValue('H'.$x, $row_kd_data['ort'])
				->setCellValue('G'.$x, $row_kd_data['plz'])
				->setCellValue('H'.$x, $row_kd_data['email'])
				->setCellValue('I'.$x, $row_kd_data['telefon']);
				$x++;	
	}

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Kunden');
	
	$save_path = 'excel/kunden/';
	$save_file = 'Kunden';
	$filename = $save_file."_".$date.$file_ending;

}

// Fertige Rechnungen (Umsätze)

else if ($action == "dealings_xls") {
	
	$objPHPExcel->getProperties()
            ->setCreator($_SESSION['benutzername'])
            ->setLastModifiedBy($_SESSION['benutzername'])
            ->setTitle("Fertige Rechnungen - Umsatzübersicht")
            ->setSubject("Fertige Rechnungen - Umsatzübersicht")
            ->setDescription("Fertige Rechnungen - Umsätze von FormerCompany")
            ->setKeywords("Umsätze, FormerCompany")
            ->setCategory("Umsätze");
		
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Rechnungsnummer')
            ->setCellValue('B1', 'Rechnungsdatum')
            ->setCellValue('C1', 'Kundennummer')
            ->setCellValue('D1', 'Vorname')
			->setCellValue('E1', 'Nachname')
            ->setCellValue('F1', 'Zahlungsbetrag')
			->setCellValue('G1', 'Rabatt')
			->setCellValue('H1', 'Skonto');
			
	$get_account_data = mysql_query("SELECT r.id, r.rechnungsnr, DATE_FORMAT (r.rechnungsdatum, '%d.%m.%Y') AS rech_date, r.kdnr, r.editierbar, r.rabatt_betrag, r.skonto_betrag, r.endbetrag, k.nachname, k.vorname FROM rechnungen r, kunden k WHERE r.kdnr = k.id AND r.editierbar = '0' ORDER BY r.id ASC");
	
	$x = 2;
	
	while ($row_account_data = mysql_fetch_assoc($get_account_data))
	{
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$x, $row_account_data['rechnungsnr'])
				->setCellValue('B'.$x, $row_account_data['rech_date'])
				->setCellValue('C'.$x, $row_account_data['kdnr'])
				->setCellValue('D'.$x, $row_account_data['vorname'])
				->setCellValue('E'.$x, $row_account_data['Nachname'])
				->setCellValue('F'.$x, $row_account_data['endbetrag'])
				->setCellValue('G'.$x, $row_account_data['rabatt_betrag'])
				->setCellValue('H'.$x, $row_account_data['skonto_betrag']);
				$x++;	
	}
	
	// Etwas Design
	
	$objPHPExcel->getActiveSheet()->getStyle('A'.$x)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$x)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$x)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$x)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$x)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$x)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$x)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$x)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	
	// Umsätze berechnen
	
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$x, 'Summe:');
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$x, '=SUM(F2:F'.($x-1).')');
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$x, '=SUM(G2:G'.($x-1).')');
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$x, '=SUM(H2:H'.($x-1).')');
	
	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Fertige Rechnungen');
	
	$save_path = 'excel/rechnungen/';
	$save_file = 'Fertige_Rechnungen';
	$filename = $save_file."_".$date.$file_ending;
	
}

// Ungültige Aktion

else {
	die("Invalid action!");	
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);

// Save Excel 2007 file

ob_end_clean();
        
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, VERSION);
ob_end_clean();

$objWriter->save('php://output');

?>