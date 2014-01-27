<?php
include 'include/Authorization.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';

$notifications = array();

if (isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
} else {
    $uid = 0;
}

// load user data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/user/user/{$uid}";
$user = http_get($databaseURI);
$user = json_decode($user, true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind(array("backTitle" => "Veranstaltungen",
               "name" => "Accounteinstellungen",
               "backURL" => "index.php?uid={$uid}",
               "notificationElements" => $notifications));

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
