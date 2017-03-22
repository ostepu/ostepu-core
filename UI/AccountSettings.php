<?php
/**
 * @file AccountSettings.php
 * Constructs a page where a user can manage his account.
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
 * @date 2013-2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2013-2014
 */

ob_start();

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/vendor/Validation/Validation.php';

$langTemplate='AccountSettings_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

$postValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('action',
           array('set_default'=>'noAction',
                 'satisfy_in_list'=>array('noAction', 'SetPassword', 'SetAccountInfo'),
                 'on_error'=>array('type'=>'error',
                                   'text'=>Language::Get('main','invalidAction', $langTemplate))));

$postResults = $postValidation->validate();
$notifications = array_merge($notifications, $postValidation->getPrintableNotifications('MakeNotification'));
$postValidation->resetNotifications()->resetErrors();

if ($postValidation->isValid() && $postResults['action'] !== 'noAction') {
    // changes the user's password
    if ($postResults['action'] === 'SetPassword') {
        $postSetPasswordValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('oldPassword',
                   array('satisfy_min_len'=>1,
                         'on_error'=>array('type'=>'warning',
                                           'text'=>Language::Get('main','invalidOldPassword', $langTemplate))))
          ->addSet('newPassword',
                   array('satisfy_exists',
                         'satisfy_min_len'=>6,
                         'on_error'=>array('type'=>'warning',
                                           'text'=>Language::Get('main','invalidNewPassword', $langTemplate))))              
          ->addSet('newPasswordRepeat',
                   array('satisfy_exists',
                         'satisfy_min_len'=>6,
                         'on_error'=>array('type'=>'warning',
                                           'text'=>Language::Get('main','invalidRepeat', $langTemplate))))                
          ->addSet('newPasswordRepeat',
                   array('satisfy_equals_field'=>'newPassword',
                         'on_error'=>array('type'=>'error',
                                           'text'=>Language::Get('main','differentPasswords', $langTemplate))));

        if($postSetPasswordValidation->isValid()) {

            $foundValues = $postSetPasswordValidation->getResult();

            $oldPassword = $foundValues['oldPassword'];
            $newPassword = $foundValues['newPassword'];
            $newPasswordRepeat = $foundValues['newPasswordRepeat'];

            // loads user data from database
            $URI = $databaseURI . "/user/user/{$uid}";
            $user_data = http_get($URI, true);
            $user_data = json_decode($user_data, true);

            // generates hashs of old and new password
            $oldSalt = $user_data['salt'];
            $oldPasswordHash = $auth->hashPassword($oldPassword, $oldSalt);

            $newSalt = $auth->generateSalt();
            $newPasswordHash = $auth->hashPassword($newPassword, $newSalt);

            // check if the old password is necessary
            if ($user_data['password'] === 'noPassword' || $oldPasswordHash === $user_data['password']) {
                // both passwords are equal
                $newUserSettings = User::encodeUser(User::createUser($uid, null, null, null, null, null, null,
                                                    $newPasswordHash, $newSalt));
                $URI = $serverURI . '/DB/DBUser/user/user/' . $uid;
                http_put_data($URI, $newUserSettings, true, $message);

                if ($message === 201) {
                    Authentication::logoutUser();
                } else {
                    $notifications[] = MakeNotification('error', Language::Get('main','errorChangePassword', $langTemplate));
                }
            }
            else {
                $notifications[] = MakeNotification('error', Language::Get('main','incorrectOldPassword', $langTemplate));
            }
        } else {
            $notifications = array_merge($notifications, $postSetPasswordValidation->getPrintableNotifications('MakeNotification'));
            $postSetPasswordValidation->resetNotifications()->resetErrors();
        }
    } else if ($postResults['action'] === 'SetAccountInfo') {   
        $postSetAccountInfoValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('language',
                   array('satisfy_exact_len'=>2,
                         'satisfy_exists',
                         'satisfy_in_list' => ['en','de'],
                         'on_error'=>array('type'=>'error',
                                           'text'=>Language::Get('main','invalidLanguage', $langTemplate))));

        if($postSetAccountInfoValidation->isValid()) {

            $foundValues = $postSetAccountInfoValidation->getResult();
            $language = $foundValues['language'];

            $newUserSettings = User::encodeUser(User::createUser($uid, null, null, null, null, null, null,
                                                null, null, null, null, null, null, null, $language));
            $URI = $serverURI . '/DB/DBUser/user/user/' . $uid;

            http_put_data($URI, $newUserSettings, true, $message);

            if ($message === 201) {
                $notifications[] = MakeNotification('success', Language::Get('main','languageChanged', $langTemplate));
            } else {
                $notifications[] = MakeNotification('error', Language::Get('main','errorChangeLanguage', $langTemplate));
            }
        } else {
            $notifications = array_merge($notifications, $postSetAccountInfoValidation->getPrintableNotifications('MakeNotification'));
            $postSetAccountInfoValidation->resetNotifications()->resetErrors();
        }
    }
}

// load user data from the database
$databaseURI = $getSiteURI . "/accountsettings/user/{$uid}";
$accountSettings_data = http_get($databaseURI, true);
$accountSettings_data = json_decode($accountSettings_data, true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($accountSettings_data);
$h->bind(array('name' => Language::Get('main','accountSettings', $langTemplate),
               'backTitle' => Language::Get('main','course', $langTemplate),
               'backURL' => 'index.php',
               'notificationElements' => $notifications));

// construct a content element for account information
$accountInfo = Template::WithTemplateFile('include/AccountSettings/AccountInfo.template.html');
$accountInfo->bind($accountSettings_data);

// construct a content element for changing password
$changePassword = Template::WithTemplateFile('include/AccountSettings/ChangePassword.template.html');
$changePassword->bind($accountSettings_data);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $accountInfo, $changePassword);
$w->defineForm(basename(__FILE__), false, $changePassword);
$w->defineForm(basename(__FILE__), false, $accountInfo);
$w->set_config_file('include/configs/config_default.json');
if (isset($maintenanceMode) && $maintenanceMode === '1'){
    $w->add_config_file('include/configs/config_maintenanceMode.json');
}

$w->show();

ob_end_flush();