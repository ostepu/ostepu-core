<?php
include 'include/Authorization.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once 'include/Helpers.php';

if (isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
} else {
    $uid = 0;
}

if (isset($_GET['error'])) {
  $error = cleanInput($_GET['error']);
}

$sites = array('0' => 'Student.php',
               '1' => 'Tutor.php',
               '3' => 'Lecturer.php');

$statusName = array('0' => 'Student',
                    '1' => 'Tutor',
                    '3' => 'Dozent');

// load user data from the database
$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/user/user/{$uid}";
$user = http_get($databaseURI, false);
$user = json_decode($user, true);

if (is_null($user)) {
    $user = array();
}

if (isset($error) && $error == "403") {
  $notifications[] = MakeNotification("error", "403: Access Forbidden!!");
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user);
$h->bind(array("name" => "Übungsplattform",
               "notificationElements" => $notifications));

$pageData = array('uid' => $user['id'],
                  'courses' => $user['courses'],
                  'sites' => $sites,
                  'statusName' => $statusName);

// construct a login element
$courseSelect = Template::WithTemplateFile('include/CourseSelect/CourseSelect.template.html');
$courseSelect->bind($pageData);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $courseSelect);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

