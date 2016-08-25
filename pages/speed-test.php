<?php
/* Only works if we have speedtest script, see settings.php */

if (!Settings::$speedTest)
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
    header("Content-Type: text/event-stream\n\n");

    if (!(Settings::$speedTest && is_executable(Settings::$speedTest)))
    {
      $this->outputError("Speed test not installed on server.");
    }

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

  private function outputError($message = "")
  {
    echo "data: \"=== ERROR ===\\n\"\n\n";
    echo "data: \"Woops, something went wrong!\\n\"\n\n";
    echo "data: \"$message\"\n\n";
    echo "data: \"\\n\"";
    echo "\n\n";
    echo "event: finish\ndata: none\n\n";

    die();
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
    if (!is_resource($this->inFile))
    {
      $this->outputError();
    }

    while (!feof($this->inFile))
    {
      $c = fgetc($this->inFile);

      if ($this->outFile)
      {
        fwrite($this->outFile, $c);
      }

      echo "data: " . json_encode($c) . "\n\n";

      flush();
    }

    echo "event: finish\ndata: none\n\n";

    if ($this->outFile)
    {
      if (Settings::$speedTestArchive)
      {
        rename(Settings::$lockFile,
               Settings::$speedTestArchive);
      }
      else
      {
        unlink(Settings::$lockFile);
      }
    }

    die();
  }
}

return new SpeedTest;

?>
