<?php
include_once 'include/Header/Header.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once 'include/Helpers.php';

if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    die('no course id!\n');
}

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
} else {
    die('no user id!\n');
}

$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/user/user/{$uid}";
$user = http_get($databaseURI);
$user = json_decode($user, true);

$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/course/course/{$cid}";
$course = http_get($databaseURI);
$course = json_decode($course, true)[0];

// construct a new header
$h = new Header($course['name'],
                "",
                $user['firstName'] . ' ' . $user['lastName'],
                $user['userName']);

$h->setBackURL("index.php?uid={$uid}");

/*
 * if (is_student($user))
 */
$h->setPoints(75);

$databaseURL = "http://141.48.9.92/uebungsplattform/DB/DBExerciseSheet/exercisesheet/course/{$cid}/exercise";

// construct some exercise sheets
$sheetString = http_get($databaseURL);

// convert the json string into an associative array
$sheets = array("sheets" =>json_decode($sheetString, true),
                "uid" => $uid,
                "cid" => $cid);

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetStudent.template.html');

$t->bind($sheets);

$w = new HTMLWrapper($h, $t);
$w->set_config_file('include/configs/config_student_tutor.json');
$w->show();
?>
