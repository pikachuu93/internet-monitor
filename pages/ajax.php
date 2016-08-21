<?php

class Ajax extends Frame
{
  protected $url      = "ajax";
  protected $menuItem = false;

  function load()
  {
    global $db;

    if (!($_POST["start"] && $_POST["end"]))
    {
      die();
    }

    $start = $_POST["start"];
    $end   = $_POST["end"];

    $res = $db->select(["count(value)",
                        "datetime",
                        "date(datetime, 'unixepoch') as date"])
              ->from("connected")
              ->where("value = 0 AND date >= '$start' AND date <= '$end'")
              ->groupBy("date")
              ->orderBy("datetime ASC")
              ->run();

    echo '[[{"type":"date","label":"Date"},{"type":"number","label":"Value"}]';

    if ($r = $res->fetchArray())
    {
      echo ",";

      do
      {
        list($y, $m, $d) = split("-", $r[2]);

        $m--;

        echo "[\"Date($y, $m, $d)\"," . $r[0] . "]";
      }
      while (($r = $res->fetchArray()) && print(","));
    }

    echo "]";

    die();
  }
}

return new Ajax;

?>
