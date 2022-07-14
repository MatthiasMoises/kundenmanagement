<?php

include('tpl/header.tpl.php');

$benutzer = new Benutzer();
$artikel = new Artikel();

$check_user = sprintf("SELECT ist_admin FROM benutzer WHERE benutzername = '%s'",$_SESSION['benutzername']);
$get_user = mysql_query($check_user);

while ($row_get_user = mysql_fetch_assoc($get_user))
{
	$benutzer->setAdmin($row_get_user['ist_admin']);	
}

$searchTerm = "";
$searchCategory = "";

$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : NULL;
$sort = isset($_GET['sort']) ? $_GET['sort'] : NULL;

if (isset($_GET['bt_search']) && $searchCategory == "")
{
	$searchTerm = isset($_GET['search']) ? $_GET['search'] : NULL;
	$searchCategory = isset($_GET['kategorie']) ? $_GET['kategorie'] : NULL;
	$query = "SELECT * FROM artikel WHERE id = '$searchTerm' OR bezeichnung LIKE '%$searchTerm%' ORDER BY id ASC";  
}
else if (isset($_GET['bt_search']) && $searchCategory != "")
{
	$searchTerm = isset($_GET['search']) ? $_GET['search'] : NULL;
	$searchCategory = isset($_GET['kategorie']) ? $_GET['kategorie'] : NULL;
	$query = "SELECT * FROM artikel WHERE id = '$searchTerm' OR bezeichnung LIKE '%$searchTerm%' AND kategorie = '$searchCategory' ORDER BY id ASC";  	
}
else if ($orderBy != NULL && $sort != NULL){
	$query = "SELECT * FROM artikel WHERE 1 ORDER BY $orderBy $sort";	
}
else if (isset($_GET['start'])) {
	// max displayed per page
	$per_page = 50;

	// get start variable
	$start = $_GET['start'];

	// count records
	$record_count = mysql_num_rows(mysql_query("SELECT * FROM artikel"));
	
	// count max pages
	$max_pages = $record_count / $per_page; // may come out as decimal
	
	if (!$start)
		$start = 0;
		
	// display data
	$query = "SELECT * FROM artikel ORDER BY id ASC LIMIT $start, $per_page";
}
else {
	$query = "SELECT * FROM artikel WHERE 1 ORDER BY id ASC";	
}

$select = mysql_query($query) or die ("MySQL select artikel error. ".mysql_error());

$row_count = mysql_num_rows($select);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Artikelbereich</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/js/scriptaculous.js"></script>
<script type="text/javascript" src="libs/js/sorttable.js"></script>
<script>

function deleteEntry(id) {
		
		if (confirm("Wollen Sie diesen Artikel wirklich endgültig löschen?"))
		{
	
			new Ajax.Updater("","edit_article_server.php", {
		
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
				alert ("Artikel "+id+" wurde erfolgreich gelöscht!");
				location.reload(true);
			}
		});
	}
}

</script>
</head>

<body>

<h2>Artikelbereich</h2>

<div style="float:left;">
	<input type="button" name="home" value="Neuen Artikel anlegen" onclick="parent.location='edit_article.php?action=new'"  />
</div>

<div id="searchField" style="float: left;">
	<form action="" method="get">
    	<label>Suche:</label>
        <input type="text" id="search" name="search" /> in 
        <select name="kategorie" id="kategorie" >
        	<option value=""></option>
            <option value="Elektro">Elektro</option>
            <option value="Kabel und Leitungen">Kabel &amp; Leitungen</option>
            <option value="Leuchten">Leuchten</option>
            <option value="Erdungsmaterial">Erdungsmaterial</option>
            <option value="Heizung und Sanitaer">Heizung &amp; Sanitär</option>
            <option value="Photovoltaik">Photovoltaik</option>
            <option value="Braune Ware">Braune Ware</option>
            <option value="Weisse Ware">Weiße Ware</option>
            <option value="Sonstige">Sonstige</option>
        </select>
        <input type="submit" id="bt_search" name="bt_search" value="Artikel suchen" />
    </form>
</div>

