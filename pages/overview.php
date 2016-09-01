<?php

class Overview extends Frame
{
  protected $name     = "Overview";
  protected $url      = "overview";
  protected $priority = 4;

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
