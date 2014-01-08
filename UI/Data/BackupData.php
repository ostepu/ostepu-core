<?php

$backups = array(
    array("id"=>"1","date"=>"01.01.2003, 18:00","file"=>"../backup.zip"),
    array("id"=>"2","date"=>"02.01.2003, 18:00","file"=>"../backup.zip"),
    array("id"=>"3","date"=>"03.01.2003, 18:00","file"=>"../backup.zip"),
    array("id"=>"4","date"=>"04.01.2003, 18:00","file"=>"../backup.zip"),
    array("id"=>"5","date"=>"01.05.2003, 18:00","file"=>"../backup.zip"),
    array("id"=>"6","date"=>"01.06.2003, 18:00","file"=>"../backup.zip"),
    array("id"=>"7","date"=>"01.07.2003, 18:00","file"=>"../backup.zip"),
    array("id"=>"8","date"=>"01.10.2003, 18:00","file"=>"../backup.zip"),
    array("id"=>"9","date"=>"01.11.2003, 18:00","file"=>"../backup.zip"),
    array("id"=>"10","date"=>"01.12.2003, 18:00","file"=>"../backup.zip"),
);

$backupCount = rand(1, 5);
$backupElements = array();

for ($i=0; $i < $backupCount; $i++) {
    $backupElements[] = $backups[rand(0, count($backups) - 1)];
}

$backups = array("backups" => $backupElements);

print json_encode($backups);

?>