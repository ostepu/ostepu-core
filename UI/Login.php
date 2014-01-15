<?php
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once 'include/Helpers.php';
include_once 'include/Authentication.php';

// no error messages
// error_reporting(0);
$auth = new Authentication();
$auth->preventSessionFix();

// check if already logged in
if($auth->checkLogin()) {
    header('location: index.php');
    exit();
}


if (isset($_POST['action'])) {
    // trim and stripslashes the input
    $input['username'] = strtolower($_POST['username']);
    $input['password'] = $_POST['password'];
    $input = cleanInput($input);

    // log in user and return result
    $signed = $auth->loginUser($input['username'], $input['password']);

    if ($signed) {
        header('location: index.php');
        exit();
    } else {
        $notifications[] = MakeNotification("error", "Die Anmeldung war fehlerhaft!");
    }
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind(array("backTitle" => "Veranstaltung wechseln",
               "name" => "Übungsplattform",
               "hideLogoutLink" => "true",
               "notificationElements" => $notifications));

// construct a login element
$userLogin = Template::WithTemplateFile('include/Login/Login.template.html');
$userLogin->bind(array());

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $userLogin);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>