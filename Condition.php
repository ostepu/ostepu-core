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

// construct a content element for setting exam paper conditions
$setCondition = Template::WithTemplateFile('include/Condition/SetCondition.template.json');
$setCondition->bind(array());

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $setCondition);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

