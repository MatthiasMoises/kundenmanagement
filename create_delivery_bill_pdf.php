<?php

session_start();

require('required/config.php');
require('libs/fpdf16/fpdf.php');

if (!$_SESSION['benutzername'])
	die("Sie haben keine Berechtigung dieses Dokument aufzurufen!");

$keycode = isset($_GET['k']) ? $_GET['k'] : NULL;
$acc_code = isset($_GET['r']) ? $_GET['r'] : NULL;
$m_code = isset($_GET['m']) ? $_GET['m'] : NULL;
$f_code = isset($_GET['f']) ? $_GET['f'] : NULL;
$base = "";

$rRechnung = mysql_query("SELECT DATE_FORMAT (lieferscheindatum, '%d.%m.%Y') AS rech_date, kundennachricht, hat_stunden, hat_artikel, rabatt_prozent, rabatt_betrag, skonto_prozent, skonto_betrag, mwst_prozent, mwst_betrag, endbetrag, bezahlt, editierbar FROM lieferscheine WHERE lieferscheinnr = '$acc_code'") or die("MySQL select delivery bill data error. ".mysql_error());

$rKunde = mysql_query("SELECT k.* FROM kunden k, lieferscheine l WHERE l.kdnr = k.id AND l.lieferscheinnr = '$acc_code'") or die("MySQL select customer error. ".mysql_error()); 

while ($row_rechnung = mysql_fetch_assoc($rRechnung))
{
	$db_rechnungsdatum = $row_rechnung['rech_date'];
	$db_message = $row_rechnung['kundennachricht'];
	$db_hat_stunden = $row_rechnung['hat_stunden'];
	$db_hat_artikel = $row_rechnung['hat_artikel'];
	$db_rabatt_prozent = $row_rechnung['rabatt_prozent'];
	$db_skonto_prozent = $row_rechnung['skonto_prozent'];
	$db_mwst_prozent = $row_rechnung['mwst_prozent'];
	$db_rabatt_betrag = $row_rechnung['rabatt_betrag'];
	$db_skonto_betrag = $row_rechnung['skonto_betrag'];
	$db_mwst_betrag = $row_rechnung['mwst_betrag'];
	$db_endbetrag = $row_rechnung['endbetrag'];
	$db_bezahlt = $row_rechnung['bezahlt'];	
	$db_editierbar = $row_rechnung['editierbar'];
	
	$db_rabatt_prozent = str_replace(".",",",$db_rabatt_prozent);
	$db_skonto_prozent = str_replace(".",",",$db_skonto_prozent);
	$db_mwst_prozent = str_replace(".",",",$db_mwst_prozent);
	
}

while ($row_kunde = mysql_fetch_assoc($rKunde))
{
	$db_kdnr = $row_kunde['id'];
	$db_anrede = $row_kunde['anrede'];
	$db_vorname = $row_kunde['vorname'];
	$db_nachname = $row_kunde['nachname'];	
	$db_strasse = $row_kunde['strasse'];
	$db_hausnummer = $row_kunde['hausnummer'];
	$db_adresszusatz_1 = $row_kunde['adresszusatz_1'];
	$db_adresszusatz_2 = $row_kunde['adresszusatz_2'];
	$db_plz = $row_kunde['plz'];
	$db_ort = $row_kunde['ort'];
}

if ($f_code == "all")
	$base = "show_all_delivery_bills.php";

$folderdate = date("Y-m");

$folder = "pdf/lieferscheine_pdf/".$folderdate;

/*
if (!is_dir($folder))
	mkdir($folder);
*/

$pdfdatei = "Lieferschein_Nr_".$acc_code.".pdf";

if (!$keycode || $keycode == "" ||(strlen($keycode) <> 32))
	die("Ung&uuml;ltiger PDF-Key!");

if (!$acc_code || $acc_code == "")	
	die("Lieferscheinnummer nicht angegeben!");
	
