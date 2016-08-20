<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <style>
      table, td
      {
        border: 1px solid black;
      }
      
      html, body
      {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
      }
    </style>

    <?php Page::outputHead() ?>

  </head>
  <body>

<?php

Page::outputBody();

$db = new SQLite3("/home/pi/connectivity.sqlite", SQLITE3_OPEN_READONLY);

$page = "main";

if (isset($_GET["page"]) && file_exists("pages/" . $_GET["page"] . ".php"))
{
  $page = $_GET["page"];
}

include("pages/$page.php");
?>

  </body>
</html>
