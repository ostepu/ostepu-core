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

// construct a content element for managing groups
$assignAutomatically = Template::WithTemplateFile('include/TutorAssign/AssignAutomatically.template.json');
$assignAutomatically->bind(array());

// construct a content element for creating groups
$assignManually = Template::WithTemplateFile('include/TutorAssign/AssignManually.template.json');
$assignManually->bind(array());

// construct a content element for joining groups
$assignCancel = Template::WithTemplateFile('include/TutorAssign/AssignCancel.template.json');
$assignCancel->bind(array());

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $assignAutomatically, $assignManually, $assignCancel);
$w->show();
?>

