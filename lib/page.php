<?php

class Page
{
  static $url;
  static $db;

  static $head    = [];
  static $body    = [];
  static $pages   = [];
  static $current = Null;

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
         . "<title>" . self::$current->getName() . "</title>"
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

    $html .= "</ul></nav></div>";

    $html .= self::$current->display();

    return $html;
  }

  public function __construct($url, $db)
  {
    self::$url = $url;
    self::$db  = $db;

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

        if ($p->getUrl() === self::$url[0])
        {
          self::$current = $p;
        }
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
