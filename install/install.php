<?php

exit();

require('../required/config.php');

// Create admin account

$create_admin = sprintf("INSERT INTO benutzer VALUES ('','%s','%s','%s','','')",ADMIN_NAME,md5(ADMIN_PASS),ADMIN_REAL_NAME);
$create = mysql_query($create_admin) or die("MySQL create admin error. ".mysql_error());

echo "Installed successfully!";

?>