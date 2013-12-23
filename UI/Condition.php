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

$data = file_get_contents("http://localhost/Uebungsplattform/UI/Data/ConditionData");
$data = json_decode($data, true);

$users = $data;

// construct a content element for setting exam paper conditions
$setCondition = Template::WithTemplateFile('include/Condition/SetCondition.template.html');

$userList = Template::WithTemplateFile('include/Condition/UserList.template.html');
$userList->bind($users);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $setCondition, $userList);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

