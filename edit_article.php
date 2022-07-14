<?php

include('tpl/header.tpl.php');

$artikel = new Artikel();

if (isset($_GET['action']))
	$action = $_GET['action'];
else
	$action = "";

$id = isset($_GET['id']) ? $_GET['id'] : NULL;

if (isset($id))
{
	$query = "SELECT * FROM artikel WHERE id = '$id'";
	$select = mysql_query($query) or die ("MySQL select artikel '$id' error. ".mysql_error());
}

if ($action == 'edit')
{
	$cmd = "update";
	
	while ($row = mysql_fetch_assoc($select))
	{
		$artikel->setId($row['id']);
		$artikel->setArtNrLieferant($row['artnr_lieferant']);
		$artikel->setLieferantenNr($row['lieferantennr']);
		$artikel->setBezeichnung($row['bezeichnung']);
		$artikel->setKategorie($row['kategorie']);
		$artikel->setPreisNetto(str_replace(".",",",round($row['preis_netto'],2)));
			
		if (strpos($artikel->getPreisNetto(),",") === false){
			$artikel->setPreisNetto($artikel->getPreisNetto().",00"); 	
		}
		
		$artikel->setEinheit($row['einheit']);
	}
}
else if ($action == 'new') {
	$cmd = "new";
	$id = "";	
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Artikel bearbeiten</title>
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
	new Ajax.Updater("","edit_article_server.php", {
	
		parameters : {
		
		'cmd'				: $('cmd').value,
		'id'				: $('id').value,
		'artnr_lieferant'	: $('artnr_lieferant').value,
		'lieferantennr'		: $('lieferantennr').value,
		'bezeichnung'		: $('bezeichnung').value,
		'kategorie'			: $('kategorie').value,
		'preis_netto'		: $('preis_netto').value,
		'einheit'			: $('einheit').value
		},
		evalScripts : true,
		encoding : 'ISO-8859-1',
		
		onFailure : function() {
			alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
		},
		
		onComplete : function() {
			$('waiting').hide();
			$('update').disabled = false;
			alert ("Artikel wurde gespeichert!");
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

if ($artikel->getId() != "")
	echo "<h2>Artikel Nr. {$artikel->getId()} bearbeiten</h2>";
else 
	echo "<h2>Artikel bearbeiten</h2>";

?>

<form action="" id="edit_article_form" method="post">

<div id="editFields">

<table>
<tr>
	<td><input type="hidden" id="id" name="id" value="<?php echo $artikel->getId(); ?>" /></td>
</tr>
<tr>
	<td><input type="hidden" id="cmd" name="cmd" value="<?php echo $cmd; ?>" /></td>
</tr>
<tr>
	<td>ArtikelNr Lieferant:</td>
    <td><input type="text" name="artnr_lieferant" id="artnr_lieferant" value='<?php echo $artikel->getArtNrLieferant(); ?>' /></td>
</tr>
<tr>
	<td>LieferantenNr:</td>
    <td><input type="text" name="lieferantennr" id="lieferantennr" value='<?php echo $artikel->getLieferantenNr(); ?>' /></td>
</tr>
<tr>
	<td>Bezeichnung:</td>
    <td><input type="text" name="bezeichnung" id="bezeichnung" value='<?php echo $artikel->getBezeichnung(); ?>' /></td>
</tr>
<tr>
	<td>Kategorie:</td>
    <td>
    <select name="kategorie" id="kategorie" >
    	<option value="Elektro" <?php if ($artikel->getKategorie() == "Elektro") echo "selected='selected'"; ?>>Elektro</option>
        <option value="Kabel und Leitungen" <?php if ($artikel->getKategorie() == "Kabel und Leitungen") echo "selected='selected'"; ?>>Kabel &amp; Leitungen</option>
        <option value="Leuchten" <?php if ($artikel->getKategorie() == "Leuchten") echo "selected='selected'"; ?>>Leuchten</option>
        <option value="Erdungsmaterial" <?php if ($artikel->getKategorie() == "Erdungsmaterial") echo "selected='selected'"; ?>>Erdungsmaterial</option>
        <option value="Heizung und Sanitaer" <?php if ($artikel->getKategorie() == "Heizung und Sanitaer") echo "selected='selected'"; ?>>Heizung &amp; Sanitär</option>
        <option value="Photovoltaik" <?php if ($artikel->getKategorie() == "Photovoltaik") echo "selected='selected'"; ?>>Photovoltaik</option>
        <option value="Braune Ware" <?php if ($artikel->getKategorie() == "Braune Ware") echo "selected='selected'"; ?>>Braune Ware</option>
        <option value="Weisse Ware" <?php if ($artikel->getKategorie() == "Weisse Ware") echo "selected='selected'"; ?>>Weiße Ware</option>
        <option value="Sonstige" <?php if ($artikel->getKategorie() == "Sonstige") echo "selected='selected'"; ?>>Sonstige</option>
    </select>
    </td>
</tr>
<tr>
	<td>Preis (netto):</td>
    <td><input type="text" name="preis_netto" id="preis_netto" value='<?php echo $artikel->getPreisNetto(); ?>' /></td>
</tr>
<tr>
	<td>Einheit:</td>
    <td><input type="text" name="einheit" id="einheit" value='<?php echo $artikel->getEinheit(); ?>' /></td>
</tr>
</table>

</div>
<div id="waiting" style="display: none;">
     Speichert. Bitte warten...<br />
     <img src="img/ajax-loader.gif" title="Loader" alt="Loader" />
</div>

<div style="clear:both"></div>

<p>
<input type="button" id="update" value="Artikel speichern" onclick="javascript:save()" />
</p>

</form>

<p>
	<input type="button" name="home" value="Zur&uuml;ck zum Artikelbereich" onclick="javascript:goBack();"  />
</p>
</body>
</html>

<?php

include("tpl/footer.tpl.php");

?>