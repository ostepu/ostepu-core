<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';

// construct a new Header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "211221492");

$h->setBackURL("index.php")
->setBackTitle("zur Veranstaltung");

$data = file_get_contents("http://localhost/Uebungsplattform/UI/Data/BackupData");
$data = json_decode($data, true);

$backups = $data;

// construct a content element for creating backups
$createBackup = Template::WithTemplateFile('include/Backup/CreateBackup.template.html');

// construct a content element for loading backups
$loadBackup = Template::WithTemplateFile('include/Backup/LoadBackup.template.html');
$loadBackup->bind($backups);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $createBackup, $loadBackup);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