class PDF extends FPDF
{
	private $endbetrag_netto = 0;
	private $endbetrag_brutto = 0;
	private $rechnungsbetrag = 0;
	private $mwst = 0;
	private $rabatt_prozent = 0;
	private $skonto_prozent = 0;
	private $mwst_prozent = 0;
	private $rabatt_betrag = 0;
	private $skonto_betrag = 0;
	private $mwst_betrag = 0;
    
//Page header
function Header()
{
	//Logo
	$this->Image('img/logo_small.gif',9,8,50);
	//Arial bold 15
	$this->SetFont('Arial','',10);
	$this->SetY(38);
    	//Info
	$this->Cell(0,0,"Ihr Fachgesch?ft f?r:",0,0);
	$this->Ln(5);
	$this->Cell(0,0,"Sat-Anlagen, Telefonanlagen, Geb?udeautomation, Verkauf und Reparatur aller Klein- u. Gro?ger?te, Sanit?rinstallation",10,0);
	// Linienbreite einstellen, 1 mm
	$this->SetLineWidth(0.2);
	// Linie zeichnen
	$this->Line(10, 45.5, 200, 45.5);
	$this->Ln(15.4);
	if ($this->PageNo() == 1) {
		//Move to the right
		$this->Cell(80);
		$this->SetRightMargin(12.6);	
		//Contact Data
		$this->SetFont('Arial','B',9);	
		$this->Cell(0,0,UN_NAME,0,1,'R');
		$this->SetFont('Arial','',9);	
		$this->Ln(4.4);
		$this->Cell(0,0,UN_STRASSE,0,1,'R');
		$this->Ln(4.4);
	    	$this->Cell(0,0,UN_PLZ." ".UN_ORT,0,1,'R');
		$this->Ln(7);
		$this->Cell(0,0,UN_TELEFON,0,1,'R');
		$this->Ln(4.4);
		$this->Cell(0,0,UN_FAX,0,1,'R');
		$this->Ln(4.4);
		$this->Cell(0,0,UN_MOBIL,0,1,'R');
		$this->Ln(4.4);
		$this->Cell(0,0,"E-Mail: ".UN_EMAIL,0,1,'R');
		$this->Ln(6.6);
		$this->Cell(0,0,UN_STEUERNR,0,1,'R');
		$this->SetRightMargin(10);
	}
}

//Page footer
function Footer()
{
	//Page number
	$this->SetY(-12);
	$this->Cell(0,0,'Seite '.$this->PageNo().' von {nb}',0,0,'C');
}

// Artikel Tabelle
function artikelTable($headerArtikel,$dataArtikel)
{  
	// get data from db
	
	$acc_code = $_GET['r'];
	$artikel_ok = false;
	$art_gesamtpreis_netto = 0;
	$art_gesamtpreis_netto_raw = 0;
	$db_art_gesamtpreis_raw = 0;
	$db_art_gesamtpreis = 0;
	
	$rArtikel = mysql_query("SELECT a.id, a.bezeichnung, a.preis_netto, a.einheit, la.art_menge, la.einzelpreis, la.gesamtpreis_artikel FROM artikel a, lieferscheine l, l_artikel la WHERE l.lieferscheinnr = '$acc_code' AND l.lieferscheinnr = la.lieferscheinnr AND la.artnr = a.id ORDER BY a.bezeichnung ASC") or die("MySQL select artikel error. ".mysql_error());
	
	if (mysql_num_rows($rArtikel) > 0)
		$artikel_ok = true;
		
	if ($artikel_ok == true)
	{	
		
		//Column widths
		$w=array(12,147,15,16);
		//Header
		$this->SetFont('Arial','B',9);
                $this->SetLineWidth(0.2);
		for($i=0;$i<count($headerArtikel);$i++) {
			if ($i==count($headerArtikel)-1)
				$this->Cell($w[$i],6,$headerArtikel[$i],'LRT',0,'C');
			else
				$this->Cell($w[$i],6,$headerArtikel[$i],'LT',0,'C');
		}
		$this->SetFont('Arial','',9);
		$this->Ln();

		while ($row_artikel = mysql_fetch_assoc($rArtikel))
		{
			$db_artnr = $row_artikel['id'];
			$db_art_bezeichnung = $row_artikel['bezeichnung'];
			$db_art_menge = $row_artikel['art_menge'];
			$db_art_einheit = $row_artikel['einheit'];
			//$db_art_netto = $row_artikel['preis_netto'];
			$db_einzelpreis = $row_artikel['einzelpreis'];
			//$db_art_netto = sprintf("%.2f",$db_art_netto);
			$db_einzelpreis = sprintf("%.2f",$db_einzelpreis);
			
			$db_art_gesamtpreis_raw = $row_artikel['gesamtpreis_artikel'];
			$db_art_gesamtpreis = sprintf("%.2f",$db_art_gesamtpreis_raw);
			
			$art_gesamtpreis_netto_raw+=$db_art_gesamtpreis_raw;
			$art_gesamtpreis_netto = sprintf("%.2f",$art_gesamtpreis_netto_raw);

			$h=5;
			// Last Row before Pagebreak?
			$border = ($this->GetY()+(2*$h) > $this->PageBreakTrigger) ? 'LTB' : 'LT';
			$border_right = ($this->GetY()+(2*$h) > $this->PageBreakTrigger) ? 'LRTB' : 'LRT';
			$this->Cell($w[0],$h,$db_artnr,$border,'','C');
			$this->Cell($w[1],$h,utf8_decode($db_art_bezeichnung),$border);
			$this->Cell($w[2],$h,str_replace(".",",",$db_art_menge),$border,'','C');
			$this->Cell($w[3],$h,$db_art_einheit,$border_right,'','C');
			//$this->Cell($w[4],$h,str_replace(".",",",$db_art_gesamtpreis),$border_right,'','C');
			$this->Ln();
			
		}

        $this->setEndbetragNetto($art_gesamtpreis_netto_raw);
		
		$this->Cell(array_sum($w),0,'','T');
		$this->Ln(4);
		$this->SetFont('Arial','B',9);
		//$this->Cell(0,0,"Zwischensumme: ".str_replace(".",",",$art_gesamtpreis_netto)." EUR",0,0,'R');
		//$this->SetFont('Arial','',9);
		//$this->Ln(8);
	}
}

// Tools

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function setEndbetragNetto($betrag)
{
    $this->endbetrag_netto += $betrag;
}

function getEndbetragNetto()
{
    return $this->endbetrag_netto;
}

function setEndbetragBrutto($db_mwst_prozent)
{
    $rechnungssteuer = 100+$db_mwst_prozent;
    $this->endbetrag_brutto = $this->endbetrag_netto  / 100 * $rechnungssteuer;
	$this->endbetrag_brutto = sprintf("%.2f",$this->endbetrag_brutto);
    return str_replace(".",",",$this->endbetrag_brutto);
}

function getEndbetragBrutto()
{
    return $this->endbetrag_brutto;
}

function setMWST($db_mwst_prozent){
	$this->mwst = $this->endbetrag_netto / 100 * $db_mwst_prozent;
	return $this->mwst;	
}

function getMWST(){
	return $this->mwst;	
}

function setRabattProzent($db_rabatt_prozent){
	$this->rabatt_prozent = $this->endbetrag_brutto / 100 * $db_rabatt_prozent;	
}

function getRabattProzent(){
	return $this->rabatt_prozent;	
}

function getRabattBetrag(){
	return $this->rabatt_betrag;	
}


function setSkontoProzent($db_skonto_prozent){
	$this->skonto_prozent = $this->endbetrag_brutto / 100 * $db_skonto_prozent;	
	$this->skonto_prozent = sprintf("%.2f",$this->skonto_prozent);
	return str_replace(".",",",$this->skonto_prozent);
}


function getSkontoBetrag(){
	return $this->skonto_betrag;	
}

function setRechnungsbetragProzent(){
	$this->rechnungsbetrag = $this->endbetrag_brutto - $this->getRabattProzent() - $this->getSkontoProzent();	
	$this->rechnungsbetrag = sprintf("%.2f",$this->rechnungsbetrag);
	return str_replace(".",",",$this->rechnungsbetrag);
}

function setRechnungsbetragBetrag(){
	$this->rechnungsbetrag = $this->endbetrag_brutto - $this->getRabattBetrag() - $this->getSkontoBetrag();	
	$this->rechnungsbetrag = sprintf("%.2f",$this->rechnungsbetrag);
	return str_replace(".",",",$this->rechnungsbetrag);
}

function setRechnungsbetrag($db_endbetrag){
	$this->rechnungsbetrag = $db_endbetrag;
	$this->rechnungsbetrag = sprintf("%.2f",$this->rechnungsbetrag);	
	return str_replace(".",",",$this->rechnungsbetrag);
}

function getRechnungsbetrag(){
	return $this->rechnungsbetrag;
}

function rundeBetrag($betrag) {
	$betrag = sprintf("%.2f",$betrag);
	$betrag = str_replace(".",",",$betrag);
	return $betrag;
}

}

