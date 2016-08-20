<?php

require("settings.php");
require("lib/loader.php");

$db = new DbConnection();

$url = new UrlHandler();

$page = new Page($url, $db);

echo $page;

?>
