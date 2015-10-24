<?php
/**
 * @file CourseManagement.php
 * Constructs the page that is used to grant and revoke a user's user-rights
 * and to change basic course settings.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 *
 * @todo PUT Request to logic not to DB
 * @todo check Rights for whole page
 * @todo use logic Controller instead of database
 * @todo you have to confirm your action before deleting coursestatus
 */

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/LArraySorter.php';
include_once dirname(__FILE__) . '/include/FormEvaluator.php';

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::ADMIN, $cid, $uid, $globalUserData);

$langTemplate='CourseManagement_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

// load Plugins data from LogicController
$URI = $serverURI . "/logic/LExtension/link/extension";
$temp = http_get($URI, true);
$plugins_data=array();
$plugins_data['plugins'] = json_decode($temp, true);

$URI = $serverURI . "/logic/LExtension/link/course/{$cid}/extension";
$temp = http_get($URI, true);
$temp = json_decode($temp, true);
$installedPlugins = array();
foreach ($plugins_data['plugins'] as &$plugin){
    foreach ($temp as &$installed){
        if ($plugin['target'] === $installed['target']){
            $installedPlugins[] = $installed['target'];
            unset($installed);
            $plugin['isInstalled'] = 1;
            break;
        }
    }  
}

if (!isset($_POST['actionSortUsers']) && isset($_POST['action'])) {
    if ($_POST['action'] == "EditExternalId") {
        $externalId = (isset($_POST['externalId']) ? cleanInput($_POST['externalId']) : array());
        if (!is_array($externalId)) $externalId = array($externalId);
        
        $RequestError = false;
        if (count($externalId) == 0){
            $RequestError = true;
            $editExternalIdNotifications[] = MakeNotification("error", Language::Get('main','noSelectedAlias', $langTemplate));
        }

        if (!$RequestError){
            foreach ($externalId as $key => $ext){
        
                // delete externalId
                $URI = $serverURI . "/DB/DBExternalId/externalid/externalid/{$ext}";
                http_delete($URI, true, $messageNewAc);
                if ($messageNewAc != "201") {
                    $RequestError = true;
                    $editExternalIdNotifications[] = MakeNotification("error", Language::Get('main','errorRemoveAlias', $langTemplate));
                    break;
                }
            }
        }
 
        // show notification
        if ($RequestError == false) {
            $editExternalIdNotifications[] = MakeNotification("success", Language::Get('main','successEditCourse', $langTemplate));
        }
    
    } elseif ($_POST['action'] == "AddExternalId") {
        $externalId = (isset($_POST['externalId']) ? cleanInput($_POST['externalId']) : '');
        $externalType = (isset($_POST['externalType']) ? cleanInput($_POST['externalType']) : '');
        $externalTypeName = (isset($_POST['externalTypeName']) ? cleanInput($_POST['externalTypeName']) : '');
        $RequestError = false;
        if (strlen($externalId) == 0){
            $RequestError = true;
            $addExternalIdNotifications[] = MakeNotification("error", Language::Get('main','missingAliasName', $langTemplate));
        }
        
        if (strlen($externalType) == 0){
            $RequestError = true;
            $addExternalIdNotifications[] = MakeNotification("error", Language::Get('main','missingAliasType', $langTemplate));
        }
        
        if ($externalType == 2 && strlen($externalTypeName) == 0){
            $RequestError = true;
            $addExternalIdNotifications[] = MakeNotification("error", Language::Get('main','missingAliasPrefix', $langTemplate));
        }
        
        if (!$RequestError){
            if ($externalType==1)
                $externalTypeName = 'S';
        
            // add externalId
            $URI = $serverURI . "/DB/DBExternalId/externalid";
            $ext = ExternalId::createExternalId($externalTypeName.'_'.$externalId,$cid);
            http_post_data($URI, ExternalId::encodeExternalId($ext), true, $messageNewAc);
            if ($messageNewAc != "201") {
                $RequestError = true;
                $addExternalIdNotifications[] = MakeNotification("error", Language::Get('main','errorCreateAlias', $langTemplate));
            }
        }
 
        // show notification
        if ($RequestError == false) {
            $addExternalIdNotifications[] = MakeNotification("success", Language::Get('main','successEditCourse', $langTemplate));
        }
    
    } elseif ($_POST['action'] == "Plugins") {
        
        $plugins = cleanInput($_POST['plugins']);
        
        // bool which is true if any error occured
        $RequestError=false;
        
        // which need to be deleted
        if (!empty($plugins)) {
            $etDelete = array_diff($installedPlugins, $plugins);
        } else {
            $etDelete = $installedPlugins;
        }
        
        // which need to be installed
        if (!empty($installedPlugins)) {
            if (!is_array($plugins)) $plugins = array($plugins);
            $etCreate = array_diff($plugins, $installedPlugins);
        } else {
            $etCreate = $plugins;
        }

        // install Plugins
        if (isset($etCreate) && !empty($etCreate)){
            foreach ($etCreate as $plugin2) {
                if ($plugin2 === '') continue;
                $URI = $serverURI . "/logic/LExtension/link/course/{$cid}/extension/{$plugin2}";
                http_post_data($URI, '', true, $messageNewAc);
                if ($messageNewAc != "201") {
                    $RequestError = true;
                    break;
                }
            }
        }
     
        // uninstall Plugins
        if (isset($etDelete) && !empty($etDelete)){
            foreach ($etDelete as $plugin3) {
                if ($plugin3 === '') continue;
                $URI = $serverURI . "/logic/LExtension/link/course/{$cid}/extension/{$plugin3}";
                http_delete($URI, true, $messageNewAc);
                if ($messageNewAc != "201") {
                    $RequestError = true;
                    break;
                }
            }
        }
        
        // load Plugins data from LogicController
        $URI = $serverURI . "/logic/LExtension/link/extension";
        $temp = http_get($URI, true);
        $plugins_data=array();
        $plugins_data['plugins'] = json_decode($temp, true);

        $URI = $serverURI . "/logic/LExtension/link/course/{$cid}/extension";
        $temp = http_get($URI, true);
        $temp = json_decode($temp, true);
        $installedPlugins = array();
        foreach ($plugins_data['plugins'] as &$plugin){
            foreach ($temp as &$installed){
                if ($plugin['target'] === $installed['target']){
                    $installedPlugins[] = $installed['target'];
                    unset($installed);
                    $plugin['isInstalled'] = 1;
                    break;
                }
            }  
        }

        // show notification
        if ($RequestError == false) {
            $pluginsNotifications[] = MakeNotification("success", Language::Get('main','successEditExtensions', $langTemplate));
        }
        else {
            $pluginsNotifications[] = MakeNotification("error", Language::Get('main','errorEditExtensions', $langTemplate));
        }
    
    } elseif ($_POST['action'] == "CourseSettings") {
        // check if POST data is send
        if(isset($_POST['courseName']) && isset($_POST['semester']) && isset($_POST['defaultGroupSize'])) {
            
            ######################
            ### begin settings ###
            ######################
            #region settings
            
            $RequestError = false;
            
            if (isset($_POST['setting'])){
                foreach ($_POST['setting'] as $key => $params){
                    if (!isset($params['type']) || !isset($params['value'])){
                        $courseSettingsNotifications[] = MakeNotification("error", Language::Get('main','faultyTransmission', $langTemplate));
                        continue;
                    }
                        
                    $value = $params['value'];
                    $type = $params['type'];
                    if (strtoupper($type) === 'BOOL'){
                    	if ($value != '1' && $value != '0'){
	                        $courseSettingsNotifications[] = MakeNotification("error", Language::Get('main','containsUnsupportedChars', $langTemplate));
	                        continue;
                    	}
                    } elseif (strtoupper($type) === 'INT'){
                    	if (trim($value) != ''){
	                    	$pregRes = @preg_match("%^([0-9]+)$%", $value);
	                        if (!$pregRes){
		                        $courseSettingsNotifications[] = MakeNotification("error", Language::Get('main','containsUnsupportedChars', $langTemplate));
		                        continue;
	                        }
                    	}
                    } elseif (strtoupper($type) === 'STRING'){
                        // nothing
                    } elseif (strtoupper($type) === 'DATE'){
                        if (trim($value) == '')
                            $value = 0;
                        $value = strtotime(str_replace(" - ", " ", $value));                        
                    } else {
                        $courseSettingsNotifications[] = MakeNotification("error", Language::Get('main','invalidType', $langTemplate));
                        continue;
                    }
                    
                    // create new setting and edit existing one
                    $newSetting = Setting::encodeSetting(Setting::createSetting($key,null,$value));
                    $URI = $databaseURI . "/setting/setting/{$key}";
                    $courseManagement_data = http_put_data($URI, $newSetting, true, $message);

                    // show notification
                    if ($message == "201" && $RequestError == false) {
                        // nothing
                    }
                    else {
                        $courseSettingsNotifications[] = MakeNotification("error", Language::Get('main','invalidType', $langTemplate));
                        $RequestError = true;
                        break;
                    }
                }
                
                if (!$RequestError){
                    $courseSettingsNotifications[] = MakeNotification("success", Language::Get('main','successSaveSettings', $langTemplate));
                }
            }
            
            ####################
            ### end settings ###
            ####################
            #endregion settings
            
            #############################
            ### begin course_settings ###
            #############################
            #region course_settings
            
            // bool which is true if any error occured
            $RequestError = false;

            // extracts the php POST data
            $courseName = cleanInput((isset($_POST['courseName']) ? $_POST['courseName'] : '' ));
            $semester = cleanInput((isset($_POST['semester']) ? $_POST['semester'] : '' ));
            $defaultGroupSize = cleanInput((isset($_POST['defaultGroupSize']) ? $_POST['defaultGroupSize'] : '' ));
            $selectedExerciseTypes = cleanInput((isset($_POST['exerciseTypes']) ? $_POST['exerciseTypes'] : '' ));

            // loads ApprovalConditions from database
            $URI = $databaseURI . "/approvalcondition/course/{$cid}";
            $approvalCondition_data = http_get($URI, true);
            $approvalCondition_data = json_decode($approvalCondition_data, true);

            // determines all possible exercise types of the course
            foreach($approvalCondition_data as $approvalCondition) {
                $currentExerciseTypes[] = $approvalCondition['exerciseTypeId'];
                $currentExerciseTypesByApprovalId[$approvalCondition['exerciseTypeId']] = $approvalCondition['id'];
            }

            // exercise types which already exist in the database and need to be deleted
            if(isset($currentExerciseTypes)){
                if (!empty($selectedExerciseTypes)) {
                    if (!is_array($currentExerciseTypes)) $currentExerciseTypes = array($currentExerciseTypes);
                    $etDelete = array_diff($currentExerciseTypes, $selectedExerciseTypes);
                } else {
                    $etDelete = $currentExerciseTypes;
                }
            }

            // exercises types which don't exist in the database and need to be created
            if (isset($currentExerciseTypes) && !empty($currentExerciseTypes)) {
                if (!is_array($selectedExerciseTypes)) $selectedExerciseTypes = array($selectedExerciseTypes);
                $etCreate = array_diff($selectedExerciseTypes, $currentExerciseTypes);
            } else {
                $etCreate = $selectedExerciseTypes;
            }

            if (isset($etDelete)){
                if ($etDelete == null) $etDelete = array();
                if (!is_array($etDelete)) $etDelete = array($etDelete);
                // deletes approvalConditions
                if (isset($currentExerciseTypesByApprovalId)){
                    foreach($etDelete as $exerciseType2) {
                        if ($exerciseType2==='')continue;
                        $URI = $databaseURI . "/approvalcondition/" . $currentExerciseTypesByApprovalId[$exerciseType2];
                        http_delete($URI, true, $message);

                        if ($message != "201") {
                            $RequestError = true;
                        }
                    }
                }
            }

            if (isset($etCreate)){
                if ($etCreate == null) $etCreate = array();
                if (!is_array($etCreate)) $etCreate = array($etCreate);
                // adds approvalConditions
                foreach($etCreate as $exerciseType3) {
                    if ($exerciseType3==='')continue;
                    $newApprovalConditionSettings = ApprovalCondition::encodeApprovalCondition(
                        ApprovalCondition::createApprovalCondition(null, $cid, $exerciseType3, 0));
                    $URI = $databaseURI . "/approvalcondition";
                    http_post_data($URI, $newApprovalConditionSettings, true, $message);

                    if ($message != "201") {
                        $RequestError = true;
                    }
                }
            }

            // create new course and edit existing one
            $newCourseSettings = Course::encodeCourse(Course::createCourse($cid,$courseName,$semester,$defaultGroupSize));
            $URI = $databaseURI . "/course/course/{$cid}";
            $courseManagement_data = http_put_data($URI, $newCourseSettings, true, $message);

            // show notification
            if ($message == "201" && $RequestError == false) {
                $courseSettingsNotifications[] = MakeNotification("success", Language::Get('main','successEditCourse', $langTemplate));
            }
            else {
                $courseSettingsNotifications[] = MakeNotification("error", Language::Get('main','errorEditCourse', $langTemplate));
            }
            
            ###########################
            ### end course_settings ###
            ###########################
            #endregion course_settings
            
        }
        else {
            $courseSettingsNotifications[] = MakeNotification("error", Language::Get('main','missingFields', $langTemplate));
        }
    } elseif ($_POST['action'] == "AddExerciseType") {
        // check if POST data is send
        if(isset($_POST['exerciseTypeName'])) {
            // clean Input
            $exerciseTypeName = cleanInput($_POST['exerciseTypeName']);

            // create new exerciseType
            $data = ExerciseType::encodeExerciseType(ExerciseType::createExerciseType(null, $exerciseTypeName));

            $url = $databaseURI . "/exercisetype";
            http_post_data($url, $data, true, $message);

            // show notification
            if ($message == "201") {
                $addExerciseTypeNotifications[] = MakeNotification("success", Language::Get('main','successCreateType', $langTemplate));
            } else {
                $addExerciseTypeNotifications[] = MakeNotification("error", Language::Get('main','errorSaveSettings', $langTemplate));
            }
        } else {
            $addExerciseTypeNotifications[] = MakeNotification("error", Language::Get('main','missingFields', $langTemplate));
        }
    } elseif ($_POST['action'] == "EditExerciseType") {
        // check if POST data is send
        if(isset($_POST['exerciseTypeID']) && isset($_POST['exerciseTypeName'])) {
            // clean Input
            $exerciseTypeID = cleanInput($_POST['exerciseTypeID']);
            $exerciseTypeName = cleanInput($_POST['exerciseTypeName']);

            // create new exerciseType
            $data = ExerciseType::encodeExerciseType(ExerciseType::createExerciseType($exerciseTypeID, $exerciseTypeName));

            $url = $databaseURI . "/exercisetype/" . $exerciseTypeID;
            http_put_data($url, $data, true, $message);

            // show notification
            if ($message == "201") {
                $editExerciseTypeNotifications[] = MakeNotification("success", Language::Get('main','successSetType', $langTemplate));
            } else {
                $editExerciseTypeNotifications[] = MakeNotification("error", Language::Get('main','errorSaveSettings', $langTemplate));
            }
        } else {
            $editExerciseTypeNotifications[] = MakeNotification("error", Language::Get('main','missingFields', $langTemplate));
        }
    } elseif ($_POST['action'] == "GrantRights") {
        // check if POST data is send
        if(isset($_POST['userID']) && isset($_POST['Rights'])) {
            // clean Input
            $userID = cleanInput($_POST['userID']);
            $Rights = cleanInput($_POST['Rights']);

            // validate POST data
            if (is_numeric($userID) == true && is_numeric($Rights) && $Rights >= 0 && $Rights < 3) {
                // create new coursestatus and edit existing one
                $data = User::encodeUser(User::createCourseStatus($userID, $cid, $Rights));

                $url = $databaseURI . "/coursestatus/course/{$cid}/user/{$userID}";
                http_put_data($url, $data, true, $message);

                // show notification
                if ($message == "201") {
                    $grantRightsNotifications[] = MakeNotification("success", Language::Get('main','successSetCourseStatus', $langTemplate));
                }
            } else {
                // otherwise show conflict page
                set_error("409");
                exit();
            }
        } else {
            $grantRightsNotifications[] = MakeNotification("error", Language::Get('main','noSelectedUser', $langTemplate));
        }
    } elseif ($_POST['action'] == "RevokeRights") {
        // check if POST data is send
        if(isset($_POST['userID'])) {
            // clean Input
            $userID = cleanInput($_POST['userID']);

            // validate POST data
            if (is_numeric($userID) == true) {
                // delete coursestatus
                $url = $databaseURI . "/coursestatus/course/{$cid}/user/{$userID}";
                http_delete($url, true, $message);

                // show notification
                if ($message == "201") {
                    $revokeRightsNotifications[] = MakeNotification("success", Language::Get('main','successRemoveUser', $langTemplate));
                }
            } else {
                // otherwise show conflict page
                set_error("409");
                exit();
            }
        } else {
            $revokeRightsNotifications[] = MakeNotification("error", Language::Get('main','noSelectedUser', $langTemplate));
        }
    } elseif ($_POST['action'] == "AddUser") {

        $f = new FormEvaluator($_POST);

        $f->checkStringForKey('userName',
                              FormEvaluator::REQUIRED,
                              'warning',
                              Language::Get('main','invalidUserName', $langTemplate),
                              array('min' => 1));

        $f->checkIntegerForKey('rights',
                               FormEvaluator::REQUIRED,
                               'warning',
                               Language::Get('main','invalidCourseStatus', $langTemplate),
                               array('min' => 0, 'max' => 2));

        if ($f->evaluate(true)) {
            $foundValues = $f->foundValues;

            $userName = $foundValues['userName'];
            $rights = $foundValues['rights'];

            $URL = $databaseURI . '/user/user/' . $userName;
            $user = http_get($URL, true);
            $user = json_decode($user, true);

            if (isset($user['id'])){
                $userId = $user['id'];

                $newUser = User::createCourseStatus($userId, $cid, $rights);
                $newUser = User::encodeUser($newUser);

                $URL = $databaseURI . '/coursestatus';
                http_post_data($URL, $newUser, true, $message);

                if ($message == "201") {
                    $addUserNotifications[] = MakeNotification('success',Language::Get('main','successAddUser', $langTemplate));
                } else {
                    $addUserNotifications[] = MakeNotification('error',Language::Get('main','errorAddUser', $langTemplate));
                }
            } else {
                $addUserNotifications[] = MakeNotification('error',
                                                    Language::Get('main','invalidUserId', $langTemplate));
            }                                        
        } else {
            if (!isset($addUserNotifications))
                $addUserNotifications = array();
            $addUserNotifications = $addUserNotifications + $f->notifications;
        }
    } else {
        $notifications[] = MakeNotification('error',Language::Get('main','invalidAction', $langTemplate));
    }
}

