<?php

session_start();

require('required/config.php');

$get_news = mysql_query("SELECT id, author, text, DATE_FORMAT (date, '%d.%m.%Y %H:%i:%s') AS date FROM news WHERE 1 ORDER BY id DESC") or die ("SELECT news error. ".mysql_error());

$get_admin = mysql_query("SELECT ist_admin FROM benutzer WHERE benutzername = '".$_SESSION['benutzername']."'");

while ($admin_row = mysql_fetch_assoc($get_admin))
{
	$user_is_admin = $admin_row['ist_admin'];	
}

$news_count = mysql_num_rows($get_news);

?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<script type="text/javascript" src="libs/js/prototype.js"></script>
<script type="text/javascript" src="libs/js/scriptaculous.js"></script>
<script type="text/javascript">

function deleteNews(id) {
		
		if (confirm("Eintrag wirklich löschen?"))
		{
	
			new Ajax.Updater("","news_server.php", {
		
			parameters : {
			
				'cmd'   :    'delete_news',
				'id'	:    id
			},
			evalScripts : true,
			encoding : 'ISO-8859-1',
			
			onFailure : function() {
				alert ("Es ist ein Fehler aufgetreten! Bitte versuchen Sie es erneut!");
			},
			
			onComplete : function() {
				alert ("Eintrag gelöscht!");
				location.reload(true);
			}
		});
	}
}


</script>
</head>
<body>

<table id="newstable">

<?php

if ($news_count > 0)
{
    while ($row_news = mysql_fetch_assoc($get_news))
    {
	    $news_id = $row_news['id'];
	    $author = $row_news['author'];
	    $text = $row_news['text'];
	    $date = $row_news['date'];	

	    echo "<tr>
		  	<td class='date'>$date</td>
		  	<td class='author'>$author</td>
		  	<td class='text'>$text</td>";
		if ($_SESSION['benutzername'] == $author)
			echo "<td align='center' class='delete'><a href='javascript:deleteNews($news_id)'>Eintrag l&ouml;schen</a></td>";	
		else
			echo "<td align='center' class='delete'><img src='img/user_bw.gif' title='$author' alt='von anderem Benutzer' /></td>";		
	    echo "</tr>";
	    echo "<tr class='spacer'></tr>";

    }
}
else {
	echo "<b>Keine Neuigkeiten oder Informationen vorhanden!</b>";
}

?>

</tr>
</table>
</body>
</html>