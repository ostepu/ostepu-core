<?php
/**
 * @file RightsManagement.php
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
               "backURL" => "Admin.php?cid={$cid}",
               "navigationElement" => $menu,
               "notificationElements" => $notifications));

// construct a content element for setting tutor rights
$tutorRights = Template::WithTemplateFile('include/RightsManagement/TutorRights.template.html');
$tutorRights->bind(array());

// construct a content element for setting lecturer rights
$lecturerRights = Template::WithTemplateFile('include/RightsManagement/LecturerRights.template.html');
$lecturerRights->bind(array());

// construct a content element for creating an user
$createUser = Template::WithTemplateFile('include/RightsManagement/CreateUser.template.html');
$createUser->bind(array());

/**
 * @todo combine the templates into a single file
 */

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h,
                     $tutorRights,
                     $lecturerRights,
                     $createUser);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
