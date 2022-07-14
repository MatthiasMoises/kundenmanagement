<?php

class Rechnung {
	
	// Variables
	
	private $id = "";
	private $rechnungsNr = "";
	private $rechnungsDatum = "";
	private $kundenNachricht = "";
	private $hatStunden = "";
	private $hatArtikel = "";
	private $rabattProzent = "";
	private $rabattBetrag = "";
	private $skontoProzent = "";
	private $skontoBetrag = "";
	private $endBetrag = "";
	private $kdnr = "";
	private $bezahlt = "";
	private $bezahltDatum = "";
	private $editierbar = "";
	private $gedruckt = "";
	
	// Construct
	
	public function __construct() {
		
	}
	
	public function Rechnung() {
		
	}
	
	// Get & Set
	
	public function setId($id) {
		$this->id = $id;	
	}
	
	public function getId() {
		return $this->id;	
	}
	
	public function setRechnungsNr($rechnungsNr) {
		$this->rechnungsNr = $rechnungsNr;
	}
	
	public function getRechnungsNr() {
		return $this->rechnungsNr;	
	}
	
	public function setRechnungsDatum($rechungsDatum) {
		$this->rechnungsDatum = $rechungsDatum;	
	}
	
	public function getRechnungsDatum() {
		return $this->rechnungsDatum;	
	}
	
	public function setKundenNachricht($kundenNachricht) {
		$this->kundenNachricht = $kundenNachricht;	
	}
	
	public function getKundenNachricht() {
		return $this->kundenNachricht;	
	}
	
	public function setHatStunden($hatStunden) {
		$this->hatStunden = $hatStunden;	
	}
	
	public function getHatStunden() {
		return $this->hatStunden;	
	}
	
	public function setHatArtikel($hatArtikel) {
		$this->hatArtikel = $hatArtikel;	
	}
	
	public function getHatArtikel() {
		return $this->hatArtikel;	
	}
	
	public function setRabattProzent($rabattProzent) {
		$this->rabattProzent = $rabattProzent;	
	}
	
	public function getRabattProzent() {
		return $this->rabattProzent;	
	}
	
	public function setRabattBetrag($rabattBetrag) {
		$this->rabattBetrag = $rabattBetrag;	
	}
	
	public function getRabattBetrag() {
		return $this->rabattBetrag;	
	}
	
	public function setSkontoProzent($skontoProzent) {
		$this->skontoProzent = $skontoProzent;
	}
	
	public function getSkontoProzent() {
		return $this->skontoProzent;	
	}
	
	public function setSkontoBetrag($skontoBetrag) {
		$this->skontoBetrag = $skontoBetrag;	
	}
	
	public function getSkontoBetrag() { 
		return $this->skontoBetrag;
	}
	
	public function setEndBetrag($endBetrag) {
		$this->endBetrag = $endBetrag;	
	}
	
	public function getEndBetrag() {
		return $this->endBetrag;	
	}
	
	public function setKdNr($kdnr) {
		$this->kdnr = $kdnr;	
	}
	
	public function getKdNr() {
		return $this->kdnr;	
	}
	
	public function setBezahlt($bezahlt) {
		$this->bezahlt = $bezahlt;	
	}
	
	public function getBezahlt() {
		return $this->bezahlt;	
	}
	
	public function setBezahltDatum($bezahltDatum) {
		$this->bezahltDatum = $bezahltDatum;	
	}
	
	public function getBezahltDatum() {
		return $this->bezahltDatum;	
	}
	
	public function setEditierbar($editierbar) {
		$this->editierbar = $editierbar;	
	}
	
	public function getEditierbar() {
		return $this->editierbar;	
	}
	
	public function setGedruckt($gedruckt) {
		$this->gedruckt = $gedruckt;	
	}
	
	public function getGedruckt() {
		return $this->gedruckt;	
	}
	
	// Functions
	
	public function save() {
		
	}
	
	public function delete() {
		
	}
	
}

?>