// load CourseManagement data from GetSite
$URI = $getSiteURI . "/coursemanagement/user/{$uid}/course/{$cid}";
$courseManagement_data = http_get($URI, true);
$courseManagement_data = json_decode($courseManagement_data, true);

$dataList = array();
foreach ($courseManagement_data['users'] as $key => $user)
    $dataList[] = array('pos' => $key,'userName'=>$user['userName'],'lastName'=>$user['lastName'],'firstName'=>$user['firstName']);
$sortTypes = array('lastName','firstName','userName');
if (!isset($_POST['sortUsers'])) $_POST['sortUsers'] = null;
$_POST['sortUsers'] = (in_array($_POST['sortUsers'],$sortTypes) ? $_POST['sortUsers'] : $sortTypes[0]);
$sortTypes = array('lastName','firstName','userName');
$dataList=LArraySorter::orderby($dataList, $_POST['sortUsers'], SORT_ASC, $sortTypes[(array_search($_POST['sortUsers'],$sortTypes)+1)%count($sortTypes)], SORT_ASC);
$tempData = array();
foreach($dataList as $data)
    $tempData[] = $courseManagement_data['users'][$data['pos']];
$courseManagement_data['users'] = $tempData;

if (isset($_POST['sortUsers'])) {
    $courseManagement_data['sortUsers'] = $_POST['sortUsers'];
}

