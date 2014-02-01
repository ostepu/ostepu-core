<?php
/**
 * @file RightsManagement.php
 * Constructs the page that is used to grant and revoke a user's user-rights.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

// load RightsManagement data from GetSite
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


// construct a content element for taking away a user's user-rights
$revokeRights = Template::WithTemplateFile('include/RightsManagement/RevokeRights.template.html');

// construct a content element for granting user-rights
$grantRights = Template::WithTemplateFile('include/RightsManagement/GrantRights.template.html');

// construct a content element for creating an user
$createUser = Template::WithTemplateFile('include/RightsManagement/CreateUser.template.html');

/**
 * @todo combine the templates into a single file
 */

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $revokeRights, $grantRights, $createUser);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
