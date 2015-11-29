<?php
/**
 * @file Student.php
 * Constructs the page that is displayed to a student.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */
include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/Validation/Validation.php';

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

$f = new Validation($_POST, array('preRules'=>array('sanitize')));
$f->addSet('deleteSubmissionWarning',
           ['set_default'=>null,
            'valid_identifier',
            'satisfy_not_equals_field'=>'deleteSubmission',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidDeleteSubmissionWarning', $langTemplate)]])
  ->addSet('deleteSubmission',
           ['set_default'=>null,
            'valid_identifier',
            'satisfy_not_equals_field'=>'deleteSubmissionWarning',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidDeleteSubmission', $langTemplate)]])
  ->addSet('downloadMarkings',
           ['set_default'=>null,
            'valid_identifier',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidSheetId', $langTemplate)]]);
                                   
$valResults = $f->validate();
$notifications = array_merge($notifications,$f->getPrintableNotifications());
$f->resetNotifications()->resetErrors();

if (isset($valResults['deleteSubmissionWarning'])) {
    $notifications[] = MakeNotification('warning', Language::Get('main','askDeleteSubmission', $langTemplate));
} elseif (isset($valResults['deleteSubmission'])) {
    $suid = $valResults['deleteSubmission'];
    
    // extractes the studentId of the submission
    $URI = $databaseURI . '/submission/' . $suid;
    $submission = http_get($URI, true);                  
    $submission = json_decode($submission, true);
                    
    // only deletes the submission if it belongs to the user
    if ($submission['studentId'] === $selectedUser) {
        $URI = $databaseURI . '/selectedsubmission/submission/' . $suid;
        http_delete($URI, true, $message);
        
        // todo: treat the case if the previous operation failed
        $submissionUpdate = Submission::createSubmission($suid,null,null,null,null,null,null,0);
        $URI = $databaseURI . '/submission/submission/' . $suid;
        http_put_data($URI, Submission::encodeSubmission($submissionUpdate), true, $message2);
        
        if ($message === 201 && $message2 === 201) {
            $notifications[] = MakeNotification('success', Language::Get('main','successDeleteSubmission', $langTemplate));
        } else {
            $notifications[] = MakeNotification('error', Language::Get('main','errorDeleteSubmission', $langTemplate));
        }
    }

} elseif (isset($valResults['downloadMarkings'])) {
    downloadMarkingsForSheet($selectedUser, $valResults['downloadMarkings']);
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
                              PRIVILEGE_LEVEL::STUDENT);
     
$userNavigation = null;
if (isset($_SESSION['selectedUser'])){
    $URI = $serverURI . "/DB/DBUser/user/course/{$cid}/status/0";
    $courseUser = http_get($URI, true);
    $courseUser = User::decodeUser($courseUser);
    $userNavigation = MakeUserNavigationElement($globalUserData,$courseUser,$privileged,
                                                PRIVILEGE_LEVEL::LECTURER);
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
$w->defineForm(basename(__FILE__).'?cid='.$cid, false, $t);
$w->set_config_file('include/configs/config_student_tutor.json');
$w->show();

