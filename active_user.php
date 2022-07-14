<?php

////// To update session status for plus_login table to get who is online ////////
if(isset($_SESSION['benutzername'])){
	$tm = date("Y-m-d H:i:s");
	$q = mysql_query("UPDATE benutzer SET status='ON',tm='$tm' WHERE benutzername='".$_SESSION['benutzername']."'") or die (mysql_error());
}
/*
else{
	echo "<center><font face='Verdana' size='2' ><a href=login.php>Already a member, please Login</a> </center></font>";
}
*/

///// ////////////// End of updating login status for who is online ///////

// Find out who is online /////////
$gap = 10; // change this to change the time in minutes, This is the time for which active users are collected. 
$tm = date ("Y-m-d H:i:s", mktime (date("H"),date("i")-$gap,date("s"),date("m"),date("d"),date("Y")));
//// Let us update the table and set the status to OFF 
////for the users who have not interacted with 
////pages in last 10 minutes ( set by $gap variable above ) ///

$ut = mysql_query("UPDATE benutzer SET status='OFF' WHERE tm < '$tm'") or die (mysql_error());
/// Now let us collect the userids from table who are online ////////
$qt = mysql_query("SELECT * FROM benutzer WHERE tm > '$tm' and status='ON'") or die (mysql_error());

echo "<p style='color: rgb(218, 211, 183);'><u>Benutzer online:</u></p>";

while($nt = mysql_fetch_array($qt))
{
	$active_user = $nt['benutzername'];
	echo "<li style='color: rgb(218, 211, 183);'><span style='color:#F90;font-weight:bold;'>".$active_user."</span></li>";	
}

?>