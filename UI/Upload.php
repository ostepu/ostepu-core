<?php
/**
 * @file Upload.php
 * Shows a form to upload solutions.
 */

include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once '../Assistants/Logger.php';

if (isset($_POST['action'])) {
    Logger::Log($_POST, LogLevel::INFO);
    Logger::Log($_FILES, LogLevel::INFO);
    /**
     * @todo actually upload the data
     */

    // redirect, so the user can reload the page without a warning
    header("Location: Upload.php");
} else {
    Logger::Log("No Sheet Data", LogLevel::INFO);
}

/**
 * @todo Read parameters from the GET Request and request data from the database
 */

if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    die('no course id!\n');
}

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
} else {
    die('no user id!\n');
}

// load user data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/user/user/{$uid}";
$user = http_get($databaseURI);
$user = json_decode($user, true);

// load course data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/course/course/{$cid}";
$course = http_get($databaseURI);
$course = json_decode($course, true)[0];

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind($course);
$h->bind(array("backTitle" => "Veranstaltung wechseln",
               "backURL" => "Student.php?cid={$cid}&uid={$uid}",
               "notificationElements" => $notifications));


/**
 * @todo detect when the form was changed by the user, this could be done by
 * hashing the form elements before handing them to the user:
 * - hash the form (simple hash/hmac?)
 * - save the calculated has in a hidden form input
 * - when the form is posted recalculate the hash and compare to the previous one
 * - log the user id?
 *
 * @sa http://www.php.net/manual/de/function.hash-hmac.php
 * @sa http://php.net/manual/de/function.hash.php
 */

$sheetData = array('sheetID' => 110,
                   'exercises' => array(array('exerciseID' => 1
                                              ),
                                        array('exerciseID' => 2
                                              )
                                        ),
                   'uid' => 2
                   );

/**
 * @todo fix the template so textareas don't contain spaces bx default
 */

$t = Template::WithTemplateFile('include/Upload/Upload.template.html');
$t->bind($sheetData);

$w = new HTMLWrapper($h, $t);
$w->set_config_file('include/configs/config_upload_exercise.json');
$w->show();
?>
