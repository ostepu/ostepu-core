<?php
/**
 * @file Login.php
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

$notifications = array();
 
include_once dirname(__FILE__) . '/include/Authentication.php';
include_once dirname(__FILE__) . '/include/HTMLWrapper.php';
include_once dirname(__FILE__) . '/include/Template.php';
include_once dirname(__FILE__) . '/../Assistants/Logger.php';
include_once dirname(__FILE__) . '/../Assistants/Validation/Validation.php';
include_once dirname(__FILE__) . '/include/Helpers.php';

$langTemplate='Login_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

$auth = new Authentication();
Authentication::preventSessionFix();

$f = new Validation($_POST, array('preRules'=>array('sanitize')));

$f->addSet('action',
           array('set_default'=>'noAction',
                 'satisfy_in_list'=>array('noAction', 'login'),
                 'on_error'=>array('type'=>'error',
                                   'text'=>'???1')));
                                   

$f2 = new Validation($_GET, array('preRules'=>array('sanitize')));
$f2->addSet('back',
            array('valid_url_query',
                  'set_default'=>null,
                  'on_error'=>array('type'=>'error',
                                    'text'=>'???4')));

                                   
$valResults = $f->validate();    
$valResults2 = $f2->validate();
$notifications = array_merge($notifications, $f->getPrintableNotifications());
$notifications = array_merge($notifications, $f2->getPrintableNotifications());
$f->resetNotifications()->resetErrors();
$f2->resetNotifications()->resetErrors();

if ($f->isValid() && $f2->isValid() && $valResults['action'] !== 'noAction') {
    if ($valResults['action'] === 'login') {
        $f->addSet('username',
                   array('satisfy_exists',
                         'valid_userName',
                         'to_lower',
                         'on_error'=>array('type'=>'error',
                                           'text'=>'???2')))
          ->addSet('password',
                   array('satisfy_exists',
                         'on_error'=>array('type'=>'error',
                                           'text'=>'???3')));

        $valResults = $f->validate();
        $notifications = array_merge($notifications, $f->getPrintableNotifications());
        $f->resetNotifications()->resetErrors();

        if ($f->isValid()){
            // if a hidden Post named back and the php file exists set backurl
            if (isset($valResults2['back']) && file_exists(parse_url($valResults2['back'], PHP_URL_PATH))) {
                /// --- ///
            } else {
                $valResults2['back'] = 'index.php';
            }

            // log in user and return result
            $signed = $auth->loginUser($valResults['username'], $valResults['password']);

            if ($signed===true) {
                header('Location: ' . $valResults2['back']);
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
if ($f2->isValid() && isset($valResults2['back']) && file_exists(parse_url($valResults2['back'], PHP_URL_PATH))) {
    $backdata = array('backURL' => $valResults2['back']);
} else {
    $backdata = array();
}

$userLogin->bind($backdata);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $userLogin);
$w->set_config_file('include/configs/config_default.json');
$w->show();

