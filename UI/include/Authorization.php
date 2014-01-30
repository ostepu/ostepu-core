<?php
include_once 'include/Authentication.php';
include_once 'include/StudIPAuthentication.php';

// start session
session_start();

$auth = new Authentication();
$StudIPauth = new StudIPAuthentication();

if (Authentication::checkLogin() == false || ($_GET['action'] == "logout")) {
    // the user's login is no longer valid or he requested to be logged out
    Authentication::logoutUser();
}

?>
