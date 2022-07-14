<?php

class Log {
	
	private $objNr = "";
	private $objName = "";
	private $username = "";
	private $action = "";
	private $date = "";
	private $log_text = "";

	public function __construct() {
		
	}

	public function Log($username, $log_text) {
		$this->username = $username;
		$this->date = date("Y-m-d H:i:s");
		$this->log_text = $log_text;
	}
	
	public function setObjNr($objNr) {
		$this->objNr = $objNr;	
	}
	
	public function getObjNr() {
		return $this->objNr;	
	}
	
	public function setObjName($objName) {
		$this->objName = $objName;	
	}
	
	public function getObjName() {
		return $this->objName;	
	}
	
	public function setAction($action) {
		$this->action = $action;	
	}
	
	public function getAction() {
		return $this->action;	
	}
	
	public function setUsername($username) {
		$this->username = $username;	
	}
	
	public function getUsername() {
		return $this->username;	
	}
	
	public function setDate($date) {
		$this->date = $date;	
	}
	
	public function getDate() {
		return $this->date;	
	}
	
	public function setLogText($log_text) {
		$this->log_text = $log_text;	
	}
	
	public function getLogText() {
		return $this->log_text;	
	}
	
	public function save() {
		if (!isset($_SESSION['benutzername']))
			die("Ohne gültige Session kann kein Logeintrag erstellt werden!");
		else {
			$this->date = date("Y-m-d H:i:s");
			$logText = $_SESSION['benutzername']." hat ".$this->getObjName()." Nr. ".$this->getObjNr(). " ".$this->getAction().".";
			
			$sql = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$this->date','$logText')") or die ("MySQL INSERT log entry error. ".mysql_error());	
			
			//return $outputText;			
		}
	}
	
}

?>