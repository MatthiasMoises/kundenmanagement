<?php

include('../tpl/adminheader.tpl.php');

$benutzer = new Benutzer();
	
$action = isset($_GET['action']) ? $_GET['action'] : "new";

$id = isset($_GET['id']) ? $_GET['id'] : NULL;

if (isset($id))
{
	$query = "SELECT * FROM benutzer WHERE id = '$id'";
	$select = mysql_query($query) or die ("MySQL select benutzer '$id' error. ".mysql_error());
}

if ($action == 'edit')
{
	$cmd = "update";
	
	while ($row = mysql_fetch_assoc($select))
	{
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
		$benutzer->setAdmin($row['ist_admin']);
		$benutzer->setGesperrt($row['gesperrt']);
	}
}
else if ($action == 'new'){
	$cmd = "new";
	$id = "";	
}
else {
	$cmd = "new";	
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Benutzer bearbeiten</title>
<link rel="stylesheet" type="text/css" href="../css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."../img/favicon.ico"; ?>" />
<script type="text/javascript" src="../libs/js/prototype.js"></script>
<script type="text/javascript">

function goBack() {
	if (confirm("Wollen Sie die Bearbeitung wirklich abbrechen?"))
	{
		window.back();
	}
}

function validate_password(){

  /*
	if ($('passwort').value != "")
	{	
  */
  
  if ($('passwort').value == $('repeat_passwort').value) 
  {
		new Ajax.Request("edit_user_server.php",
		{
			parameters: {
				'cmd'		  : 'validate_password',
				'passwort' : $('passwort').value
			},
			onSuccess : function(result){

				var response = result.responseText;

				if (response == "fehler") {
					alert("Ihr Passwort enthält keine Sonderzeichen! Bitte korrigieren!");
				}
				else if (response == "erfolg"){
					save();
				}
			}
		});
	  } else {
      alert("Die Passwörter stimmen nicht überein!");
   }
  /*
  }
	else {
		alert("Sie haben kein Passwort angegeben!");
	}
	*/
}

function save() {
	
	 $('waiting').show();
	 $('update').disabled = true;

	/*
	if ($('anrede').value != "" && $('vorname').value != "" && $('nachname').value != "" && $('kontennummer').value != "" && $('strasse').value != "" && $('hausnummer').value != "" && $('plz').value != "" && $('ort').value != "" && $('email').value != "" && $('telefon').value != "")
	{
	*/
	new Ajax.Updater("","edit_user_server.php", {
	
		parameters : {
		
		'cmd'			: $('cmd').value,
		'id'			: $('id').value,
		'username'		: $('username').value,
		'passwort'		: $('passwort').value,
		'kuerzel'		: $('kuerzel').value,
		'name'			: $('name').value,
		'vorname'		: $('vorname').value,
		'strasse'		: $('strasse').value,
		'hausnummer'	: $('hausnummer').value,
		'plz'			: $('plz').value,
		'ort'			: $('ort').value,
		'telefon'		: $('telefon').value,
		'email'			: $('email').value,
		'stundensatz'	: $('stundensatz').value,
		'ist_admin'		: ($('ist_admin').checked) ? '1' : '0',
		'gesperrt'		: ($('gesperrt').checked) ? '1' : '0'
		},
		evalScripts : true,
		encoding : 'ISO-8859-1',
		
		onFailure : function() {
			alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
		},
		
		onComplete : function() {
	 		$('waiting').hide();
			$('update').disabled = false;
			alert ("Benutzer wurde gespeichert!");
			window.history.back();
		}
	});
	/*
	}
	else {
		alert ("Bitte fÃ¼llen Sie alle Felder aus um den Kunden zu speichern!");
	}
	*/
}

</script>
</head>

<body id="admin">

<?php

if ($benutzer->getId() != "")
	echo "<h1>Benutzer Nr. {$benutzer->getId()} bearbeiten</h1>";
else
	echo "<h1>Benutzer bearbeiten</h1>";
?>

<form action="" id="edit_user_form" method="post">

<div id="editFields">

<table>
<tr>
	<td><input type="hidden" id="id" name="id" value="<?php echo $benutzer->getId(); ?>" /></td>
</tr>
<tr>
	<td><input type="hidden" id="cmd" name="cmd" value="<?php echo $cmd; ?>" /></td>
</tr>
<tr>
	<td>Benutzername:</td>
    <td><input type="text" name="username" id="username" value="<?php echo $benutzer->getBenutzername(); ?>" /></td>
</tr>
<tr>
	<td>Passwort:</td>
    <td><input type="password" name="passwort" id="passwort" /> <small style="color:#F00;">(Wird nicht angezeigt, nur dann einen Wert eintragen wenn das Passwort geÃ¤ndert werden soll!)</small></td>
</tr>
<tr>
  <td>Passwort wiederholen:</td>
  <td><input type="password" name="repeat_passwort" id="repeat_passwort" />
</tr>
<tr>
	<td>KÃ¼rzel:</td>
    <td><input type="text" name="kuerzel" id="kuerzel" value="<?php echo $benutzer->getKuerzel(); ?>" /> (3 Zeichen)</td>
</tr>
<tr>
	<td>Nachname:</td>
    <td><input type="text" name="name" id="name" value="<?php echo $benutzer->getNachname(); ?>" /></td>
</tr>
<tr>
	<td>Vorname:</td>
    <td><input type="text" name="vorname" id="vorname" value="<?php echo $benutzer->getVorname(); ?>" /></td>
</tr>
<tr>
	<td>StraÃŸe:</td>
    <td><input type="text" name="strasse" id="strasse" value="<?php echo $benutzer->getStrasse(); ?>" /></td>
</tr>
<tr>
	<td>Hausnummer:</td>
    <td><input type="text" name="hausnummer" id="hausnummer" value="<?php echo $benutzer->getHausnummer(); ?>" /></td>
</tr>
<tr>
	<td>PLZ:</td>
    <td><input type="text" name="plz" id="plz" value="<?php echo $benutzer->getPlz(); ?>" /></td>
</tr>
<tr>
	<td>Ort:</td>
    <td><input type="text" name="ort" id="ort" value="<?php echo $benutzer->getOrt(); ?>" /></td>
</tr>
<tr>
	<td>Telefon:</td>
    <td><input type="text" name="telefon" id="telefon" value="<?php echo $benutzer->getTelefon(); ?>" /></td>
</tr>
<tr>
	<td>Email:</td>
    <td><input type="text" name="email" id="email" value="<?php echo $benutzer->getEmail(); ?>" /></td>
</tr>
<tr>
	<td>Stundensatz:</td>
    <td><input type="text" name="stundensatz" id="stundensatz" value="<?php echo $benutzer->getStundensatz(); ?>" /></td>
</tr>
<tr>
	<td>Ist Administrator:</td>
    <td><input type="checkbox" name="ist_admin" id="ist_admin" <?php if ($benutzer->getAdmin() == 1) echo 'checked="checked"'; ?> /></td>
</tr>
<tr>
	<td>Benutzer sperren:</td>
    <td><input type="checkbox" name="gesperrt" id="gesperrt" <?php if ($benutzer->getGesperrt() == 1) echo 'checked="checked"'; ?> /></td>
</tr>
</table>

</div>
<div id="waiting" style="display: none;">
     Speichert. Bitte warten...<br />
     <img src="../img/ajax-loader.gif" title="Loader" alt="Loader" />
</div>

<div style="clear:both;"></div>

<p>
<input type="button" id="update" value="Benutzer speichern" onclick="javascript:validate_password()" />
</p>

</form>

<p>
	<input type="button" name="home" value="Zur&uuml;ck zum Benutzerbereich" onclick="javascript:goBack();"  />
</p>
</body>
</html>

<?php

include('../tpl/footer.tpl.php');

?>