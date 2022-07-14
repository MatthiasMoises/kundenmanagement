<?php

$t = mysql_query("select version() as ve") or die ("Get MySQL Version error: ".mysql_error());
$r = mysql_fetch_object($t) or die ("Get MySQL Version error: ".mysql_error());

$year_start = "2011";
$copyright_year = date("Y");

echo "<div id='footer' style='color: rgb(218, 211, 183);'>";

	echo "<hr/>";

	echo "<div id='copyright' style='float:left; width:250px;'>";
		echo "<em>Letztes Update: 03.05.2018</em><br/><br/>";
		if ($year_start == $copyright_year)
			echo "(c) $copyright_year <> iWeDe Company<br/>";
		else
			echo "(c) $year_start - $copyright_year <> iWeDe Company<br/>";
		echo "Anwendung optimiert f&uuml;r Mozilla Firefox<br/><br />";
	echo "</div>";

	echo "<div id='system_version' style='float:left; width: 150px; margin-left: 280px; margin-right: 280px;'>";
		echo "System-Version: 1.8.5";
	echo "</div>";
	
	echo "<div id='resource_version' style='width: 200px; float:right'>";
		echo "PHP-Version: ".phpversion()."<br/>";
		echo "MySQL-Version: ".$r->ve;
	echo "</div>";
	
	echo "<div style='clear:both;'></div>";

echo "</div>";
