<?php
include 'include/Header/Header.php';
include 'include/ExerciseSheet/ExerciseSheetTutor.php';
include 'include/HTMLWrapper.php';

// construct a new header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "Kontrolleur", 
                "--");

// construct some exercise sheets
$sheetString = file_get_contents("http://localhost/Uebungsplattform/Sheet");

// convert the json string into an associative array
$sheets = json_decode($sheetString, true);

$w = new HTMLWrapper($h);

$content = array();

foreach ($sheets as $sheet) {
    $ex = $sheet['exercises'];
    $e = new ExerciseSheet($sheet['name'], $ex);

    // wrap the element in some HTML
    $w->insert($e);
}
$w->show();
?>

