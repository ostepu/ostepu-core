<?php
/**
 * @file Lecturer.php
 * Constructs the page that is displayed to a lecturer.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

// load GetSite data for Lecturer.php
$databaseURI = $getSiteURI . "/lecturer/user/{$uid}/course/{$cid}";
$lecturer_data = http_get($databaseURI, false);
$lecturer_data = json_decode($lecturer_data, true);
$lecturer_data['filesystemURI'] = $filesystemURI;

$user_course_data = $lecturer_data['user'];

// check userrights for course
Authentication::checkRights(PRIVILEGE_LEVEL::LECTURER, $cid, $uid, $user_course_data);

if (is_null($user_course_data)) {
    $user_course_data = array();
}

$menu = MakeNavigationElementForCourseStatus($user_course_data['courses']);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "notificationElements" => $notifications,
               "navigationElement" => $menu));


$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetLecturer.template.html');
$t->bind($lecturer_data);

$w = new HTMLWrapper($h, $t);
$w->set_config_file('include/configs/config_admin_lecturer.json');
$w->show();

?>
