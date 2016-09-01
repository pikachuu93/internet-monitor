<?php

function loadDir($dir)
{
  $files = scandir(Settings::$root . $dir);
  
  foreach ($files as $f)
  {
    if ($f[0] === ".")
      continue;

    /* this is gross! */
    if ($f === "loader.php")
      continue;
  
    include(Settings::$root . "$dir/$f");
  }
}

loadDir("lib");

?>
