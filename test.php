<?php

$db = new SQLite3("/home/pi/connectivity.sqlite", SQLITE3_OPEN_READONLY);

$res = $db->query("SELECT * FROM connected ORDER BY datetime DESC LIMIT 50;");

while ($r = $res->fetchArray())
{
	var_dump($r);
}


?>
