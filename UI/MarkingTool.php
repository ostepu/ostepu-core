<?php
/**
 * @file MarkingTool.php
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

// load user data from the database
$databaseURL = $databaseURI . "/user/user/{$uid}";
$user = http_get($databaseURL);
$user = json_decode($user, true);

// load course data from the database
$databaseURL = $databaseURI . "/course/course/{$cid}";
$course = http_get($databaseURL);
$course = json_decode($course, true)[0];


// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind($course);
$h->bind(array("backTitle" => "zur Veranstaltung",
               "backURL" => "Tutor.php?cid={$cid}",
               "notificationElements" => $notifications));


$searchSettings = Template::WithTemplateFile('include/MarkingTool/SearchSettings.template.html');
$markingElement = Template::WithTemplateFile('include/MarkingTool/MarkingElement.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $searchSettings, $markingElement);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
