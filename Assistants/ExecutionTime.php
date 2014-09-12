<?php
header('Content-type: text');

if (!isset($_GET['split'])){
    $_GET['split'] = 4;
}

$input = file_get_contents(dirname(__FILE__) . '/../executionTime.log');
$input = explode("\n",$input);
foreach ($input as &$in){
    $in = substr($in, stripos($in,'[DEBUG]: ')+9,strlen($in));
    $in = rtrim($in,"s");
    $in = explode(" ",$in);
    
    $in[0] = explode('/',$in[0]);
    $res = "";
    for ($i = 0; $i < count($in[0]) && $i<=$_GET['split'] && count($in[0])>1; $i++) {
    $res = $res . $in[0][$i] . "/";
    }
    $in[0] = $res;
    
    if ($in[0]!=""){
        if (!isset($result[$in[0]])){
            $result[$in[0]]['count'] = 1;
            $result[$in[0]]['time'] = $in[1];
        }else{
        $result[$in[0]]['count']++;
        $result[$in[0]]['time'] += $in[1];
        }
    }
}

echo str_pad("quantity", 5, " ", STR_PAD_LEFT) . "    " . str_pad("elapsed time", 13, " ", STR_PAD_LEFT) . "     " .  "link" . "\n";

$summe = 0;
$gesamtzeit = 0;
foreach ($result as $key => $value){
echo str_pad($value['count'], 5, " ", STR_PAD_LEFT) . "    " . str_pad((round($value['time']/$value['count'],2)), 13, " ", STR_PAD_LEFT) . "s    " .  $key . "\n";
$summe+=$value['count'];
$gesamtzeit+=$value['time'];
}

echo "\ntotal:        " . str_pad($summe, 8, " ", STR_PAD_LEFT). "\n";
echo "average time: " . str_pad((round($gesamtzeit/$summe,2)), 8, " ", STR_PAD_LEFT). "s\n";
?>