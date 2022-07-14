<?php

include('tpl/header.tpl.php');

$benutzer = new Benutzer();

$get_data = mysql_query("SELECT benutzername, name, vorname FROM benutzer WHERE 1") or die ("MySQL get user data error. ".mysql_error());

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mitarbeiterliste</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
</head>

<body>

<h1>Mitarbeiterliste</h1>

<table>
	<thead>
    	<th>Benutzername</th>
        <th>Nachname</th>
       	<th>Vorname</th>
    </thead>
    
    <?php
	
	while ($row = mysql_fetch_assoc($get_data)) {
		$benutzer->setBenutzername($row['benutzername']);
		$benutzer->setNachname($row['name']);
		$benutzer->setVorname($row['vorname']);	
	
		echo "
			<tr>
				<td>{$benutzer->getBenutzername()}</td>
				<td>{$benutzer->getNachname()}</td>
				<td>{$benutzer->getVorname()}</td>
			</tr>
		";
		
	}
	
	?>

</table>

</body>
</html>

<?php

include('tpl/footer.tpl.php');

?>