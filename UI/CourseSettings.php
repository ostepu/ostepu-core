<?php
/**
 * @file CourseSettings.php
 * Constructs the page for managing basic settings for a course.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

// load user and course data from the database
$databaseURI = $databaseURI . "/coursestatus/course/{$cid}/user/{$uid}";
$user_course_data = http_get($databaseURI, true, $message);

$user_course_data = json_decode($user_course_data, true);

/**
 * @todo check rights
 */

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "notificationElements" => $notifications));

$t = Template::WithTemplateFile('include/CourseSettings/SetCourseSettings.template.html');


$w = new HTMLWrapper($h, $t);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
