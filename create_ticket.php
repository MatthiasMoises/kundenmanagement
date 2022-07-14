<?php

include('tpl/header.tpl.php');

// If set, get GET variables

$action = isset($_GET['action']) ? $_GET['action'] : "";
$ticket_nr = isset($_GET['ticket_nr']) ? $_GET['ticket_nr'] : "";

// Set update vars

$ticket_author = "";
$ticket_date = "";
$ticket_short = "";
$ticket_long = "";
$ticket_status = "";
$ticket_time_needed = "";

// Check for new or update

if ($action == "" && $ticket_nr == "") {

	// Count entries
	
	$count_entries = mysql_query("SELECT * FROM support WHERE 1") or die ("MySQL get support entries error. ".mysql_error());
	$counter = mysql_num_rows($count_entries);
	$counter += 1;
	
	// Set current date
	
	$date = date("Y-m-d H:i:s");
	
	// Set author with session
	
	$author = $_SESSION['benutzername'];
	
	// Set status
	
	$status = 0;

}
else {
	
	$get_ticket_data = mysql_query("SELECT id, author, DATE_FORMAT (date, '%d.%m.%Y %H:%i:%s') AS date, short_desc, long_desc, status, current, admin_comment, time_needed FROM support WHERE id = '$ticket_nr'") or die("MySQL get ticket data error. ".mysql_error());
	
	while ($row_ticket = mysql_fetch_assoc($get_ticket_data)) {
		$ticket_id = $row_ticket['id'];
		$ticket_author = $row_ticket['author'];
		$ticket_date = $row_ticket['date'];
		$ticket_short = $row_ticket['short_desc'];
		$ticket_long = $row_ticket['long_desc'];
		$ticket_status = $row_ticket['status'];
		$ticket_current = $row_ticket['current'];
		$ticket_admin_comment = $row_ticket['admin_comment'];
		$ticket_time_needed = $row_ticket['time_needed'];
		$ticket_time_needed = str_replace(".",",",$ticket_time_needed);
	}
	
}

// Get admin email

$get_mail = mysql_query("SELECT email FROM benutzer WHERE id = '1' OR id = '2'") or die ("MySQL get admin email error. ".mysql_error());

// Get Form data

