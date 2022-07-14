<?php

session_start();

require('required/config.php');

$artikelCount = isset($_POST['artikelCount']) ? $_POST['artikelCount'] : NULL;

$sql = "SELECT id, bezeichnung FROM artikel WHERE bezeichnung LIKE '%" . $_POST['bezeichnung_'.$artikelCount] . "%'";
$rs = mysql_query($sql);

?>

<ul>

<?php while($data = mysql_fetch_assoc($rs)) { ?>
    <li><?php echo stripslashes($data['bezeichnung']);?></li>
<?php } ?>

</ul>
