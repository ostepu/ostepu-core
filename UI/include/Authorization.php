<?php
include_once 'include/Authentication.php';

$auth = new Authentication();


// Benutzer prüfen
if (!$auth->checkLogin()||$_GET['action'] == "logout")
{
    $auth->logoutUser();
}
?>