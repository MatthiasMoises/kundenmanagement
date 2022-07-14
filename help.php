<?php

include('tpl/header.tpl.php');

if (isset($_POST['save_entry']))
{
	$author = $_SESSION['benutzername'];
	$txt = isset ($_POST['help_text']) ? $_POST['help_text'] : NULL;
	
	if ($txt != "") {
		$insert_help = mysql_query("INSERT INTO help (author, helptext) VALUES ('$author','$txt')");
		
		$log_date = date("Y-m-d H:i:s");
		$log_txt = $_SESSION['benutzername']." hat einen neuen Hilfetext angelegt.";
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
		
		echo "<p>Neuer Eintrag wurde erfolgreich gespeichert!</p>";
	}
}

if (isset($_POST['delete_help']))
{
	$help_id = isset($_POST['help_id']) ? $_POST['help_id'] : NULL;
	$delete_help = mysql_query("DELETE FROM help WHERE id = '$help_id'") or die ("MySQL DELETE FROM help error. ".mysql_error());
	
	$log_date = date("Y-m-d H:i:s");
	$log_txt = $_SESSION['benutzername']." hat Hilfetext Nr. $help_id gel&ouml;scht.";
	$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
	
}

$select_help = mysql_query("SELECT * FROM help WHERE 1 ORDER BY id ASC") or die ("MySQL select help failed. ".mysql_error());

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Hilfe</title>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<link rel="SHORTCUT ICON" href="<?php echo DOCUMENT_ROOT."img/favicon.ico"; ?>" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript">

</script>
</head>

<body>

<h2>Hilfe - Hinweise und Tipps f&uuml;r alle Benutzer</h2>

<table>

<?php

if (mysql_num_rows($select_help) > 0)
{
	while ($row_help = mysql_fetch_assoc($select_help))
	{
		$help_id = $row_help['id'];
		$help_author = $row_help['author'];
		$help_text = $row_help['helptext'];
		
		echo "
			<tr>";
				if ($help_author == $_SESSION['benutzername'])
					echo "
						<form id='delete' action='' method='post'>
							<input type='hidden' id='help_id' name='help_id' value='".$help_id."' />
							<td><b>$help_author: <input type='submit' id='delete_help' name='delete_help' value='Diesen Eintrag von mir l&ouml;schen' />
						</form>
					";
				else {
					echo "<td><b>$help_author:";	
				}
		echo "	</b></td>
			</tr>
			<tr>
				<td>$help_text</td>
			</tr>
		";
	}
}
else {
	echo "<p><b>Keine Hilfeeintr&auml;ge gefunden!</b></p>";	
}

?>

</table>

<br/>

<h2>Neuen Hinweis anlegen</h2>

<div id="help">
<form id="create_entry" name="create_entry" action="" method="post">
<textarea id="help_text" name="help_text"></textarea>

<p>
<input type="submit" id="save_entry" name="save_entry" value="Hilfeeintrag speichern" />
</p>

</form>
</div>

</body>
</html>

<?php

include("tpl/footer.tpl.php");

?>