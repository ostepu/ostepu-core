<?php
/**
 * @file Error.php
 *
 * @author Ralf Busch
 */

include_once 'include/Authentication.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once '../Assistants/Logger.php';
include_once 'include/Helpers.php';

if (isset($_GET['msg'])) {
  $msg = cleanInput($_GET['msg']);
}

if (isset($msg) && $msg == "403") {
    header("HTTP/1.0 403 Access Forbidden");
    $notifications[] = MakeNotification("error", "403: Access Forbidden!!");
}

if (isset($msg) && $msg == "404") {
    header("HTTP/1.0 404 Not Found");
    $notifications[] = MakeNotification("error", "404: Not Found!!");
}

if (isset($msg) && $msg == "409") {
    header("HTTP/1.0 404 Not Found");
    $notifications[] = MakeNotification("error", "409: Conflict!!");
}

$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind(array("name" => "Übungsplattform",
               "backTitle" => "Startseite",
               "backURL" => "index.php",
               "hideLogoutLink" => "true",
               "notificationElements" => $notifications));

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>