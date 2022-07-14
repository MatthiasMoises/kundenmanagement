<?php

include('tpl/header.tpl.php');

$get_rights = mysql_query("SELECT ist_admin FROM benutzer WHERE benutzername = '".$_SESSION['benutzername']."'") or die ("MySQL get rights error. ".mysql_error());

while ($row_get_rights = mysql_fetch_assoc($get_rights))
{
	$ist_admin = $row_get_rights['ist_admin'];	
}

$rNr = isset($_GET['acc_nr']) ? $_GET['acc_nr'] : NULL;
$action = isset($_GET['action']) ? $_GET['action'] : NULL;
$i = 0;

$neededArtikelRows = 0;

$db_artnr = "";
$db_art_bezeichnung = "";
$db_art_menge = "";
$db_art_netto = "";
$db_art_gesamtpreis = "";

$rRechnung = mysql_query("SELECT id, lieferscheinnr, DATE_FORMAT (lieferscheindatum, '%d.%m.%Y') AS rech_date, kundennachricht, hat_stunden, hat_artikel, rabatt_prozent, rabatt_betrag, skonto_prozent, skonto_betrag, mwst_prozent, endbetrag, kdnr, bezahlt, editierbar FROM lieferscheine WHERE lieferscheinnr = '$rNr'") or die("MySQL select delivery bill data error. ".mysql_error());

$rKunde = mysql_query("SELECT k.* FROM kunden k, lieferscheine l WHERE l.kdnr = k.id AND l.lieferscheinnr = '$rNr'") or die("MySQL select customer error. ".mysql_error()); 

$rArtikel = mysql_query("SELECT a.id, a.bezeichnung, a.preis_netto, la.art_menge, la.gesamtpreis_artikel FROM artikel a, lieferscheine l, l_artikel la WHERE l.lieferscheinnr = '$rNr' AND l.lieferscheinnr = la.lieferscheinnr AND la.artnr = a.id ORDER by la.id ASC") or die("MySQL select artikel error. ".mysql_error());

while ($row_rechnung = mysql_fetch_assoc($rRechnung))
{
	$db_rechnungs_id = $row_rechnung['id'];
	$db_rechnungsnr = $row_rechnung['lieferscheinnr'];
	$db_rechnungsdatum = $row_rechnung['rech_date'];
	$db_message = $row_rechnung['kundennachricht'];
	$db_bezahlt = $row_rechnung['bezahlt'];	
	$db_rabatt_prozent = $row_rechnung['rabatt_prozent'];
	$db_skonto_prozent = $row_rechnung['skonto_prozent'];
	$db_rabatt_betrag = $row_rechnung['rabatt_betrag'];
	$db_skonto_betrag = $row_rechnung['skonto_betrag'];
	$db_mwst_prozent = $row_rechnung['mwst_prozent'];
	$db_editierbar = $row_rechnung['editierbar'];
	
	$db_rabatt_betrag = str_replace(".",",",$db_rabatt_betrag);
	$db_skonto_betrag = str_replace(".",",",$db_skonto_betrag);	
	
	$db_rabatt_prozent = str_replace(".",",",$db_rabatt_prozent);
	$db_skonto_prozent = str_replace(".",",",$db_skonto_prozent);
	$db_mwst_prozent = str_replace(".",",",$db_mwst_prozent);
}

while ($row_kunde = mysql_fetch_assoc($rKunde))
{
	$db_kdnr = $row_kunde['id'];
	$db_vorname = $row_kunde['vorname'];
	$db_nachname = $row_kunde['nachname'];	
}

$i = 0;
while ($row_artikel = mysql_fetch_assoc($rArtikel))
{
	$neededArtikelRows++;
	$db_artnr[$i] = $row_artikel['id'];
	$db_art_bezeichnung[$i] = $row_artikel['bezeichnung'];
	$db_art_menge[$i] = $row_artikel['art_menge'];
	$db_art_netto[$i] = $row_artikel['preis_netto'];
	$db_art_gesamtpreis[$i] = $row_artikel['gesamtpreis_artikel'];
    ++$i;
}