//Instanciation of inherited class
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->SetAuthor("FormerCompany",true);
$pdf->SetAutoPageBreak(true,30);
$pdf->SetTopMargin(16.9);

//Column titles
$headerArtikel = array('ArtNr','Bezeichnung','Menge','Einheit');
//Data loading
$dataArtikel = "";

// function
$pdf->setRabattProzent($db_rabatt_prozent);

// Anschrift
//$pdf->SetFillColor(244, 244, 244);
//$pdf->Rect(10, 47, 95, 35, 'F');
$pdf->SetY(60);
$pdf->SetLeftMargin(24.1);
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,0,utf8_decode($db_anrede),0,0);
$pdf->Ln(4.2);
$pdf->Cell(0,0,utf8_decode($db_vorname." ".$db_nachname),0,0);
$pdf->Ln(4.2);
if (isset($db_adresszusatz_1) && $db_adresszusatz_1 != "") {
	$pdf->Cell(0,0,utf8_decode($db_adresszusatz_1),0,0);
	$pdf->Ln(4.2);
}
if (isset($db_adresszusatz_2) && $db_adresszusatz_2 != "") {
	$pdf->Cell(0,0,utf8_decode($db_adresszusatz_2),0,0);
	$pdf->Ln(4.2);
}
$pdf->Cell(0,0,utf8_decode($db_strasse." ".$db_hausnummer),0,0);
$pdf->Ln(4.2);
$pdf->Cell(0,0,utf8_decode($db_plz." ".$db_ort),0,0);
$pdf->SetY(103);
$pdf->SetX(10);

