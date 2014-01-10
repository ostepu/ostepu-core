<?php
/**
 * @file Upload.php
 * Shows a form to upload solutions.
 */

include_once 'include/Header/Header.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once '../Assistants/Logger.php';
?>

<?php
    if (isset($_POST['action'])) {
        Logger::Log($_POST, LogLevel::INFO);
        /**
         * @todo actually upload the data
         */

        // redirect, so the user can reload the page without a warning
        header("Location: Upload.php");
    } else {
        Logger::Log("No Sheet Data", LogLevel::INFO);
    }
?>

<?php
/**
 * @todo Read parameters from the GET Request and request data from the database
 */

// construct a new header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "211221492");
$h->setBackURL('index.php');

/*
 * if (is_student($user))
 */
$h->setPoints(75);

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
                                        array('exerciseID' => 1
                                              )
                                        )
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
