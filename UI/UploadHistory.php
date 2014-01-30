<?php
/**
 * @file Upload.php
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

if (isset($_POST['sheetID'])) {
    /**
     * @todo load data for the slected user
     */
    Logger::Log($_POST, LogLevel::INFO);
} else {
    Logger::Log("No Data", LogLevel::INFO);
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
               "backURL" => "Admin.php?cid={$cid}",
               "navigationElement" => $menu,
               "notificationElements" => $notifications));

// construct a content element for the ability to look at the upload history of a student
$uploadHistory = Template::WithTemplateFile('include/UploadHistory/UploadHistory.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $uploadHistory);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