// Rechnungsdaten
$pdf->SetFillColor(210, 210, 210);
$pdf->Rect(10, 100, 190, 6, 'F');
$pdf->SetLeftMargin(0);
$pdf->Cell(0,0,"Lieferschein vom ".$db_rechnungsdatum,0,1,'L'); // Betreff
$pdf->Ln(8);
$pdf->SetFont('Arial','',9);
if ($db_message != "") {
	$pdf->SetFont('Arial','B',9);
	$pdf->SetLeftMargin(10);
	$pdf->MultiCell(75,4.2,utf8_decode($db_message),0,'L',0); 
	$pdf->Ln(8);
	$pdf->SetY(113);
	$pdf->SetFont('Arial','',9);
}
//Move to the right
$pdf->Cell(80);
$pdf->SetRightMargin(12.6);
$pdf->Cell(0,0,"Lieferscheinnummer: ".$acc_code,0,1,'R');
$pdf->Ln(4.2);
$pdf->Cell(0,0,"Kundennummer: ".$db_kdnr,0,1,'R');
$pdf->SetLeftMargin(10);
if ($db_message != ""){
	$pdf->Ln(10);
}
else {
	$pdf->Ln(2);	
}

$pdf->SetFont('Arial','B',9);
$pdf->Cell(0,0,"Alle gelieferten Waren in der ?bersicht:",0,1,'L');
$pdf->SetFont('Arial','',9);
$pdf->Ln(4);

