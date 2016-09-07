<?php

class History extends Frame
{
  protected $name    = "History";
  protected $url     = "history";
  protected $priority = 3;

  function load()
  {
    if (isset(Page::$url[1]) && Page::$url[1] === "ajax")
    {
      $this->doAjax();
    }

    Page::addHead("<script type='text/javascript' src='js/lib/chart.bundle.min.js'></script>");
    Page::addHead("<script type='text/javascript' src='js/history.js'></script>");

    global $db;

    $max = date("Y-m-d");

    $div = "<div><form method='post' onchange='doAjax(this)'>"
         . "<label>Start:<input type='date' name='start' min='2016-07-16' max='$max'/></label>"
         . "<label>End:<input type='date' name='end' min='2016-07-16' max='$max' value='$max'/></label>"
         . "</form>"
         . "<canvas id='graph-container' "
         . "style='height:800px;width:100%;background:#EEE;' "
         . "height='600'></canvas></div><script>"
         . "window.onload = function(){"
         . "loadGraph("
         . json_encode($this->getData(date("Y-m-d")))
         . ");};</script>";
          
    Page::addBody($div);
  }

  function doAjax()
  {
    die(json_encode($this->getData($_POST["start"], $_POST["end"])));
  }

  function getData($start, $end = null)
  {
    global $db;

    if ($end === null)
    {
      $end = $start;
    }

    if ($start === $end)
    {
      list($y, $m, $d) = explode("-", $start);

      $startTime = mktime(0,  0, 0, $m, $d, $y);
      $endTime   = mktime(24, 0, 0, $m, $d, $y);

      $last = $divider = 60;

      if ($end === date("Y-m-d"))
      {
        $last = date("i") + 1;
      }

      $res = $db->select(["sum(value)",
                          "datetime",
                          "strftime('%Y-%m-%d-%H-00-00', datetime, 'unixepoch') as date"])
                ->from("connected")
                ->where("datetime >= $startTime AND datetime < $endTime")
                ->groupBy("date")
                ->orderBy("datetime ASC")
                ->run();
    }
    else
    {
      list($y, $m, $d) = explode("-", $start);
      $startTime = mktime(0, 0, 0, $m, $d, $y);

      list($y, $m, $d) = explode("-", $end);
      $endTime   = mktime(24, 0, 0, $m, $d, $y);

      $last = $divider = 1440;
      if ($end === date("Y-m-d"))
      {
        $last    = 60 * date("G") + date("i");
      }

      $res = $db->select(["sum(value)",
                          "datetime",
                          "date(datetime, 'unixepoch') as date"])
                ->from("connected")
                ->where("datetime >= $startTime AND datetime <= $endTime")
                ->groupBy("date")
                ->orderBy("datetime ASC")
                ->run();
    }

    $data   = [];

    while ($r = $res->fetchArray())
    {
      $data[]   = ["x" => $r[1], "y" => 100 * $r[0] / $divider];
    }

    $end       = array_pop($data);
    $end["y"] *= $divider / $last;
    $data[]    = $end;

    return $data;
  }
}

return new History;

?>
