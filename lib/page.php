<?php

class Page
{
  static $head  = [];
  static $body  = [];
  static $pages = [];

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
         . self::outputMenu()
         . implode("\n", self::$body)
         . "</body>";
  }

  public static function outputMenu()
  {
    $html = "<div id='top-bar'><div class='";
    $html .= "status-marker' style='background:";

    $data = getConnectionStatus();

    if ($data[0])
    {
      $html .= "green";
    }
    else
    {
      $html .= "red";
    }

    $html .= ";'></div><nav><ul>";

    foreach (self::$pages as $p)
    {
      if ($p->hasMenuItem())
      {
        $html .= "<li><a href='" . $p->getUrl()
               . "'>" . $p->getName() . "</a></li>";
      }
    }

    $html .= "</ul></nav>";

    return $html;
  }

  public function __construct($url, $db)
  {
    $this->url = $url;
    $this->db  = $db;

    foreach (scandir(Settings::$root . "pages") as $f)
    {
      if ($f[0] === ".")
      {
        continue;
      }

      $p = include(Settings::$root . "pages/" . $f);

      if ($p instanceof Frame)
      {
        self::$pages[] = $p;
      }
    }
  }

  public function __toString()
  {
    self::defaultStyles();

    return "<!DOCTYPE html><html>"
         . self::outputHead()
         . self::outputBody()
         . "</html>";
  }
}

?>
