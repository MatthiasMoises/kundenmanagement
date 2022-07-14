<?php

ob_start();

session_start();

header('Content-Type: text/html; charset=utf-8');

require('../required/config.php');

include('../classes/artikel.class.php');
include('../classes/benutzer.class.php');
include('../classes/kunde.class.php');
include('../classes/rechnung.class.php');
include('../classes/angebot.class.php');
include('../classes/lieferschein.class.php');
include('../classes/mahnung.class.php');
include('../classes/kalender.class.php');
include('../classes/log.class.php');

include('../functions.php');

if (!$_SESSION['benutzername']) {
	$date = date("Y-m-d H:i:s");
	$ip = $_SERVER['REMOTE_ADDR'];
	$failure_type = "Unauthorized page access";
	$page_name = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	$failed_log = sprintf("INSERT INTO login_stats (date, login_name, tried_password, login_ip, failure_type, page_name) VALUES ('%s','%s','%s','%s','%s','%s')",$date,"unknown","unknown",$_SERVER['REMOTE_ADDR'],$failure_type,$page_name);
	
	mysql_query($failed_log);

	header("Location: index.php");
}
	
$day_name = strftime("%A");
$date = date("d.m.Y");
	
echo "Willkommen, <span style='color:#FF9900'>".$_SESSION['benutzername']."</span>!<br />";

echo "<div style='font-size: 30px; font-family: Trebuchet MS, Verdana, Arial, sans-serif; color: #DAD3B7; float: left; height: 70px; padding-top:15px;'>$day_name, $date</div>";

echo "<div><font style='font-size: 30px;' id='ur' face='Trebuchet MS, Verdana, Arial, sans-serif' color='#DAD3B7'></font></div>";

// include file to display active user
echo "<div style='margin-left:250px;float: left;padding-top:15px;'>";

include("../active_user.php");

echo "</div>";

echo "<div id='bt_exit'>";

echo "<a href='logout.php'><img src='../img/exit.png' alt='exit' title='Logout' /></a>";

echo "</div>";

echo "<div style='clear:both;'></div>";

ob_flush();

?>

<head>
<script type="text/javascript">
function UR_Start() 
{
    UR_Nu = new Date;
	   UR_Indhold = showFilled(UR_Nu.getHours()) + ":" + showFilled(UR_Nu.getMinutes()) + ":" + showFilled(UR_Nu.getSeconds());
	   document.getElementById("ur").innerHTML = UR_Indhold;
	   setTimeout("UR_Start()",1000);
}

function showFilled(Value)
{
	   return (Value > 9) ? "" + Value : "0" + Value;
}

</script>
</head>

<!--
<body onLoad="UR_Start()">
-->

<body>

<hr />

<h1>Hauptmen&uuml;</h1>



<div id="admin_menu">
	<input type="button" name="home" value="Startseite" onClick="parent.location='admin.php'"  />
	<input type="button" name="home" value="Neuen Benutzer anlegen" onClick="parent.location='edit_user.php?action=new'"  />
	<input type="button" name="home" value="Benutzer anzeigen" onClick="parent.location='show_users.php'"  />
    <input type="button" name="home" value="Fertige Rechnungen entsperren" onClick="parent.location='unlock_finalized_accounts.php?start=0'"  />
    <input type="button" name="home" value="Fertige Angebote entsperren" onClick="parent.location='unlock_finalized_offers.php?start=0'"  />
</div>

<hr  />    

</body>
</html>