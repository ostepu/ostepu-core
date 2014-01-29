<?php
/**
 * @file CreateSheet.php
 * Constructs a page where a user can create an exercise sheet.
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

if (isset($_POST['action'])) {
    Logger::Log($_POST, LogLevel::INFO);
    /**
     * @todo create a new sheet or update the existing one
     */

    // redirect, so the user can reload the page without a warning
    header("Location: CreateSheet.php");
} else {
    Logger::Log("No Sheet Data", LogLevel::INFO);
}

$notifications = array();

if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    Logger::Log('no course id!\n');
}

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
} else {
    Logger::Log('no user id!\n');
}

// load user data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/user/user/{$uid}";
$user = http_get($databaseURI);
$user = json_decode($user, true);

// load course data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/course/course/{$cid}";
$course = http_get($databaseURI);
$course = json_decode($course, true)[0];

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind($course);
$h->bind(array("backTitle" => "zur Veranstaltung",
               "backURL" => "Lecturer.php?cid={$cid}&uid={$uid}",
               "notificationElements" => $notifications));

/**
 * @todo combine the templates in a single file
 */
$sheetSettings = Template::WithTemplateFile('include/CreateSheet/SheetSettings.template.html');

$createExercise = Template::WithTemplateFile('include/CreateSheet/CreateExercise.template.html');

$exerciseSettings = Template::WithTemplateFile('include/CreateSheet/ExerciseSettings.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, "<form action=\"CreateSheet.php\" method=\"POST\">", $sheetSettings, $createExercise, "</form>");
$w->set_config_file('include/configs/config_createSheet.json');
$w->show();

?>
