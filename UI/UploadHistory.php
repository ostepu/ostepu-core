<?php
/**
 * @file Upload.php
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

if (isset($_POST['sheetID'])) {
    /**
     * @todo load data for the slected user
     */
    Logger::Log($_POST, LogLevel::INFO);
} else {
    Logger::Log("No Data", LogLevel::INFO);
}

// set $sid = 0 if there is no sid in the URL
if (!isset($_GET['sid'])) {
    $sid = 0;
}

// load uploadHistory data from GetSite
$URL = $getSiteURI . "/uploadhistory/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
$uploadHistory_data = http_get($URL, false);
$uploadHistory_data = json_decode($uploadHistory_data, true);
$uploadHistory_data['filesystemURI'] = $filesystemURI;

$user_course_data = $uploadHistory_data['user'];

$menu = MakeNavigationElementForCourseStatus($user_course_data['courses']);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Admin.php?cid={$cid}",
               "notificationElements" => $notifications,
               "navigationElement" => $menu));

// construct a content element for the ability to look at the upload history of a student

/**
 * @todo Load uploadHistory data depending on the settings in uploadHistorySettings (student and sheet).
 */
$uploadHistorySettings = Template::WithTemplateFile('include/UploadHistory/UploadHistorySettings.template.html');

$uploadHistory = Template::WithTemplateFile('include/UploadHistory/UploadHistory.template.html');
$uploadHistory->bind($uploadHistory_data);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $uploadHistorySettings, $uploadHistory);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
