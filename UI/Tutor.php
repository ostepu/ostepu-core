<?php
/**
 * @file Tutor.php
 * Constructs the page that is displayed to a tutor.
 */
include 'include/Authorization.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once 'include/Helpers.php';

if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    die('no course id!\n');
}

if (isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
} else {
    die('no user id!\n');
}

// load user and course data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/coursestatus/course/{$cid}/user/{$uid}";
$user_course_data = http_get($databaseURI);
$user_course_data = json_decode($user_course_data, true);

// check userrights for course
$auth->checkRights(1, $cid, $uid, $user_course_data);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php?uid={$uid}",
               "notificationElements" => $notifications));

$databaseURL = "http://141.48.9.92/uebungsplattform/DB/DBExerciseSheet/exercisesheet/course/{$cid}/exercise";

// construct some exercise sheets
$sheetString = http_get($databaseURL);

// convert the json string into an associative array
$sheets = array("sheets" =>json_decode($sheetString, true),
                "uid" => $uid,
                "cid" => $cid);

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetTutor.template.html');
$t->bind($sheets);

$w = new HTMLWrapper($h, $t);
$w->set_config_file('include/configs/config_student_tutor.json');
$w->show();
?>

