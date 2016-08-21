<?php

class System extends Frame
{
  protected $name = "System";
  protected $url  = "system";

  public function load()
  {
    $temp = `cat /sys/class/thermal/thermal_zone0/temp`;

    $html = "Temp: \t\t" . ($temp / 1000) . "&deg;C";

    $df = `df -h /`;

    $html .= "\n\n$df";

    Page::addBody("<pre>$html</pre>");
  }
}

return new System;

?>
