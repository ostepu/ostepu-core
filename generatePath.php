<?php

$files = scandir(dirname(__FILE__).'/path');
foreach ($files as $file){
    if ($file=='.' || $file=='..') continue;
    $para= '("C:/Program Files/Graphviz2.36/bin/dot" -O -Tpng '.dirname(__FILE__).'/path/'.$file.') 2>&1';
    ob_start();
    system($para,$return);
    $output=explode("\n",ob_get_contents());
    ob_end_clean();
}
?>