<?php

include('tpl/header.tpl.php');

$kunde = new Kunde();

if (isset($_GET['action']))
	$action = $_GET['action'];
else
	$action = "";

$id = isset($_GET['id']) ? $_GET['id'] : NULL;

if (isset($id))
{
	$query = "SELECT * FROM kunden WHERE id = '$id'";
	$select = mysql_query($query) or die ("MySQL select kunde '$id' error. ".mysql_error());
}

if ($action == 'edit')
{
	$cmd = "update";
	
	while ($row = mysql_fetch_assoc($select))
	{
		$kunde->setId($row['id']);
		$kunde->setAnrede($row['anrede']);
		$kunde->setVorname(stripslashes($row['vorname']));
		$kunde->setNachname(stripslashes($row['nachname']));
		$kunde->setKontennummer($row['kontennummer']);
		$kunde->setStrasse($row['strasse']);
		$kunde->setHausnummer($row['hausnummer']);
		$kunde->setAdressZusatz1($row['adresszusatz_1']);
		$kunde->setAdressZusatz2($row['adresszusatz_2']);
		$kunde->setPlz($row['plz']);
		$kunde->setOrt($row['ort']);
		$kunde->setEmail($row['email']);
		$kunde->setTelefon($row['telefon']);
	}
}
else if ($action == 'new'){
	$cmd = "new";
	$id = "";	
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kunde bearbeiten</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript">

function goBack() {
	if (confirm("Wollen Sie die Bearbeitung wirklich abbrechen?"))
	{
		window.back();
	}
}

function save() {
	
	$('waiting').show();
	$('update').disabled = true;

	/*
	if ($('anrede').value != "" && $('vorname').value != "" && $('nachname').value != "" && $('kontennummer').value != "" && $('strasse').value != "" && $('hausnummer').value != "" && $('plz').value != "" && $('ort').value != "" && $('email').value != "" && $('telefon').value != "")
	{
	*/
	new Ajax.Updater("","edit_customer_server.php", {
	
		parameters : {
		
		'cmd'			  : $('cmd').value,
		'id'			  : $('id').value,
		'anrede'		  : $('anrede').value,
		'vorname'		  : $('vorname').value,
		'nachname'		  : $('nachname').value,
		'kontennummer'	  : $('kontennummer').value,
		'strasse'		  : $('strasse').value,
		'hausnummer'	  : $('hausnummer').value,
		'adresszusatz_1'  : $('adresszusatz_1').value,
		'adresszusatz_2'  : $('adresszusatz_2').value,
		'plz'			  : $('plz').value,
		'ort'			  : $('ort').value,
		'email'			  : $('email').value,
		'telefon'		  : $('telefon').value
		},
		evalScripts : true,
		encoding : 'ISO-8859-1',
		
		onFailure : function() {
			alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
		},
		
		onComplete : function() {
			$('waiting').hide();
			$('update').disabled = false;
			alert ("Kunde wurde gespeichert!");
			window.history.back();
		}
	});
	/*
	}
	else {
		alert ("Bitte füllen Sie alle Felder aus um den Kunden zu speichern!");
	}
	*/
}

function closeIt()
{
  return "Any string value here forces a dialog box to \n" + 
         "appear before closing the window.";
}
window.onbeforeunload = closeIt;

</script>
</head>

<body>

<?php

if ($kunde->getId() != "")
	echo "<h2>Kunde Nr. {$kunde->getId()} bearbeiten</h2>";
else 
	echo "<h2>Kunde bearbeiten</h2>";

?>

<form action="" id="edit_customer_form" method="post">

<div id="editFields">

<table>
<tr>
	<td>Anrede:</td>
    <td>
    <select name="anrede" id="anrede">
    	<option value="Herr" <?php if ($kunde->getAnrede() == "Herr") echo "selected='selected'"; ?>>Herr</option>
        <option value="Frau" <?php if ($kunde->getAnrede() == "Frau") echo "selected='selected'"; ?>>Frau</option>
        <option value="Familie" <?php if ($kunde->getAnrede() == "Familie") echo "selected='selected'"; ?>>Familie</option>
        <option value="Firma" <?php if ($kunde->getAnrede() == "Firma") echo "selected='selected'"; ?>>Firma</option>
        <option value="An die" <?php if ($kunde->getAnrede() == "An die") echo "selected='selected'"; ?>>An die</option>
        <option value="An das" <?php if ($kunde->getAnrede() == "An das") echo "selected='selected'"; ?>>An das</option>
        <option value="An den" <?php if ($kunde->getAnrede() == "An den") echo "selected='selected'"; ?>>An den</option>
    </select>
    </td>
</tr>
<tr>
	<td><input type="hidden" id="id" name="id" value="<?php echo $kunde->getId(); ?>" /></td>
</tr>
<tr>
	<td><input type="hidden" id="cmd" name="cmd" value="<?php echo $cmd; ?>" /></td>
</tr>
<tr>
	<td>Vorname:</td>
    <td><input type="text" name="vorname" id="vorname" value='<?php echo $kunde->getVorname(); ?>' /></td>
</tr>
<tr>
	<td>Nachname:</td>
    <td><input type="text" name="nachname" id="nachname" value='<?php echo $kunde->getNachname(); ?>' /></td>
</tr>
<tr>
	<td>Kontennummer:</td>
    <td><input type="text" name="kontennummer" id="kontennummer" value='<?php echo $kunde->getKontennummer(); ?>' /></td>
</tr>
<tr>
	<td>Strasse:</td>
    <td><input type="text" name="strasse" id="strasse" value='<?php echo $kunde->getStrasse(); ?>' /></td>
</tr>
<tr>
	<td>Hausnummer:</td>
    <td><input type="text" name="hausnummer" id="hausnummer" value='<?php echo $kunde->getHausnummer(); ?>' /></td>
</tr>
<tr>
	<td>Adresszusatz 1:</td>
    <td><input type="text" name="adresszusatz_1" id="adresszusatz_1" value='<?php echo $kunde->getAdressZusatz1(); ?>' /></td>
</tr>
<tr>
	<td>Adresszusatz 2:</td>
    <td><input type="text" name="adresszusatz_2" id="adresszusatz_2" value='<?php echo $kunde->getAdressZusatz2(); ?>' /></td>
</tr>
<tr>
	<td>PLZ:</td>
    <td><input type="text" name="plz" id="plz" value='<?php echo $kunde->getPlz(); ?>' /></td>
</tr>
<tr>
	<td>Ort:</td>
    <td><input type="text" name="ort" id="ort" value='<?php echo $kunde->getOrt(); ?>' /></td>
</tr>
<tr>
	<td>Email:</td>
    <td><input type="text" name="email" id="email" value='<?php echo $kunde->getEmail(); ?>' /></td>
</tr>
<tr>
	<td>Telefon:</td>
    <td><input type="text" name="telefon" id="telefon" value='<?php echo $kunde->getTelefon(); ?>' /></td>
</tr>
</table>

</div>
<div id="waiting" style="display: none;">
     Speichert.<br />Bitte warten...<br />
     <img src="img/ajax-loader.gif" title="Loader" alt="Loader" />
</div>

<div style="clear:both;"></div>

<p>
<input type="button" id="update" value="Kunde speichern" onclick="javascript:save()" />
</p>

</form>

<p>
	<input type="button" name="home" value="Zur&uuml;ck zum Kundenbereich" onclick="javascript:goBack();"  />
</p>
</body>
</html>

<?php

include("tpl/footer.tpl.php");

?>