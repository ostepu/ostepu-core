<?php
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';

$notifications = array();

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
} else {
    $uid = 0;
}

if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    die('no course id!\n');
}

// load user data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/user/user/{$uid}";
$user = http_get($databaseURI);
$user = json_decode($user, true);

// load course data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/course/course/{$cid}";
$course = http_get($databaseURI);
$course = json_decode($course, true)[0];


// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind($course);
$h->bind(array("backTitle" => "zur Veranstaltung",
               "backURL" => "Tutor.php?cid={$cid}&uid={$uid}",
               "notificationElements" => $notifications));


$searchSettings = Template::WithTemplateFile('include/MarkingTool/SearchSettings.template.html');
$markingElement = Template::WithTemplateFile('include/MarkingTool/MarkingElement.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $searchSettings, $markingElement);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
