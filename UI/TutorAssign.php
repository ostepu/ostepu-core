<?php
include 'include/Authorization.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once '../Assistants/Logger.php';
?>

<?php
if (isset($_POST['action'])) {
    Logger::Log($_POST, LogLevel::INFO);
    /**
     * @todo assign tutors based on the selected method
     */

    // redirect, so the user can reload the page without a warning
    header("Location: TutorAssign.php");
} else {
    Logger::Log("No Assignment Data", LogLevel::INFO);
}
?>

<?php
if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    die('no course id!\n');
}

if (isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
} else {
    die('no user id!\n');
}

if (isset($_GET['sid'])) {
    $sid = $_GET['sid'];
} else {
    die('no sheet id!\n');
}

/**
 * @todo Combine user course and status request into GetSite request
 */
// load user and course data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/coursestatus/course/{$cid}/user/{$uid}";
$user_course_data = http_get($databaseURI, true, $message);

$user_course_data = json_decode($user_course_data, true);

Logger::Log("Course request done");

// check userrights for course
Authentication::checkRights(1, $cid, $uid, $user_course_data);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Tutor.php?cid={$cid}",
               "notificationElements" => $notifications));

$data = http_get("http://localhost/uebungsplattform/logic/GetSite/tutorassignment/course/{$cid}/exercisesheet/{$sid}", true);
$data = json_decode($data, true);

$tutorAssignment = array("tutorAssignments" => $data);

// construct a content element for managing groups
$assignAutomatically = Template::WithTemplateFile('include/TutorAssign/AssignAutomatically.template.html');
$assignAutomatically->bind($tutorAssignment);

// construct a content element for creating groups
$assignManually = Template::WithTemplateFile('include/TutorAssign/AssignManually.template.html');
$assignManually->bind($tutorAssignment);

// construct a content element for joining groups
$assignCancel = Template::WithTemplateFile('include/TutorAssign/AssignRemove.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $assignAutomatically, $assignManually, $assignCancel);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

