<?php

class History extends Frame
{
  protected $name    = "History";
  protected $url     = "history";
  protected $priority = 3;

  public $svg = <<<svg
<svg height="25" width="25">
<line x1="1" y1="1" x2="1" y2="25" />
<line x1="1" y1="24" x2="24" y2="24" />
<line class="highlight" x1="6" y1="20" x2="10" y2="10" />
<line class="highlight" x1="10" y1="10" x2="15" y2="17" />
<line class="highlight" x1="15" y1="17" x2="22" y2="15" />
</svg>
svg;

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
         . "style='height:800px;width:100%;' "
         . "height='600'></canvas></div><script type='text/javascript'>"
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
