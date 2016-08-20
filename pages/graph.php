<?php

global $db;
  
Page::addHead("<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>");
Page::defaultStyles();
Page::addBody(new StatusRibon());

$div = "<div>" . new DateRangePicker() . "<div id='graph-container'></div></div>";

Page::addBody($div);

?>