$user_course_data = $courseManagement_data['user'];

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::ADMIN,
                              true);

// load externalId data
$URI = $serverURI . "/DB/DBExternalId/externalid/course/{$cid}";
$externalid_data = array();
$externalid_data['externalId'] = http_get($URI, true);
$externalid_data['externalId'] = json_decode($externalid_data['externalId'], true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "notificationElements" => $notifications,
               "navigationElement" => $menu));

// construct a content element for changing course settings
$courseSettings = Template::WithTemplateFile('include/CourseManagement/CourseSettings.template.html');
$courseSettings->bind($courseManagement_data);
if (isset($courseSettingsNotifications))
    $courseSettings->bind(array("CourseSettingsNotificationElements" => $courseSettingsNotifications));

// construct a content element for plugins
$plugins = Template::WithTemplateFile('include/CourseManagement/Plugins.template.html');
$plugins->bind($plugins_data);
if (isset($pluginsNotifications))
    $plugins->bind(array("PluginsNotificationElements" => $pluginsNotifications));

// construct a content element for adding exercise types
$addExerciseType = Template::WithTemplateFile('include/CourseManagement/AddExerciseType.template.html');
if (isset($addExerciseTypeNotifications))
    $addExerciseType->bind(array("AddExerciseTypeNotificationElements" => $addExerciseTypeNotifications));

