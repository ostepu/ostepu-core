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
include_once dirname(__FILE__) . '/../Assistants/Validation/Validation.php'; 

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::STUDENT, $cid, $uid, $globalUserData);
/// gehört SID zur CID ??? ///
$langTemplate='UploadHistory_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); 

$postValidation = Validation::open($_POST, array('preRules'=>array()))
  ->addSet('action',
            ['sanitize',
             'set_default'=>'noAction',
             'satisfy_in_list'=>['noAction', 'ShowUploadHistory'],
             'on_error'=>['type'=>'error',
                          'text'=>Language::Get('main','invalidAction', $langTemplate)]])
  ->addSet('sheetID',
           ['sanitize',
            'valid_identifier',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidSheetID', $langTemplate)]])
  ->addSet('updateSelectedSubmission',
           ['to_array_from_json',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidUpdateSelectedSubmission', $langTemplate)]])
  ->addSet('sortUsers',
           ['sanitize',
            'satisfy_in_list'=>['lastName','firstName','userName'],
            'set_default'=>'lastName',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','errorSortUsers', $langTemplate)]])
  ->addSet('actionSortUsers',
           ['sanitize',
            'set_default'=>'noAction',
            'satisfy_in_list'=>['noAction', 'sort'],
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','errorActionSortUsers', $langTemplate)]]); 

$getValidation = Validation::open($_GET, array('preRules'=>array('sanitize')))
  ->addSet('action',
           ['set_default'=>'noAction',
            'satisfy_in_list'=>['noAction', 'ShowUploadHistory'],
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidAction', $langTemplate)]]); 

$postResults = $postValidation->validate();
$getResults = $getValidation->validate();
$notifications = array_merge($notifications,$postValidation->getPrintableNotifications('MakeNotification'));
$notifications = array_merge($notifications,$getValidation->getPrintableNotifications('MakeNotification'));
$postValidation->resetNotifications()->resetErrors();
$getValidation->resetNotifications()->resetErrors(); 

if (isset($postResults['sheetID'])){
    $sid = $postResults['sheetID'];
} 

$selectedUser = $uid;
$privileged = 0;
if (Authentication::checkRight(PRIVILEGE_LEVEL::LECTURER, $cid, $uid, $globalUserData)){
    if (isset($_POST['selectedUser'])){
        $URI = $serverURI . "/DB/DBUser/user/course/{$cid}/status/0";
        $courseUser = http_get($URI, true);
        $courseUser = User::decodeUser($courseUser); 

        $correct = false;
        foreach ($courseUser as $user){
            if ($user->getId() == $_POST['selectedUser']){
                $correct = true;
                break;
            }
        } 

        if ($correct){
            $_SESSION['selectedUser'] = $_POST['selectedUser'];
        }
    }
    $selectedUser = isset($_SESSION['selectedUser']) ? $_SESSION['selectedUser'] : $uid; 

    if (isset($_POST['privileged'])){
        $_SESSION['privileged'] = $_POST['privileged'];
    }
    $privileged = (isset($_SESSION['privileged']) ? $_SESSION['privileged'] : $privileged); 

    if (isset($_POST['selectedSheet'])){
        $URI = $serverURI . "/DB/DBExerciseSheet/exerciseSheet/course/{$cid}";
        $courseSheets = http_get($URI, true);
        $courseSheets = ExerciseSheet::decodeExerciseSheet($courseSheets); 

        $correct = false;
        foreach ($courseSheets as $sheet){
            if ($sheet->getId() == $_POST['selectedSheet']){
                $correct = true;
                break;
            }
        } 

        if ($correct){
            $sid = $_POST['selectedSheet'];
        }
    }
} 

if (isset($sid)){
    $sheetID = $sid;
    $postResults['sheetID'] = $sid;
}
if (isset($getResults['action']) && !isset($postResults['action'])){
    $postResults['action'] = $getResults['action'];
} 

// updates the selectedSubmissions for the group
if (isset($postResults['updateSelectedSubmission'])) {
    $obj = $postResults['updateSelectedSubmission']; /// darf er das ??? /// 

    // bool which is true if any error occured
    $RequestError = false;
    $message=null;
    updateSelectedSubmission($databaseURI,
                             $obj['leaderId'],
                             $obj['id'],
                             $obj['exerciseId'],
                             $message,
                             1); 

    if ($message !== 201) {
        $RequestError = true;
    } 

    // shows notification
    if ($RequestError == false) {
        $uploadHistoryNotifications[] = MakeNotification('success', Language::Get('main','successSelectSubmission', $langTemplate));
    }else {
        $uploadHistoryNotifications[] = MakeNotification('error', Language::Get('main','errorSelectSubmission', $langTemplate));
    }
} 

// loads data for the settings element
$URL = $getSiteURI . "/uploadhistoryoptions/user/{$selectedUser}/course/{$cid}";
$uploadHistoryOptions_data = http_get($URL, true);
$uploadHistoryOptions_data = json_decode($uploadHistoryOptions_data, true); 

$dataList = array();
$sortUsersValue = 'lastName';
if ($postValidation->isValid()){
    $sortUsersValue = $postResults['sortUsers'];
} 

foreach ($uploadHistoryOptions_data['users'] as $key => $user)
    $dataList[] = array('pos' => $key,'userName'=>$user['userName'],'lastName'=>$user['lastName'],'firstName'=>$user['firstName']);
$sortTypes = array('lastName','firstName','userName');
$dataList=LArraySorter::orderby($dataList, $sortUsersValue, SORT_ASC, $sortTypes[(array_search($sortUsersValue,$sortTypes)+1)%count($sortTypes)], SORT_ASC);
$tempData = array();
foreach($dataList as $data)
    $tempData[] = $uploadHistoryOptions_data['users'][$data['pos']];
$uploadHistoryOptions_data['users'] = $tempData; 

// adds the selected uploadUserID and sheetID
$uploadHistoryOptions_data['uploadUserID'] = isset($uploadUserID) ? $uploadUserID : '';
$uploadHistoryOptions_data['sheetID'] = isset($sheetID) ? $sheetID : ''; 

$uploadHistoryOptions_data['sortUsers'] = $sortUsersValue; 

$user_course_data = $uploadHistoryOptions_data['user']; 

if (isset($user_course_data['courses'][0]['status'])){   
    $courseStatus = $user_course_data['courses'][0]['status'];
} else
    $courseStatus =  -1; 

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::STUDENT,
                              true); 

$userNavigation = null;
if (isset($_SESSION['selectedUser'])){
    $URI = $serverURI . "/DB/DBUser/user/course/{$cid}/status/0";
    $courseUser = http_get($URI, true);
    $courseUser = User::decodeUser($courseUser);
    $URI = $serverURI . "/DB/DBExerciseSheet/exercisesheet/course/{$cid}/";
    $courseSheets = http_get($URI, true);
    $courseSheets = ExerciseSheet::decodeExerciseSheet($courseSheets);
    $courseSheets = array_reverse($courseSheets); 

    if (!$privileged){
        foreach ($courseSheets as $key => $sheet){
            if ($sheet->getEndDate()!==null && $sheet->getStartDate()!==null){
                // bool if endDate of sheet is greater than the actual date
                $isExpired = date('U') > date('U', $sheet->getEndDate());  

                // bool if startDate of sheet is greater than the actual date
                $hasStarted = date('U') > date('U', $sheet->getStartDate());
                if ($isExpired || !$hasStarted){
                   unset($courseSheets[$key]);
                }
            } else {
                unset($courseSheets[$key]);
            }
        }
    } 

    $userNavigation = MakeUserNavigationElement($globalUserData,$courseUser,$privileged,
                                                PRIVILEGE_LEVEL::LECTURER,$sid,$courseSheets);
} 

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
        if ($isExpired && !$privileged){
            set_error(Language::Get('main','expiredExercisePerion', $langTemplate,array('endDate'=>date('d.m.Y  -  H:i', $sheet['endDate']))));
        } elseif (!$hasStarted && !$privileged){
            set_error(Language::Get('main','noStartedExercisePeriod', $langTemplate,array('startDate'=>date('d.m.Y  -  H:i', $sheet['startDate']))));
        } 

    } else
        set_error(Language::Get('main','noExercisePeriod', $langTemplate));
} 

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array('name' => $user_course_data['courses'][0]['course']['name'],
               'notificationElements' => $notifications,
               'navigationElement' => $menu,
               'userNavigationElement' => $userNavigation)); 

if ($postValidation->isValid() && $postResults['actionSortUsers'] === 'noAction' && $postResults['action'] !== 'noAction') {
    if ($postResults['action'] === 'ShowUploadHistory') {
        $postShowUploadHistoryValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('userID',
                   ['valid_identifier',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidUserID', $langTemplate)]]);
        $foundValues = $postShowUploadHistoryValidation->validate();
        $notifications = array_merge($notifications,$postShowUploadHistoryValidation->getPrintableNotifications('MakeNotification'));
        $postShowUploadHistoryValidation->resetNotifications()->resetErrors(); 

        if ($postShowUploadHistoryValidation->isValid() && isset($postResults['sheetID'])) {
            if ($courseStatus==0){
                $foundValues['userID'] = $selectedUser;
            }
            $uploadUserID = $foundValues['userID']; 

            if (isset($postResults['sheetID']))
                $sheetID = $postResults['sheetID']; 

            // loads the upload history of the selected user (uploadUserID) in the 
            // selected course from GetSite
            $URL = $getSiteURI . "/uploadhistory/user/{$selectedUser}/course/{$cid}/exercisesheet/{$sheetID}/uploaduser/{$uploadUserID}";
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
    $uploadHistory->bind(array('UploadHistoryNotificationElements' => $uploadHistoryNotifications)); 

$uploadHistory->bind(array('privileged' => $privileged)); 

if ($courseStatus >= 1 /* PRIVILEGE_LEVEL::TUTOR */){
    $uploadHistoryGroup = Template::WithTemplateFile('include/UploadHistory/UploadHistoryGroup.template.html');
    if (isset($uploadHistory_data))$uploadHistoryGroup->bind($uploadHistory_data);
    if (isset($uploadHistoryGroupNotifications))
        $uploadHistoryGroup->bind(array('UploadHistoryNotificationElements' => $uploadHistoryGroupNotifications));
} 

if (isset($uploadHistoryGroup)){
    $uploadHistoryGroup->bind(array('privileged' => $privileged));
} 

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, isset($uploadHistorySettings) ? $uploadHistorySettings : null, isset($uploadHistoryGroup) ? $uploadHistoryGroup : null, isset($uploadHistory) ? $uploadHistory : null);
if (isset($uploadHistorySettings)) $w->defineForm(basename(__FILE__).'?cid='.$cid, false, $uploadHistorySettings);
if (isset($uploadHistory))$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $uploadHistory);
$w->set_config_file('include/configs/config_default.json');
$w->show(); 

