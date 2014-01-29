<?php
/**
 * @file Admin.php
 * Constructs the page that is displayed to an admin.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Authorization.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once '../Assistants/Logger.php';
include_once 'include/Helpers.php';

if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    Logger::Log('no course id!\n');
}

if (isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
} else {
    Logger::Log('no user id!\n');
}

// load user and course data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/coursestatus/course/{$cid}/user/{$uid}";
$user_course_data = http_get($databaseURI, true, $message);

if ($message == "401") {
    $auth->logoutUser();
}

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
               "notificationElements" => $notifications)
        );


$databaseURL = "http://141.48.9.92/uebungsplattform/DB/DBExerciseSheet/exercisesheet/course/{$cid}/exercise";

// construct some exercise sheets
$sheetString = http_get($databaseURL, true, $message);

if ($message == "401") {
    $auth->logoutUser();
}

// convert the json string into an associative array
$sheets = array("sheets" => json_decode($sheetString, true),
                "uid" => $uid,
                "cid" => $cid
                );

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetLecturer.template.html');
$t->bind($sheets);

$w = new HTMLWrapper($h, $t);
$w->set_config_file('include/configs/config_admin_lecturer.json');
$w->show();

?>
