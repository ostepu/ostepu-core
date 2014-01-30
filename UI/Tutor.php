<?php
/**
 * @file Tutor.php
 * Constructs the page that is displayed to a tutor.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

// load user and course data from the database
$databaseURL = $databaseURI . "/coursestatus/course/{$cid}/user/{$uid}";
$user_course_data = http_get($databaseURL, true, $message);
$user_course_data = json_decode($user_course_data, true);

Logger::Log($user_course_data);

// check userrights for course
Authentication::checkRights(1, $cid, $uid, $user_course_data);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "notificationElements" => $notifications));

$databaseURL = $databaseURI . "/exercisesheet/course/{$cid}/exercise";
$sheetData = http_get($databaseURL, true, $message);
$sheetData = json_decode($sheetData, true);

// convert the json string into an associative array
$sheetData = array("sheets" => $sheetData,
                   "uid" => $uid,
                   "cid" => $cid);

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetTutor.template.html');
$t->bind($sheetData);

$w = new HTMLWrapper($h, $t);
$w->set_config_file('include/configs/config_student_tutor.json');
$w->show();

?>
