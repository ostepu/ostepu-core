<?php
include_once 'include/Header/Header.php';
include_once 'include/ExerciseSheet/ExerciseSheetStudent.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';

// construct a new header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "211221492");

/*
 * if (is_student($user))
 */
$h->setPoints(75);

// construct some exercise sheets
$sheetString = file_get_contents("http://localhost/uebungsplattform/Sheet");

// convert the json string into an associative array
$sheets = json_decode($sheetString, true);

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetStudent.template.json');

$t->bind($sheets);

$w = new HTMLWrapper($h, $t);
$w->show();
?>
