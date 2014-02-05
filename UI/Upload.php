<?php
/**
 * @file Upload.php
 * Shows a form to upload solutions.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

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

// load user data from the database
$databaseURL = $databaseURI . "/user/user/{$uid}";
$user = http_get($databaseURL);
$user = json_decode($user, true);

// load course data from the database
$databaseURL = $databaseURI . "/course/course/{$cid}";
$course = http_get($databaseURL);
$course = json_decode($course, true)[0];

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind($course);
$h->bind(array("backTitle" => "Zur Veranstaltung",
               "backURL" => "Student.php?cid={$cid}",
               "notificationElements" => $notifications));


/**
 * @todo detect when the form was changed by the user, this could be done by
 * hashing the form elements before handing them to the user:
 * - hash the form (simple hash/hmac?)
 * - save the calculated has in a hidden form input
 * - when the form is posted recalculate the hash and compare to the previous one
 * - log the user id?
 *
 * @see http://www.php.net/manual/de/function.hash-hmac.php
 * @see http://php.net/manual/de/function.hash.php
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
