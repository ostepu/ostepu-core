<?php
/**
 * @file MainSettings.php
 * Constructs the page that is used to create and delete users and
 * to create new courses.
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2014
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2014
 *
 * @todo POST Request to logic instead of DB
 * @todo create a navigation bar for super admins
 * @todo unset $_POST on success
 */

ob_start();

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/vendor/Validation/Validation.php';

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::SUPER_ADMIN, null, $uid, $globalUserData);

$langTemplate='MainSettings_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

// load Plugins data from LogicController
$URI = $serverURI . "/logic/LExtension/link/extension";
$temp = http_get($URI, true);
$plugins_data = json_decode($temp, true);

$postValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('action',
           ['set_default'=>'noAction',
            'satisfy_in_list'=>['noAction', 'CreateCourse', 'SetAdmin', 'CreateUser', 'DeleteUser', 'ModifyUserAccess', ''],
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidAction', $langTemplate)]]);
$postResults = $postValidation->validate();
$notifications = array_merge($notifications,$postValidation->getPrintableNotifications('MakeNotification'));
$postValidation->resetNotifications()->resetErrors();

if ($postValidation->isValid() && $postResults['action'] !== 'noAction') {
    // creates a new course
    if ($postResults['action'] === 'CreateCourse') {
        $createCourseNotifications = array();
        
        $postCreateCourseValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('courseName',
                   ['satisfy_exists',
                    'valid_alpha_numeric',
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
                                 'text'=>Language::Get('main','invalidExerciseType', $langTemplate)]])
          ->addSet('plugins',
                   ['is_array',
                    'set_default'=>null,
                    'perform_this_array'=>[[['key_all'],
                                      ['valid_identifier']]],
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidExtensionId', $langTemplate)]]);

        $foundValues = $postCreateCourseValidation->validate();
        $createCourseNotifications = array_merge($createCourseNotifications,$postCreateCourseValidation->getPrintableNotifications('MakeNotification'));
        $postCreateCourseValidation->resetNotifications()->resetErrors();

        if($postCreateCourseValidation->isValid()) {
            // bool which is true if any error occured
            $RequestError = false;

            // extracts the php POST data
            $courseName = $foundValues['courseName'];
            $semester = $foundValues['semester'];
            $defaultGroupSize = $foundValues['defaultGroupSize'];
            $plugins = $foundValues['plugins'];
            $exerciseTypes = $foundValues['exerciseTypes'];

            // creates a new course
            if ($RequestError === false){
                $newCourse = Course::createCourse(null, $courseName, $semester, $defaultGroupSize);
                $newCourseSettings = Course::encodeCourse($newCourse);
                $URI = $serverURI . '/logic/LCourse/course';
                $newCourse = http_post_data($URI, $newCourseSettings, true, $messageNewCourse);

                if ($messageNewCourse !== 201){
                    $RequestError = true;
                }
            }

            if ($RequestError === false){
                // extracts the id of the new course
                $newCourse = json_decode($newCourse, true);
                if (isset($newCourse['id'])){
                    $newCourseId = $newCourse['id'];
                } else {
                    $RequestError = true;
                }
            }

            // creates a new approvalCondition for every selected exerciseType
            if ($RequestError === false && isset($exerciseTypes) && !empty($exerciseTypes)){
                foreach ($exerciseTypes as $exerciseType) {
                    $newApprovalCondition = ApprovalCondition::createApprovalCondition(null,
                                                                                       $newCourseId,
                                                                                       $exerciseType,
                                                                                       0);
                    $newApprovalConditionSettings = ApprovalCondition::encodeApprovalCondition($newApprovalCondition);
                    $URI = $databaseURI . '/approvalcondition';
                    http_post_data($URI, $newApprovalConditionSettings, true, $messageNewAc);

                    if ($messageNewAc !== 201) {
                        $RequestError = true;
                        break;
                    }

                }
            }

            // create Plugins
            if ($RequestError === false && isset($plugins) && !empty($plugins)){
                foreach ($plugins as $plugin) {
                    $URI = $serverURI . "/logic/LExtension/link/course/{$newCourseId}/extension/{$plugin}";
                    http_post_data($URI, '', true, $messageNewAc);
                    if ($messageNewAc !== 201) {
                        $RequestError = true;
                        break;
                    }
                }
            }

            // creates a notification depending on RequestError
            if ($RequestError === false) {
                $createCourseNotifications[] = MakeNotification('success',
                                                    Language::Get('main','successCreateCourse', $langTemplate));
            } else {
                $createCourseNotifications[] = MakeNotification('error',
                                                    Language::Get('main','errorCreateCourse', $langTemplate));
            }
        }
    }

    if ($postResults['action'] === 'SetAdmin') {
        $setAdminNotifications = array();
        
        $postSetAdminValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('courseID',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_identifier',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidCourseId', $langTemplate)]])
          ->addSet('userName',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_userName',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidUserName', $langTemplate)]]);

        $foundValues = $postSetAdminValidation->validate();
        $setAdminNotifications = array_merge($setAdminNotifications,$postSetAdminValidation->getPrintableNotifications('MakeNotification'));
        $postSetAdminValidation->resetNotifications()->resetErrors();

        if ($postSetAdminValidation->isValid()){
            // clean Input
            $courseID = $foundValues['courseID'];
            $userName = $foundValues['userName'];

            // extracts the userID
            $URI = $databaseURI . "/user/user/{$userName}";
            $user_data = http_get($URI, true);
            $user_data = json_decode($user_data, true);

            // sets admin rights for the user
            if (empty($user_data)) {
                $setAdminNotifications[] = MakeNotification('error', Language::Get('main','invalidUserId', $langTemplate));
            } else {
                $userID = $user_data['id'];
                $status = 3;

                $data = User::encodeUser(User::createCourseStatus($userID, $courseID, $status));
                $url = $databaseURI . '/coursestatus';
                http_post_data($url, $data, true, $message);

                if ($message !== 201) {
                    $data = User::encodeUser(User::createCourseStatus($userID, $courseID, $status));
                    $url = $databaseURI . "/coursestatus/course/{$courseID}/user/{$userID}";
                    http_put_data($url, $data, true, $message);

                    if ($message === 201) {
                        $setAdminNotifications[] = MakeNotification('success', Language::Get('main','successSetAdmin', $langTemplate));
                    } else {
                        $setAdminNotifications[] = MakeNotification('error', Language::Get('main','errorSetAdmin', $langTemplate));
                    }
                } else {
                    $setAdminNotifications[] = MakeNotification('success', Language::Get('main','successSetAdmin', $langTemplate));
                }
            }
        }
    }

    // creates a new user
    if ($postResults['action'] === 'CreateUser') {
        $createUserNotifications = array();
        
        $postCreateUserValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('lastName',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_alpha_numeric',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidLastName', $langTemplate)]])
          ->addSet('firstName',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_alpha_numeric',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidFirstName', $langTemplate)]])
          ->addSet('userName',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_userName',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidUserName', $langTemplate)]])
          ->addSet('email',
                   ['valid_email',
                   'set_default'=>null,
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidMail', $langTemplate)]])
          ->addSet('password',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_userName',
                    'satisfy_min_len'=>6,
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidPassword', $langTemplate)]])
          ->addSet('passwordRepeat',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_userName',
                    'satisfy_min_len'=>6,
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidPasswordRepeat', $langTemplate)]])
          ->addSet('passwordRepeat',
                   array('satisfy_equals_field'=>'password',
                         'on_error'=>array('type'=>'error',
                                           'text'=>Language::Get('main','differentPasswords', $langTemplate))));

        $foundValues = $postCreateUserValidation->validate();
        $createUserNotifications = array_merge($createUserNotifications,$postCreateUserValidation->getPrintableNotifications('MakeNotification'));
        $postCreateUserValidation->resetNotifications()->resetErrors();

        if($postCreateUserValidation->isValid()) {
            $salt = $auth->generateSalt();
            $passwordHash = $auth->hashPassword($foundValues['password'], $salt);

            $newUser = User::createUser(null,
                                        $foundValues['userName'],
                                        $foundValues['email'],
                                        $foundValues['firstName'],
                                        $foundValues['lastName'],
                                        null,
                                        1,
                                        $passwordHash,
                                        $salt,
                                        0);

            $newUserSettings = User::encodeUser($newUser);

            $URI = $databaseURI . '/user';
            $answer=http_post_data($URI, $newUserSettings, true, $message);

            if ($message === 201) {
                $user = User::decodeUser($answer);
                if ($user->getStatus() == '201'){
                    $createUserNotifications[] = MakeNotification('success', Language::Get('main','successCreateUser', $langTemplate));
                } else
                    $createUserNotifications[] = MakeNotification('error', Language::Get('main','errorCreateUser', $langTemplate));
            } else {
                $createUserNotifications[] = MakeNotification('error', Language::Get('main','errorCreateUser', $langTemplate));
            }
        }
    }

    
    // deletes an user
    if ($postResults['action'] === 'DeleteUser') {
        $deleteUserNotifications = array();
        
        $postDeleteUserValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('userName',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_userName',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidUserName', $langTemplate)]]);

        $foundValues = $postDeleteUserValidation->validate();
        $deleteUserNotifications = array_merge($deleteUserNotifications,$postDeleteUserValidation->getPrintableNotifications('MakeNotification'));
        $postDeleteUserValidation->resetNotifications()->resetErrors();

        if($postDeleteUserValidation->isValid()) {
            // clean Input
            $userName = $foundValues['userName'];

            // extracts the userID
            $URI = $databaseURI . "/user/user/{$userName}";
            $user_data = http_get($URI, true);
            $user_data = json_decode($user_data, true);

            if (empty($user_data)) {
                $deleteUserNotifications[] = MakeNotification('error', Language::Get('main','invalidUserId', $langTemplate));
            } else {
                $userID = $user_data['id'];

                // deletes the user
                $url = $databaseURI . "/user/user/{$userID}";
                http_delete($url, true, $message);

                if ($message === 201) {
                    $deleteUserNotifications[] = MakeNotification('success',Language::Get('main','successDeleteUser', $langTemplate));
                } else {
                    $deleteUserNotifications[] = MakeNotification('error', Language::Get('main','errorDeleteUser', $langTemplate));
                }
            }
        }
    }
    
      // this area changes or reverts a user's password
    if ($postResults['action'] === 'ModifyUserAccess') {
        $modifyUserAccessNotifications = array();
        
        $postModifyUserAccessValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
           ->addSet('userName',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'valid_userName',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidUserName', $langTemplate)]])
          ->addSet('password',
                   ['valid_userName',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidPassword', $langTemplate)]])
          ->addSet('passwordRepeat',
                   ['valid_userName',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidPasswordRepeat', $langTemplate)]])
          ->addSet('passwordRepeat',
                   array('satisfy_equals_field'=>'password',
                         'on_error'=>array('type'=>'error',
                                           'text'=>Language::Get('main','differentPasswords', $langTemplate))));

        $foundValues = $postModifyUserAccessValidation->validate();
        $modifyUserAccessNotifications = array_merge($modifyUserAccessNotifications,$postModifyUserAccessValidation->getPrintableNotifications('MakeNotification'));
        $postModifyUserAccessValidation->resetNotifications()->resetErrors();

        if($postModifyUserAccessValidation->isValid()) {
            
            if ($foundValues['password'] == ''){
                // wir setzten den Zugang zurück, sodass der Nutzer sein Passwort also selbst neu setzen muss
                $salt = 'noSalt';
                $passwordHash = 'noPassword';
            } else {
                // es wurde ein gültiges Passwort eingegeben
                $salt = $auth->generateSalt();
                $passwordHash = $auth->hashPassword($foundValues['password'], $salt);
            }

            $newUser = User::createUser(null,
                                        null,
                                        null,
                                        null,
                                        null,
                                        null,
                                        1,
                                        $passwordHash,
                                        $salt,
                                        0);

            $newUserSettings = User::encodeUser($newUser);

            $URI = $databaseURI . '/user/user/'.$foundValues['userName'];
            $answer=http_put_data($URI, $newUserSettings, true, $message);

            if ($message === 201) {
                $user = User::decodeUser($answer);
                if ($user->getStatus() == '201'){
                    $modifyUserAccessNotifications[] = MakeNotification('success', Language::Get('main','successModifyUserAccess', $langTemplate, array('userName'=>$foundValues['userName'])));
                } else
                    $modifyUserAccessNotifications[] = MakeNotification('error', Language::Get('main','errorModifyUserAccess', $langTemplate, array('userName'=>$foundValues['userName'])));
            } else {
                $modifyUserAccessNotifications[] = MakeNotification('error', Language::Get('main','errorModifyUserAccess', $langTemplate, array('userName'=>$foundValues['userName'])));
            }
        }
    }

}

// load mainSettings data from GetSite
$databaseURI = $getSiteURI . "/mainsettings/user/{$uid}";
$mainSettings_data = http_get($databaseURI, true);
$mainSettings_data = json_decode($mainSettings_data, true);

$mainSettings_data['plugins'] = $plugins_data;

$user_course_data = $mainSettings_data['user'];

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::SUPER_ADMIN,true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array('name' => Language::Get('main','settings', $langTemplate),
               'backTitle' => Language::Get('main','courses', $langTemplate),
               'backURL' => 'index.php',
               'notificationElements' => $notifications,
               'navigationElement' => $menu));

// construct a content element for creating new courses
$createCourse = Template::WithTemplateFile('include/MainSettings/CreateCourse.template.html');
$createCourse->bind($mainSettings_data);
if (isset($createCourseNotifications))
    $createCourse->bind(array('CreateCourseNotificationElements' => $createCourseNotifications));

// construct a content element for setting admins
$setAdmin = Template::WithTemplateFile('include/MainSettings/SetAdmin.template.html');
$setAdmin->bind($mainSettings_data);
if (isset($setAdminNotifications))
    $setAdmin->bind(array('SetAdminNotificationElements' => $setAdminNotifications));

// construct a content element for creating new users
$createUser = Template::WithTemplateFile('include/MainSettings/CreateUser.template.html');
if (isset($createUserNotifications))
    $createUser->bind(array('CreateUserNotificationElements' => $createUserNotifications));

// construct a content element for deleting users
$deleteUser = Template::WithTemplateFile('include/MainSettings/DeleteUser.template.html');
if (isset($deleteUserNotifications))
    $deleteUser->bind(array('DeleteUserNotificationElements' => $deleteUserNotifications));

// construct a content element to modify users
$modifyUserAccess = Template::WithTemplateFile('include/MainSettings/ModifyUserAccess.template.html');
if (isset($modifyUserAccessNotifications))
    $modifyUserAccess->bind(array('ModifyUserAccessNotificationElements' => $modifyUserAccessNotifications));

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $createCourse, $setAdmin, $createUser, $deleteUser, $modifyUserAccess);
$w->defineForm(basename(__FILE__), false, $createCourse);
$w->defineForm(basename(__FILE__), false, $setAdmin);
$w->defineForm(basename(__FILE__), false, $createUser);
$w->defineForm(basename(__FILE__), false, $deleteUser);
$w->defineForm(basename(__FILE__), false, $modifyUserAccess);
$w->set_config_file('include/configs/config_default.json');
if (isset($maintenanceMode) && $maintenanceMode === '1'){
    $w->add_config_file('include/configs/config_maintenanceMode.json');
}

$w->show();

ob_end_flush();