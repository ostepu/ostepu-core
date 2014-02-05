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
$databaseURI = $getSiteURI . "/condition/user/{$uid}/course/{$cid}";
$condition_data = http_get($databaseURI, false);
$condition_data = json_decode($condition_data, true);

$user_course_data = $condition_data['user'];

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Admin.php?cid={$cid}",
               "notificationElements" => $notifications));


// construct a content element for setting exam paper conditions
$setCondition = Template::WithTemplateFile('include/Condition/SetCondition.template.html');
$setCondition->bind($condition_data);

$userList = Template::WithTemplateFile('include/Condition/UserList.template.html');
$userList->bind($condition_data);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $setCondition, $userList);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
