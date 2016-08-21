<?php

class Frame
{
   protected $name;
   protected $menuItem = true;
   protected $url;

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
}

?>