if (isset($_POST['einschicken'])) {
	
	if ($action == "edit") {
		$author = $ticket_author;
		$short = $ticket_short;
		$long = $ticket_long;
		$status = $_POST['status'];
		if ($status == "Wird nicht erledigt") {
			$current = "Wird nicht erledigt";
		}
		else if ($status < 100 && $status != "Wird nicht erledigt") {
			$current = "Offen";	
		}
		else {
			$current = "Geschlossen";	
		}
		$admin_comment = stripslashes(mysql_real_escape_string($_POST['comment']));
		$time_needed = stripslashes(mysql_real_escape_string($_POST['time_needed']));
		$time_needed = str_replace(",",".",$time_needed);
	}
	else {
		$current = "Offen";
		$short = stripslashes(mysql_real_escape_string($_POST['short_desc']));
		$long = stripslashes(mysql_real_escape_string($_POST['long_desc']));	
		$admin_comment = "";
	}
	
	if ($author != "" && $short != "" && $long != "") {
		
		if ($action == "" && $ticket_nr == "") {
			$save = mysql_query("INSERT INTO support (author,date,short_desc,long_desc,status,current,admin_comment) VALUES ('$author','$date','$short','$long','$status','$current','$admin_comment')") or die ("MySQL save support ticket error. ".mysql_error());
			
			$counter += 1;
			
			if ($save) {
				echo "<p style='color:yellow;'>Ihr Ticket wurde erfolgreich angelegt, Sie werden &uuml;ber die Bearbeitung auf dem Laufenden gehalten.</p>";	
				
				$subject = "Es wurde ein neues Ticket im Kundenmanagement erstellt";
				$message = "$author hat ein neues Support-Ticket angelegt ($date)
       \n\n
							Beschreibung:\n
							$long
							\n\n";
				$header = 'From: admin@kundenmanagement' . "\r\n" .
							'Reply-To: admin@kundenmanagement' . "\r\n" .
							'X-Mailer: PHP/' . phpversion();
							
				while ($row = mysql_fetch_assoc($get_mail)) {
					$email = $row['email'];	
					@mail($email,$subject,$message,$header);
				}
			}
			else {
				echo "<p style='color:red'>Es ist leider ein Fehler aufgetreten. Bitte versuchen Sie es erneut!";	
			}
		}
		else {
			
			// get author email
	
			if ($status == 100 || $status == "Wird nicht erledigt") {
	
				$get_mail_finish = mysql_query("SELECT email FROM benutzer WHERE benutzername = '$ticket_author'") or die ("MySQL get author email error. ".mysql_error());
				
				while ($row_auth_mail = mysql_fetch_assoc($get_mail_finish)) {
					$author_mail = $row_auth_mail['email'];	
				}
				
				$stunden = $time_needed / 60;
				
				$subject = "Der Status Ihres Tickets wurde aktualisiert";
				$message = "Ihr Ticket mit der Kennnummer $ticket_id wurde aktualisiert. Status: $current ($date)
							\n\n
							Kommentar:\n
							$admin_comment
							\n\n
							Zeitaufwand:\n
							$time_needed Minuten\n
							($stunden Stunden)
							\n\n";
				$header = 'From: admin@kundenmanagement' . "\r\n" .
							'Reply-To: admin@kundenmanagement' . "\r\n" .
							'X-Mailer: PHP/' . phpversion();
							
				@mail($author_mail,$subject,$message,$header);
				
				$subject = "CC: Der Status Ihres Tickets wurde aktualisiert";
				while ($row = mysql_fetch_assoc($get_mail)) {
					$email = $row['email'];	
					@mail($email,$subject,$message,$header);
				}
			}
			
			$update = mysql_query("UPDATE support SET status = '$status', current = '$current', admin_comment = '$admin_comment', time_needed = '$time_needed' WHERE id = '$ticket_nr'") or die ("MySQL update ticket error. ".mysql_error());
			if ($update)
				echo "<p style='color:green'>Ticket wurde erfolgreich aktualisiert</p>";
			else 
				echo "Beim Aktualisieren ist leider ein Fehler aufgetreten!";
		}
	}
	else {
		echo "<p style='color:red'>Sie haben nicht alle Felder ausgef&uuml;llt!";	
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Support - Ticket erstellen</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
</head>

<body>

<?php

if ($action == "" && $ticket_nr == "") {
?>
	<h1>Support - Neues Ticket anlegen</h1>
<?php
}
else {
?>
	<h1>Support - Ticket Nr. <?php echo $ticket_nr; ?> bearbeiten</h1>
<?php
}
?>



<form id="ticket" action="" method="post">
	<table>
    <tr>
    	<td><label>Ticket-ID:</label></td>
        <td><input type="text" id="id" name="id" disabled="disabled" value="<?php if ($ticket_nr != "") {echo $ticket_nr;} else {echo $counter;} ?>" /></td>
    </tr>
    <tr>
    	<td><label>Ersteller:</label></td>
        <td><input type="text" id="author" name="author" disabled="disabled" value="<?php if ($ticket_author != "") {echo $ticket_author;} else {echo $author;} ?>" /></td>
    </tr>
    <tr>
    	<td><label>Datum:</label></td>
        <td><input type="text" id="date" name="date" disabled="disabled" value="<?php if ($ticket_date != "") {echo $ticket_date;} else {echo $date;} ?>" /></td>
    </tr>
    <tr>
    	<td><label>Kurzbeschreibung:</label></td>
        <td><input type="text" id="short_desc" name="short_desc" value="<?php if ($ticket_short != "") {echo $ticket_short;} else {echo "";} ?>" /></td>
    </tr>
    <tr>
    	<td><label>Ausf&uuml;hrliche Beschreibung</label></td>
        <td><textarea id="long_desc" name="long_desc"><?php if ($ticket_long != "") {echo $ticket_long;} else {echo "";} ?></textarea><br/></td>
    </tr>
    <?php
		if ($action == "edit" && isset($ticket_nr)) {
			?>
            <tr>
            	<td><label>Status:</label></td>
                <td>
                	<select id="status" name="status">
                		<option id="0" <?php if ($ticket_status == 0) echo 'selected="selected"'; ?>>0</option>
                        <option id="10" <?php if ($ticket_status == 10) echo 'selected="selected"'; ?>>10</option>
                        <option id="20" <?php if ($ticket_status == 20) echo 'selected="selected"'; ?>>20</option>
                        <option id="30" <?php if ($ticket_status == 30) echo 'selected="selected"'; ?>>30</option>
                        <option id="40" <?php if ($ticket_status == 40) echo 'selected="selected"'; ?>>40</option>
                        <option id="50" <?php if ($ticket_status == 50) echo 'selected="selected"'; ?>>50</option>
                        <option id="60" <?php if ($ticket_status == 60) echo 'selected="selected"'; ?>>60</option>
                        <option id="70" <?php if ($ticket_status == 70) echo 'selected="selected"'; ?>>70</option>
                        <option id="80" <?php if ($ticket_status == 80) echo 'selected="selected"'; ?>>80</option>
                        <option id="90" <?php if ($ticket_status == 90) echo 'selected="selected"'; ?>>90</option>
                        <option id="100" <?php if ($ticket_status == 100) echo 'selected="selected"'; ?>>100</option>
                        <option id="no" <?php if ($ticket_status == "Wird nicht erledigt") echo 'selected="selected"'; ?>>Wird nicht erledigt</option>
                    </select>
                </td>
            </tr>
            <?php
			if ($action == "edit" && isset($ticket_nr)) {
			?>
            	<tr>
                	<td><label>Kommentar</label></td>
                	<td><textarea id="comment" name="comment"><?php if ($ticket_admin_comment != "") {echo $ticket_admin_comment;} else {echo "";} ?></textarea></td>
                </tr>
            <?php
			}
			?>
            <tr>
            	<td><label>Zeitaufwand (Min.)</label></td>
            	<td><input type="text" id="time_needed" name="time_needed" value="<?php if ($ticket_time_needed != "") {echo $ticket_time_needed;} else {echo "";} ?>" /></td>
            </tr>
            <?php	
		}
	if ($action == "" && $ticket_nr == "") {
	?>
    <tr>
    	<td colspan="2"><input type="submit" id="einschicken" name="einschicken" value="Ticket anlegen" /></td>
        <td></td>
    </tr>
    <?php
	}
	else {
	?>
    <tr>
    	<td colspan="2"><input type="submit" id="einschicken" name="einschicken" value="Ticket aktualisieren" /></td>
        <td></td>
    </tr>    
    <?php
	}
	?>
    </table>
</form>

<p>
<a href="support.php">Zur&uuml;ck</a>
</p>

</body>
</html>

<?php

include('tpl/footer.tpl.php');

?>