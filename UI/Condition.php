<?php
/**
 * @file Condition.php
 * Constructs the page that is displayed when managing exam conditions.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

// load user data from the database
$databaseURL = $databaseURI . "/user/user/{$uid}";
$user = http_get($databaseURL, false);
$user = json_decode($user, true);

Logger::Log($user);

// load course data from the database
$databaseURL = $databaseURI . "/course/course/{$cid}";
$course = http_get($databaseURL, true);
$course = json_decode($course, true)[0];

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind(array("backTitle" => "zur Veranstaltung",
               "backURL" => "Admin.php?cid={$cid}",
               "navigationElement" => $menu,
               "notificationElements" => $notifications));

$data = file_get_contents("http://localhost/Uebungsplattform/UI/Data/ConditionData");
$data = json_decode($data, true);

$listOfUsers = $data;

// construct a content element for setting exam paper conditions
$setCondition = Template::WithTemplateFile('include/Condition/SetCondition.template.html');

$userList = Template::WithTemplateFile('include/Condition/UserList.template.html');
$userList->bind($listOfUsers);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $setCondition, $userList);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
