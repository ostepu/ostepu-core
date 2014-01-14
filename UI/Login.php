<?php
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once 'include/Helpers.php';

// no error messages
// error_reporting(0);
 
// force to use session-cookies and to transmit SID over URL
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
 
// start session
session_start();

// prevent possible session fixation attack
if (!isset( $_SESSION['server_SID'] )) {
    // delete session content
    session_unset();
    $_SESSION = array();
    // restart session
    session_destroy();
    session_start();
    // generate new session id
    session_regenerate_id();
    // save status that serverSID is given
    $_SESSION['server_SID'] = true;
}

// check if already logged in
if(checkLogin()) {
    header('location: index.php?uid='.$_SESSION['uid']);
    exit();
}

function loginUser($username, $password)
{
    $databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/user/user/{$username}";
    $info =array();
    $user = http_get($databaseURI,$message);
    $user = json_decode($user, true);

    // check if user exists 
    if ($message != "404") {
        /**
         * @todo implement correct Hash password method
         */
        if ($password == $user['password'] && $user['flag'] == 1) {
            /**
             * @todo reset counter for failed logins
             */
            // save logged in uid
            $_SESSION['uid'] = $user['id'];
            return true;
        } else {
            /**
             * @todo update counter for failed logins, increase it
             */
        }
    }
    return false;
}

function updateUser($username, $password)
{
    $_SESSION['session'] = hash('sha256', session_id().$username.$password);
    /**
     * @todo create session on server with $_SESSION['session']
     */

    /**
     * @todo only if created session on server is successful
     */
    $_SESSION['signed'] = true;
    $_SESSION['lastactive'] = $_SERVER['REQUEST_TIME'];
    

    return true;
}

if (isset($_POST['action'])) {
    // trim and stripslashes the input
    $input['username'] = strtolower($_POST['username']);
    $input['password'] = $_POST['password'];
    $input = cleanInput($input);

    // log in user and return result
    $signed = loginUser($input['username'], $input['password']);

    if ($signed) {
        // update user and return result
        $update = updateUser($input['username'], $input['password']);

        if ($update) {
            header('location: index.php?uid='.$_SESSION['uid']);
            exit();
        } else {
            print "Bei der Anmeldung ist ein Problem aufgetreten!";
        }
    } else {
        print "Die Anmeldung war fehlerhaft!";
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