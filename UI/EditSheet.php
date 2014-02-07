<?php
/**
 * @file EditSheet.php
 * Constructs a page where a user can edit an existing exercise sheet.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

// load GetSite data for Admin.php
/*
 * @todo Use EditSheet data from GetSite
 */
$databaseURI = $getSiteURI . "/admin/user/{$uid}/course/{$cid}";
$sheet_data = http_get($databaseURI, false);
$sheet_data = json_decode($sheet_data, true);

$user_course_data = $sheet_data['user'];

$menu = MakeNavigationElementForCourseStatus($user_course_data['courses']);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "notificationElements" => $notifications,
               "navigationElement" => $menu));


$sheetSettings = Template::WithTemplateFile('include/EditSheet/SheetSettings.template.html');
$sheetSettings->bind($sheet_data);

/*
 * @todo Manage exercises.
 */

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $sheetSettings);
$w->set_config_file('include/configs/config_createSheet.json');
$w->show();

?>
