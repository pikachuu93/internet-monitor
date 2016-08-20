<?php

class UrlHandler implements ArrayAccess
{
  private $url;

  function __construct()
  {
    $this->parseUrl();
  }

  public function offsetExists($offset)
  {
    return isset($this->url[$offset]);
  }

  public function offsetGet($offset)
  {
    return $this->url[$offset];
  }

  public function offsetSet($offset, $value)
  {
    $this->url[$offset] = $value;
  }

  public function offsetUnset($offset)
  {
    unset($this->url[$offset]);
  }

  private function parseUrl()
  {
    $this->url = array_values(array_filter(explode("/", $_SERVER["REQUEST_URI"])));

    if (!$this->url)
    {
      $this->url[] = "";
    }
  }
}

?>
