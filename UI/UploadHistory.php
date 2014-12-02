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

// updates the selectedSubmissions for the group
if (isset($_POST['updateSelectedSubmission'])) {
    $obj = json_decode($_POST['updateSelectedSubmission'],true);
    
    // bool which is true if any error occured
    $RequestError = false;
    $message=null;
    updateSelectedSubmission($databaseURI,
                             $obj['leaderId'],
                             $obj['id'],
                             $obj['exerciseId'],
                             $message);

    if ($message != "201") {
        $RequestError = true;
    }

    // shows notification
    if ($RequestError == false) {
        $uploadHistoryNotifications[] = MakeNotification("success", "Die Einsendung wurde ausgewählt.");
    }
    else {
        $uploadHistoryNotifications[] = MakeNotification("error", "Beim Speichern ist ein Fehler aufgetreten!");
    }
}
    
if (!isset($_POST['actionSortUsers']))
if (isset($_POST['action'])) {
    if ($_POST['action'] == "ShowUploadHistory") {
        if (isset($_POST['userID']) && isset($_POST['sheetID'])) {
            $uploadUserID = cleanInput($_POST['userID']);
            $sheetID = cleanInput($_POST['sheetID']);

            // loads the upload history of the selected user (uploadUserID) in the 
            // selected course from GetSite
            $URL = $getSiteURI . "/uploadhistory/user/{$uid}/course/{$cid}/exercisesheet/{$sheetID}/uploaduser/{$uploadUserID}";
            $uploadHistory_data = http_get($URL, true);
            $uploadHistory_data = json_decode($uploadHistory_data, true);
            $uploadHistory_data['filesystemURI'] = $filesystemURI;
        }
    }
}

// loads data for the settings element
$URL = $getSiteURI . "/uploadhistoryoptions/user/{$uid}/course/{$cid}";
$uploadHistoryOptions_data = http_get($URL, true);
$uploadHistoryOptions_data = json_decode($uploadHistoryOptions_data, true);

$dataList = array();
foreach ($uploadHistoryOptions_data['users'] as $key => $user)
    $dataList[] = array('pos' => $key,'userName'=>$user['userName'],'lastName'=>$user['lastName'],'firstName'=>$user['firstName']);
$sortTypes = array('lastName','firstName','userName');
if (!isset($_POST['sortUsers'])) $_POST['sortUsers'] = null;
$_POST['sortUsers'] = (in_array($_POST['sortUsers'],$sortTypes) ? $_POST['sortUsers'] : $sortTypes[0]);
$sortTypes = array('lastName','firstName','userName');
$dataList=LArraySorter::orderby($dataList, $_POST['sortUsers'], SORT_ASC, $sortTypes[(array_search($_POST['sortUsers'],$sortTypes)+1)%count($sortTypes)], SORT_ASC);
$tempData = array();
foreach($dataList as $data)
    $tempData[] = $uploadHistoryOptions_data['users'][$data['pos']];
$uploadHistoryOptions_data['users'] = $tempData;
    
// adds the selected uploadUserID and sheetID
$uploadHistoryOptions_data['uploadUserID'] = isset($uploadUserID) ? $uploadUserID : '';
$uploadHistoryOptions_data['sheetID'] = isset($sheetID) ? $sheetID : '';

if (isset($_POST['sortUsers']))
    $uploadHistoryOptions_data['sortUsers'] = $_POST['sortUsers'];

$user_course_data = $uploadHistoryOptions_data['user'];
Authentication::checkRights(PRIVILEGE_LEVEL::ADMIN, $cid, $uid, $user_course_data);
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
if (isset($uploadHistory_data))$uploadHistory->bind($uploadHistory_data);
if (isset($uploadHistoryNotifications))
    $uploadHistory->bind(array("UploadHistoryNotificationElements" => $uploadHistoryNotifications));

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $uploadHistorySettings, $uploadHistory);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $uploadHistorySettings);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $uploadHistory);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
