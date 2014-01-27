<?php
/**
 * @file Group.php
 * Constructs the page that is displayed to a student, when managing a group.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Authorization.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once '../Assistants/Logger.php';
include_once 'include/Helpers.php';

if (isset($_POST['action'])) {
    Logger::Log($_POST, LogLevel::INFO);
    header("Location: Group.php");
} else {
    Logger::Log("No Group Data", LogLevel::INFO);
}

if (isset($_GET['sid'])) {
    $sid = $_GET['sid'];
} else {
    Logger::Log('no sheet id!\n');
}

if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    Logger::Log('no course id!\n');
}

if (isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
} else {
    Logger::Log('no user id!\n');
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
               "backURL" => "Student.php?cid={$cid}",
               "navigationElement" => $menu,
               "notificationElements" => $notifications));

// load exercise sheet data from the database
$databaseURL = "http://141.48.9.92/uebungsplattform/DB/DBGroup/group/user/{$uid}/exercisesheet/{$sid}";

$data = http_get($databaseURL);

if ($data) {
    $data = json_decode($data, true);
    $group = $data[0];
} else {
    $group = array("leader" => $user);
}

$groupInfo = array();
$invitation = array();

// construct a content element for managing groups
$manageGroup = Template::WithTemplateFile('include/Group/ManageGroup.template.html');
$manageGroup->bind($group);

// construct a content element for creating groups
$createGroup = Template::WithTemplateFile('include/Group/InviteGroup.template.html');
$createGroup->bind($groupInfo);

// construct a content element for joining groups
$invitations = Template::WithTemplateFile('include/Group/Invitations.template.html');
$invitations->bind($invitation);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $manageGroup, $createGroup, $invitations);
$w->set_config_file('include/configs/config_group.json');
$w->show();

?>
