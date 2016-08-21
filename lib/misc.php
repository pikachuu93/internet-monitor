<?php

function getConnectionStatus()
{
  global $db;

  $res = $db->select(["value", "max(datetime)"])->from("connected")->limit(1)->run();
  return $res->fetchArray();
}

?>
