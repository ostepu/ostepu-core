<?php
include_once 'include/Header/Header.php';
include_once 'include/ExerciseSheet/ExerciseSheetTutor.php';
include_once 'include/HTMLWrapper.php';

// construct a new header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "Admin");

$menu = '<ul id="navigation" class="navigation">
<li><a href="#">Kurz</a></li>
<li><a id="selected" href="#">Angeklickt</a></li>
<li><a href="#">Ziemlich langer Menüpunkt</a></li>
<li><a href="#">BlubbBlubb</a></li>
</ul>';


$w = new HTMLWrapper($h);

// construct some exercise sheets
$sheetString = file_get_contents("http://localhost/Uebungsplattform/Sheet");



// convert the json string into an associative array
$sheets = json_decode($sheetString, true);

$w = new HTMLWrapper($h);

$content = array();

foreach ($sheets as $sheet) {
    $ex = $sheet['exercises'];
    $e = new ExerciseSheetTutor($sheet['name'], $ex,
                                $sheet['exerciseSheetInfo'], $sheet['endTime']);

    // wrap the element in some HTML
    $w->insert($e);
}



$w->setNavigationElement($menu);
$w->set_config_file('include/configs/config_admin.json');
$w->show();
?>

