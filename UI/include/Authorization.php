<?php
include_once  dirname(__FILE__) . '/Authentication.php';
include_once dirname(__FILE__) . '/StudIPAuthentication.php';

$auth = new Authentication();
$StudIPauth = new StudIPAuthentication();

$invalidLogin = Authentication::checkLogin() == false;
$shouldLogOut = isset($_GET['action']) && $_GET['action'] == "logout";

if ($invalidLogin == true || $shouldLogOut == true) {
    // the user's login is no longer valid or he requested to be logged out
    Authentication::logoutUser();
}

?>
