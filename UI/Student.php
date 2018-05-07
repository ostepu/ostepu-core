<?php
/**
 * @file Student.php
 * Constructs the page that is displayed to a student.
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2013-2014
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2013-2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2013-2014
 */

ob_start();

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/vendor/Validation/Validation.php';

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::STUDENT, $cid, $uid, $globalUserData);

$langTemplate='Student_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

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
    } elseif (!isset($_SESSION['selectedUser'])) {
        $_SESSION['selectedUser'] = $uid;
    }
    $selectedUser = isset($_SESSION['selectedUser']) ? $_SESSION['selectedUser'] : $uid;

    if (isset($_POST['privileged'])){
        $_SESSION['privileged'] = $_POST['privileged'];
    }
    $privileged = (isset($_SESSION['privileged']) ? $_SESSION['privileged'] : $privileged);
}

$postValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('deleteSubmissionWarning',
           ['valid_identifier',
            'satisfy_not_equals_field'=>'deleteSubmission',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidDeleteSubmissionWarning', $langTemplate)]])
  ->addSet('deleteSubmission',
           ['valid_identifier',
            'satisfy_not_equals_field'=>'deleteSubmissionWarning',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidDeleteSubmission', $langTemplate)]])
  ->addSet('downloadMarkings',
           ['valid_identifier',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidSheetId', $langTemplate)]])
  ->addSet('redirect',
           ['valid_identifier',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidRedirectData', $langTemplate)]]);

$postResults = $postValidation->validate();
$notifications = array_merge($notifications,$postValidation->getPrintableNotifications('MakeNotification'));
$postValidation->resetNotifications()->resetErrors();


if ($postValidation->isValid() && isset($postResults['redirect'])) {
    $dat = explode('_',$postResults['redirect']);
    $sheetid = array_shift($dat);
    $postResults['redirect'] = implode('_',$dat);
    
    // nur wenn die Veranstaltung zur Umleitung passt, ist die Aktion erlaubt
    if (Redirect::getCourseFromRedirectId($postResults['redirect']) === $cid){
        // nun soll der redirect ermittelt und ausgelöst werden
        $URI = $serverURI . "/DB/DBRedirect/redirect/redirect/".$postResults['redirect'];
        $redirect = http_get($URI, true, $message);
        
        if ($message == 200){
            // die Umleitung existiert
            
            $redirect = Redirect::decodeRedirect($redirect);
            if (executeRedirect($redirect, $selectedUser, $cid, $sheetid) === false){
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

if (isset($postResults['deleteSubmissionWarning'])) {
    $notifications[] = MakeNotification('warning', Language::Get('main','askDeleteSubmission', $langTemplate));
} elseif (isset($postResults['deleteSubmission'])) {
    $suid = $postResults['deleteSubmission'];

    // extractes the studentId of the submission
    $URI = $databaseURI . '/submission/submission/' . $suid;
    $submission = http_get($URI, true);                 
    $submission = json_decode($submission, true);

    // only deletes the submission if it belongs to the user
    if ($submission['studentId'] === $selectedUser) {
        // setzt den Zeiger für die ausgewählte Einsendung zurück
        $URI = $databaseURI . '/selectedsubmission/submission/' . $suid;
        http_delete($URI, true, $message);
        
        if ($message === 201) {
            // markiert die Einsendung als "gelöscht", sie wird hierbei nicht wirklich
            // aus der Datenbank entfernt, sondern nur verborgen
            $submissionUpdate = Submission::createSubmission($suid,null,null,null,null,null,null,0);
            $URI = $databaseURI . '/submission/submission/' . $suid;
            http_put_data($URI, Submission::encodeSubmission($submissionUpdate), true, $message);

            if ($message === 201) {
                $notifications[] = MakeNotification('success', Language::Get('main','successDeleteSubmission', $langTemplate));
            } else {
                $notifications[] = MakeNotification('error', Language::Get('main','errorDeleteSubmission', $langTemplate));
            }
        } else {
            $notifications[] = MakeNotification('error', Language::Get('main','errorDeleteSubmission', $langTemplate));
        }
    }

} elseif (isset($postResults['downloadMarkings'])) {
    downloadMarkingsForSheet($selectedUser, $postResults['downloadMarkings']);
}

// load tutor data from GetSite
$URI = $getSiteURI . "/student/user/{$selectedUser}/course/{$cid}";
$student_data = http_get($URI, true);
$student_data = json_decode($student_data, true);
$student_data['filesystemURI'] = $filesystemURI;
$student_data['cid'] = $cid;
$student_data['uid'] = $selectedUser;
$user_course_data = $student_data['user'];

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::STUDENT,
                              false,
                              false,
                              (isset($student_data['redirect']) ? $student_data['redirect'] : array()));

$userNavigation = null;
if (isset($_SESSION['selectedUser'])){
    $courseStatus = null;
    if (isset($globalUserData['courses'][0]) && isset($globalUserData['courses'][0]['status']))
        $courseStatus = $globalUserData['courses'][0]['status'];
    
    $URI = $serverURI . "/DB/DBUser/user/course/{$cid}/status/0";
    $courseUser = http_get($URI, true);
    $courseUser = User::decodeUser($courseUser);
    $userNavigation = MakeUserNavigationElement($globalUserData,
                                                $courseUser,
                                                $privileged,
                                                PRIVILEGE_LEVEL::LECTURER,
                                                null,
                                                null,
                                                false,
                                                false,
                                                array('page/admin/studentMode','studentMode.md'),
                                                array(array('title'=>Language::Get('main','leaveStudent', $langTemplate),'target'=>PRIVILEGE_LEVEL::$SITES[$courseStatus].'?cid='.$cid)));
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array('name' => $user_course_data['courses'][0]['course']['name'],
               'backTitle' => Language::Get('main','changeCourse', $langTemplate),
               'backURL' => 'index.php',
               'notificationElements' => $notifications,
               'navigationElement' => $menu,
               'userNavigationElement' => $userNavigation));
$h->bind($student_data);

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetStudent.template.html');
$t->bind($student_data);
$t->bind(array('uid'=>$selectedUser, 'privileged'=>$privileged));

$w = new HTMLWrapper($h, $t);
$w->defineHeaderForm(basename(__FILE__).'?cid='.$cid, false, $h);
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $t);
$w->set_config_file('include/configs/config_student_tutor.json');
if (isset($maintenanceMode) && $maintenanceMode === '1'){
    $w->add_config_file('include/configs/config_maintenanceMode.json');
}

$w->show();

ob_end_flush();