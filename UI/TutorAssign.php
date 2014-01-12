<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';
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
$h->bind(array("backTitle" => "zur Veranstaltung",
               "backURL" => "Tutor.php?cid={$cid}&uid={$uid}",
               "notificationElements" => $notifications));

$data = file_get_contents("http://localhost/Uebungsplattform/UI/Data/TutorAssignData");
$data = json_decode($data, true);

$tutorAssignment = $data;

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

