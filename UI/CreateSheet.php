<?php
include_once 'include/Header/Header.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once '../Assistants/Logger.php';
?>

<?php
    if (isset($_POST['sheet'])) {
        Logger::Log($_POST, LogLevel::INFO);
        /**
         * @todo rename to 'action' instead of sheet
         * @todo create a new sheet or update the existing one
         */

        // redirect, so the user can reload the page without a warning
        header("Location: CreateSheet.php");
    } else {
        Logger::Log("No Sheet Data", LogLevel::INFO);
    }
?>

<?php

// construct a new Header
$h = new Header("Datenstrukturen",
                "",
                "Admin",
                "211221492");

$h->setBackURL("Lecturer.php")
->setBackTitle("zur Veranstaltung");

$sheetSettings = Template::WithTemplateFile('include/CreateSheet/SheetSettings.template.html');

$createExercise = Template::WithTemplateFile('include/CreateSheet/CreateExercise.template.html');

$exerciseSettings = Template::WithTemplateFile('include/CreateSheet/ExerciseSettings.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, "<form action=\"CreateSheet.php\" method=\"POST\">", $sheetSettings, $createExercise, "</form>");
$w->set_config_file('include/configs/config_createSheet.json');
$w->show();
?>

