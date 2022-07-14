<?php

session_start();

require('required/config.php');

$cmd = isset($_POST['cmd']) ? $_POST['cmd'] : NULL;

switch($cmd)
{
	case 'delete_news':
		$news_id = isset($_POST['id']) ? $_POST['id'] : NULL;

		$delete_news = mysql_query("DELETE FROM news WHERE id = '$news_id'") or die ("DELETE news query failed. ".mysql_error());
		
		$log_date = date("Y-m-d H:i:s");
		$log_txt = $_SESSION['benutzername']." hat Nachricht Nr. $news_id gel&ouml;scht.";
		$log_entry = mysql_query("INSERT INTO log (date, log_txt) VALUES ('$log_date','$log_txt')") or die ("MySQL INSERT log error. ".mysql_error());
		
		break;
	default:
		echo '<span class="error">unknown name</span>';
		break;
}
?>