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

    Page::addHead("<script type='text/javascript' src='js/lib/chart.bundle.min.js'></script>");
    Page::addHead("<script type='text/javascript' src='js/graph.js'></script>");

    global $db;

    $div = "<div>" . new DateRangePicker()
         . "<canvas id='graph-container' style='height:800px;width:100%;background:#EEE;' height='600'></canvas></div>";

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

      $divider = 60;

      $res = $db->select(["sum(value)",
                          "datetime",
                          "strftime('%Y-%m-%d-%H-00-00', datetime, 'unixepoch') as date"])
                ->from("connected")
                ->where("date >= '$start' AND date < '$y-$m-$d'")
                ->groupBy("date")
                ->orderBy("datetime ASC")
                ->run();
    }
    else
    {
      $divider = 1440;

      $res = $db->select(["sum(value)",
                          "datetime",
                          "date(datetime, 'unixepoch') as date"])
                ->from("connected")
                ->where("date >= '$start' AND date <= '$end'")
                ->groupBy("date")
                ->orderBy("datetime ASC")
                ->run();
    }

    $data   = [];

    while ($r = $res->fetchArray())
    {
      $data[]   = ["x" => $r[1], "y" => 100 * $r[0] / $divider];
    }

      echo json_encode($data);

    die();
  }
}

?>
