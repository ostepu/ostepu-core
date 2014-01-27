<?php
include 'include/Authorization.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once 'include/Helpers.php';

$notifications = array();

if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    $notifications[] = MakeNotification("error", "No course id!");
}

if (isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
} else {
    $notifications[] = MakeNotification("error", "No user id!");
}

// load user and course data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/coursestatus/course/{$cid}/user/{$uid}";
$user_course_data = http_get($databaseURI, true, $message);

$user_course_data = json_decode($user_course_data, true);

// check userrights for course
Authentication::checkRights(3, $cid, $uid, $user_course_data);

if (is_null($user_course_data)) {
    $user_course_data = array();
}

$menu = Template::WithTemplateFile('include/Navigation/NavigationLecturer.template.html');

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "navigationElement" => $menu,
               "notificationElements" => $notifications));

$databaseURL = "http://141.48.9.92/uebungsplattform/DB/DBExerciseSheet/exercisesheet/course/{$cid}/exercise";

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
