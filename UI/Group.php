<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once 'include/Helpers.php';
?>

<?php
if (isset($_POST['action'])) {
    Logger::Log($_POST, LogLevel::INFO);
    header("Location: Group.php");
} else {
    Logger::Log("No Group Data", LogLevel::INFO);
}
?>

<?php
if (isset($_GET['sid'])) {
    $sid = $_GET['sid'];
} else {
    die('no sheet id!\n');
}

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

$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/user/user/{$uid}";
$user = http_get($databaseURI);
$user = json_decode($user, true);

$databaseURI = "http://141.48.9.92/uebungsplattform/DB/DBControl/course/course/{$cid}";
$course = http_get($databaseURI);
$course = json_decode($course, true)[0];

// construct a new header
$h = new Header($course['name'],
                "",
                $user['firstName'] . ' ' . $user['lastName'],
                $user['userName']);

$h->setBackURL("Student.php?cid={$cid}&uid={$uid}")
->setBackTitle("zur Veranstaltung");

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

