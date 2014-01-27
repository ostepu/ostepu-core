<?php
include_once 'include/Authorization.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once '../Assistants/Logger.php';
include_once 'include/Helpers.php';

if (isset($_POST['sheetID'])) {
    /**
     * @todo load data for the slected user
     */
    Logger::Log($_POST, LogLevel::INFO);
} else {
    Logger::Log("No Data", LogLevel::INFO);
}

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

$menu = Template::WithTemplateFile('include/Navigation/NavigationAdmin.template.html');

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind($course);
$h->bind(array("backTitle" => "zur Veranstaltung",
               "backURL" => "Admin.php?cid={$cid}&uid={$uid}",
               "navigationElement" => $menu,
               "notificationElements" => $notifications));

// construct a content element for the ability to look at the upload history of a student
$uploadHistory = Template::WithTemplateFile('include/UploadHistory/UploadHistory.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $uploadHistory);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
