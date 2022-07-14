<?php

ob_start();

session_start();

header('Content-Type: text/html; charset=utf-8');

require('required/config.php');

include('classes/artikel.class.php');
include('classes/benutzer.class.php');
include('classes/kunde.class.php');
include('classes/rechnung.class.php');
include('classes/angebot.class.php');
include('classes/lieferschein.class.php');
include('classes/mahnung.class.php');
include('classes/kalender.class.php');
include('classes/log.class.php');

include('functions.php');

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

$session_data = $_SESSION['benutzername'];

$user_data = mysql_query("SELECT benutzername, ist_admin FROM benutzer WHERE benutzername = '$session_data'") or die("MySQL get user data error. ".mysql_error());

while ($row_user = mysql_fetch_assoc($user_data))
{
	$db_benutzername = $row_user['benutzername'];
	$db_ist_admin = $row_user['ist_admin'];	
}	

// Update session if user is active, else logout

$timestamp = date("Y-m-d H:i:s");
$time = time();

$get_session_time = mysql_query("SELECT session_time FROM benutzer WHERE benutzername = '".$_SESSION['benutzername']."'");

while ($row_session = mysql_fetch_assoc($get_session_time))
{
	$db_session_time = $row_session['session_time'];	
}

if ($db_session_time == "0000-00-00 00:00:00")
{
	mysql_query("UPDATE benutzer SET session_time = '".$timestamp."' WHERE benutzername = '".$_SESSION['benutzername']."'");	
}
else {
	if ((strtotime($db_session_time) + 1800) < $time)
		header("Location: logout.php?session_timeout=true");
	else
		mysql_query("UPDATE benutzer SET session_time = '".$timestamp."' WHERE benutzername = '".$_SESSION['benutzername']."'");	
}

// session update end

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

<?php

echo "Willkommen, <span style='color:#FF9900;font-weight:bold;'>".$_SESSION['benutzername']."</span>!<br />";

echo "<div style='font-size: 30px; font-family: Trebuchet MS, Verdana, Arial, sans-serif; color: #DAD3B7; float: left; height: 70px; padding-top:15px;'>$day_name, $date</div>";

echo "<div><font style='font-size: 30px;' id='ur' face='Trebuchet MS, Verdana, Arial, sans-serif' color='#DAD3B7'></font></div>";

// include file to display active user
echo "<div style='margin-left:250px;float: left;padding-top:15px;'>";

include("active_user.php");

echo "</div>";

echo "<div id='bt_exit'>";

echo "<a href='logout.php'><img src='img/exit.png' alt='exit' title='Logout' /></a>";

echo "</div>";

echo "<div style='clear:both;'></div>";

?>

<hr />
<hr />

<h1 style="color:#000;">Hauptmen&uuml;</h1>

<div id="main_menu">
	<input type="button" name="home" value="Startseite" onClick="parent.location='main.php'"  />
    <input type="button" name="home" value="Kundendaten" onClick="parent.location='show_customers.php?start=0'"  />
    <input type="button" name="home" value="Artikelstamm" onClick="parent.location='show_articles.php?start=0'"  />
    <input type="button" name="home" value="Neue Rechnung" onClick="parent.location='new_account.php'"  />
    <input type="button" name="home" value="Alle Rechnungen" onClick="parent.location='show_all_accounts.php?start=0'"  />
	<?php
	if ($db_ist_admin == "1")
	{
	?>
    <input type="button" name="home" value="Offene Rechnungen" onClick="parent.location='show_open_accounts.php?start=0'"  />
    <input type="button" name="home" value="Fertige Rechnungen" onClick="parent.location='show_finalized_accounts.php?start=0'"  />
    <?php
	}
	if ($db_ist_admin == "1")
	{
	?>
    <!-- -->
    <?php
	}
	?>
    <input type="button" style="width:280px;" name="home" value="Neues Angebot / Auftragsbest&auml;tigung" onClick="parent.location='new_offer.php'"  />
    <input type="button" style="width:300px;" name="home" value="Erstellte Angebote / Auftragsbest&auml;tigungen" onClick="parent.location='show_offers.php?start=0'"  />
    <?php
    if ($db_ist_admin == "1")
	{
	?>
    <input type="button" name="home" value="Fertige Angebote" onClick="parent.location='show_finalized_offers.php'"  />
    <?php
	}
	?>
    <input type="button" name="home" value="Neuer Lieferschein" onClick="parent.location='new_delivery_bill.php'"  />
    <input type="button" name="home" value="Erstellte Lieferscheine" onClick="parent.location='show_open_delivery_bills.php?start=0'"  />
    <?php
    if ($db_ist_admin == "1")
	{
	?>
    <input type="button" name="home" value="Mahnungen" onClick="parent.location='show_demand_notes.php?start=0'"  />
    <?php
	}
	?>
    <input type="button" name="home" value="Terminverwaltung" onClick="parent.location='calendar.php'"  />
    <input type="button" name="home" value="Hilfe" onClick="parent.location='help.php'"  />
    <input type="button" name="home" value="Logbuch" onClick="parent.location='log.php'"  />
    <input type="button" name="home" value="Support" onClick="parent.location='support.php'"  />
    <?php
	if ($db_benutzername == "admin")
	{
	?>
    <input type="button" name="home" value="Admin only" onClick="parent.location='admin_news_create.php'"  />
    <?php
	}
	?>

</div>

<hr />    
<hr />

</body>
</html>