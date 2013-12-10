<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';

// construct a new Header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "211221492");

$h->setBackURL("index.php")
->setBackTitle("zur Veranstaltung");

$data = file_get_contents("http://localhost/Uebungsplattform/Data/UserData");
$data = json_decode($data, true);

$user = $data['user'];
unset($data['user']);

// construct a content element for account information
$accountInfo = Template::WithTemplateFile('include/AccountSettings/AccountInfo.template.json');
$accountInfo->bind($user);

// construct a content element for changing password
$changePassword = Template::WithTemplateFile('include/AccountSettings/ChangePassword.template.json');
$changePassword->bind(array());

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $accountInfo, $changePassword);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

