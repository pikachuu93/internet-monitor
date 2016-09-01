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

    $connected = getConnectionStatus()[0];

    if ($connected)
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
        $html .= "<li><a href='" . $p->getUrl();

        if ($p === self::$current)
        {
          $html .= "' class='selected";
        }

        $html .= "'>" . $p->getName() . "</a></li>";
      }
    }

    $html .= "</ul></nav>";
      
      
    if (!$connected)
    {
      $html .= "<a href='http://192.168.0.1/sky_self_heal.cgi' "
             . "class='fix-me' target='_blank'>reconnect</a>";
    }

    $html .= "</div>";

    return $html;
  }

  public function loadDefault()
  {
    # This is super buggy!!!
    self::$current = self::$pages[0];
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

    usort(self::$pages, function($a, $b){$v = $b->getPriority() - $a->getPriority(); return ($v > 0) - ($v < 0);});

    if (self::$current === Null)
    {
      self::loadDefault();
    }

    self::$current->load();
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
