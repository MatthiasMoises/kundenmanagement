<?php

include('tpl/header.tpl.php');

$kunde = new Kunde();

$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : NULL;
$sort = isset($_GET['sort']) ? $_GET['sort'] : NULL;

if (isset($_GET['bt_search']))
{
	$searchTerm = $_GET['search'];
	$query = "SELECT * FROM kunden WHERE id = '$searchTerm' OR nachname LIKE '%$searchTerm%' ORDER BY id ASC";  
}
else if ($orderBy != NULL && $sort != NULL){
	$query = "SELECT * FROM kunden WHERE 1 ORDER BY $orderBy $sort";	
}
else if (isset($_GET['start'])) {
	// max displayed per page
	$per_page = 50;

	// get start variable
	$start = $_GET['start'];

	// count records
	$record_count = mysql_num_rows(mysql_query("SELECT * FROM kunden"));
	
	// count max pages
	$max_pages = $record_count / $per_page; // may come out as decimal
	
	if (!$start)
		$start = 0;
		
	// display data
	$query = "SELECT * FROM kunden ORDER BY id ASC LIMIT $start, $per_page";
}
else {
	$query = "SELECT * FROM kunden WHERE 1 ORDER BY id ASC";	
}

$select = mysql_query($query) or die ("MySQL select kunden error. ".mysql_error());

$row_count = mysql_num_rows($select);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kundenbereich</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/js/scriptaculous.js"></script>
<script>

function del(id) {
		
		if (confirm("Wollen Sie diesen Kunden wirklich endgültig löschen?"))
		{
	
			new Ajax.Updater("","edit_customer_server.php", {
		
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
				alert ("Kunde "+id+" wurde erfolgreich gelöscht!");
				location.reload(true);
			}
		});
	}
}

</script>
</head>

<body>

<h2>Kundenbereich</h2>

<div style="float:left;">
	<input type="button" name="home" value="Neuen Kunden anlegen" onClick="parent.location='edit_customer.php?action=new'" />
</div>

<div id="searchField" style="float: left;">
	<form action="" method="get">
    	<label>Suche:</label>
        <input type="text" id="search" name="search" />
        <input type="submit" id="bt_search" name="bt_search" value="Kunde suchen" />
    </form>
</div>

<div id="excel_save">
	<a href="create_excel.php?action=customer_xls" title="Kunden-Excel-Datei erstellen"><img src="img/excel.gif" alt="excel" /></a> (Kunden nach Excel 2007 exportieren)
</div>

<div style="clear:both;"></div>

<hr />

<?php

if (isset($_GET['start']))
{
	// setup previous and next variables
	$prev = $start - $per_page;
	$next = $start + $per_page;
	
	// show previous button
	if (!($start <= 0)) {
		echo "<div style='float: left;'>";
		echo "<a href='show_customers.php?start=$prev'>Vorherige</a> ";
		echo "</div>";
	}
	
	// show page numbers
	
	// set variable for first page
	$i = 1;
	
	for ($x = 0; $x < $record_count; $x = $x + $per_page)
	{
		if ($start != $x) {
			echo "<div style='float: left;'>";
			echo " <a href='show_customers.php?start=$x'>$i</a> ";
			echo "</div>";
		}
		else { 
			echo "<div style='float: left;'>";
			echo " <a href='show_customers.php?start=$x'><b style='color: orange'>$i</b></a> ";
			echo "</div>";
		}
		$i++;
	}
	
	// show next button
	if (!($start >= $record_count - $per_page))	{
		echo "<div style='float: left;'>";
		echo "<a href='show_customers.php?start=$next'>N&auml;chste</a>";
		echo "</div>";	
	}
	
	echo "<div align='right'>";
	echo "<p><b>Zeige Eintr&auml;ge $start bis $next</b></p>";	
	echo "</div>";
}
else {
	echo "<b>Zeige $row_count Eintr&auml;ge</b><br/>";	
}

?>

<hr/>

