<?php

define("SITE_ROOT", '/web/dnasys.isoshu.com');

include('../config/core.php');

// test app runtime
include(LIB."/runtime.class.php");
$runtime= new runtime;
$runtime->start();

include('../module/index.php');

$runtime->stop();
echo "\n<!-- the page cost ". $runtime->spent() ." ms -->\n";
?>
