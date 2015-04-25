<?php
/**
 * @file Login.php
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once dirname(__FILE__) . '/include/Authentication.php';
include_once dirname(__FILE__) . '/include/HTMLWrapper.php';
include_once dirname(__FILE__) . '/include/Template.php';
include_once dirname(__FILE__) . '/../Assistants/Logger.php';
include_once dirname(__FILE__) . '/include/Helpers.php';

$auth = new Authentication();
Authentication::preventSessionFix();

if (isset($_POST['action'])) {
    // trim and stripslashes the input
    $input['username'] = strtolower($_POST['username']);
    $input['password'] = $_POST['password'];

    $input = cleanInput($input);

    // if a hidden Post named back and the php file exists set backurl
    if (isset($_POST['back']) && file_exists(parse_url($_POST['back'], PHP_URL_PATH))) {
        $input['back'] = $_POST['back'];
    } else {
        $input['back'] = "index.php";
    }

    // log in user and return result
    $signed = $auth->loginUser($input['username'], $input['password']);

    if ($signed===true) {
        header('Location: ' . $input['back']);
        exit();
    } else {
        if ($signed!==false){
            $notifications[] = $signed;
        } else 
            $notifications[] = MakeNotification("error", "Die Anmeldung war fehlerhaft!");
    }
} else {
    $notifications = array();
}

// check if already logged in
if(Authentication::checkLogin()) {
    header('Location: index.php');
    exit();
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind(array("backTitle" => "Veranstaltung wechseln",
               "name" => "Übungsplattform",
               "hideBackLink" => "true",
               "hideLogoutLink" => "true",
               "notificationElements" => $notifications));

// construct a login element
$userLogin = Template::WithTemplateFile('include/Login/Login.template.html');
// if back Parameter is given bind it to the userLogin to create hidden input
if (isset($_GET['back'])) {
    $backparameter = cleanInput($_GET['back']);
    $backdata = array("backURL" => $backparameter);
} else {
    $backdata = array();
}

$userLogin->bind($backdata);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $userLogin);
$w->set_config_file('include/configs/config_default.json');
$w->show();

