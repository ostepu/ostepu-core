<?php
include_once 'include/Authentication.php';

$auth = new Authentication();

if (Authentication::checkLogin() == false || ($_GET['action'] == "logout")) {
    // the user's login is no longer valid or he requested to be logged out
    Authentication::logoutUser();
}

?>
