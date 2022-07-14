<?php

session_start();

require('libs/PHPMailer_v5.1/class.phpmailer.php');
require('libs/PHPMailer_v5.1/class.pop3.php');

$pop = new POP3();
$pop->Authorise(POP3, PORT_1, PORT_2, SEND_ADRESS, PASS, 1);

$mail = new PHPMailer();

$mail->IsSMTP();
$mail->SMTPDebug = 2;
$mail->IsHTML(true);

$mail->Host     = MAIL_HOST;

$mail->From     = FROM_MAIL;
$mail->FromName = FROM_NAME;

$mail->Subject  =  'Kundenmanagement - Termininformation';

// GET Kalender entry data

$kalender_query = "SELECT DATE_FORMAT (start_date, '%d.%m.%Y') AS start_date, DATE_FORMAT (end_date, '%d.%m.%Y') AS end_date, start_time, end_time, description, author, important FROM kalender WHERE id = ".LAST_ID;	

$select_data = mysql_query($kalender_query) or die ("MySQL select termine error. ".mysql_error());

while ($kal_row = mysql_fetch_assoc($select_data))
{
	  $sd = $kal_row['start_date'];	
	  $ed = $kal_row['end_date'];
	  $st = $kal_row['start_time'];
	  $et = $kal_row['end_time'];
	  $de = $kal_row['description'];
	  $au = $kal_row['author'];
	  $im = $kal_row['important'];
}

if ($im == 1){
	$im = "Dringend";
	$color = 'red';
}
else {
	$im = "Neutral";
	$color = 'blue';
}

$mail->Body     =  "<p><b>Ein neuer Termin wurde angelegt!</b></p>
					<b>Beschreibung:</b>       						".$de."<br/>
					<b>Autor:</b>              						".$au."<br/>
					<b>Startdatum:</b>         						".$sd."<br/>
					<b>Enddatum:</b>	        					".$ed."<br/>
					<b>Startzeit:</b>          						".$st."<br/>
					<b>Endzeit:</b>            						".$et."<br/>
					<b>Status:</b><span style='color:".$color."'>   ".$im."</span><br/>
					<p><a href='".DOCUMENT_ROOT."' target='_blank'>Zum Kundenmanagement</a></p>
					<p><b>Rein zu Informationszwecken! Bitte antworten Sie nicht auf diese E-Mail!</b></p> 
					";

$select_adress = mysql_query("SELECT benutzername, email FROM benutzer WHERE 1") or die (mysql_error());

while ($row_email = mysql_fetch_assoc($select_adress))
{
	$db_name = $row_email['benutzername'];
	$db_email = $row_email['email'];	

	if ($db_email != "")
		$mail->AddAddress("$db_email", "$db_name");
}

if (!$mail->Send())
{
	echo $mail->ErrorInfo;
}			

?>