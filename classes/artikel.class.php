<?php

class Artikel {
	
	// Variables
	
	private $id = "";
	private $artNrLieferant = "";
	private $lieferantenNr = "";
	private $bezeichnung = "";
	private $kategorie = "";
	private $preisNetto = "";
	private $steuersatz = "";
	private $preisBrutto = "";
	private $einheit = "";
	private $image = "";
	
	// Construct
	
	public function __construct(){
		
	}
	
	public function Artikel($artNrLieferant,$lieferantenNr,$bezeichnung,$kategorie,$preisNetto,$steuersatz,$preisBrutto,$einheit) {
		$this->artNrLieferant = $artNrLieferant;
		$this->lieferantenNr = $lieferantenNr;
		$this->bezeichnung = $bezeichnung;
		$this->kategorie = $kategorie;
		$this->preisNetto = $preisNetto;
		$this->steuersatz = $steuersatz;
		$this->preisBrutto = $preisBrutto;
		$this->einheit = $einheit;		
	}
	
	// Set
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function setArtNrLieferant($artNrLieferant){
		$this->artNrLieferant = $artNrLieferant;	
	}
	
	public function setLieferantenNr($lieferantenNr) {
		$this->lieferantenNr = $lieferantenNr;	
	}
	
	public function setBezeichnung($bezeichnung){
		$this->bezeichnung = $bezeichnung;	
	}
	
	public function setKategorie($kategorie) {
		$this->kategorie = $kategorie;	
	}
	
	public function setPreisNetto($preisNetto){
		$this->preisNetto = $preisNetto;	
	}
	
	public function setSteuersatz($steuersatz){
		$this->steuersatz = $steuersatz;	
	}
	
	public function setPreisBrutto($preisBrutto){
		$this->preisBrutto = $preisBrutto;	
	}
	
	public function setEinheit($einheit){
		$this->einheit = $einheit;	
	}
	
	public function setImage($image) {
		$this->image = $image;	
	}
	
	// Get
	
	public function getId() {
		return $this->id;	
	}
	
	public function getArtNrLieferant(){
		return $this->artNrLieferant;	
	}
	
	public function getLieferantenNr() {
		return $this->lieferantenNr;	
	}
	
	public function getBezeichnung(){
		return $this->bezeichnung;	
	}
	
	public function getKategorie() {
		return $this->kategorie;	
	}
	
	public function getPreisNetto(){
		return $this->preisNetto;	
	}
	
	public function getSteuersatz(){
		return $this->steuersatz;	
	}
	
	public function getPreisBrutto(){
		return $this->preisBrutto;	
	}
	
	public function getEinheit(){
		return $this->einheit;	
	}
	
	public function getImage() {
		return $this->image;	
	}
	
	// functions
	
	public function save() {
		$sql = sprintf("INSERT INTO artikel VALUES('','%s','%s','%s','%s','%s','%s','%s','%s')",
		$this->getArtNrLieferant(),$this->getLieferantenNr(),$this->getBezeichnung(),$this->getKategorie(),$this->getPreisNetto(),DEFAULT_MWST,$this->getPreisBrutto(),$this->getEinheit());
		
		$action = mysql_query($sql) or die("MySQL insert into artikel table error. ".mysql_error());
		
		if ($action)
			return "Artikel wurde erfolgreich gespeichert.";	
		else 
			return "Beim Speichern des Artikels ist leider ein Fehler aufgetreten.";				
	}
	
	public function update() {
		$sql = sprintf("UPDATE artikel SET artnr_lieferant = '%s', lieferantennr = '%s', bezeichnung = '%s', kategorie = '%s', preis_netto = '%s', steuersatz = '%s', preis_brutto = '%s', einheit = '%s' WHERE id = '%s'",$this->getArtNrLieferant(),$this->getLieferantenNr(),$this->getBezeichnung(),$this->getKategorie(),$this->getPreisNetto(),DEFAULT_MWST,$this->getPreisBrutto(),$this->getEinheit(),$this->getId());	
		
		$action = mysql_query($sql) or die ("MySQL update artikel table error. ".mysql_error());
	
		if ($action)
		  return "Artikel wurde erfolgreich aktualisiert.";
		else
		  return "Beim Aktualisieren des Artikels ist leider ein Fehler aufgetreten.";
	}

	public function delete() {
		$sql = "DELETE FROM artikel WHERE id = '{$this->getId()}' LIMIT 1";
		$action = mysql_query($sql) or die ("MySQL delete artikel error. ".mysql_error());	
		
		if ($action)
		  return "Löschen des Artikels erfolgreich.";
		else
		  return "Beim Löschen des Artikels ist leider ein Fehler aufgetreten";		
	}
	
}

?>