<?php
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once 'include/Helpers.php';

$notifications = array();

if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    $uid = -1;
    $notifications[] = MakeNotification("error", "No course id!");
}

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
} else {
    $cid = -1;
    $notifications[] = MakeNotification("error", "No user id!");
}

// load user data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/user/user/{$uid}";
$user = http_get($databaseURI);
$user = json_decode($user, true);

if (is_null($user)) {
    $user = array();
}

// load course data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/course/course/{$cid}";
$course = http_get($databaseURI);
$course = json_decode($course, true)[0];

if (is_null($course)) {
    $course = array();
}

$menu = Template::WithTemplateFile('include/Navigation/NavigationLecturer.template.html');

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind($course);
$h->bind(array("backTitle" => "Veranstaltung wechseln",
               "navigationElement" => $menu,
               "notificationElements" => $notifications));

$databaseURL = "http://141.48.9.92/uebungsplattform/DB/DBExerciseSheet/exercisesheet/course/{$cid}/exercise";

// construct some exercise sheets
$sheetString = http_get($databaseURL);

// convert the json string into an associative array
$sheets = array("sheets" =>json_decode($sheetString, true),
                "uid" => $uid,
                "cid" => $cid);

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetLecturer.template.html');
$t->bind($sheets);

$w = new HTMLWrapper($h, $createSheet, $t);
$w->set_config_file('include/configs/config_admin_lecturer.json');
$w->show();
?>