// construct a content element for editing exercise types
$editExerciseType = Template::WithTemplateFile('include/CourseManagement/EditExerciseType.template.html');
$editExerciseType->bind($courseManagement_data);
if (isset($editExerciseTypeNotifications))
    $editExerciseType->bind(array("EditExerciseTypeNotificationElements" => $editExerciseTypeNotifications));

// construct a content element for granting user-rights
$grantRights = Template::WithTemplateFile('include/CourseManagement/GrantRights.template.html');
$grantRights->bind($courseManagement_data);
if (isset($grantRightsNotifications))
    $grantRights->bind(array("GrantRightsNotificationElements" => $grantRightsNotifications));

// construct a content element for taking away a user's user-rights
$revokeRights = Template::WithTemplateFile('include/CourseManagement/RevokeRights.template.html');
$revokeRights->bind($courseManagement_data);
if (isset($revokeRightsNotifications))
    $revokeRights->bind(array("RevokeRightsNotificationElements" => $revokeRightsNotifications));

$editExternalId = Template::WithTemplateFile('include/CourseManagement/EditExternalId.template.html');
$editExternalId->bind($externalid_data);
if (isset($editExternalIdNotifications))
    $editExternalId->bind(array("EditExternalIdNotificationElements" => $editExternalIdNotifications));

$addExternalId = Template::WithTemplateFile('include/CourseManagement/AddExternalId.template.html');
if (isset($addExternalIdNotifications))
    $addExternalId->bind(array("AddExternalIdNotificationElements" => $addExternalIdNotifications));
    
// construct a content element for adding users
$addUser = Template::WithTemplateFile('include/CourseManagement/AddUser.template.html');
if (isset($addUserNotifications))
    $addUser->bind(array("AddUserNotificationElements" => $addUserNotifications));

/**
 * @todo combine the templates into a single file
 */

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $courseSettings, $plugins, $addExerciseType, $editExerciseType, $grantRights, $revokeRights, $addUser, $editExternalId, $addExternalId);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $courseSettings);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $plugins);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $addExerciseType);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $editExerciseType);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $grantRights);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $revokeRights);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $addUser);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $editExternalId);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $addExternalId);
$w->set_config_file('include/configs/config_course_management.json');
$w->show();

