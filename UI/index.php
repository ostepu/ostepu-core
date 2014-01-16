<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';

// construct a new Header
$h = new Header("Übungsplattform",
                "",
                "",
                "");

$h->setBackURL("index.php")
->setBackTitle("zur Veranstaltung");

// construct a login element
$courseSelect = Template::WithTemplateFile('include/CourseSelect/CourseSelect.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $courseSelect);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