<form id="customer_form" action="" method="post">

<table id="customer_table" border="1">
	<thead>
		<th>KdNr</th>
        <th>Anrede</th>
        <th>Vorname</th>
        <th>Nachname <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=nachname&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=nachname&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <th>Kontennr</th>
        <th>Strasse</th>
        <th>Hausnr</th>
        <th>Adresszusatz 1</th>
        <th>Adresszusatz 2</th>
        <th>PLZ</th>
        <th>Ort</th>
        <th>Email</th>
        <th>Telefon</th>
        <th colspan="2">Optionen</th>
	</thead>
    
    	<?php
		
		$count = 0;
		
		while ($row = mysql_fetch_assoc($select))
		{
			$count++;
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
			
			if (($count % 2) == 0)
			{
				echo "<tr style='background-color:#FFF1AA;'>";
			}
			else {
				echo "<tr style='background-color:#9AE9FA;'>";	
			}
			
			echo "
					<td align='center'>{$kunde->getId()}</td>
					<td align='center'>{$kunde->getAnrede()}</td>
					<td align='center'>{$kunde->getVorname()}</td>
					<td align='center'>{$kunde->getNachname()}</td>
					<td align='center'>{$kunde->getKontennummer()}</td>
					<td align='center'>{$kunde->getStrasse()}</td>
					<td align='center'>{$kunde->getHausnummer()}</td>
					<td align='center'>{$kunde->getAdressZusatz1()}</td>
					<td align='center'>{$kunde->getAdressZusatz2()}</td>
					<td align='center'>{$kunde->getPlz()}</td>
					<td align='center'>{$kunde->getOrt()}</td>
					<td align='center'>{$kunde->getEmail()}</td>
					<td align='center'>{$kunde->getTelefon()}</td>
					<td align='center'><a href='edit_customer.php?action=edit&id={$kunde->getId()}'><img src='img/edit.gif' alt='edit' title='Kunde bearbeiten' /></a></td>
					<td align='center'><a href='javascript:del({$kunde->getId()})'><img src='img/cross.gif' alt='delete' title='Kunde l&ouml;schen' /></a></td>
				</tr>
				";
		}
		
    	?>
    
</table>

</form>

<hr/>

<?php

if (isset($_GET['start']))
{
	// setup previous and next variables
	$prev = $start - $per_page;
	$next = $start + $per_page;
	
	// show previous button
	if (!($start <= 0)) {
		echo "<div style='float: left;'>";
		echo "<a href='show_customers.php?start=$prev'>Vorherige</a> ";
		echo "</div>";
	}
	
	// show page numbers
	
	// set variable for first page
	$i = 1;
	
	for ($x = 0; $x < $record_count; $x = $x + $per_page)
	{
		if ($start != $x) {
			echo "<div style='float: left;'>";
			echo " <a href='show_customers.php?start=$x'>$i</a> ";
			echo "</div>";
		}
		else { 
			echo "<div style='float: left;'>";
			echo " <a href='show_customers.php?start=$x'><b style='color: orange'>$i</b></a> ";
			echo "</div>";
		}
		$i++;
	}
	
	// show next button
	if (!($start >= $record_count - $per_page))	{
		echo "<div style='float: left;'>";
		echo "<a href='show_customers.php?start=$next'>N&auml;chste</a>";
		echo "</div>";	
	}
	
	echo "<div align='right'>";
	echo "<p><b>Zeige Eintr&auml;ge $start bis $next</b></p>";	
	echo "</div>";
}
else {
	echo "<b>Zeige $row_count Eintr&auml;ge</b><br/>";	
}

?>

<hr />

<div>
	<input type="button" name="home" value="Neuen Kunden anlegen" onClick="parent.location='edit_customer.php?action=new'" />
</div>

<hr  />

<p>
<a href="main.php">Zur&uuml;ck zur Startseite</a>
</p>

</body>
</html>

<?php

include("tpl/footer.tpl.php");

?>