<?php

class Kunde {
	
	// Variables

	private $id = "";
	private $anrede = "";
	private $vorname = "";
	private $nachname = "";
	private $kontennummer = "";
	private $strasse = "";
	private $hausnummer = "";
	private $adresszusatz_1 = "";
	private $adresszusatz_2 = "";
	private $plz = "";
	private $ort = "";
	private $email = "";
	private $telefon = "";
	
	// Construct
	
	public function __construct(){
		
	}
	
	public function Kunde($vorname,$nachname,$kontennummer,$strasse,$hausnummer,$adresszusatz_1,$adresszusatz_2,$plz,$ort,$email,$telefon) {
		$this->vorname = $vorname;
		$this->nachname = $nachname;
		$this->kontennummer = $kontennummer;
		$this->strasse = $strasse;
		$this->hausnummer = $hausnummer;
		$this->adresszusatz_1 = $adresszusatz_1;
		$this->adresszusatz_2 = $adresszusatz_2;
		$this->plz = $plz;
		$this->ort = $ort;
		$this->email = $email;
		$this->telefon = $telefon;
	}
	
	// Set
	
	public function setId($id) {
		$this->id = $id;	
	}
	
	public function getId() {
		return $this->id;	
	}
	
	public function setAnrede($anrede){
		$this->anrede = $anrede;	
	}
	
	public function setVorname($vorname){
		$this->vorname = $vorname;	
	}
	
	public function setNachname($nachname){
		$this->nachname = $nachname;	
	}
	
	public function setKontennummer($kontennummer){
		$this->kontennummer = $kontennummer;	
	}
	
	public function setStrasse($strasse){
		$this->strasse = $strasse;	
	}
	
	public function setHausnummer($hausnummer){
		$this->hausnummer = $hausnummer;	
	}
	
	public function setAdressZusatz1($adresszusatz_1){
		$this->adresszusatz_1 = $adresszusatz_1;	
	}
	
	public function setAdressZusatz2($adresszusatz_2){
		$this->adresszusatz_2 = $adresszusatz_2;	
	}
	
	public function setOrt($ort){
		$this->ort = $ort;	
	}
	
	public function setPlz($plz){
		$this->plz = $plz;	
	}
	
	public function setEmail($email){
		$this->email = $email;	
	}
	
	public function setTelefon($telefon){
		$this->telefon = $telefon;	
	}
	
	// Get
	
	public function getAnrede(){
		return $this->anrede;	
	}
	
	public function getVorname(){
		return $this->vorname;	
	}
	
	public function getNachname(){
		return $this->nachname;	
	}
	
	public function getKontennummer(){
		return $this->kontennummer;	
	}
	
	public function getStrasse(){
		return $this->strasse;	
	}
	
	public function getHausnummer(){
		return $this->hausnummer;	
	}
	
	public function getAdressZusatz1(){
		return $this->adresszusatz_1;	
	}
	
	public function getAdressZusatz2(){
		return $this->adresszusatz_2;	
	}
	
	public function getOrt(){
		return $this->ort;	
	}
	
	public function getPlz(){
		return $this->plz;	
	}
	
	public function getEmail(){
		return $this->email;	
	}
	
	public function getTelefon(){
		return $this->telefon;	
	}
	
	// functions
	
	public function save() {
		$sql = sprintf("INSERT INTO kunden VALUES('','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
		$this->getAnrede(),$this->getVorname(),$this->getNachname(),$this->getKontennummer(),$this->getStrasse(),$this->getHausnummer(),$this->getAdressZusatz1(),$this->getAdressZusatz2(),$this->getOrt(),$this->getPlz(),$this->getEmail(),$this->getTelefon());
		
		$action = mysql_query($sql) or die("MySQL insert into kunden table error. ".mysql_error());
		
		if ($action)
			return "Kunde wurde erfolgreich gespeichert.";	
		else 
			return "Beim Speichern des Kunden ist leider ein Fehler aufgetreten.";			
	}
	
	public function update() {
		/*
		$sql = "UPDATE kunden SET";
		if ($this->getAnrede() != "")
			$sql .= "anrede = '{$this->getAnrede()}',";
		if ($this->getVorname() != "")
			$sql .= "vorname = '{$this->getVorname()}',";	
		if ($this->getNachname() != "")
			$sql .= "nachname = '{$this->getNachname()}',";
		if ($this->getKontennummer() != "")
			$sql .= "kontennummer = '{$this->getKontennummer()}',";
		if ($this->getStrasse() != "")
			$sql .= "strasse = '{$this->getStrasse()}',";
		if ($this->getHausnummer() != "")
			$sql .= "hausnummer = '{$this->getHausnummer()}',";
		if ($this->getAdressZusatz1() != "") 
			$sql .= "adresszusatz_1 = '{$this->getAdressZusatz1()}',";
		if ($this->getAdressZusatz2() != "") 
			$sql .= "adresszusatz_2 = '{$this->getAdressZusatz2()}',";
		if ($this->getOrt() != "")
			$sql .= "ort = '{$this->getOrt()}',";
		if ($this->getPlz() != "")
			$sql .= "plz = '{$this->getPlz()}',";
		if ($this->getEmail() != "")
			$sql .= "email = '{$this->getEmail()}',";
		if ($this->getTelefon() != "")
			$sql .= "telefon = '{$this->getTelefon()}',";
		$sql .= "WHERE id = '{$this->getId()}'";
		*/
		
		$sql = sprintf("UPDATE kunden SET anrede = '%s', vorname = '%s', nachname = '%s', kontennummer = '%s', strasse = '%s', hausnummer = '%s', adresszusatz_1 = '%s', adresszusatz_2 = '%s', ort = '%s', plz = '%s', email = '%s', telefon = '%s' WHERE id = '%s'",$this->getAnrede(),$this->getVorname(),$this->getNachname(),$this->getKontennummer(),$this->getStrasse(),$this->getHausnummer(),$this->getAdressZusatz1(),$this->getAdressZusatz2(),$this->getOrt(),$this->getPlz(),$this->getEmail(),$this->getTelefon(),$this->getId());
		
		$action = mysql_query($sql) or die ("MySQL update kunden table error. ".mysql_error());
	
		if ($action)
		  return "Kunde wurde erfolgreich aktualisiert.";
		else
		  return "Beim Update ist ein Fehler aufgetreten.";
		
	}
	
	public function delete() {
		$sql = "DELETE FROM kunden WHERE id = '{$this->getId()}'";
		$action = mysql_query($sql) or die ("MySQL delete from kunden table error. ".mysql_error());
	  
		if ($action)
		  return "Löschen des Kunden erfolgreich.";
		else
		  return "Beim Löschen des Kunden ist leider ein Fehler aufgetreten";		
	}
	
}

?>