<?php

class Page
{
  static $head = [];
  static $body = [];

  public static function addHead($str)
  {
    self::$head[] = $str;
  }

  public static function addBody($str)
  {
    self::$body[] = $str;
  }

  public static function defaultStyles()
  {
    self::addHead("<link rel='stylesheet' type='text/css' href='css/main.css'/>");
  }

  public static function outputHead()
  {
    return "<head>"
         . implode("\n", self::$head)
         . "</head>";
  }

  public static function outputBody()
  {
    return "<body>"
         . implode("\n", self::$body)
         . "</body>";
  }

  public function __construct($url, $db)
  {
    $this->url = $url;
    $this->db  = $db;

    $page = Settings::$root . "pages/" . $this->url[0] . ".php";

    if (file_exists($page))
    {
      include($page);
    }
    else
    {
      include(Settings::$root . "pages/graph.php");
    }
  }

  public function __toString()
  {
    return "<html>"
         . self::outputHead()
         . self::outputBody()
         . "</html>";
  }
}

?>
