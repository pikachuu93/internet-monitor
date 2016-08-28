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

    if ($start === $end)
    {
      list($y, $m, $d) = explode("-", $start);
      $d++;

      $res = $db->select(["sum(value)",
                          "datetime",
                          "strftime('%Y-%m-%d-%H-00-00', datetime, 'unixepoch') as date"])
                ->from("connected")
                ->where("date >= '$start' AND date < '$y-$m-$d'")
                ->groupBy("date")
                ->orderBy("datetime ASC")
                ->run();

      echo '[[{"type":"datetime","label":"Date"},{"type":"number","label":"Value"}]';


      while ($r = $res->fetchArray())
      {
        echo ",";
        list($y, $m, $d, $h, $M, $s) = split("-", $r[2]);

        $m--;

        echo "[\"Date($y,$m,$d,$h,$M,$s)\"," . (60 - $r[0]) . "]";
      }

      echo "]";
    }
    else
    {
      $res = $db->select(["sum(value)",
                          "datetime",
                          "date(datetime, 'unixepoch') as date"])
                ->from("connected")
                ->where("date >= '$start' AND date <= '$end'")
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

          echo "[\"Date($y, $m, $d)\"," . (1440 - $r[0]) . "]";
        }
        while (($r = $res->fetchArray()) && print(","));
      }

      echo "]";
    }

    die();
  }
}

return new Ajax;

?>
