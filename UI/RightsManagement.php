<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';

// construct a new Header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "Admin");

$h->setBackURL("index.php")
->setBackTitle("zur Veranstaltung");

// construct a content element for setting tutor rights
$tutorRights = Template::WithTemplateFile('include/RightsManagement/TutorRights.template.html');
$tutorRights->bind(array());

// construct a content element for setting lecturer rights
$lecturerRights = Template::WithTemplateFile('include/RightsManagement/LecturerRights.template.html');
$lecturerRights->bind(array());

// construct a content element for creating an user
$createUser = Template::WithTemplateFile('include/RightsManagement/CreateUser.template.html');
$createUser->bind(array());

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $tutorRights, $lecturerRights, $createUser);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

