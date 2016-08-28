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

    $div = "<button onclick='startEvents();' id='speed-test-button'>"
         . "Start Test</button><pre id='event-output'></pre>'";

    Page::addBody($div);
  }
}

class EventServer
{
  private $inFile;
  private $outFile;

  function __construct()
  {
    header("Content-Type: text/event-stream");
    header('Cache-Control: no-cache');

    ob_end_clean();
    set_time_limit(0);
    ignore_user_abort(true);

    if (!(Settings::$speedTest && is_executable(Settings::$speedTest)))
    {
      $this->outputError("Speed test not installed on server.");
    }

    $test = $this->getLockFile();

    if ($test)
    {
      $this->outFile = $test;
      $this->inFile  = $this->startSpeedTest();

      if (is_resource($this->inFile))
      {
        echo "data: \"=== Speed test started. ===\\n\"";
        echo "\n\n";
      }
      else
      {
        $this->outputError("Failed to start speed test.");
      }

      register_shutdown_function(function()
      {
        if ($this->inFile)
        {
          pclose($this->inFile);
        }

        if ($this->outFile)
        {
          $this->closeLockFile();
        }
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
    return @fopen(Settings::$lockFile, "x");
  }

  private function closeLockFile()
  {
    if (is_resource($this->outFile))
    {
      fclose($this->outFile);
    }

    if (file_exists(Settings::$lockFile))
    {
      if (Settings::$speedTestArchive)
      {
        $newName = date("Y-m-d-H-i-s") . ".txt";
        rename(Settings::$lockFile,
               Settings::$speedTestArchive . $newName);
      }
      else
      {
        unlink(Settings::$lockFile);
      }
    }
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

    while (!feof($this->inFile)
        || (!$this->outFile && file_exists(Settings::$lockFile)))
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
      $this->closeLockFile();

      pclose($this->inFile);
      $this->inFile = false;
    }

    die();
  }
}

return new SpeedTest;

?>
