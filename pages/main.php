<?php 

#<table>
#
#if (isset($_GET["error"]) && $_GET["error"] == "true")
#{
#	$q = "SELECT * FROM connected WHERE value = 0 ORDER BY datetime DESC;";
#}
#else
#{
#	$q = "SELECT * FROM connected ORDER BY datetime DESC LIMIT 60;";
#}
#
#$res = $db->query($q);
#
#while ($r = $res->fetchArray())
#{
#	echo "<tr>";
#	echo "<td>" . strftime("%Y-%m-%d %H:%M:%S", $r[0]) . "</td><td>" . $r[1] . "</tr>";
#	echo "</tr>";
#}
#
#</table>
?>
