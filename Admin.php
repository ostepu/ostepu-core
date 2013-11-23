<?php
include_once 'include/Header/Header.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';

// construct a new header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "Admin");

$menu = '<ul id="navigation" class="navigation">
<li><a href="#">Studentenrolle einnehmen</a></li>
<li><a id="selected" href="#">Personen verwalten</a></li>
<li><a href="#">Zulassungsbedingungen</a></li>
<li><a href="#">Backups</a></li>
</ul>';


$w = new HTMLWrapper($h);

// construct some exercise sheets
$sheetString = file_get_contents("http://localhost/Uebungsplattform/Sheet");



// convert the json string into an associative array
$sheets = json_decode($sheetString, true);

// construct some exercise sheets
$sheetString = file_get_contents("http://localhost/Uebungsplattform/Sheet");

// convert the json string into an associative array
$sheets = json_decode($sheetString, true);

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetTutor.template.json');

$t->bind($sheets);

$w = new HTMLWrapper($h, $t);
$w->setNavigationElement($menu);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

