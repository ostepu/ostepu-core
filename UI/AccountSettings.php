<?php
include_once 'include/Authorization.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once '../Assistants/Logger.php';
include_once 'include/Helpers.php';

$notifications = array();

if (isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
} else {
    Logger::Log('no user id!\n');
}

// load user data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/user/user/{$uid}";
$user = http_get($databaseURI);
$user = json_decode($user, true);

$pagedata = array("backTitle" => "Veranstaltungen",
                  "name" => "Accounteinstellungen",
                  "backURL" => "index.php?uid={$uid}",
                  "notificationElements" => $notifications
                  );

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind($pagedata);

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
