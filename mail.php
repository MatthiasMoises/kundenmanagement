<?php

$header = 'From: some@mail.de' . "\r\n" .
          'Reply-To: some@mail.de' . "\r\n" .
          'X-Mailer: PHP/' . phpversion();
		  
$header .= 'MIME-Version: 1.0' . "\r\n";
$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$subject  =  'Kundenmanagement - Termininformation';

// GET Kalender entry data

$kalender_query = "SELECT DATE_FORMAT (start_date, '%d.%m.%Y') AS start_date, DATE_FORMAT (end_date, '%d.%m.%Y') AS end_date, DATE_FORMAT (start_time, '%H:%i') AS start_time, DATE_FORMAT (end_time, '%H:%i') AS end_time, description, author, important FROM kalender WHERE id = ".LAST_ID;	

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

$select_adress = mysql_query("SELECT benutzername, email FROM benutzer WHERE 1") or die (mysql_error());

while ($row_email = mysql_fetch_assoc($select_adress))
{
	$recipient_name = $row_email['name'];
	$recipient_username = $row_email['benutzername'];
	$recipient_mail = $row_email['email'];	
	
	$message        =  "<p><b>Hallo $recipient_name ($recipient_username),</b></p>
						<p><b>ein neuer Termin wurde angelegt!</b></p>
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

	if ($recipient_mail != "")
		mail($recipient_mail, $subject, $message, $header);	
}

		

?>