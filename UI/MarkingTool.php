<?php
/**
 * @file MarkingTool.php
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

// load MarkingTool data from GetSite
$URI = $getSiteURI . "/markingtool/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
$markingTool_data = http_get($URI, true);
$markingTool_data = json_decode($markingTool_data, true);
$markingTool_data['filesystemURI'] = $filesystemURI;

$user_course_data = $markingTool_data['user'];


// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Admin.php?cid={$cid}",
               "notificationElements" => $notifications,
               "navigationElement" => $menu));


$searchSettings = Template::WithTemplateFile('include/MarkingTool/SearchSettings.template.html');

$markingElement = Template::WithTemplateFile('include/MarkingTool/MarkingElement.template.html');
$markingElement->bind($markingTool_data);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $searchSettings, $markingElement);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
