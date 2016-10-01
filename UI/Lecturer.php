<?php
/**
 * @file Lecturer.php
 * Constructs the page that is displayed to a lecturer.
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
include_once dirname(__FILE__).'/../Assistants/LArraySorter.php';
include_once dirname(__FILE__) . '/../Assistants/vendor/Validation/Validation.php';

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::LECTURER, $cid, $uid, $globalUserData);

$langTemplate='Lecturer_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

$sheetNotifications = array();

unset($_SESSION['selectedUser']);

$postValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('action',
           ['set_default'=>'noAction',
            'satisfy_in_list'=>['noAction', 'ExerciseSheetLecturer', 'navigation'],
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidAction', $langTemplate)]]);
$postResults = $postValidation->validate();
$notifications = array_merge($notifications,$postValidation->getPrintableNotifications('MakeNotification'));
$postValidation->resetNotifications()->resetErrors();

if ($postValidation->isValid() && $postResults['action'] !== 'noAction') {
    $postDeleteSheetValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
      ->addSet('deleteSheetWarning',
               ['set_default'=>null,
                'valid_identifier',
                'satisfy_not_equals_field'=>'deleteSheet',
                'on_error'=>['type'=>'error',
                             'text'=>Language::Get('main','errorDeleteSheetWarningValidation', $langTemplate)]])
      ->addSet('deleteSheet',
               ['set_default'=>null,
                'valid_identifier',
                'satisfy_not_equals_field'=>'deleteSheetWarning',
                'on_error'=>['type'=>'error',
                             'text'=>Language::Get('main','errorDeleteSheetValidation', $langTemplate)]])
      ->addSet('redirect',
               ['valid_identifier',
                'on_error'=>['type'=>'error',
                             'text'=>Language::Get('main','invalidRedirectData', $langTemplate)]]);

    $foundValues = $postDeleteSheetValidation->validate();
    $notifications = array_merge($notifications,$postDeleteSheetValidation->getPrintableNotifications('MakeNotification'));
    $postDeleteSheetValidation->resetNotifications()->resetErrors();
    
    
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

    if ($postDeleteSheetValidation->isValid() && $postResults['action'] === 'ExerciseSheetLecturer' && isset($foundValues['deleteSheetWarning'])) {
        $sheetNotifications[$foundValues['deleteSheetWarning']][] = MakeNotification('warning', Language::Get('main','askDeleteSheet', $langTemplate));
    } elseif ($postDeleteSheetValidation->isValid() && $postResults['action'] == 'ExerciseSheetLecturer' && isset($foundValues['deleteSheet'])) { /// !!! darf er das ??? ///

        $URL = $logicURI . "/exercisesheet/exercisesheet/{$foundValues['deleteSheet']}"; /// !!! darf er das ??? ///
        $result = http_delete($URL, true, $message);

        if ($message === 201){
            $sheetNotifications[$foundValues['deleteSheet']][] = MakeNotification('success', Language::Get('main','successDeleteSheet', $langTemplate));
        } else {
            $sheetNotifications[$foundValues['deleteSheet']][] = MakeNotification('error', Language::Get('main','errorDeleteSheet', $langTemplate));
        }
    }
}

// load GetSite data for Lecturer.php
$URL = $getSiteURI . "/lecturer/user/{$uid}/course/{$cid}";
$lecturer_data = http_get($URL, true);
$lecturer_data = json_decode($lecturer_data, true);
$lecturer_data['filesystemURI'] = $filesystemURI;
$lecturer_data['cid'] = $cid;

$user_course_data = $lecturer_data['user'];

if (is_null($user_course_data)) {
    $user_course_data = array();
}

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::LECTURER,
                              false,
                              false,
                              (isset($lecturer_data['redirect']) ? $lecturer_data['redirect'] : array()));
// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array('name' => $user_course_data['courses'][0]['course']['name'],
               'backTitle' => Language::Get('main','changeCourse', $langTemplate),
               'backURL' => 'index.php',
               'notificationElements' => $notifications,
               'navigationElement' => $menu));


$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetLecturer.template.html');
$t->bind($lecturer_data);
if (isset($sheetNotifications))
    $t->bind(array('SheetNotificationElements' => $sheetNotifications));

$w = new HTMLWrapper($h, $t);
$w->defineHeaderForm(basename(__FILE__).'?cid='.$cid, false, $h);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $t);
$w->set_config_file('include/configs/config_admin_lecturer.json');
$w->show();

ob_end_flush();