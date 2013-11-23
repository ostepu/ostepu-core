<?php
include_once 'include/Header/Header.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';

// construct a new header
$h = new Header("Datenstrukturen",
                "",
                "Felix Schmidt",
                "Dozent");

// construct some exercise sheets
$sheetString = file_get_contents("http://localhost/Uebungsplattform/Sheet");

// convert the json string into an associative array
$sheets = json_decode($sheetString, true);

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetLecturer.template.json');

$t->bind($sheets);

$w = new HTMLWrapper($h, $t);
$w->show();
?>

