<?php
/**
 * @file Tutor.php
 * Constructs the page that is displayed to a tutor.
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2013-2014
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2013-2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2013-2014
 */

ob_start();

include_once dirname(__FILE__).'/include/Boilerplate.php';
include_once dirname(__FILE__).'/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/vendor/Validation/Validation.php';

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::TUTOR, $cid, $uid, $globalUserData);

$langTemplate='Tutor_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

unset($_SESSION['selectedUser']);

$sheetNotifications = array();

unset($_SESSION['selectedUser']);

$postValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('redirect',
           ['valid_identifier',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidRedirectData', $langTemplate)]]);

$foundValues = $postValidation->validate();
$notifications = array_merge($notifications,$postValidation->getPrintableNotifications('MakeNotification'));
$postValidation->resetNotifications()->resetErrors();


if ($postValidation->isValid() && isset($foundValues['redirect'])) {
    $dat = explode('_',$foundValues['redirect']);
    $sheetid = array_shift($dat);
    $foundValues['redirect'] = implode('_',$dat);
    
    // nur wenn die Veranstaltung zur Umleitung passt, ist die Aktion erlaubt
    if (Redirect::getCourseFromRedirectId($foundValues['redirect']) === $cid){
        // nun soll der redirect ermittelt und ausgelöst werden
        $URI = $serverURI . "/DB/DBRedirect/redirect/redirect/".$foundValues['redirect'];
        $redirect = http_get($URI, true, $message);
        
        if ($message == 200){
            // die Umleitung existiert
            
            $redirect = Redirect::decodeRedirect($redirect);
            if (executeRedirect($redirect, $uid, $cid, $sheetid) === false){
                $notifications[] = MakeNotification('error', Language::Get('main','errorRedirect', $langTemplate));
            }
        } else {
            // unbekannte Umleitung
            $notifications[] = MakeNotification('error', Language::Get('main','invalidRedirect', $langTemplate));
        }
    } else {
        // falsche Veranstaltung
        $notifications[] = MakeNotification('error', Language::Get('main','invalidCourse', $langTemplate));
    }
}

// load tutor data from GetSite
$URI = $getSiteURI . "/tutor/user/{$uid}/course/{$cid}";
$tutor_data = http_get($URI, true);
$tutor_data = json_decode($tutor_data, true);
$tutor_data['filesystemURI'] = $filesystemURI;
$tutor_data['cid'] = $cid;

// check userrights for course
$user_course_data = $tutor_data['user'];

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::TUTOR,
                              false,
                              false,
                              (isset($tutor_data['redirect']) ? $tutor_data['redirect'] : array()));

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array('name' => $user_course_data['courses'][0]['course']['name'],
               'backTitle' => Language::Get('main','changeCourse', $langTemplate),
               'backURL' => 'index.php',
               'notificationElements' => $notifications,
               'navigationElement' => $menu));

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetTutor.template.html');
$t->bind($tutor_data);

$w = new HTMLWrapper($h, $t);
$w->defineHeaderForm(basename(__FILE__).'?cid='.$cid, false, $h);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $t);
$w->set_config_file('include/configs/config_student_tutor.json');
$w->show();

ob_end_flush();