<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';

// construct a new Header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "211221492");

$h->setBackURL("index.php")
->setBackTitle("zur Veranstaltung");

$searchSettings = Template::WithTemplateFile('include/MarkingTool/SearchSettings.template.html');
$markingElement = Template::WithTemplateFile('include/MarkingTool/MarkingElement.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $searchSettings, $markingElement);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

