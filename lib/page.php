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
         . "<div id='content'>"
         . implode("\n", self::$body)
         . "</div>"
         . "</body>";
  }

  public static function outputMenu()
  {
    $html = "<div id='nav-bar'><div class='";
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
        $html .= "<li>";

        $html .= "<a href='" . $p->getUrl();

        if ($p === self::$current)
        {
          $html .= "' class='selected";
        }

        $html .= "'>";
        
        if (isset($p->svg))
        {
          $html .= $p->svg;
        }
        
        $html .= $p->getName() . "</a></li>";
      }
    }
      
    if (!$connected)
    {
      $html .= "<li><a href='http://192.168.0.1/sky_self_heal.cgi' "
        . "class='fix-me' target='_blank'>";
      
      $html .= <<<SVG
<svg width="25" height="25">
<path d="M 9 2 L 16 2 L 23 18 L 19 23 L 6 23 L 2 18 L 9 2" />
<line x1="12.5" y1="6" x2="12.5" y2="11" class="highlight" />
<line x1="9" y1="14" x2="9" y2="18" class="highlight" />
<line x1="16" y1="14" x2="16" y2="18" class="highlight" />
</svg>
SVG;
      
      $html .= "Reconnect</a></li>";
    }

    $html .= "</ul></nav>";

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