?>
 
<script type="text/javascript">
 
db_artnr = <?php echo json_encode($db_artnr); ?>;
db_art_bezeichnung = <?php echo json_encode($db_art_bezeichnung); ?>;
db_art_menge = <?php echo json_encode($db_art_menge); ?>;
db_art_netto = <?php echo json_encode($db_art_netto); ?>;
db_art_gesamtpreis = <?php echo json_encode($db_art_gesamtpreis); ?>;
 
</script>

<?php

$alleMitarbeiter = mysql_query("SELECT name FROM benutzer WHERE 1");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Lieferschein Nr. <?php echo $rNr; ?> bearbeiten</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/js/scriptaculous.js"></script>
<script type="text/javascript" src="libs/js/effects.js"></script>
<script type="text/javascript" src="libs/js/controls.js"></script>
<style type="text/css">
	#bezeichnung, ul {background-color:#ffffff; height:150px; overflow:scroll; padding: 3px; width: 500px; border: 1px solid #999; font-family: verdana; arial, sans-serif; font-size: 12px;}
	ul { list-style-type: none; font-family: verdana; arial, sans-serif; font-size: 12px;  margin: 5px 0 0 0}
	li { margin: 0 0 5px 0; cursor: default; color: blue;}
	li:hover { background: #ffc; }
</style>

<?php

if ($action == "" || $rNr == "" || !isset($action) || !isset($rNr))
{
	die("<span style='color:red'>Keine Lieferscheinnummer angegeben!</span>");
}

if ($db_editierbar == '0')
{
	die("<span style='color:red'>Dieser Lieferschein kann nicht mehr bearbeitet werden!</span>");	
}
?>

<script type="text/javascript">
var stundenCount = 0;
var artikelCount = 0;
var gesamtCount = 0;

function prepareElements(neededArtikel){
	$('sameNameCustomers').hide();	

	for (var i = 1; i <= neededArtikel; i++)
	{
        addRowArtikel('artikel');
        $('artikelNr_'+i).value = db_artnr[i-1];
        $('bezeichnung_'+i).value = db_art_bezeichnung[i-1];
        $('menge_'+i).value = db_art_menge[i-1].replace(".",",");
        $('euro_stueck_'+i).value = db_art_netto[i-1].replace(".",",");
		$('euro_gesamt_'+i).value = db_art_gesamtpreis[i-1].replace(".",",");
    }
}

function form_input_is_numeric(input){
    return !isNaN(input);
}

function openArticleWindow() {
	articleWindow = window.open("show_articles_window.php","articleWindow","scrollbars=1,width=600,height=500,left=350,top=100");	
}

function openCustomerWindow() {
	customerWindow = window.open("show_customers_window.php","customerWindow","scrollbars=1,width=600,height=500,left=350,top=100");	
}
	
function switchName(){
	var choice = document.getElementById('sameName').value;

	var kdaten = choice.split("~");
	$('kd_nr').value = kdaten[0];
	$('vorname').value = kdaten[1];	
}

function resetRowArtikel(id){
	id = id.replace("resetAr_","");
	$('artikelNr_'+id).value = "";
	$('bezeichnung_'+id).value = "";
	$('menge_'+id).value = "";
	$('euro_stueck_'+id).value = "";
	$('euro_gesamt_'+id).value = "";
}

function addRowArtikel(id){
	
	++artikelCount;
	
	var artikelNr = document.createElement("input")
	artikelNr.name = "artikelNr_"
	artikelNr.id = artikelNr.name+artikelCount
	artikelNr.style.width = "50"
	var bezeichnung = document.createElement("input")
	bezeichnung.name = "bezeichnung_"
	bezeichnung.id = bezeichnung.name+artikelCount
	bezeichnung.style.width = "500"
	var menge = document.createElement("input")
	menge.name = "menge_"
	menge.id = menge.name+artikelCount
	menge.style.width = "50"
	var euro_stueck = document.createElement("input")
	euro_stueck.name = "euro_stueck_"
	euro_stueck.id = euro_stueck.name+artikelCount
	euro_stueck.style.width = "100"
	euro_stueck.setAttribute("disabled",true)
	var euro_gesamt = document.createElement("input")
	euro_gesamt.name = "euro_gesamt_"
	euro_gesamt.id = euro_gesamt.name+artikelCount
	euro_gesamt.style.width = "100"
	euro_gesamt.setAttribute("disabled",true)
	var resetBtAr = document.createElement("input")
	resetBtAr.setAttribute("type","button")
	resetBtAr.setAttribute("value","Reset")
	resetBtAr.name = "resetAr_"
	resetBtAr.id = resetBtAr.name+artikelCount
	resetBtAr.setAttribute("onclick","javascript:resetRowArtikel(this.id)")
	var deleteBtAr = document.createElement("input")
	deleteBtAr.setAttribute("type","button")
	deleteBtAr.setAttribute("value","Zeile löschen")
	deleteBtAr.name = "rowAr_"
	deleteBtAr.id = deleteBtAr.name+artikelCount
	deleteBtAr.setAttribute("onclick","javascript:removeSpecificRow(this.id, 'artikel', artikelCount)")
	
	var tbody = document.getElementById(id).getElementsByTagName("tbody")[0];
	var row = document.createElement("tr")
	row.name = "rowAr_"
	row.id = row.name+artikelCount
	var data1 = document.createElement("td")
	data1.appendChild(artikelNr)
	var data2 = document.createElement("td")
	data2.appendChild (bezeichnung)
	var data3 = document.createElement("td")
	data3.appendChild(menge)
	var data4 = document.createElement("td")
	data4.appendChild (euro_stueck)
	var data5 = document.createElement("td")
	data5.appendChild(euro_gesamt)
	var data6 = document.createElement("td")
	data6.appendChild(resetBtAr)
	var data7 = document.createElement("td")
	data7.appendChild(deleteBtAr)
	var divCompleter = document.createElement("div")
	divCompleter.name = "autoCompleter_"
	divCompleter.id = divCompleter.name+artikelCount
	data2.appendChild(divCompleter);
	row.appendChild(data1);
	row.appendChild(data2);
	row.appendChild(data3);
	row.appendChild(data4);
	row.appendChild(data5);
	row.appendChild(data6);
	row.appendChild(data7);
	tbody.appendChild(row);
	
	new Ajax.Autocompleter(bezeichnung.id,divCompleter.id,"autocomplete_server.php", {parameters: artikelCount, minChars: 1});
	
	$('btErfasseArtikel').show();
	
}

function removeSpecificRow(id, tableName, count)
{
	var tbody = document.getElementById(tableName).getElementsByTagName("tbody")[0];
	var lastRow = tbody.rows.length;
	var rowIndex = document.getElementById(id).rowIndex;	
	
	if (tableName == "artikel")
		id = id.replace("rowAr_","");
		
	if (id > 0)	{
		document.getElementById(tableName).deleteRow(rowIndex);
	}
}

function removeRowFromTable(id){	
	var tbody = document.getElementById(id).getElementsByTagName("tbody")[0];
	var lastRow = tbody.rows.length;
	if (lastRow > 1) tbody.deleteRow(lastRow - 1);
	
	if (id == "artikel")
		artikelCount--;	

}

function sucheKunde(){	
		if ($('kd_nr').value != "" || $('nachname').value != "") 
		{
	
		$('waiting').show();
		var i;
		for(i=document.getElementById('sameName').options.length-1;i>=0;i--)
		{
		document.getElementById('sameName').remove(i);
		}
		$('sameNameCustomers').hide();
		
		$('vorname').value = "";
		
		var kundennr = $('kd_nr').value;
		var nachname = $('nachname').value;
		
		new Ajax.Request('edit_delivery_bill_server.php',
		{
			method : 'post',
			parameters : {
				'cmd'			: 'search_user',
				'kundennr'		: kundennr,
				'nachname'		: nachname
		},
		onSuccess : function(transport){
			var response = transport.responseText;
			
			if (response != "")
			{
				$('waiting').hide();
				var daten = response.split("|");
				var firstResult = true;
				daten.each(function(kdaten) {
					var kundendaten = kdaten.split("~");
					if (daten.length == 0)
					{
						$('kd_nr').value = "";
						$('nachname').value = "";
						$('vorname').value = "";	
						alert("Kunde nicht gefunden!");
					}
					else if (daten.length == 1) {
						$('kd_nr').value = kundendaten[0];
						$('nachname').value = kundendaten[1];
						$('vorname').value = kundendaten[2];	
					}
					else {
					if (firstResult) {
						$('kd_nr').value = kundendaten[0];
						$('nachname').value = kundendaten[1];
						$('vorname').value = kundendaten[2];
					}
					$('sameNameCustomers').show();	
					var option = document.createElement("option");
					option.setAttribute("value",kundendaten[0]+"~"+kundendaten[2]);
					var optiontext = kundendaten[1]+", "+kundendaten[2];
					option.appendChild(document.createTextNode(optiontext));
					document.getElementById("sameName").appendChild(option);
					}
					firstResult = false;
			})	
		}
		else {
			$('waiting').hide();
			alert("Es konnte leider kein passender Kunde gefunden werden!");	
		}
			
		},
		
		onFailure: function () {
			$('waiting').hide(); 
			alert("Es ist leider ein Fehler aufgetreten!");}
		});
	}
	else {
		$('waiting').hide();
		alert("Bitte KdNr oder Nachname angeben!");	
	}
}

function check_account_nr_update(){
	
	$('waiting').show();

        var db_rechnungs_id = <?php echo $db_rechnungs_id;?>;
        var db_rechnungsnr_alt = <?php echo $db_rechnungsnr;?>;
	if ($('rechnungsnr').value != "")
	{	
		new Ajax.Request("edit_delivery_bill_server.php",
		{
			parameters: {
				'cmd'		      : 'check_account_nr_update',
                'rechnungs_id'    : db_rechnungs_id,
                'rechnungsnr_alt' : db_rechnungsnr_alt,
				'rechnungsnr'     : $('rechnungsnr').value
			},
			onSuccess : function(result){
				var response = result.responseText;
				
				if (response === "fehler") {
					alert("Lieferscheinnummer bereits vergeben!");
				}
				else if (response === "update"){
					updateAccount();
				}
                else if (response === "save"){
					saveAccount();
				}
			}
		});
	}	
	else {
		$('waiting').hide();
		alert("Sie haben keine Lieferscheindaten eingegeben!");
	}
}

function updateAccount(){

	  if ($('rechnungsnr').value > 0 && $('rechnungsdatum').value != "")
	  {	
		  if ($('kd_nr').value != "" && $('nachname').value != "" && $('vorname').value != "")
		  {
			  
			  	$('save_account').disabled = true;
			  
				  var artikelString = new Array();
				  
				  if (document.getElementsByName("artikelNr_").length > 0)
				  {
					  for (var i=0;i<document.getElementsByName("artikelNr_").length;i++) {
						  artikelString[i] = document.getElementsByName("artikelNr_")[i].value+";"+document.getElementsByName("bezeichnung_")[i].value+";"+document.getElementsByName("menge_")[i].value.replace(",",".")+";"+document.getElementsByName("euro_stueck_")[i].value+";"+document.getElementsByName("euro_gesamt_")[i].value;
					  }	
				  }
				  
				  if (artikelString != "")
				  {
					  new Ajax.Updater("","edit_delivery_bill_server.php", {
						  
					  parameters : {
							  
					  // cmd		
							  
					  'cmd'				: 'update',
					  
					  // Kundendaten
					  
					  'rechnungs_id'	  : $('rechnungs_id').value,
					  'rechnungsnr'		  : $('rechnungsnr').value,
					  'rechnungsdatum'	  : $('rechnungsdatum').value,
					  'message' 		  : $('message').value,
					  'kd_nr'			  : $('kd_nr').value,
					  'rabatt_prozent'	  : $('rabatt_prozent').value,
					  'skonto_prozent'	  : $('skonto_prozent').value,
					  'rabatt_betrag'     : $('rabatt_betrag').value,
					  'skonto_betrag'     : $('skonto_betrag').value,
					  'mwst_prozent'	  : $('mwst_prozent').value,
					  'bezahlt'			  : ($('bezahlt').checked) ? 1 : 0,
					  'editierbar'		  : ($('editierbar').checked) ? 1 : 0,
					  'artikelString[]'   : artikelString
					  },
					  evalScripts : true,
					  encoding : 'ISO-8859-1',
							  
					  onFailure : function() {
						  $('waiting').hide();
						  alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
					  },
							  
					  onComplete : function() {
						  $('waiting').hide();
						  $('save_account').disabled = false;
						  alert ("Lieferschein Nr. "+$('rechnungsnr').value+" wurde gespeichert!");
						  location.href="show_open_delivery_bills.php";
					  }
					  });
			  
				  }
				  else if (artikelString == "")
				  {
					  $('waiting').hide();
					  if (artikelString == "")
					  {
						  alert("Sie haben keinen Artikel angegeben!");	
					  }
				  }
		  }
		  else {
			  $('waiting').hide();
			  alert("Sie haben keinen Kunden ausgewählt oder die Daten sind unvollständig!");	
		  }
	  }
	  else  {
		  $('waiting').hide();
		  alert("Sie haben keine oder ungültige Lieferscheindaten eingegeben!");
	  }
}

function erfasseArtikel(){
	
	$('waiting').show();

	var artikelListe = new Array();
	var error = false;
	

	for (var i = 0; i < document.getElementsByName("artikelNr_").length; i++) {	
		
		if (document.getElementsByName("artikelNr_")[i].value != ""){
			
			artikelListe[i] = document.getElementsByName("artikelNr_")[i].value;
			error = false;
		}
		else if (document.getElementsByName("artikelNr_")[i].value == "" && document.getElementsByName("bezeichnung_")[i].value != ""){
			artikelListe[i] = document.getElementsByName("bezeichnung_")[i].value;
			artikelListe[i].replace(",","~");
			
			var intIndexOfMatch = artikelListe[i].indexOf( "," );

			while (intIndexOfMatch != -1){

				artikelListe[i] = artikelListe[i].replace( ",", "~" );
				intIndexOfMatch = artikelListe[i].indexOf( "," );
			}
			error = false;
		}
		else if (document.getElementsByName("artikelNr_")[i].value == "" && document.getElementsByName("bezeichnung_")[i].value == ""){
			artikelListe = ",";
			error = true;
		}
		else{
			artikelListe = ",";
			error = true;
			alert("Sie haben nicht alle notwendigen Artikeldaten angegeben!");
		}
	}
	
	if (artikelListe != "," && error == false)
	{
		new Ajax.Request('edit_delivery_bill_server.php',
		{
			method : 'post',
			parameters : {
				'cmd'				: 'get_article',
				'artikelListe[]'	: artikelListe
			},
			onSuccess : function(transport){
				$('waiting').hide();
				var response = transport.responseText;
				
				if (response != "")
				{
					var daten = response.split("|");
					var count = 0;
					
					daten.each(function(artDaten) {
						
						var artikeldaten = artDaten.split("~");
						document.getElementsByName("artikelNr_")[count].value = artikeldaten[0];
						document.getElementsByName("bezeichnung_")[count].value = artikeldaten[1];
						document.getElementsByName("euro_gesamt_")[count].value = runde(artikeldaten[2] * document.getElementsByName("menge_")[count].value.replace(",","."));
						
						document.getElementsByName("euro_stueck_")[count].value = runde(artikeldaten[2]);
						count++;
					})	
				}
				else {
					$('waiting').hide();
					alert("Mindestens 1 Artikel wurde nicht gefunden! Bitte korrigieren!");	
				}
			},
			onFailure: function () { 
				$('waiting').hide();
				alert("Es ist leider ein Fehler aufgetreten!");
			}
		});
	}		
	else {
		$('waiting').hide();
		alert("Keine Artikel angegeben! Löschen sie eventuelle Leerzeilen und füllen Sie alle notwendigen Felder aus!");	
	}
}	

function textLimit(field, maxlen) {
	if (field.value.length > maxlen + 1)
	alert('Zeichenlimit erreicht, Text wird automatisch gekürzt!');
	if (field.value.length > maxlen)
	field.value = field.value.substring(0, maxlen);
}

function runde(x) {
  var k = (Math.round(x * 100) / 100).toString();
  k += (k.indexOf('.') == -1)? '.00' : '00';
  var p = k.indexOf('.');
  return k.substring(0, p) + ',' + k.substring(p+1, p+3);
}

function closeIt()
{
  return "Any string value here forces a dialog box to \n" + 
         "appear before closing the window.";
}
window.onbeforeunload = closeIt;

</script>
</head>

<body onLoad="javascript:prepareElements(<?php echo $neededArtikelRows; ?>)">

<h1>Lieferschein bearbeiten</h1>

<hr />

<h2>Kundendaten</h2>

<div>
<div style="float:left;">
<table id="allgemein" border="0">
    <tr>
    	<td>Lieferscheinnr:</td>
        <td><input type="text" id="rechnungsnr" name="rechnungsnr" value="<?php echo $db_rechnungsnr; ?>" /></td>
   		<input type="hidden" id="rechnungs_id" name="rechnungs_id" value="<?php echo $db_rechnungs_id; ?>" />
    </tr>	
     <tr>
    	<td>Lieferscheindatum:</td>
        <td><input type="text" id="rechnungsdatum" name="rechnungsdatum" value="<?php echo $db_rechnungsdatum; ?>" /></td>
    </tr>	
</table>
</div>

<div style="margin-left:300px; float:left;">
Bezahlt: <input type="checkbox" id="bezahlt" name="bezahlt" <?php if ($db_bezahlt == 1) echo 'checked="checked"'; ?> />
</div>

<div style="margin-left:700px;">
Weiterhin editierbar: <input type="checkbox" id="editierbar" name="editierbar" <?php if ($db_editierbar == 1) echo 'checked="checked"'; ?> />
</div>

</div>
<div style="clear:both"></div>
<br />

<form>

<div style="float:left;">
<table id="kunde" cellspacing="0" border="1" width="500">
	<tr>
    	<td>KdNr</td>
        <td><input style="width:100px;" type="text" id="kd_nr" name="kd_nr" value="<?php echo $db_kdnr; ?>" /></td>
        <td><input type="button" id="suche_kd" name="suche_kd" value="Suche Kunde" onClick="javascript:sucheKunde()" /></td>
    </tr>
    <tr>
    	<td>Nachname</td>
        <td><input style="width:300px;" type="text" id="nachname" name="nachname" value="<?php echo $db_nachname; ?>" /></td>
        <td><input type="reset" name="reset_kunde" id="reset_kunde" /></td>
    </tr>	
        <tr>
    	<td>Vorname</td>
        <td><input style="width:300px;" type="text" id="vorname" name="vorname" value="<?php echo $db_vorname; ?>"/></td>
    </tr>	
</table>
</div>

</form>

<div style="margin-left:550px;">
<p>
[<a href="show_customers_window.php" target="customerWindow" onClick="openCustomerWindow()">Kundenliste anzeigen</a>]
</p>
</div>

<div style="clear:both;"></div>

<p>

<div id="sameNameCustomers">
<span style="color:#F00;">Mehrere Treffer! Bitte entsrpechenden Kunden ausw&auml;hlen:</span><br/>
<select id="sameName" name="sameName" onChange="javascript:switchName()">

</select>
</div>
</p>

<h2>Informationen f&uuml;r den Kunden angeben (optional)</h2>

<div id="kundenInfo">
<textarea id="message" name="message" cols="1" rows="4" onKeyUp="textLimit(this, 148);"><?php echo $db_message; ?></textarea>
</div>

<h2>Artikeldaten angeben</h2>

<p>
[<a href="show_articles_window.php" target="articleWindow" onClick="openArticleWindow()">Artikelliste anzeigen</a>]
</p>

<table id="artikel" cellspacing="0" border="1" width="1000">
<tbody>
<tr>
	<td><b>ArtNr</b></td>
    <td><b>Bezeichnung</b></td>
    <td><b>Menge</b></td>
    <td><b>Euro/St&uuml;ck</b></td>
    <td><b>Euro Gesamt</b></td>
    <td><b>Reset</b></td>
    <td><b>Zeile l&ouml;schen</b></td>
</tr>
</tbody>
</table>

<p>
<button onClick="javascript:addRowArtikel('artikel')">Neue Zeile</button>
<button onClick="javascript:removeRowFromTable('artikel')">Letzte Zeile entfernen</button>
<div id="btErfasseArtikel">
<?php
if ($ist_admin == 1)
{
?>
<button style="margin-left:500px;" onClick="javascript:erfasseArtikel()">Erfasse Artikel</button>
<?php
}
else {
?>
<button style="margin-left:500px;" onClick="javascript:erfasseArtikel()" disabled="disabled">Erfasse Artikel</button>
<?php
}
?>

</div>
</p>
<hr />

<h2>MWST angeben</h2>

<table>
	<tr>
    	<td><label>MWST %: </label></td>
        <td><input type="text" id="mwst_prozent" name="mwst_prozent" value="<?php if ($db_mwst_prozent != 0) echo $db_mwst_prozent; else echo "19";?>" /></td>
    </tr>
</table>

<h2>Rabatt und Skonto angeben</h2>

<table>
	<tr>
    	<td><label>Rabatt %: </label></td>
        <td><input type="text" id="rabatt_prozent" name="rabatt_prozent" value="<?php if ($db_rabatt_prozent != 0) echo $db_rabatt_prozent; else echo "0";?>" /></td>
        <td><label>Skonto %: </label></td>
        <td><input type="text" id="skonto_prozent" name="skonto_prozent" value="<?php if ($db_skonto_prozent != 0) echo $db_skonto_prozent; else echo "0";?>" /></td>
    </tr>
    <tr>
    	<td><label>Rabatt &euro;: </label></td>
        <td><input type="text" id="rabatt_betrag" name="rabatt_betrag" value="<?php if ($db_rabatt_betrag != 0) echo $db_rabatt_betrag; else echo "0";?>" /></td>
        <td><label>Skonto &euro;: </label></td>
        <td><input type="text" id="skonto_betrag" name="skonto_betrag" value="<?php if ($db_skonto_betrag != 0) echo $db_skonto_betrag; else echo "0";?>" /></td>
    </tr>
</table>

<p>(<b>Hinweis:</b> Nur Prozent- ODER W&auml;hrungsbetr&auml;ge angeben! Es werden nicht beide Felder ausgewertet!</p>

<hr />
<div style="float:left;">
<p>
<button style="margin-left:500px;" id="save_account" name="save_account" onClick="javascript:check_account_nr_update()">Lieferschein speichern</button>
</p>
</div>
<div id="waiting" style="display: none;">
     Bitte warten...<br />
     <img src="img/ajax-loader.gif" title="Loader" alt="Loader" />
</div>
<div style="clear:both"></div>
</body>
</html>

<?php

include("tpl/footer.tpl.php");

?>