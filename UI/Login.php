<?php
/**
 * @file Login.php
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
 * @date 2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2013-2014
 */

ob_start();

$notifications = array();

include_once dirname(__FILE__) . '/include/Authentication.php';
include_once dirname(__FILE__) . '/include/HTMLWrapper.php';
include_once dirname(__FILE__) . '/include/Template.php';
include_once dirname(__FILE__) . '/../Assistants/Logger.php';
include_once dirname(__FILE__) . '/../Assistants/vendor/Validation/Validation.php';
include_once dirname(__FILE__) . '/include/Helpers.php';

$langTemplate='Login_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

$auth = new Authentication();
Authentication::preventSessionFix();

$postValidation = Validation::open($_POST, array('preRules'=>array('')))
  ->addSet('back',
           array('valid_url_query',
                 'set_default'=>null,
                 'on_error'=>array('type'=>'error',
                                   'text'=>Language::Get('main',Language::Get('main','invalidBackURL', $langTemplate), $langTemplate))))
  ->addSet('action',
           array('sanitize',
                 'set_default'=>'noAction',
                 'satisfy_in_list'=>array('noAction', 'login'),
                 'on_error'=>array('type'=>'error',
                                   'text'=>Language::Get('main','invalidAction', $langTemplate))));

$getValidation = Validation::open($_GET, array('preRules'=>array('')))
  ->addSet('back',
           array('valid_url_query',
                 'set_default'=>null,
                 'on_error'=>array('type'=>'error',
                                   'text'=>Language::Get('main',Language::Get('main','invalidBackURL', $langTemplate), $langTemplate))));

$postResults = $postValidation->validate();   
$getResults = $getValidation->validate();
$notifications = array_merge($notifications, $postValidation->getPrintableNotifications('MakeNotification'));
$notifications = array_merge($notifications, $getValidation->getPrintableNotifications('MakeNotification'));
$postValidation->resetNotifications()->resetErrors();
$getValidation->resetNotifications()->resetErrors();

if ($postValidation->isValid() && $getValidation->isValid() && $postResults['action'] !== 'noAction') {
    if ($postResults['action'] === 'login') {
        $postLoginValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('username',
                   array('satisfy_exists',
                         'valid_userName',
                         'to_lower',
                         'on_error'=>array('type'=>'error',
                                           'text'=>Language::Get('main','invalidUserName', $langTemplate))))
          ->addSet('password',
                   array('satisfy_exists',
                         'on_error'=>array('type'=>'error',
                                           'text'=>Language::Get('main','invalidPassword', $langTemplate))));

        $foundValues = $postLoginValidation->validate();
        $notifications = array_merge($notifications, $postLoginValidation->getPrintableNotifications('MakeNotification'));
        $postLoginValidation->resetNotifications()->resetErrors();

        if ($postLoginValidation->isValid()){
            // if a hidden Post named back and the php file exists set backurl
            if (isset($postResults['back']) && file_exists(parse_url($postResults['back'], PHP_URL_PATH))) {
                /// --- ///
            } else {
                $postResults['back'] = 'index.php';
            }

            // log in user and return result
            $signed = $auth->loginUser($foundValues['username'], $foundValues['password']);

            if ($signed===true) {
                header('Location: ' . $postResults['back']);
                exit();
            } else {
                if ($signed!==false){
                    $notifications[] = $signed;
                } else
                    $notifications[] = MakeNotification('error', Language::Get('main','errorLogin', $langTemplate));
            }
        }
    }
}

// check if already logged in
if(Authentication::checkLogin()) {
    header('Location: index.php');
    exit();
}

// prüfe den Wartungsmodus
if ($maintenanceMode === '1'){
    $text = $maintenanceText;
    if (trim($maintenanceText) == '') $text = "Wartungsarbeiten!!!";
    $notifications[] = MakeNotification('error', $text);
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind(array('backTitle' => Language::Get('main','changeCourse', $langTemplate),
               'name' => Language::Get('main','title', $langTemplate),
               'hideBackLink' => 'true',
               'hideLogoutLink' => 'true',
               'notificationElements' => $notifications));

// construct a login element
$userLogin = Template::WithTemplateFile('include/Login/Login.template.html');
// if back Parameter is given bind it to the userLogin to create hidden input
if ($getValidation->isValid() && isset($getResults['back']) && file_exists(parse_url($getResults['back'], PHP_URL_PATH))) {
    $backdata = array('backURL' => $getResults['back']);
} else {
    $backdata = array();
}

$userLogin->bind($backdata);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $userLogin);
$w->set_config_file('include/configs/config_default.json');
$w->show();

ob_end_flush();