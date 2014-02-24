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
    //header("Location: CreateSheet.php");
}

// load user data from the database
$URL = $getSiteURI . "/createsheet/user/{$uid}/course/{$cid}";
$createsheetData = http_get($URL);
$createsheetData = json_decode($createsheetData, true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($createsheetData);

$h->bind(array("navigationElement" => $menu,
               "notificationElements" => $notifications));

Authentication::checkRights(PRIVILEGE_LEVEL::LECTURER, $cid, $uid, $createsheetData);

/**
 * @todo combine the templates in a single file
 */
$sheetSettings = Template::WithTemplateFile('include/CreateSheet/SheetSettings.template.html');
$sheetSettings->bind($admin_data);

$createExercise = Template::WithTemplateFile('include/CreateSheet/CreateExercise.template.html');

$exerciseSettings = Template::WithTemplateFile('include/CreateSheet/ExerciseSettings.template.php');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $sheetSettings, $createExercise, $exerciseSettings);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $sheetSettings, $createExercise, $exerciseSettings);
$w->set_config_file('include/configs/config_createSheet.json');
$w->show();

?>
