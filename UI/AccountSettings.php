<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';

$notifications = array();

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
} else {
    $uid = 0;
}

if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    die('no course id!\n');
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


$data = file_get_contents("http://localhost/Uebungsplattform/UI/Data/UserData");
$data = json_decode($data, true);


// construct a content element for account information
$accountInfo = Template::WithTemplateFile('include/AccountSettings/AccountInfo.template.html');
$accountInfo->bind($user);

// construct a content element for changing password
$changePassword = Template::WithTemplateFile('include/AccountSettings/ChangePassword.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $accountInfo, $changePassword);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

