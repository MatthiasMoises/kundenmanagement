<?php

class Benutzer {
	
	// Variables
	
	private $id = "";
	private $benutzername = "";
	private $passwort = "";
	private $kuerzel = "";
	private $nachname = "";
	private $vorname = "";
	private $strasse = "";
	private $hausnummer = "";
	private $plz = "";
	private $ort = "";
	private $telefon = "";
	private $stundensatz = "";
	private $email = "";
	private $loginDatum = "";
	private $admin = "";
	private $gesperrt = "";
	
	// Construct
	
	public function __construct() {
		
	}
	
	public function Benutzer($benutzername,$passwort,$kuerzel,$nachname,$vorname,$strasse,$hausnummer,$plz,$ort,$telefon,$email,$stundensatz,$admin,$gesperrt) {
		$this->benutzername = $benutzername;
		$this->passwort = $passwort;
		$this->kuerzel = $kuerzel;
		$this->nachname = $nachname;
		$this->vorname = $vorname;
		$this->strasse = $strasse;
		$this->hausnummer = $hausnummer;
		$this->plz = $plz;
		$this->ort = $ort;
		$this->telefon = $telefon;
		$this->email = $email;
		$this->stundensatz = $stundensatz;
		$this->admin = $admin;
		$this->gesperrt = $gesperrt;
	}
	
	// Get & Set
	
	public function setId($id) {
		$this->id = $id;		
	}
	
	public function getId() {
		return $this->id;	
	}
	
	public function setBenutzername($benutzername) {
		$this->benutzername = $benutzername;	
	}
	
	public function getBenutzername() {
		return $this->benutzername;	
	}
	
	public function setPasswort($passwort) {
		$this->passwort = $passwort;	
	}
	
	public function getPasswort() {
		return $this->passwort;	
	}
	
	public function setKuerzel($kuerzel) {
		$this->kuerzel = $kuerzel;	
	}
	
	public function getKuerzel() {
		return $this->kuerzel;	
	}
	
	public function setNachname($nachname) {
		$this->nachname = $nachname;	
	}
	
	public function getNachname() {
		return $this->nachname;	
	}
	
	public function setVorname($vorname) {
		$this->vorname = $vorname;	
	}
	
	public function getVorname() {
		return $this->vorname;	
	}
	
	public function setStrasse($strasse) {
		$this->strasse = $strasse;	
	}
	
	public function getStrasse() {
		return $this->strasse;	
	}
	
	public function setHausnummer($hausnummer) {
		$this->hausnummer = $hausnummer;	
	}
	
	public function getHausnummer() {
		return $this->hausnummer;	
	}
	
	public function setPlz($plz) {
		$this->plz = $plz;	
	}
	
	public function getPlz() {
		return $this->plz;	
	}
	
	public function setOrt($ort) {
		$this->ort = $ort;
	}	
	
	public function getOrt() {
		return $this->ort;	
	}
	
	public function setTelefon($telefon) {
		$this->telefon = $telefon;	
	}
	
	public function getTelefon() {
		return $this->telefon;	
	}
	
	public function setStundensatz($stundensatz) {
		$this->stundensatz = $stundensatz;	
	}
	
	public function getStundensatz() {
		return $this->stundensatz;	
	}
	
	public function setEmail($email) {
		$this->email = $email;	
	}
	
	public function getEmail() {
		return $this->email;	
	}
	
	public function setLetzterLogin($loginDatum) {
		$this->loginDatum = $loginDatum;	
	}
	
	public function getLetzterLogin() {
		return $this->loginDatum;	
	}
	
	public function setAdmin($admin) {
		$this->admin = $admin;	
	}
	
	public function getAdmin() {
		return $this->admin;	
	}
	
	public function setGesperrt($gesperrt) {
		$this->gesperrt = $gesperrt;	
	}
	
	public function getGesperrt() {
		return $this->gesperrt;	
	}
	
	// functions
	
	public function save() {
		
		$this->setPasswort(md5($this->getPasswort()));
		
		$sql = sprintf("INSERT INTO benutzer VALUES('','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','','%s','','','','','%s')",
		$this->getBenutzername(),$this->getPasswort(),$this->getKuerzel(),$this->getNachname(),$this->getVorname(),$this->getStrasse(),$this->getHausnummer(),$this->getPlz(),$this->getOrt(),$this->getTelefon(),$this->getStundensatz(),$this->getEmail(),$this->getAdmin(),$this->getGesperrt());
		
		$action = mysql_query($sql) or die ("MySQL insert new user error. ".mysql_error());
		
		if ($action) 
			echo "Benutzer erfolgreich gespeichert.";
		else
			echo "Beim Speichern des Benutzers ist leider ein Fehler aufgetreten.";
	}
	
	public function update() {
		
		if ($this->getPasswort() != "") {
			$this->setPasswort(md5($this->getPasswort()));
			$sql = sprintf("UPDATE benutzer SET benutzername = '%s', passwort = '%s', kuerzel = '%s', name = '%s', vorname = '%s', strasse = '%s', hausnummer = '%s', plz = '%s', ort = '%s', telefon = '%s', stundensatz = '%s', email = '%s', ist_admin = '%s', gesperrt = '%s' WHERE id = '%s'",$this->getBenutzername(),$this->getPasswort(),$this->getKuerzel(),$this->getNachname(),$this->getVorname(),$this->getStrasse(),$this->getHausnummer(),$this->getPlz(),$this->getOrt(),$this->getTelefon(),$this->getStundensatz(),$this->getEmail(),$this->getAdmin(),$this->getGesperrt(),$this->getId());	
		}
		else {
			$sql = sprintf("UPDATE benutzer SET benutzername = '%s', kuerzel = '%s', name = '%s', vorname = '%s', strasse = '%s', hausnummer = '%s', plz = '%s', ort = '%s', telefon = '%s', stundensatz = '%s', email = '%s', ist_admin = '%s', gesperrt = '%s' WHERE id = '%s'",$this->getBenutzername(),$this->getKuerzel(),$this->getNachname(),$this->getVorname(),$this->getStrasse(),$this->getHausnummer(),$this->getPlz(),$this->getOrt(),$this->getTelefon(),$this->getStundensatz(),$this->getEmail(),$this->getAdmin(),$this->getGesperrt(),$this->getId());			
		}
		   
		$action = mysql_query($sql) or die("MySQL update benutzer error. ".mysql_error());
		
		if ($action) 
			echo "Benutzer wurde erfolgreich aktualisiert.";
		else
			echo "Beim Speichern des Benutzers ist leider ein Fehler aufgetreten.";
	}
	
	public function delete() {
		$sql = "DELETE FROM benutzer WHERE id = '{$this->getId()}' LIMIT 1";
		$action = mysql_query($sql) or die ("MySQL delete benutzer error. ".mysql_error());	
		
		if ($action)
		  return "Löschen des Benutzers erfolgreich.";
		else
		  return "Beim Löschen des Benutzers ist leider ein Fehler aufgetreten";	
	}
	
}

?>