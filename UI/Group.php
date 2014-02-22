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

// load mainSettings data from GetSite
$databaseURI = $getSiteURI . "/group/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
$group_data = http_get($databaseURI, true);
$group_data = json_decode($group_data, true);
$group_data['filesystemURI'] = $filesystemURI;

$user_course_data = $group_data['user'];

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "notificationElements" => $notifications));

// construct a content element for group information
$groupMembers = Template::WithTemplateFile('include/Group/GroupMembers.template.html');
$groupMembers->bind($group_data['group']);

// construct a content element for managing groups
$manageGroup = Template::WithTemplateFile('include/Group/ManageGroup.template.html');
$manageGroup->bind($group_data);

// construct a content element for creating groups
$createGroup = Template::WithTemplateFile('include/Group/InviteGroup.template.html');
$createGroup->bind($group_data);

// construct a content element for joining groups
$invitations = Template::WithTemplateFile('include/Group/Invitations.template.html');
$invitations->bind($group_data);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $groupMembers, $manageGroup, $createGroup, $invitations);
$w->set_config_file('include/configs/config_group.json');
$w->show();

?>