if ($db_hat_artikel){
	$pdf->artikelTable($headerArtikel,$dataArtikel);
	$pdf->Ln(7);
}

/*
$pdf->CheckPageBreak(35);
$y=$pdf->GetY();
$pdf->Line(150,$y,200,$y);
$pdf->Ln(3);	
$pdf->Cell(0,0,"Netto gesamt: ".$pdf->rundeBetrag($pdf->getEndbetragNetto())." EUR",0,0,'R');
$pdf->Ln(5);
$pdf->Cell(0,0,"MWST ".$db_mwst_prozent."%: ".$pdf->rundeBetrag($pdf->setMWST($db_mwst_prozent))." EUR",0,0,'R');
$pdf->Ln(5);
$pdf->Cell(0,0,"Brutto gesamt: ".$pdf->setEndbetragBrutto($db_mwst_prozent)." EUR",0,0,'R');

if ($db_rabatt_betrag != 0 || $db_rabatt_prozent != 0 || $db_skonto_betrag != 0 || $db_skonto_prozent != 0) {
	$pdf->Ln(3);
	$y=$pdf->GetY();
	$pdf->Line(150,$y,200,$y);	
}

if ($db_rabatt_prozent != 0 && $db_rabatt_betrag != 0) {
	$pdf->Ln(5);
	$pdf->Cell(0,0,"Rabatt: ".$db_rabatt_prozent."%: ".$pdf->rundeBetrag($db_rabatt_betrag)." EUR",0,0,'R');
}
if ($db_skonto_prozent != 0 && $db_skonto_betrag != 0) {
	$pdf->Ln(5);
	$pdf->Cell(0,0,"Skonto: ".$db_skonto_prozent."%: ".$pdf->rundeBetrag($db_skonto_betrag)." EUR",0,0,'R');
}
if ($db_rabatt_betrag != 0 && $db_rabatt_prozent == 0) {
	$pdf->Ln(5);
	$pdf->Cell(0,0,"Rabatt: ".$pdf->rundeBetrag($db_rabatt_betrag)." EUR",0,0,'R');
}
if ($db_skonto_betrag != 0 && $db_skonto_prozent == 0) {
	$pdf->Ln(5);
	$pdf->Cell(0,0,"Skonto: ".$pdf->rundeBetrag($db_skonto_betrag)." EUR",0,0,'R');
}

$pdf->Ln(3);
$y=$pdf->GetY();
$pdf->Line(150,$y,200,$y);
$pdf->Ln(3);	
$pdf->SetFont('Arial','B',9);
$pdf->Cell(0,0,"Warenbetrag: ".$pdf->setRechnungsbetrag($db_endbetrag)." EUR",0,0,'R');
$pdf->SetFont('Arial','',9);

*/

//Footer last page
$pdf->SetAutoPageBreak(false);
//Position at 1.5 cm from bottom
$pdf->SetY(-35);
$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,0,DELIVERYTEXT,0,0,'C'); $pdf->Ln(6);
$pdf->SetFont('Arial','B',8);	
$pdf->Cell(0,0,DELIVERY_INFO_TXT,0,0,'C'); $pdf->Ln(4);
//Arial italic 8
$pdf->SetFont('Arial','',8);
// Footer text
$pdf->Cell(0,0,FOOTER_DEL_1,0,0,'C'); $pdf->Ln(4);
$pdf->Cell(0,0,FOOTER_DEL_2,0,0,'C'); $pdf->Ln(4);
$pdf->Cell(0,0,FOOTER_DEL_3,0,0,'C'); $pdf->Ln(6);

//Output
$pdf->Output($pdfdatei,$m_code);

echo "<p>";
echo "Lieferschein Nr. ".$acc_code." erfolgreich im Ordner ".$folder." gespeichert!";
echo "</p>";

echo "<p>";
echo "<a href='$base'>zur&uuml;ck</a>";
echo "</p>";

?>
