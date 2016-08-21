<?php

class History extends Frame
{
  protected $name = "History";
  protected $url  = "history";

  function __construct()
  {
    if (Page::$url[0] === $this->url)
    {
      Page::addHead("<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>");
      Page::addHead("<script type='text/javascript' src='js/graph.js'></script>");
    }
  }

  public function display()
  {
    global $db;

    $div = "<div>" . new DateRangePicker()
         . "<div id='graph-container' style='height:400px;background:#EEE;'></div></div>";

    Page::addBody($div);
  }
}

return new History;

?>
