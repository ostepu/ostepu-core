<?php
/**
 * @file AccountSettings.php
 * Constructs a page where a user can manage his account.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

// load user data from the database
$URL = $getSiteURI . "/accountsettings/user/{$uid}";
$user_data = http_get($URL);
$user_data = json_decode($user_data, true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_data);
$h->bind(array("name" => "Account Einstellungen",
               "backTitle" => "Veranstaltungen",
               "backURL" => "index.php",
               "notificationElements" => $notifications));

// construct a content element for account information
$accountInfo = Template::WithTemplateFile('include/AccountSettings/AccountInfo.template.html');
$accountInfo->bind($user_data);

// construct a content element for changing password
$changePassword = Template::WithTemplateFile('include/AccountSettings/ChangePassword.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $accountInfo, $changePassword);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
