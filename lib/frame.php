<?php

class Frame
{
   protected $name;
   protected $menuItem = true;
   protected $url;
   protected $priority = 0;

  public function getName()
  {
    return $this->name;
  }

  public function hasMenuItem()
  {
    return $this->menuItem;
  }

  public function getUrl()
  {
    return $this->url;
  }

  public function getPriority()
  {
    return $this->priority;
  }

  public function load()
  {
  }
}

?>
