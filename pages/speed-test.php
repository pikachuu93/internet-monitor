<?php

class SpeedTest extends Frame
{
  protected $name = "Speed Test";
  protected $url  = "speed-test";

  function __construct()
  {
    if (Page::$url[0] === $this->url)
    {
    }
  }

  public function display()
  {
    global $db;

    $div = "<div>" . "Hello World!" . "</div>";

    Page::addBody($div);
  }
}

return new SpeedTest;

?>
