<?php
include_once 'include/Authentication.php';

$auth = new Authentication();

// Benutzer prüfen
if (!Authentication::checkLogin()||$_GET['action'] == "logout")
{
    Authentication::logoutUser();
}

?>
