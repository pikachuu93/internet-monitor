<?php

class Overview extends Frame
{
  protected $name     = "Overview";
  protected $url      = "overview";
  protected $priority = 4;

  public $svg = <<<svg
<svg height="25" width="25">
<rect x="2" y="2" width="21" height="21" style="stroke:white;stroke-width:2;" />
<line x1="6" y1="7" x2="19" y2="7" style="stroke:white;stroke-width:2" />
<line x1="6" y1="12" x2="19" y2="12" style="stroke:white;stroke-width:2" />
<line x1="6" y1="17" x2="19" y2="17" style="stroke:white;stroke-width:2" />
</svg>
svg;

  public function load()
  {
    $qs = ["Number of rows" => "SELECT COUNT(*) FROM connected;",
           "Last Outage"    => "SELECT datetime(datetime, 'unixepoch') "
                             . "FROM connected WHERE value=0 ORDER BY datetime DESC LIMIT 1;",
           "Total Outage"   => "SELECT COUNT(*) FROM connected WHERE value=0;",
           "Total Uptime"   => "SELECT COUNT(*) FROM connected WHERE value=1;"];

    $html = "<table>";
    foreach ($qs as $key => $q)
    {
      $html .= "<tr>";

      $res = Page::$db->query($q);

      $html .= "<td>$key</td><td>" . $res->fetchArray()[0] . "</td>";

      $html .= "</tr>";
    }

    $html .= "</table>";

    Page::addBody($html);
  }
}

return new Overview;

?>
