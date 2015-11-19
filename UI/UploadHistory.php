<?php
/**
 * @file UploadHistory.php
 * Constructs the page for managing the upload history of a user.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once dirname(__FILE__) . '/include/Boilerplate.php';

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::STUDENT, $cid, $uid, $globalUserData);
/// gehört SID zur CID ??? ///
$langTemplate='UploadHistory_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

if (isset($_POST['sheetID'])){
    $sid = cleanInput($_POST['sheetID']);
}
if (isset($sid)){
    $sheetID = $sid;
    $_POST['sheetID'] = $sid;
}
if (isset($_GET['action']) && !isset($_POST['action'])){
    $_POST['action'] = cleanInput($_GET['action']);
}

// updates the selectedSubmissions for the group
if (isset($_POST['updateSelectedSubmission'])) {
    $obj = json_decode($_POST['updateSelectedSubmission'],true); /// darf er das ??? ///
    
    // bool which is true if any error occured
    $RequestError = false;
    $message=null;
    updateSelectedSubmission($databaseURI,
                             $obj['leaderId'],
                             $obj['id'],
                             $obj['exerciseId'],
                             $message,
                             1);

    if ($message != "201") {
        $RequestError = true;
    }

    // shows notification
    if ($RequestError == false) {
        $uploadHistoryNotifications[] = MakeNotification("success", Language::Get('main','successSelectSubmission', $langTemplate));
    }else {
        $uploadHistoryNotifications[] = MakeNotification("error", Language::Get('main','errorSelectSubmission', $langTemplate));
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
    $uploadHistoryOptions_data['sortUsers'] = cleanInput($_POST['sortUsers']);

$user_course_data = $uploadHistoryOptions_data['user'];

if (isset($user_course_data['courses'][0]['status'])){   
    $courseStatus = $user_course_data['courses'][0]['status'];
} else
    $courseStatus =  -1;

if ($courseStatus==0)
    $_POST['userID'] = $uid;

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::STUDENT,
                              true);
                              
$isExpired=null;
$hasStarted=null;

if ($courseStatus<=0 /* PRIVILEGE_LEVEL::STUDENT */){
    // extract the correct sheet
    $sheet=null;
    foreach ($uploadHistoryOptions_data['sheets'] as $key => $value){
        if ($value['id'] == $sheetID){
            $sheet = $value;
            break;
        }
    }
    
    if ($sheet!==null && isset($sheet['endDate']) && isset($sheet['startDate'])){
        // bool if endDate of sheet is greater than the actual date
        $isExpired = date('U') > date('U', $sheet['endDate']); 

        // bool if startDate of sheet is greater than the actual date
        $hasStarted = date('U') > date('U', $sheet['startDate']);
        if ($isExpired){
            set_error(Language::Get('main','expiredExercisePerion', $langTemplate,array('endDate'=>date('d.m.Y  -  H:i', $sheet['endDate']))));
        } elseif (!$hasStarted){
            set_error(Language::Get('main','noStartedExercisePeriod', $langTemplate,array('startDate'=>date('d.m.Y  -  H:i', $sheet['startDate']))));
        }
        
    } else
        set_error(Language::Get('main','noExercisePeriod', $langTemplate));
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "notificationElements" => $notifications,
               "navigationElement" => $menu));

if (!isset($_POST['actionSortUsers']))
if (isset($_POST['action'])) {
    if ($_POST['action'] == "ShowUploadHistory") {
        if (isset($_POST['userID']) && (isset($_POST['sheetID']) || isset($sheetID))) {
            $uploadUserID = cleanInput($_POST['userID']);
            
            if (isset($_POST['sheetID']))
                $sheetID = cleanInput($_POST['sheetID']);

            // loads the upload history of the selected user (uploadUserID) in the 
            // selected course from GetSite
            $URL = $getSiteURI . "/uploadhistory/user/{$uid}/course/{$cid}/exercisesheet/{$sheetID}/uploaduser/{$uploadUserID}";
            $uploadHistory_data = http_get($URL, true);
            $uploadHistory_data = json_decode($uploadHistory_data, true);
            $uploadHistory_data['filesystemURI'] = $filesystemURI;
            $uploadHistory_data['hasStarted'] = $hasStarted;
            $uploadHistory_data['isExpired'] = $isExpired;
        }
    }
}

// construct a content element for the ability to look at the upload history of a student
if ($courseStatus>=1 /* PRIVILEGE_LEVEL::TUTOR */){
    $uploadHistorySettings = Template::WithTemplateFile('include/UploadHistory/UploadHistorySettings.template.html');
    $uploadHistorySettings->bind($uploadHistoryOptions_data);
}

$uploadHistory = Template::WithTemplateFile('include/UploadHistory/UploadHistory.template.html');
if (isset($uploadHistory_data))$uploadHistory->bind($uploadHistory_data);
if (isset($uploadHistoryNotifications))
    $uploadHistory->bind(array("UploadHistoryNotificationElements" => $uploadHistoryNotifications));

if ($courseStatus >= 1 /* PRIVILEGE_LEVEL::TUTOR */){
    $uploadHistoryGroup = Template::WithTemplateFile('include/UploadHistory/UploadHistoryGroup.template.html');
    if (isset($uploadHistory_data))$uploadHistoryGroup->bind($uploadHistory_data);
    if (isset($uploadHistoryGroupNotifications))
        $uploadHistoryGroup->bind(array("UploadHistoryNotificationElements" => $uploadHistoryGroupNotifications));
}

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, isset($uploadHistorySettings) ? $uploadHistorySettings : null, isset($uploadHistoryGroup) ? $uploadHistoryGroup : null, isset($uploadHistory) ? $uploadHistory : null);
if (isset($uploadHistorySettings)) $w->defineForm(basename(__FILE__)."?cid=".$cid, false, $uploadHistorySettings);
if (isset($uploadHistory))$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $uploadHistory);
$w->set_config_file('include/configs/config_default.json');
$w->show();

