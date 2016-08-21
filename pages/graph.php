<?php

class History extends Frame
{
  protected $name = "History";
  protected $url  = "history";

  public function display()
  {
    global $db;
      
    Page::addHead("<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>");
    Page::defaultStyles();

    $div = "<div>" . new DateRangePicker() . "<div id='graph-container'></div></div>";

    Page::addBody($div);
  }
}

return new History;

?>
