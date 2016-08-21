<?php

class Settings
{
  public static $root          = "/var/www/html/";
  public static $dbPath        = "/home/pi/connectivity.sqlite";

  /* Need speed test script from https://github.com/sivel/speedtest-cli */
  public static $haveSpeedTest = true;
  public static $lockFile      = "/var/www/html/speed-test.lock";
  public static $speedTest     = "/var/www/html/speedtest_cli.py";
}

?>
