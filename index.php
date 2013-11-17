<?php
include_once 'include/Header/Header.php';
include_once 'include/ExerciseSheet/ExerciseSheetStudent.php';
include_once 'include/HTMLWrapper.php';

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

$w = new HTMLWrapper($h);

$content = array();

foreach ($sheets as $sheet) {
    $ex = $sheet['exercises'];
    $e = new ExerciseSheetStudent($sheet['name'], $ex,
                                  $sheet['percent'], $sheet['endTime']);

    // wrap the element in some HTML
    $w->insert($e);
}
$w->show();
?>
