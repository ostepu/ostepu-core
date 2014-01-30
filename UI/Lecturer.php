<?php
/**
 * @file Lecturer.php
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

// check userrights for course
Authentication::checkRights(3, $cid, $uid, $user_course_data);

if (is_null($user_course_data)) {
    $user_course_data = array();
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "notificationElements" => $notifications));

$databaseURL = $databaseURI . "/exercisesheet/course/{$cid}/exercise";

// construct some exercise sheets
$sheetData = http_get($databaseURL, true, $message);
$sheetData = json_decode($sheetData, true);

// convert the json string into an associative array
$sheets = array("sheets" => $sheetData,
                "uid" => $uid,
                "cid" => $cid);

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetLecturer.template.html');
$t->bind($sheets);

$w = new HTMLWrapper($h, $createSheet, $t);
$w->set_config_file('include/configs/config_admin_lecturer.json');
$w->show();

?>
