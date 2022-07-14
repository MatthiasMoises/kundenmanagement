<?php

class Kalender {
	
	// Variables

	private $id = "";
	private $startDate = "";
	private $endDate = "";
	private $startTime = "";
	private $endTime = "";
	private $description = "";
	private $author = "";
	private $important = "";
	private $mailSent = "";
	
	// Construct
	
	public function __construct(){
		
	}
	
	public function Kalender($startDate,$endDate,$startTime,$endTime,$description,$author,$important,$mailSent) {
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->description = $description;
		$this->author = $author;
		$this->important = $important;
		$this->mailSent = $mailSent;
	}
	
	// Get & Set
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getId() {
		return $this->id;	
	}
	
	public function setStartDate($startDate) {
		$this->startDate = $startDate;	
	}
	
	public function getStartDate() {
		return $this->startDate;	
	}
	
	public function setEndDate($endDate) {
		$this->endDate = $endDate;	
	}
	
	public function getEndDate() {
		return $this->endDate;	
	}
	
	public function setStartTime($startTime) {
		$this->startTime = $startTime;
	}
	
	public function getStartTime() {
		return $this->startTime;	
	}
	
	public function setEndTime($endTime) {
		$this->endTime = $endTime;
	}
	
	public function getEndTime() {
		return $this->endTime;	
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	
	public function getDescription() {
		return $this->description;	
	}
	
	public function setAuthor($author) {
		$this->author = $author;	
	}
	
	public function getAuthor() {
		return $this->author;	
	}
	
	public function setImportant($important) {
		$this->important = $important;	
	}
	
	public function getImportant() {
		return $this->important;	
	}
	
	public function setMailSent($mailSent) {
		$this->mailSent = $mailSent;
	}
	
	public function getMailSent() {
		return $this->mailSent;	
	}
	
	// functions
	
	public function sendInfoMail() {
		
	}
	
}

?>