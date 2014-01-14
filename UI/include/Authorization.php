<?php
include_once 'include/Helpers.php';

// force to use session-cookies and to transmit SID over URL
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
 
// start session
session_start();

function resetUser()
{
    session_destroy();
    header('location: Login.php');
    exit;
}

// Benutzer prüfen
if (!checkLogin()||$_GET['action'] == "logout")
{
    resetUser();
}
?>