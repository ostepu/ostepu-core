<?php
/**
 * @file Group.php
 * Constructs the page that is displayed to a student, when managing a group.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 *
 * @todo load data from the database and fill the page
 */

include_once 'include/Boilerplate.php';

if (isset($_POST['action'])) {
    Logger::Log($_POST, LogLevel::INFO);
    header("Location: Group.php");
} else {
    Logger::Log("No Group Data", LogLevel::INFO);
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind(array("backTitle" => "zur Veranstaltung",
               "backURL" => "Student.php?cid={$cid}",
               "navigationElement" => $menu,
               "notificationElements" => $notifications));

// construct a content element for managing groups
$manageGroup = Template::WithTemplateFile('include/Group/ManageGroup.template.html');
$manageGroup->bind(array());

// construct a content element for creating groups
$createGroup = Template::WithTemplateFile('include/Group/InviteGroup.template.html');
$createGroup->bind(array());

// construct a content element for joining groups
$invitations = Template::WithTemplateFile('include/Group/Invitations.template.html');
$invitations->bind(array());

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $manageGroup, $createGroup, $invitations);
$w->set_config_file('include/configs/config_group.json');
$w->show();

?>
