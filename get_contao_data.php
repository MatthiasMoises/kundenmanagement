<?php

include('tpl/header.tpl.php');

$con = mysql_connect(CONTAO_DB_HOST,CONTAO_DB_USER,CONTAO_DB_PASS);
$sel = mysql_select_db(CONTAO_DB_NAME);

if (isset($_POST['copy'])) {
	$copy_query = mysql_query("SELECT * FROM tl_customer_data WHERE 1") or die ("MySQL select contao customers data error. ".mysql_error());
	
	while ($row_copy_data = mysql_fetch_assoc($copy_query))
	{
		$copy_id = $row_copy_data['id'];
		$copy_anrede = $row_copy_data['anrede'];
		$copy_vorname = $row_copy_data['vorname'];
		$copy_nachname = $row_copy_data['nachname'];
		$copy_strasse = $row_copy_data['strasse'];
		$copy_hausnummer = $row_copy_data['hausnummer'];
		$copy_plz = $row_copy_data['plz'];
		$copy_ort = $row_copy_data['ort'];
		$copy_email = $row_copy_data['email'];
		$copy_telefon = $row_copy_data['telefon'];	
		
		$write = mysql_query("INSERT INTO kunden (anrede, vorname, nachname, strasse, hausnummer, ort, plz, email, telefon) VALUES ('$copy_anrede','$copy_vorname','$copy_nachname','$copy_strasse','$copy_hausnummer','$copy_plz','$copy_ort','$copy_email','$telefon')");
		
		if ($write) {
			echo "Kunden wurden erfolgreich übertragen.";
			mysql_query("SELECT FROM tl_customer_data WHERE 1") or die ("MySQL delete contao customers data error. ".mysql_error());
		}	
		else
			echo "Beim Übertragen ist ein Fehler aufgetreten. Bitte Administrator kontaktieren.";
	}	
}

$query = mysql_query("SELECT * FROM tl_customer_data WHERE 1") or die ("MySQL select contao customers data error. ".mysql_error());

echo "<h2>&Uuml;ber Homepage registrierte Kunden</h2>";

echo "
<form id='copy_customers' action='' method='post'>
	<input type='submit' id='copy' name='copy' value='Registrierte Kunden in Kundenstamm übertragen' />
</form>
";

echo "<table>
		<thead>
			<th>ID</th>
			<th>Anrede</th>
			<th>Vorname</th>
			<th>Nachname</th>
			<th>Strasse</th>
			<th>Hausnummer</th>
			<th>PLZ</th>
			<th>Ort</th>
			<th>E-Mail</th>
			<th>Telefon</th>
		</thead>";

while ($row_data = mysql_fetch_assoc($query))
{
	$c_id = $row_data['id'];
	$c_anrede = $row_data['anrede'];
	$c_vorname = $row_data['vorname'];
	$c_nachname = $row_data['nachname'];
	$c_strasse = $row_data['strasse'];
	$c_hausnummer = $row_data['hausnummer'];
	$c_plz = $row_data['plz'];
	$c_ort = $row_data['ort'];
	$c_email = $row_data['email'];
	$c_telefon = $row_data['telefon'];
	
	if (mysql_num_rows($query) > 0) {
		echo "<tr>
				<td>$c_id</td>
				<td>$c_anrede</td>
				<td>$c_vorname</td>
				<td>$c_nachname</td>
				<td>$c_strasse</td>
				<td>$c_hausnummer</td>
				<td>$c_plz</td>
				<td>$c_ort</td>
				<td>$c_email</td>
				<td>$c_telefon</td>		
			 </tr>";
	}
	else 
		echo "Keine Datensätze gefunden!";
}

echo "</table>";

mysql_close($con);

include('tpl/footer.tpl.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Contao - Kundendaten</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."/img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/js/scriptaculous.js"></script>
<script>

</script>
</head>

<body>

</body>
</html>