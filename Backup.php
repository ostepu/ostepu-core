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

// construct a content element for creating backups
$createBackup = Template::WithTemplateFile('include/Backup/CreateBackup.template.json');
$createBackup->bind(array());

// construct a content element for loading backups
$loadBackup = Template::WithTemplateFile('include/Backup/LoadBackup.template.json');
$loadBackup->bind(array());

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $createBackup, $loadBackup);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

