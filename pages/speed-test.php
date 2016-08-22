<?php
/* Only works if we have speedtest script, see settings.php */

if (!Settings::$haveSpeedTest)
{
  return;
}

class SpeedTest extends Frame
{
  protected $name = "Speed Test";
  protected $url  = "speed-test";

  public function load()
  {
    Page::addHead("<script src='js/events.js'></script>");
    if (isset(Page::$url[1]) && Page::$url[1] === "events")
    {
      $e = new EventServer();
    }

    global $db;

    $div = "<button onclick='startEvents();'>Start Test</button>"
         . "<pre id='event-output'></pre>'";

    Page::addBody($div);
  }
}

class EventServer
{
  private $inFile;
  private $outFile;

  function __construct()
  {
    $test = $this->getLockFile();

    if ($test)
    {
      $this->outFile = $test;
      $this->inFile  = $this->startSpeedTest();
      register_shutdown_function(function()
      {
        pclose($this->inFile);
      });
    }
    else
    {
      $this->inFile = $this->openReadOnly();
    }

    $this->sendEvents();
  }

  private function getLockFile()
  {
    return fopen(Settings::$lockFile, "x");
  }

  private function openReadOnly()
  {
    return fopen(Settings::$lockFile, "r");
  }

  private function startSpeedTest()
  {
    return popen(Settings::$speedTest, "r");
  }

  private function sendEvents()
  {
    header("Content-Type: text/event-stream\n\n");

    while (!feof($this->inFile))
    {
      $c = fgetc($this->inFile);

      if ($this->outFile)
      {
        fwrite($this->outFile, $c);
      }

      echo "data: " . json_encode($c) . "\n\n";

      ob_end_flush();
      flush();
    }

    echo "event: finish\ndata: none\n\n";

    if ($this->outFile)
    {
      unlink(Settings::$lockFile);
    }

    die();
  }
}

return new SpeedTest;

?>
