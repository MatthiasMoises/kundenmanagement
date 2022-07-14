<?php

include('../tpl/adminheader.tpl.php');

$benutzer = new Benutzer();

$query = "SELECT * FROM benutzer WHERE 1 ORDER BY id ASC";
$select = mysql_query($query) or die ("MySQL select benutzer error. ".mysql_error());

$row_count = mysql_num_rows($select);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Benutzerbereich</title>
<link rel="stylesheet" type="text/css" href="../css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."../img/favicon.ico"; ?>" />
<script type="text/javascript" src="../libs/js/prototype.js"></script>
<script type="text/javascript" src="../libs/js/scriptaculous.js"></script>
<script>

function del(id) {
		
		if (confirm("Wollen Sie diesen Benutzer wirklich endgültig löschen?"))
		{
	
			new Ajax.Updater("","edit_user_server.php", {
		
			parameters : {
			
			'cmd'			: 'delete',
			'id'			: id
			},
			evalScripts : true,
			encoding : 'ISO-8859-1',
			
			onFailure : function() {
				alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
			},
			
			onComplete : function() {
				alert ("Benutzer wurde erfolgreich gelöscht!");
				location.reload(true);
			}
		});
	}
}

</script>
</head>

<body id="admin">

<h1>Benutzerbereich</h1>

<div>
	<input type="button" name="home" value="Neuen Benutzer anlegen" onClick="parent.location='edit_user.php?action=new'" />
</div>

<hr />

<?php

echo "<b>Zeige $row_count Eintr&auml;ge</b><br/><hr/>";

?>

<form id="user_table" action="" method="post">

<table border="1">
	<thead>
		<th>BenutzerID</th>
        <th>Benutzername</th>
        <th>K&uuml;rzel</th>
        <th>Nachname</th>
        <th>Vorname</th>
        <th>Telefon</th>
        <th>Strasse</th>
		<th>Hausnr</th>
        <th>PLZ</th>
        <th>Ort</th>
        <th>Email</th>
        <th>Stundensatz</th>
        <th>Letzter Login</th>
        <th>Ist Administrator</th>
        <th>Status</th>
        <th colspan="2">Optionen</th>
	</thead>
    
    	<?php
		
		$count = 0;
		
		while ($row = mysql_fetch_assoc($select))
		{
			$count++;
			$benutzer->setId($row['id']);
			$benutzer->setBenutzername($row['benutzername']);
			$benutzer->setPasswort($row['passwort']);
			$benutzer->setKuerzel($row['kuerzel']);
			$benutzer->setNachname($row['name']);
			$benutzer->setVorname($row['vorname']);
			$benutzer->setStrasse($row['strasse']);
			$benutzer->setHausnummer($row['hausnummer']);
			$benutzer->setPlz($row['plz']);
			$benutzer->setOrt($row['ort']);
			$benutzer->setTelefon($row['telefon']);
			$benutzer->setStundensatz($row['stundensatz']);
			$benutzer->setEmail($row['email']);
			$benutzer->setLetzterLogin($row['letzter_login']);
			$benutzer->setAdmin($row['ist_admin']);
			$benutzer->setGesperrt($row['gesperrt']);
			
			if (($count % 2) == 0)
			{
				echo "<tr style='background-color:#FFF1AA;'>";
			}
			else {
				echo "<tr style='background-color:#9AE9FA;'>";	
			}
			
			echo "
					<td>{$benutzer->getId()}</td>
					<td>{$benutzer->getBenutzername()}</td>
					<td>{$benutzer->getKuerzel()}</td>
					<td>{$benutzer->getNachname()}</td>
					<td>{$benutzer->getVorname()}</td>
					<td>{$benutzer->getStrasse()}</td>
					<td>{$benutzer->getHausnummer()}</td>
					<td>{$benutzer->getPlz()}</td>
					<td>{$benutzer->getOrt()}</td>
					<td>{$benutzer->getTelefon()}</td>
					<td>{$benutzer->getEmail()}</td>
					<td>{$benutzer->getStundensatz()}</td>
					<td>{$benutzer->getLetzterLogin()}</td>
					<td align='center'>";
					if ($benutzer->getAdmin() == "1")
						echo "<img src='../img/is_admin.gif' alt='ist admin' />";
			echo "	</td>
					<td align='center'>";
					if ($benutzer->getGesperrt() == "1")
						echo "<img src='../img/user_red.gif' alt='gesperrt' title='Benutzer gesperrt' />";
					else
						echo "<img src='../img/user_go.gif' alt='ok' title='Benutzer ok' />";	
			echo "	</td>
					<td align='center'>";
					if ($benutzer->getBenutzername() !== "admin")
						echo "<a href='edit_user.php?action=edit&id={$benutzer->getId()}'><img src='../img/edit.gif' alt='edit' title='Benutzer bearbeiten' /></a>";
			echo " </td>
					<td align='center'>";
					if ($benutzer->getBenutzername() !== "admin")
						echo "<a href='javascript:del({$benutzer->getId()})'><img src='../img/cross.gif' alt='delete' title='Benutzer l&ouml;schen' /></a></td>";
			echo "</td>";		
		}
		
    	?>
    
</table>

</form>

<hr />

<div>
	<input type="button" name="home" value="Neuen Benutzer anlegen" onClick="parent.location='edit_user.php?action=new'" />
</div>

<hr  />

<p>
<a href="admin.php">Zur&uuml;ck zum Adminmen&uuml;</a>
</p>

</body>
</html>

<?php

include('../tpl/footer.tpl.php');

?>