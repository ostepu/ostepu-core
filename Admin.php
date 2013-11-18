<?php
include_once 'include/Header/Header.php';
include_once 'include/ExerciseSheet/ExerciseSheetTutor.php';
include_once 'include/HTMLWrapper.php';

// construct a new header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "Admin");

$menu = '<ul id="navigation">
<li><a href="#">Kurz</a></li>
<li><a id="selected" href="#">Angeklickt</a></li>
<li><a href="#">Ziemlich langer Menüpunkt</a></li>
<li><a href="#">Blubb</a></li>
</ul>';

$w = new HTMLWrapper($h, $menu);

$w->show();
?>

