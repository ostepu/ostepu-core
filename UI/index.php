<?php
/**
 * @file index.php
 * Generates a page that shows an overview of a user's courses.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

// load user data from the database
$databaseURI = $databaseURI . "/user/user/{$uid}";
$user = http_get($databaseURI, false);
$user = json_decode($user, true);

if (is_null($user)) {
    $user = array();
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind(array("name" => "Übungsplattform",
               "notificationElements" => $notifications));

$pageData = array('uid' => $user['id'],
                  'courses' => $user['courses'],
                  'sites' => $sites,
                  'statusName' => PRIVILEGE_LEVEL::$NAMES);

// construct a login element
$courseSelect = Template::WithTemplateFile('include/CourseSelect/CourseSelect.template.html');
$courseSelect->bind($pageData);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $courseSelect);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
