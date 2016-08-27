<?php

class System extends Frame
{
  protected $name = "System";
  protected $url  = "system";

  public function load()
  {
    $cmds = [(`cat /sys/class/thermal/thermal_zone0/temp` / 1000) . "&deg;C",
             `df -h /`,
             `ps -ef | grep python`];

    $html = implode("\n\n", $cmds);

    Page::addBody("<pre>$html</pre>");
  }
}

return new System;

?>
