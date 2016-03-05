<?php
/**
 * @file CourseManagement.php
 * Constructs the page that is used to grant and revoke a user's user-rights
 * and to change basic course settings.
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2014
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2013-2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2013-2014
 *
 * @todo PUT Request to logic not to DB
 * @todo use logic Controller instead of database
 * @todo you have to confirm your action before deleting coursestatus
 */

ob_start();

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/LArraySorter.php';
include_once dirname(__FILE__) . '/include/FormEvaluator.php';
include_once dirname(__FILE__) . '/../Assistants/vendor/Validation/Validation.php';

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::ADMIN, $cid, $uid, $globalUserData);

$langTemplate='CourseManagement_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

// load Plugins data from LogicController
$URI = $serverURI . '/logic/LExtension/link/extension';
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

$postValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('action',
           ['set_default'=>'noAction',
            'satisfy_in_list'=>['noAction', 'EditExternalId', 'AddExternalId', 'Plugins', 'CourseSettings', 'AddExerciseType', 'EditExerciseType', 'GrantRights', 'RevokeRights', 'AddUser'],
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidAction', $langTemplate)]])
  ->addSet('sortUsers',
           ['satisfy_in_list'=>['lastName','firstName','userName'],
            'set_default'=>'lastName',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidSortUser', $langTemplate)]])
  ->addSet('actionSortUsers',
           ['set_default'=>'noAction',
            'satisfy_in_list'=>['noAction', 'sort'],
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidActionSortUsers', $langTemplate)]]);

$postResults = $postValidation->validate();
$notifications = array_merge($notifications,$postValidation->getPrintableNotifications('MakeNotification'));
$postValidation->resetNotifications()->resetErrors();

if ($postValidation->isValid() && $postResults['actionSortUsers'] === 'noAction' && $postResults['action'] !== 'noAction' ) {
    if ($postResults['action'] === 'EditExternalId') {
        $editExternalIdNotifications = array();

        $postEditExternalIdValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('externalId',
                   ['satisfy_exists',
                    'is_array',
                    'satisfy_not_empty',
                    'on_error'=>['type'=>'warning',
                                 'text'=>Language::Get('main','noSelectedAlias', $langTemplate)]])
          ->addSet('externalId',
                   ['perform_this_array'=>[[['key_all'],
                                            ['satisfy_regex'=>'%^([a-zA-Z0-9_]+)$%']]],
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidExternalId', $langTemplate)]]);

        $foundValues = $postEditExternalIdValidation->validate();
        $editExternalIdNotifications = array_merge($editExternalIdNotifications,$postEditExternalIdValidation->getPrintableNotifications('MakeNotification'));
        $postEditExternalIdValidation->resetNotifications()->resetErrors();

        if ($postEditExternalIdValidation->isValid()){

            $RequestError = false;
            foreach ($foundValues['externalId'] as $key => $ext){ /// !!! gehört diese ID zur cid ??? ///

                // delete externalId
                $URI = $serverURI . "/DB/DBExternalId/externalid/externalid/{$ext}";
                http_delete($URI, true, $messageNewAc);
                if ($messageNewAc !== 201) {
                    $RequestError = true;
                    $editExternalIdNotifications[] = MakeNotification('error', Language::Get('main','errorRemoveAlias', $langTemplate));
                    break;
                }
            }

             // show notification
            if ($RequestError === false) {
                $editExternalIdNotifications[] = MakeNotification('success', Language::Get('main','successEditCourse', $langTemplate));
            }
        }

    }

    if ($postResults['action'] === 'AddExternalId') {
        $addExternalIdNotifications = array();

        $postAddExternalIdValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('externalId',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_alpha_numeric',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','missingAliasName', $langTemplate)]])
          ->addSet('externalType',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','missingAliasType', $langTemplate)]])
          ->addSet('externalType',
                   ['to_integer',
                    'satisfy_in_list' => [1,2],
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidExternalType', $langTemplate)]])
          ->addSet('externalTypeName',
                   ['satisfy_exact_len'=>1,
                    'valid_alpha',
                    'to_upper',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidExternalTypeName', $langTemplate)]]);

        $foundValues = $postAddExternalIdValidation->validate();
        $addExternalIdNotifications = array_merge($addExternalIdNotifications,$postAddExternalIdValidation->getPrintableNotifications('MakeNotification'));
        $postAddExternalIdValidation->resetNotifications()->resetErrors();

        if ($postAddExternalIdValidation->isValid()){          
            if ($foundValues['externalType'] === 1){
                $foundValues['externalTypeName'] = 'S';
            }

            // add externalId
            $URI = $serverURI . '/DB/DBExternalId/externalid';
            $ext = ExternalId::createExternalId($foundValues['externalTypeName'].'_'.$foundValues['externalId'],$cid);
            http_post_data($URI, ExternalId::encodeExternalId($ext), true, $messageNewAc);
            if ($messageNewAc !== 201) {
                $addExternalIdNotifications[] = MakeNotification('error', Language::Get('main','errorCreateAlias', $langTemplate));
            } else {
                $addExternalIdNotifications[] = MakeNotification('success', Language::Get('main','successEditCourse', $langTemplate));
            }

        }
    }

    if ($postResults['action'] === 'Plugins') {
        $pluginsNotifications = array();

        $postPluginsValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('plugins',
                   ['set_default'=>array(),
                    'is_array',
                    'perform_this_foreach'=>[['key',
                                             ['valid_integer']],
                                            ['elem',
                                             ['valid_identifier']]],
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidExtensionId', $langTemplate)]]);

        $foundValues = $postPluginsValidation->validate();
        $pluginsNotifications = array_merge($pluginsNotifications,$postPluginsValidation->getPrintableNotifications('MakeNotification'));
        $postPluginsValidation->resetNotifications()->resetErrors();

        if ($postPluginsValidation->isValid()){

            // bool which is true if any error occured
            $RequestError=false;
            $plugins = $foundValues['plugins'];

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
            if (!$RequestError && isset($etCreate) && !empty($etCreate)){
                foreach ($etCreate as $plugin2) {
                    if ($plugin2 === '') continue;
                    $URI = $serverURI . "/logic/LExtension/link/course/{$cid}/extension/{$plugin2}";
                    http_post_data($URI, '', true, $messageNewAc);
                    if ($messageNewAc !== 201) {
                        $RequestError = true;
                        break;
                    }
                }
            }

            // uninstall Plugins
            if (!$RequestError && isset($etDelete) && !empty($etDelete)){
                foreach ($etDelete as $plugin3) {
                    if ($plugin3 === '') continue;
                    $URI = $serverURI . "/logic/LExtension/link/course/{$cid}/extension/{$plugin3}";
                    http_delete($URI, true, $messageNewAc);
                    if ($messageNewAc !== 201) {
                        $RequestError = true;
                        break;
                    }
                }
            }

            // load Plugins data from LogicController
            $URI = $serverURI . '/logic/LExtension/link/extension';
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
            if ($RequestError === false) {
                $pluginsNotifications[] = MakeNotification('success', Language::Get('main','successEditExtensions', $langTemplate));
            }
            else {
                $pluginsNotifications[] = MakeNotification('error', Language::Get('main','errorEditExtensions', $langTemplate));
            }
        }
    }

    if ($postResults['action'] === 'CourseSettings') {
        $courseSettingsNotifications = array();

        $postCourseSettingsValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('courseName',
                   ['satisfy_exists',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidCourseName', $langTemplate)]])
          ->addSet('semester',
                   ['satisfy_exists',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidSemester', $langTemplate)]])
          ->addSet('defaultGroupSize',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidGroupSize', $langTemplate)]])
          ->addSet('defaultGroupSize',
                   ['valid_integer',
                    'satisfy_min_numeric' => 0,
                    'to_integer',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidGroupSize', $langTemplate)]])
          ->addSet('exerciseTypes',
                   ['is_array',
                    'perform_this_array'=>[[['key_all'],
                                       ['valid_identifier']]],
                    'set_default'=>array(),
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidExerciseTypes', $langTemplate)]])
          ->addSet('setting',
                   ['is_array',
                    'set_default'=>array(),
                    'perform_this_foreach'=>[['key',
                                              ['valid_identifier']],
                                             ['elem',
                                              ['is_array',
                                               'satisfy_not_empty',
                                               'perform_this_array'=>[['type',
                                                                       ['satisfy_exists',
                                                                        'satisfy_not_empty',
                                                                        'to_upper',
                                                                        'satisfy_in_list'=>array('INT','BOOL','STRING','DATE'),
                                                                        'on_error'=>['type'=>'error',
                                                                                     'text'=>Language::Get('main','invalidType', $langTemplate)]]],
                                                                      ['value',
                                                                       ['satisfy_exists',
                                                                        '',
                                                                        'on_error'=>['type'=>'error',
                                                                                     'text'=>Language::Get('main','faultyTransmission', $langTemplate)]]]]]]],
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidSettings', $langTemplate)]]);

        $foundValues = $postCourseSettingsValidation->validate();
        $courseSettingsNotifications = array_merge($courseSettingsNotifications,$postCourseSettingsValidation->getPrintableNotifications('MakeNotification'));
        $postCourseSettingsValidation->resetNotifications()->resetErrors();

        if($postCourseSettingsValidation->isValid()) {

            ######################
            ### begin settings ###
            ######################
            #region settings

            $RequestError = false;

            if (empty($foundValues['setting']) === false){
                foreach ($foundValues['setting'] as $key => $params){

                    $value = $params['value'];
                    $type = $params['type'];
                    if ($type === 'BOOL'){
                    	if ($value != '1' && $value != '0'){
	                        $courseSettingsNotifications[] = MakeNotification('error', Language::Get('main','containsUnsupportedChars', $langTemplate));
	                        continue;
                    	}
                    } elseif ($type === 'INT'){
                    	if (trim($value) != ''){
	                    	$pregRes = @preg_match("%^([0-9]+)$%", $value);
	                        if (!$pregRes){
		                        $courseSettingsNotifications[] = MakeNotification('error', Language::Get('main','containsUnsupportedChars', $langTemplate));
		                        continue;
	                        }
                    	}
                    } elseif ($type === 'STRING'){
                        // nothing
                    } elseif ($type === 'DATE'){
                        if (trim($value) == '')
                            $value = 0;
                        $value = strtotime(str_replace(" - ", " ", $value));                      
                    }

                    // create new setting and edit existing one
                    $newSetting = Setting::encodeSetting(Setting::createSetting($key,null,$value));
                    $URI = $databaseURI . "/setting/setting/{$key}";
                    $courseManagement_data = http_put_data($URI, $newSetting, true, $message);

                    // show notification
                    if ($message === 201 && $RequestError == false) {
                        // nothing
                    }
                    else {
                        $courseSettingsNotifications[] = MakeNotification('error', Language::Get('main','errorSaveSettings', $langTemplate));
                        $RequestError = true;
                        break;
                    }
                }

                if ($RequestError === false){
                    $courseSettingsNotifications[] = MakeNotification('success', Language::Get('main','successSaveSettings', $langTemplate));
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

            $selectedExerciseTypes = $foundValues['exerciseTypes'];

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

            if ($RequestError === false && isset($etDelete)){
                if ($etDelete === null) $etDelete = array();
                if (!is_array($etDelete)) $etDelete = array($etDelete);
                // deletes approvalConditions
                if (isset($currentExerciseTypesByApprovalId)){
                    foreach($etDelete as $exerciseType2) {
                        if ($exerciseType2 === '')continue;
                        $URI = $databaseURI . '/approvalcondition/' . $currentExerciseTypesByApprovalId[$exerciseType2];
                        http_delete($URI, true, $message);

                        if ($message !== 201) {
                            $RequestError = true;
                            $courseSettingsNotifications[] = MakeNotification('error', Language::Get('main','errorDeleteApprovalCondition', $langTemplate));
                            break;
                        }
                    }
                }
            }

            if ($RequestError === false && isset($etCreate)){
                if ($etCreate === null) $etCreate = array();
                if (!is_array($etCreate)) $etCreate = array($etCreate);
                // adds approvalConditions
                foreach($etCreate as $exerciseType3) {
                    if ($exerciseType3 === '')continue;
                    $newApprovalConditionSettings = ApprovalCondition::encodeApprovalCondition(
                        ApprovalCondition::createApprovalCondition(null, $cid, $exerciseType3, 0));
                    $URI = $databaseURI . '/approvalcondition';
                    http_post_data($URI, $newApprovalConditionSettings, true, $message);

                    if ($message !== 201) {
                        $RequestError = true;
                        $courseSettingsNotifications[] = MakeNotification('error', Language::Get('main','errorAddApprovalCondition', $langTemplate));
                        break;
                    }
                }
            }

            if ($RequestError === false){
                // create new course and edit existing one
                $newCourseSettings = Course::encodeCourse(Course::createCourse($cid,$foundValues['courseName'],$foundValues['semester'],$foundValues['defaultGroupSize']));
                $URI = $databaseURI . "/course/course/{$cid}";
                $courseManagement_data = http_put_data($URI, $newCourseSettings, true, $message);

                // show notification
                if ($message === 201) {
                    $courseSettingsNotifications[] = MakeNotification('success', Language::Get('main','successEditCourse', $langTemplate));
                }
                else {
                    $courseSettingsNotifications[] = MakeNotification('error', Language::Get('main','errorEditCourse', $langTemplate)); 
                    $RequestError = true;
                }
            }

            ###########################
            ### end course_settings ###
            ###########################
            #endregion course_settings

        }
    }

    if ($postResults['action'] === 'AddExerciseType') {
        $addExerciseTypeNotifications = array();

        $postAddExerciseTypeValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('exerciseTypeName',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_alpha_numeric',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidExerciseTypeName', $langTemplate)]]);

        $foundValues = $postAddExerciseTypeValidation->validate();
        $addExerciseTypeNotifications = array_merge($addExerciseTypeNotifications,$postAddExerciseTypeValidation->getPrintableNotifications('MakeNotification'));
        $postAddExerciseTypeValidation->resetNotifications()->resetErrors();

        // check if POST data is send
        if($postAddExerciseTypeValidation->isValid()) {
            // create new exerciseType
            $data = ExerciseType::encodeExerciseType(ExerciseType::createExerciseType(null, $foundValues['exerciseTypeName']));

            $url = $databaseURI . '/exercisetype';
            http_post_data($url, $data, true, $message);

            // show notification
            if ($message === 201) {
                $addExerciseTypeNotifications[] = MakeNotification('success', Language::Get('main','successCreateType', $langTemplate));
            } else {
                $addExerciseTypeNotifications[] = MakeNotification('error', Language::Get('main','errorSaveSettings', $langTemplate));
            }
        }
    }

    if ($postResults['action'] === 'EditExerciseType') {
        $editExerciseTypeNotifications = array();

        $postEditExerciseTypeValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('exerciseTypeName',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_alpha_numeric',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidExerciseTypeName', $langTemplate)]])
          ->addSet('exerciseTypeID',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_identifier',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidExerciseType', $langTemplate)]]);

        $foundValues = $postEditExerciseTypeValidation->validate();
        $editExerciseTypeNotifications = array_merge($editExerciseTypeNotifications,$postEditExerciseTypeValidation->getPrintableNotifications('MakeNotification'));
        $postEditExerciseTypeValidation->resetNotifications()->resetErrors();

        // check if POST data is send
        if($postEditExerciseTypeValidation->Valid()) {

            // create new exerciseType
            $data = ExerciseType::encodeExerciseType(ExerciseType::createExerciseType($foundValues['exerciseTypeID'], $foundValues['exerciseTypeName']));

            $url = $databaseURI . '/exercisetype/' . $foundValues['exerciseTypeID'];
            http_put_data($url, $data, true, $message);

            // show notification
            if ($message === 201) {
                $editExerciseTypeNotifications[] = MakeNotification('success', Language::Get('main','successSetType', $langTemplate));
            } else {
                $editExerciseTypeNotifications[] = MakeNotification('error', Language::Get('main','errorSaveSettings', $langTemplate));
            }
        }
    }

    if ($postResults['action'] === 'GrantRights') {
        $grantRightsNotifications = array();

        $postGrantRightsValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('Rights',
                   ['satisfy_exists',
                    'satisfy_min_numeric'=>0,
                    'satisfy_max_numeric'=>3,
                    'to_integer',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidCourseStatus', $langTemplate)]])
          ->addSet('userID',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_identifier',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','noSelectedUser', $langTemplate)]]);

        $foundValues = $postGrantRightsValidation->validate();
        $grantRightsNotifications = array_merge($grantRightsNotifications,$postGrantRightsValidation->getPrintableNotifications('MakeNotification'));
        $postGrantRightsValidation->resetNotifications()->resetErrors();

        // check if POST data is send
        if($postGrantRightsValidation->isValid()) {
            // create new coursestatus and edit existing one
            $data = User::encodeUser(User::createCourseStatus($foundValues['userID'], $cid, $foundValues['Rights']));

            $url = $databaseURI . '/coursestatus/course/'.$cid.'/user/'.$foundValues['userID'];
            http_put_data($url, $data, true, $message);

            // show notification
            if ($message === 201) {
                $grantRightsNotifications[] = MakeNotification('success', Language::Get('main','successSetCourseStatus', $langTemplate));
            } else {
               $grantRightsNotifications[] = MakeNotification('error', Language::Get('main','errorSetCourseStatus', $langTemplate));
            }
        }
    }

    if ($postResults['action'] === 'RevokeRights') {
        $revokeRightsNotifications = array();

        $postRevokeRightsValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('userID',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_identifier',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','noSelectedUser', $langTemplate)]]);

        $foundValues = $postRevokeRightsValidation->validate();
        $revokeRightsNotifications = array_merge($revokeRightsNotifications,$postRevokeRightsValidation->getPrintableNotifications('MakeNotification'));
        $postRevokeRightsValidation->resetNotifications()->resetErrors();

        // check if POST data is send
        if($postRevokeRightsValidation->isValid()) {
            // delete coursestatus
            $url = $databaseURI . '/coursestatus/course/'.$cid.'/user/'.$foundValues['userID'];
            http_delete($url, true, $message);

            // show notification
            if ($message === 201) {
                $revokeRightsNotifications[] = MakeNotification('success', Language::Get('main','successRemoveUser', $langTemplate));
            } else {
                $revokeRightsNotifications[] = MakeNotification('error', Language::Get('main','errorRemoveUser', $langTemplate));
            }
        }
    }

    if ($postResults['action'] === 'AddUser') {
        $addUserNotifications = array();

        $postAddUserValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('rights',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'satisfy_min_numeric'=>0,
                    'satisfy_max_numeric'=>3,
                    'to_integer',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidCourseStatus', $langTemplate)]])
          ->addSet('userName',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_userName',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','noSelectedUser', $langTemplate)]]);

        $foundValues = $postAddUserValidation->validate();
        $addUserNotifications = array_merge($addUserNotifications,$postAddUserValidation->getPrintableNotifications('MakeNotification'));
        $postAddUserValidation->resetNotifications()->resetErrors();

        if ($postAddUserValidation->isValid()) {
            $URL = $databaseURI . '/user/user/' . $foundValues['userName'];
            $user = http_get($URL, true);
            $user = json_decode($user, true);

            if (isset($user['id'])){
                $userId = $user['id'];

                $newUser = User::createCourseStatus($userId, $cid, $foundValues['rights']);
                $newUser = User::encodeUser($newUser);

                $URL = $databaseURI . '/coursestatus';
                http_post_data($URL, $newUser, true, $message);

                if ($message === 201) {
                    $addUserNotifications[] = MakeNotification('success',Language::Get('main','successAddUser', $langTemplate));
                } else {
                    $addUserNotifications[] = MakeNotification('error',Language::Get('main','errorAddUser', $langTemplate));
                }
            } else {
                $addUserNotifications[] = MakeNotification('error',
                                                    Language::Get('main','invalidUserId', $langTemplate));
            }                                      
        }
    }
}

// load CourseManagement data from GetSite
$URI = $getSiteURI . "/coursemanagement/user/{$uid}/course/{$cid}";
$courseManagement_data = http_get($URI, true);
$courseManagement_data = json_decode($courseManagement_data, true);

$dataList = array();
$sortUsersValue = 'lastName';
if ($postValidation->isValid()){
    $sortUsersValue = $postResults['sortUsers'];
}

foreach ($courseManagement_data['users'] as $key => $user)
    $dataList[] = array('pos' => $key,'userName'=>$user['userName'],'lastName'=>$user['lastName'],'firstName'=>$user['firstName']);
$sortTypes = array('lastName','firstName','userName');
$dataList=LArraySorter::orderby($dataList, $sortUsersValue, SORT_ASC, $sortTypes[(array_search($sortUsersValue,$sortTypes)+1)%count($sortTypes)], SORT_ASC);
$tempData = array();
foreach($dataList as $data)
    $tempData[] = $courseManagement_data['users'][$data['pos']];
$courseManagement_data['users'] = $tempData;
unset($tempData);

$courseManagement_data['sortUsers'] = $sortUsersValue;

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
$h->bind(array('name' => $user_course_data['courses'][0]['course']['name'],
               'notificationElements' => $notifications,
               'navigationElement' => $menu));

// construct a content element for changing course settings
$courseSettings = Template::WithTemplateFile('include/CourseManagement/CourseSettings.template.html');
$courseSettings->bind($courseManagement_data);
if (isset($courseSettingsNotifications))
    $courseSettings->bind(array('CourseSettingsNotificationElements' => $courseSettingsNotifications));

// construct a content element for plugins
$plugins = Template::WithTemplateFile('include/CourseManagement/Plugins.template.html');
$plugins->bind($plugins_data);
if (isset($pluginsNotifications))
    $plugins->bind(array('PluginsNotificationElements' => $pluginsNotifications));

// construct a content element for adding exercise types
$addExerciseType = Template::WithTemplateFile('include/CourseManagement/AddExerciseType.template.html');
if (isset($addExerciseTypeNotifications))
    $addExerciseType->bind(array('AddExerciseTypeNotificationElements' => $addExerciseTypeNotifications));

// construct a content element for editing exercise types
$editExerciseType = Template::WithTemplateFile('include/CourseManagement/EditExerciseType.template.html');
$editExerciseType->bind($courseManagement_data);
if (isset($editExerciseTypeNotifications))
    $editExerciseType->bind(array('EditExerciseTypeNotificationElements' => $editExerciseTypeNotifications));

// construct a content element for granting user-rights
$grantRights = Template::WithTemplateFile('include/CourseManagement/GrantRights.template.html');
$grantRights->bind($courseManagement_data);
if (isset($grantRightsNotifications))
    $grantRights->bind(array('GrantRightsNotificationElements' => $grantRightsNotifications));

// construct a content element for taking away a user's user-rights
$revokeRights = Template::WithTemplateFile('include/CourseManagement/RevokeRights.template.html');
$revokeRights->bind($courseManagement_data);
if (isset($revokeRightsNotifications))
    $revokeRights->bind(array('RevokeRightsNotificationElements' => $revokeRightsNotifications));

$editExternalId = Template::WithTemplateFile('include/CourseManagement/EditExternalId.template.html');
$editExternalId->bind($externalid_data);
if (isset($editExternalIdNotifications))
    $editExternalId->bind(array('EditExternalIdNotificationElements' => $editExternalIdNotifications));

$addExternalId = Template::WithTemplateFile('include/CourseManagement/AddExternalId.template.html');
if (isset($addExternalIdNotifications))
    $addExternalId->bind(array('AddExternalIdNotificationElements' => $addExternalIdNotifications));

// construct a content element for adding users
$addUser = Template::WithTemplateFile('include/CourseManagement/AddUser.template.html');
if (isset($addUserNotifications))
    $addUser->bind(array('AddUserNotificationElements' => $addUserNotifications));

// construct a content element for course notifications
$courseNotifications = Template::WithTemplateFile('include/CourseManagement/CourseNotifications.template.html');
$courseNotifications->bind($courseManagement_data);
if (isset($courseNotificationsNotifications))
    $courseNotifications->bind(array('GrantRightsNotificationElements' => $courseNotificationsNotifications));

/**
 * @todo combine the templates into a single file
 */

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $courseSettings, $plugins, $courseNotifications, $addExerciseType, $editExerciseType, $grantRights, $revokeRights, $addUser, $editExternalId, $addExternalId);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $courseSettings);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $plugins);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $courseNotifications);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $addExerciseType);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $editExerciseType);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $grantRights);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $revokeRights);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $addUser);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $editExternalId);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $addExternalId);
$w->set_config_file('include/configs/config_course_management.json');
$w->show();

ob_end_flush();