<?php

class DateRangePicker
{
  public function __construct()
  {
    static $jsAdded = false;

    if (!$jsAdded)
    {
      Page::addHead("<script type='text/javascript' async src='js/graph.js'></script>");
      $jsAdded = true;
    }
  }

  public function __toString()
  {
    $max = date("Y-m-d");

    return "<form method='post' onchange='doAjax(this)'>"
         . "<label>Start:<input type='date' name='start' min='2016-07-16' max='$max'/></label>"
         . "<label>End:<input type='date' name='end' min='2016-07-16' max='$max' value='$max'/></label>"
         . "</form>";
  }
}

?>
