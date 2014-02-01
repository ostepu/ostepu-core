<?php
/**
 * @file UserManagement.php
 * Constructs the page that is used to create and delete users.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

/**
 * @todo use userManagement GetSite data instead of rightsManagement data.
 */
$databaseURI = $getSiteURI . "/rightsmanagement/user/{$uid}/course/{$cid}";
$rightsManagement_data = http_get($databaseURI);
$rightsManagement_data = json_decode($rightsManagement_data, true);

$user_course_data = $rightsManagement_data;

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Admin.php?cid={$cid}",
               "notificationElements" => $notifications));

// construct a content element for changing course settings
$createUser = Template::WithTemplateFile('include/UserManagement/CreateUser.template.html');

/**
 * @todo construct a "delete user" page.
 */ 

/**
 * @todo combine the templates into a single file
 */

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $courseSettings, $revokeRights, $grantRights, $createUser);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
