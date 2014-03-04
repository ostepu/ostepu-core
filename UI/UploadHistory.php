<?php
/**
 * @file UploadHistory.php
 * Constructs the page for managing the upload history of a user.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

if (isset($_POST['action'])) {
    if ($_POST['action'] == "ShowUploadHistory") {
        if (isset($_POST['userID']) && isset($_POST['sheetID'])) {
            $uploadUserID = cleanInput($_POST['userID']);
            $sheetID = cleanInput($_POST['sheetID']);

            // loads the upload history of the selected user (uploadUserID) in the 
            // selected course from GetSite
            $URL = $getSiteURI . "/uploadhistory/user/{$uid}/course/{$cid}/exercisesheet/{$sheetID}/uploaduser/{$uploadUserID}";
            $uploadHistory_data = http_get($URL, false);
            $uploadHistory_data = json_decode($uploadHistory_data, true);
            $uploadHistory_data['filesystemURI'] = $filesystemURI;
        }
    }
}

// loads data for the settings element
$URL = $getSiteURI . "/uploadhistoryoptions/user/{$uid}/course/{$cid}";
$uploadHistoryOptions_data = http_get($URL, false);
$uploadHistoryOptions_data = json_decode($uploadHistoryOptions_data, true);

// adds the selected uploadUserID and sheetID
$uploadHistoryOptions_data['uploadUserID'] = $uploadUserID;
$uploadHistoryOptions_data['sheetID'] = $sheetID;

$user_course_data = $uploadHistoryOptions_data['user'];

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::ADMIN,
                              true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "notificationElements" => $notifications,
               "navigationElement" => $menu));


// construct a content element for the ability to look at the upload history of a student
$uploadHistorySettings = Template::WithTemplateFile('include/UploadHistory/UploadHistorySettings.template.html');
$uploadHistorySettings->bind($uploadHistoryOptions_data);

$uploadHistory = Template::WithTemplateFile('include/UploadHistory/UploadHistory.template.html');
$uploadHistory->bind($uploadHistory_data);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $uploadHistorySettings, $uploadHistory);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $uploadHistorySettings);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
