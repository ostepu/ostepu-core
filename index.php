<?php
include_once 'include/Header/Header.php';
include_once 'include/ExerciseSheet/ExerciseSheet.php';
include_once 'include/HTMLWrapper.php';

// construct a new header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "211221492", 
                "75%");

// construct some exercise sheets
$s = new ExerciseSheet("Serie 2", array(
                       array("exerciseType" => "Normal",
                             "points" => "10",
                             "maxPoints" => "10"
                             ),
                       array("exerciseType" => "Bonus",
                             "points" => "10",
                             "maxPoints" => "10"
                             ),
                       ));

$s2 = new ExerciseSheet("Serie 1", array(
                       array("exerciseType" => "Normal",
                             "points" => "5",
                             "maxPoints" => "10"
                             ),
                       array("exerciseType" => "Bonus",
                             "points" => "3",
                             "maxPoints" => "4"
                             ),
                       ));

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $s, $s2);
$w->show();
?>
