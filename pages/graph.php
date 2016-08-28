<?php

class History extends Frame
{
  protected $name = "History";
  protected $url  = "history";

  function load()
  {
    if (isset(Page::$url[1]) && Page::$url[1] === "ajax")
    {
      new Ajax;
      die();
    }

    Page::addHead("<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>");
    Page::addHead("<script type='text/javascript' src='js/graph.js'></script>");

    global $db;

    $div = "<div>" . new DateRangePicker()
         . "<div id='graph-container' style='height:400px;background:#EEE;'></div></div>";

    Page::addBody($div);
  }
}

return new History;

class Ajax
{
  function __construct()
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

?>