<div id="excel_save">
	<a href="create_excel.php?action=article_xls" title="Kunden-Excel-Datei erstellen"><img src="img/excel.gif" alt="excel" /></a> (Artikel nach Excel 2007 exportieren)
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
		echo "<a href='show_articles.php?start=$prev'>Vorherige</a> ";
		echo "</div>";
	}
	
	// show page numbers
	
	// set variable for first page
	$i = 1;
	
	for ($x = 0; $x < $record_count; $x = $x + $per_page)
	{
		if ($start != $x) {
			echo "<div style='float: left;'>";
			echo " <a href='show_articles.php?start=$x'>$i</a> ";
			echo "</div>";
		}
		else { 
			echo "<div style='float: left;'>";
			echo " <a href='show_articles.php?start=$x'><b style='color: orange'>$i</b></a> ";
			echo "</div>";
		}
		$i++;
	}
	
	// show next button
	if (!($start >= $record_count - $per_page))	{
		echo "<div style='float: left;'>";
		echo "<a href='show_articles.php?start=$next'>N&auml;chste</a>";
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

<table id="article_table" border="1">
	<thead>
		<th>ArtNr <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=id&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=id&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren"/></a></th>
        <th>ArtikelNr Lieferant <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=artnr_lieferant&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=artnr_lieferant&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <th>LiferantenNr <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=lieferantennr&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=lieferantennr&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <th>Bezeichnung <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=bezeichnung&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=bezeichnung&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <th>Kategorie <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=kategorie&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=kategorie&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <?php if ($benutzer->getAdmin() == 1) { ?>
        <th>Preis (netto) <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=preis_netto&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=preis_netto&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <?php
		}
		?>
        <th>Einheit <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=einheit&sort=DESC"><img src="img/sort_desc.gif" alt="sort_desc" title="absteigend sortieren" /></a><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?orderBy=einheit&sort=ASC"><img src="img/sort_asc.gif" alt="sort_asc" title="aufsteigend sortieren" /></a></th>
        <th colspan="2">Optionen</th>
    </thead>
    
    	<?php
		
		$count = 0;
		
		while ($row = mysql_fetch_assoc($select))
		{
			$count++;
			$artikel->setId($row['id']);
			$artikel->setArtNrLieferant($row['artnr_lieferant']);
			$artikel->setLieferantenNr($row['lieferantennr']);
			$artikel->setBezeichnung($row['bezeichnung']);
			$artikel->setKategorie($row['kategorie']);
			$artikel->setPreisNetto(str_replace(".",",",round($row['preis_netto'],2)));			
			$artikel->setEinheit($row['einheit']);
			
			echo "<tbody>";
			
			if (($count % 2) == 0)
			{
				echo "<tr style='background-color:#FFF1AA;'>";
			}
			else {
				echo "<tr style='background-color:#9AE9FA;'>";	
			}
			
			echo "
					<td align='center'>{$artikel->getId()}</td>
					<td align='center'>{$artikel->getArtNrLieferant()}</td>
					<td align='center'>{$artikel->getLieferantenNr()}</td>
					<td align='center'>{$artikel->getBezeichnung()}</td>
					<td align='center'>{$artikel->getKategorie()}</td>";
					if ($benutzer->getAdmin() == 1)
						echo "<td align='center'>{$artikel->getPreisNetto()}</td>";
			echo   "<td align='center'>{$artikel->getEinheit()}</td>
					<td align='center'><a href='edit_article.php?action=edit&id={$artikel->getId()}'><img src='img/edit.gif' alt='edit' title='Artikel bearbeiten' /></a></td>
					<td align='center'><a href='javascript:deleteEntry({$artikel->getId()})'><img src='img/cross.gif' alt='delete' title='Artikel l&ouml;schen' /></a></td>
				</tr>
				";
		}
		
		echo "</tbody>";
		
    	?>
    
</table>

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
		echo "<a href='show_articles.php?start=$prev'>Vorherige</a> ";
		echo "</div>";
	}
	
	// show page numbers
	
	// set variable for first page
	$i = 1;
	
	for ($x = 0; $x < $record_count; $x = $x + $per_page)
	{
		if ($start != $x) {
			echo "<div style='float: left;'>";
			echo " <a href='show_articles.php?start=$x'>$i</a> ";
			echo "</div>";
		}
		else { 
			echo "<div style='float: left;'>";
			echo " <a href='show_articles.php?start=$x'><b style='color: orange'>$i</b></a> ";
			echo "</div>";
		}
		$i++;
	}
	
	// show next button
	if (!($start >= $record_count - $per_page))	{
		echo "<div style='float: left;'>";
		echo "<a href='show_articles.php?start=$next'>N&auml;chste</a>";
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
	<input type="button" name="home" value="Neuen Artikel anlegen" onclick="parent.location='edit_article.php?action=new'"  />
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