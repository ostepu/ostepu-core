<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';

// construct a new Header
$h = new Header("Datenstrukturen",
                "",
                "Admin",
                "211221492");

$h->setBackURL("index.php")
->setBackTitle("zur Veranstaltung");

$sheetSettings = Template::WithTemplateFile('include/CreateSheet/SheetSettings.template.json');
$sheetSettings->bind(array());

$createExercise = Template::WithTemplateFile('include/CreateSheet/CreateExercise.template.json');
$createExercise->bind(array());

$exerciseSettings = Template::WithTemplateFile('include/CreateSheet/ExerciseSettings.template.json');
$exerciseSettings->bind(array());

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $sheetSettings, $createExercise, $exerciseSettings);
$w->set_config_file('include/configs/config_createSheet.json');
$w->show();
?>

