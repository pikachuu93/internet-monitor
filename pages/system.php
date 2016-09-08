<?php

class System extends Frame
{
  protected $name = "System";
  protected $url  = "system";

  public $svg = <<<svg
<svg height="25" width="25">
<circle cx="12.5" cy="20" r="4" style="stroke:white;stroke-width:2;" />
<path d="M 11 17 L 11 5 A 1.5 1.5 0 0 1 14 5 L 14 17" style="stroke:white;stroke-width:2;" />
</svg>
svg;

  public function load()
  {
    $cmds = [(`cat /sys/class/thermal/thermal_zone0/temp` / 1000) . "&deg;C\n",
             `df -h /`,
             `ps -ef | grep python | grep -v grep`,
             file("/proc/meminfo")[1]];

    $html = implode("\n\n", $cmds);

    Page::addBody("<pre>$html</pre>");
  }
}

return new System;

?>
