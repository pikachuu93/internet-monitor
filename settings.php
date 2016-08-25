<?php

class Settings
{
  public static $root          = "/var/www/html/";
  public static $dbPath        = "/home/pi/internet-monitor/connectivity.sqlite";

  /* Need speed test script from https://github.com/sivel/speedtest-cli */
  public static $lockFile      = "/var/www/html/speed-test.lock";
  public static $speedTest     = "/home/pi/speedtest-cli.py";
  public static $speedTestArchive = "/home/pi/speed-tests/";
}

?>
