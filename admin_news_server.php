<?php

session_start();

require('required/config.php');

$cmd = isset($_POST['cmd']) ? $_POST['cmd'] : NULL;

switch($cmd)
{
	case 'delete_news':
		$news_id = isset($_POST['id']) ? $_POST['id'] : NULL;

		$delete_news = mysql_query("DELETE FROM admin_news WHERE id = '$news_id'") or die ("DELETE admin news query failed. ".mysql_error());
		break;
	default:
		echo '<span class="error">unknown name</span>';
		break;
}
?>