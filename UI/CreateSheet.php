<?php
/**
 * @file CreateSheet.php
 * Constructs a page where a user can create an exercise sheet.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

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

// load user data from the database
$databaseURL = $databaseURI . "/user/user/{$uid}";
$user = http_get($databaseURL);
$user = json_decode($user, true);

// load course data from the database
$databaseURL = $databaseURI . "/course/course/{$cid}";
$course = http_get($databaseURL);
$course = json_decode($course, true)[0];

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind($course);
$h->bind(array("backTitle" => "zur Veranstaltung",
               "backURL" => "Lecturer.php?cid={$cid}",
               "navigationElement" => $menu,
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
