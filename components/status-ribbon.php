<?php

class StatusRibon
{
  private $data;

  public function __construct()
  {
    $this->getStatus();
  }

  public function __toString()
  {
    $text = "Internet ";

    if ($this->data[1] === 1)
    {
      $colour = "green";
    }
    else
    {
      $text  .= "<b>NOT</b> ";
      $colour = "red";
    }

    $text .= "available - <i style='color: #222;'>"
           . strftime("%Y-%m-%d %H:%M:%S", $this->data[0])
           . "</i>";

    return "<div style='background : $colour;' class='ribon'>"
         . $text
         . "</div>";
  }

  private function getStatus()
  {
    global $db;
  
    $res = $db->select(["max(datetime)", "value"])->from("connected")->limit(1)->run();
    $this->data = $res->fetchArray();
  }
}

?>
