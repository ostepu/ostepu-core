<?php
header('Content-type: text');

if (!isset($_GET['teilen'])){
    $_GET['teilen'] = 4;
}

$input = file_get_contents("http://141.48.9.92/uebungsplattform/laufzeit.log");
$input = explode("\n",$input);
foreach ($input as &$in){
    $in = substr($in, stripos($in,'[DEBUG]: ')+9,strlen($in));
    $in = rtrim($in,"s");
    $in = explode(" ",$in);
    
    $in[0] = explode('/',$in[0]);
    $res = "";
    for ($i = 0; $i < count($in[0]) && $i<=$_GET['teilen'] && count($in[0])>1; $i++) {
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

echo str_pad("Anzahl", 5, " ", STR_PAD_LEFT) . "    " . str_pad("Zeit", 8, " ", STR_PAD_LEFT) . "     " .  "Link" . "\n";

$summe = 0;
$gesamtzeit = 0;
foreach ($result as $key => $value){
echo str_pad($value['count'], 5, " ", STR_PAD_LEFT) . "    " . str_pad((round($value['time']/$value['count'],2)), 8, " ", STR_PAD_LEFT) . "s    " .  $key . "\n";
$summe+=$value['count'];
$gesamtzeit+=$value['time'];
}

echo "\n\nGesamtanzahl: " . $summe . "\n";
echo "Gesamtzeit: " . round($gesamtzeit,2) . "s\n";
echo "Zeit: " . str_pad((round($gesamtzeit/$summe,2)), 8, " ", STR_PAD_LEFT). "s\n";
?>