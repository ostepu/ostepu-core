<?php
/**
 * @file Admin.php
 * Constructs the page that is displayed to an admin.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

// load GetSite data for Admin.php
$databaseURI = $getSiteURI . "/admin/user/{$uid}/course/{$cid}";
$admin_data = http_get($databaseURI);
$admin_data = json_decode($admin_data, true);

$user_course_data = $admin_data['user'];

/**
 * @todo check rights
 */

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "notificationElements" => $notifications));


$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetLecturer.template.html');
$t->bind($admin_data);

$w = new HTMLWrapper($h, $t);
$w->set_config_file('include/configs/config_admin_lecturer.json');
$w->show();

?>