<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';

// construct a new Header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "211221492", 
                "75%");

$h->setBackURL("Student.php")
->setBackTitle("zur Veranstaltung");

$data = file_get_contents("http://localhost/Uebungsplattform/UI/Data/GroupData");
$data = json_decode($data, true);

$group = $data['group'];
$groupInfo = $data['groupInfo'];
unset($data['group']);
unset($data['groupInfo']);
$invitation = $data;

// construct a content element for managing groups
$manageGroup = Template::WithTemplateFile('include/Group/ManageGroup.template.json');
$manageGroup->bind($group);

// construct a content element for creating groups
$createGroup = Template::WithTemplateFile('include/Group/InviteGroup.template.json');
$createGroup->bind($groupInfo);

// construct a content element for joining groups
$invitations = Template::WithTemplateFile('include/Group/Invitations.template.json');
$invitations->bind($invitation);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $manageGroup, $createGroup, $invitations);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

