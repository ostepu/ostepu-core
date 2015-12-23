<?php
/**
 * @file Upload.php
 * Shows a form to upload solutions.
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

$langTemplate='Upload_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

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
    }

    $selectedUser = isset($_SESSION['selectedUser']) ? $_SESSION['selectedUser'] : $uid;

    if (isset($_POST['privileged'])){
        $_SESSION['privileged'] = $_POST['privileged'];
    }
    $privileged = (isset($_SESSION['privileged']) ? $_SESSION['privileged'] : $privileged);

    if (isset($_POST['selectedSheet'])){
        $URI = $serverURI . "/DB/DBExerciseSheet/exerciseSheet/course/{$cid}";
        $courseSheets = http_get($URI, true);
        $courseSheets = ExerciseSheet::decodeExerciseSheet($courseSheets);

        $correct = false;
        foreach ($courseSheets as $sheet){
            if ($sheet->getId() == $_POST['selectedSheet']){
                $correct = true;
                break;
            }
        }

        if ($correct){
            $sid = $_POST['selectedSheet'];
        }
    }
}

$postValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('action',
            ['set_default'=>'noAction',
             'satisfy_in_list'=>['noAction', 'submit'],
             'on_error'=>['type'=>'error',
                          'text'=>Language::Get('main','invalidAction', $langTemplate)]]);

$postResults = $postValidation->validate();
$notifications = array_merge($notifications,$postValidation->getPrintableNotifications('MakeNotification'));
$postValidation->resetNotifications()->resetErrors();

if ($postValidation->isValid() && $postResults['action'] === 'submit') {
    // handle uploading files
    $timestamp = time();

    $URL = $databaseURI . '/group/user/' . $selectedUser . '/exercisesheet/' . $sid;
    $group = http_get($URL, true);
    $group = json_decode($group, true);

    if (!isset($group['leader'])) {
        $errormsg = Language::Get('main','errorNoGroup', $langTemplate, array('status'=>500));
        $notifications[] = MakeNotification('error',
                                            $errormsg);
        Logger::Log('error', "No group set for user {$selectedUser} in course {$cid}!");
    } else {

        $URL = $databaseURI . '/exercisesheet/exercisesheet/' . $sid . '/exercise';
        $sheet = http_get($URL, true);
        $sheet = json_decode($sheet, true);

        // die Veranstaltung muss zum Aufgabenblatt gehören
        if (!isset($sheet['courseId']) || ($sheet['courseId']!=$cid)) {
            set_error(Language::Get('main','errorInvalidSheetId', $langTemplate),500);
        }

        $isExpired=null;
        $hasStarted=null;
        if (isset($sheet['endDate']) && isset($sheet['startDate'])){
            // bool if endDate of sheet is greater than the actual date
            $isExpired = date('U') > date('U', $sheet['endDate']); 

            // bool if startDate of sheet is greater than the actual date
            $hasStarted = date('U') > date('U', $sheet['startDate']);
            if ($isExpired){
                // empty
            } elseif (!$hasStarted){
                set_error(Language::Get('main','noExercisePeriod', $langTemplate, array('startDate'=>date('d.m.Y  -  H:i', $sheet['startDate']))));
            }

        } else {
            set_error(Language::Get('main','noExercisePeriod', $langTemplate));
        }

        $leaderId = $group['leader']['id'];

        // stellt eine Liste mit Aufgabennummern zusammen, welche zu dieser Serie gehören
        $allowedExerciseIDs=array();
        if (isset($sheet['exercises'])){
            foreach($sheet['exercises'] as $ex){
                if (isset($ex['id'])){
                    $allowedExerciseIDs[] = $ex['id'];
                }
            }

        }

        $postSubmitValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('exercises',
                    ['set_default'=>array(),
                     'perform_this_foreach'=>[['key',
                                               ['valid_integer']],
                                              ['elem',
                                               []]], /// muss noch erweitert werden
                     'on_error'=>['type'=>'error',
                                  'text'=>Language::Get('main','invalidExercisesInput', $langTemplate)]]);

        $foundValues = $postSubmitValidation->validate();
        $notifications = array_merge($notifications,$postSubmitValidation->getPrintableNotifications('MakeNotification'));
        $postSubmitValidation->resetNotifications()->resetErrors();

        if ($postSubmitValidation->isValid()){
            foreach ($foundValues['exercises'] as $key => $exercise) {
                $exerciseId = cleanInput($exercise['exerciseID']);
                $fileName = "file{$exerciseId}";

                if (in_array($exerciseId,$allowedExerciseIDs)) {
                    #region generate form-data
                    $formdata = array();
                    if(isset($exercise['choices'])){
                        $formtext = $exercise['choices'];
                        foreach ($formtext as $formId => $choiceData2) {
                            $form = new Form();
                            $form->setFormId($formId);
                            $form->setExerciseId($exerciseId);

                            $choiceText = $choiceData2;
                            $choices = array();
                            foreach ($choiceText as $tempKey => $choiceData) {
                                if (trim($choiceData) === '') continue;
                                $choice = new Choice();
                                $choice->SetText(htmlentities(htmlentities(htmlspecialchars_decode($choiceData))));
                                $choice->SetFormId($formId);
                                $choices[] = $choice;
                            }

                            if ($choices !== null && $choices !== array()){
                                $form->setChoices($choices);
                                $formdata[] = $form;
                            }
                        }
                    }
                    #endregion

                    if (isset($_FILES[$fileName]) || $formdata !== array()) {
                    $error=0;

                    if (isset($_FILES[$fileName])){
                        $file = $_FILES[$fileName];
                        $error = $file['error'];

                        if ($error === 0){
                            $maxFileSize = parse_size(ini_get('upload_max_filesize'));

                            global $globalUserData;
                            if (isset($globalUserData['courses'][0]['course'])){
                                $obj = Course::decodeCourse(Course::encodeCourse($globalUserData['courses'][0]['course']));
                                $maxFileSize = Course::containsSetting($obj,'MaxStudentUploadSize');
                            }

                            if ($file['size']>$maxFileSize){
                                $msg = Language::Get('main','errorUploadSubmissionFileToLarge', $langTemplate, array('maxFileSize'=>formatBytes($maxFileSize),'status'=>412,'exerciseName'=>$exercise['name']));
                                $notifications[] = MakeNotification('error',$msg);
                                $error = -1;
                            }
                        }
                    }
                    else
                    {   
                        $file = null;
                        $error = 0;
                    }

                    if ($error === 0) {
                        $errormsg = '';

                        // prüfe ob nur erlaubte Zeichen im Dateinamen verwendet wurden
                        $pregRes = @preg_match("%^((?!\.)[a-zA-Z0-9\\.\\-_]+)$%", $file['name']);
                        if ($file === null || $pregRes){

                            if (isset($_FILES[$fileName])){
                                $filePath = $file['tmp_name'];
                                $uploadFile = File::createFile(null,$file['name'],null,$timestamp,null,null);
                                $uploadFile->setBody(Reference::createReference($file['tmp_name']));
                            } else {
                                $uploadFile = File::createFile(null,null,null,$timestamp,null,null);
                                $uploadFile->setBody(Form::encodeForm($formdata),true);
                            }

                            $uploadSubmission = Submission::createSubmission(null,$selectedUser,null,$exerciseId,$exercise['comment'],1,$timestamp,null,$leaderId);
                            $uploadSubmission->setFile($uploadFile);
                            $uploadSubmission->setExerciseName(isset($exercise['name']) ? $exercise['name'] : null);
                            $uploadSubmission->setSelectedForGroup('1');

                            if ($isExpired){
                                $uploadSubmission->setAccepted(0);
                            }

                            $URL = $serverURI.'/logic/LProcessor/submission';
        ///echo Submission::encodeSubmission($uploadSubmission);return;

                            $result = http_post_data($URL, Submission::encodeSubmission($uploadSubmission), true, $message);

                            if ($message !== 201) {
                                $result = Submission::decodeSubmission($result);
                                $exercise = $key + 1;
                                $errormsg = Language::Get('main','errorUploadSubmission', $langTemplate, array('status'=>$message,'exerciseName'=>$exercise['name']));

                                if ($result!==null && !empty($result)){
                                    $errormsg .= '<br><br>';
                                    $messages = $result->getMessages();
                                    foreach ($messages as $message){
                                        $errormsg.=str_replace("\n",'<br>',$message).'<br>';
                                    }
                                }

                                $notifications[] = MakeNotification('error',
                                                                    $errormsg);
                                continue;
                            }
                            else{
                                $result = Submission::decodeSubmission($result);

                                // if using forms, upload user input
                                if(isset($exercise['choices'])){

                                    $i=0;   
                                    foreach($formdata as &$form){
                                        $choices = $form->getChoices();
                                        foreach($choices as &$choice){
                                            $choice->setSubmissionId($result->getId());
                                        }

                                        $URL = $serverURI.'/DB/DBChoice/formResult/choice';
                                        $result2 = http_post_data($URL, Choice::encodeChoice($choices), true, $message);

                                            if ($message !== 201) {
                                                $result2 = Choice::decodeChoice($result2);
                                                $exercise = $key + 1;
                                                $errormsg = Language::Get('main','errorUploadSubmission', $langTemplate, array('status'=>$message,'exerciseName'=>$exercise['name']));

                                                if ($result2!==null){
                                                    $errormsg .= '<br><br>';
                                                    $messages2 = $result2->getMessages();
                                                    foreach ($messages2 as $message){
                                                        $errormsg.=str_replace("\n",'<br>',$message).'<br>';
                                                    }
                                                }

                                                $notifications[] = MakeNotification('error',
                                                                                    $errormsg);
                                                continue;
                                            }
                                        $i++;
                                    }

                                }

                                $messages = $result->getMessages();

                                if ($messages !== null){
                                    foreach ($messages as $message){
                                        $errormsg.=str_replace("\n",'<br>',$message).'<br>';
                                    }
                                }
                            }


                            $msg = Language::Get('main','successUploadSubmission', $langTemplate, array('exerciseName'=>$exercise['name'])).'<br>'.$errormsg;
                            $notifications[] = MakeNotification('success',
                                                                $msg);

                            if ($isExpired){
                                $msg = Language::Get('main','successLateSubmission', $langTemplate, array('exerciseName'=>$exercise['name']));
                                $notifications[] = MakeNotification('warning',
                                                                    $msg);   
                            }
                        } else {
                            $msg = Language::Get('main','errorUploadSubmissionSymbols', $langTemplate, array('status'=>412,'exerciseName'=>$exercise['name']));
                            $notifications[] = MakeNotification('error',$msg);
                        }
                    } else {
                        if ($error === UPLOAD_ERR_INI_SIZE){
                            $msg = Language::Get('main','errorUploadSubmissionFileToLarge', $langTemplate, array('maxFileSize'=>formatBytes(parse_size(ini_get('upload_max_filesize'))),'status'=>412,'exerciseName'=>$exercise['name']));
                            $notifications[] = MakeNotification('error',$msg);
                        } else if($error === UPLOAD_ERR_PARTIAL || $error === UPLOAD_ERR_NO_TMP_DIR || $error === UPLOAD_ERR_CANT_WRITE || $error === UPLOAD_ERR_EXTENSION){
                            $msg = Language::Get('main','errorUploadSubmission', $langTemplate, array('status'=>500,'exerciseName'=>$exercise['name']));
                            $notifications[] = MakeNotification('error',$msg);
                        }
                    }
                }
                } else {
                    $msg = Language::Get('main','errorInvalidExerciseId', $langTemplate, array('status'=>412));
                    $notifications[] = MakeNotification('error',$msg);
                }
            }
        }
    }
}

// load user data from the database
$URL = $getSiteURI . "/upload/user/{$selectedUser}/course/{$cid}/exercisesheet/{$sid}";
$upload_data = http_get($URL, true);
$upload_data = json_decode($upload_data, true);
$upload_data['filesystemURI'] = $filesystemURI;
$upload_data['cid'] = $cid;
$upload_data['sid'] = $sid;

if (!isset($group)){
    $URL = $databaseURI . "/group/user/{$selectedUser}/exercisesheet/{$sid}";
    $group = http_get($URL, true);
    $group = json_decode($group, true);
    $upload_data['group'] = $group;
}

$user_course_data = $upload_data['user'];

$isExpired=null;
$hasStarted=null;

if (isset($upload_data['exerciseSheet']['endDate']) && isset($upload_data['exerciseSheet']['startDate'])){
    // bool if endDate of sheet is greater than the actual date
    $isExpired = date('U') > date('U', $upload_data['exerciseSheet']['endDate']); 

    // bool if startDate of sheet is greater than the actual date
    $hasStarted = date('U') > date('U', $upload_data['exerciseSheet']['startDate']);
    if ($isExpired){
        $allowed = 0;

        if (isset($user_course_data['courses'][0]['course'])){
            $obj = Course::decodeCourse(Course::encodeCourse($user_course_data['courses'][0]['course']));
            $allowed = Course::containsSetting($obj,'AllowLateSubmissions');
        }

        ///set_error("Der Übungszeitraum ist am ".date('d.m.Y  -  H:i', $upload_data['exerciseSheet']['endDate'])." abgelaufen!");
        if ($allowed  === null || $allowed==1){
            $msg = Language::Get('main','expiredExercisePerionDesc', $langTemplate,array('endDate'=>date('d.m.Y  -  H:i', $upload_data['exerciseSheet']['endDate'])));
            $notifications[] = MakeNotification('warning',
                                                $msg);
        } else {
            if ($privileged){
                $msg = Language::Get('main','expiredExercisePerionDesc', $langTemplate,array('endDate'=>date('d.m.Y  -  H:i', $upload_data['exerciseSheet']['endDate'])));
                $notifications[] = MakeNotification('warning',
                                                    $msg); 
            } else {
                set_error(Language::Get('main','expiredExercisePerion', $langTemplate,array('endDate'=>date('d.m.Y  -  H:i', $upload_data['exerciseSheet']['endDate']))));
            }
        }

    } elseif (!$hasStarted && !$privileged){
        set_error(Language::Get('main','noStartedExercisePeriod', $langTemplate,array('startDate'=>date('d.m.Y  -  H:i', $upload_data['exerciseSheet']['startDate']))));
    }

} else
    set_error(Language::Get('main','noExercisePeriod', $langTemplate));

//$formdata = file_get_contents('FormSample.json');
$URL = $serverURI."/DB/DBForm/form/exercisesheet/{$sid}";
$formdata = http_get($URL, true);

$formdata = Form::decodeForm($formdata);
if (!is_array($formdata))$formdata=array($formdata);
foreach ($formdata as $value){
    foreach ($upload_data['exercises'] as &$key){
        if ($value->getExerciseId() == $key['id']){
            $key['form'] = $value;
            break;
        }
    }
}

$upload_data['hasStarted'] = $hasStarted;
$upload_data['isExpired'] = $isExpired;
$user_course_data = $upload_data['user'];
$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::STUDENT);

$userNavigation = null;
if (isset($_SESSION['selectedUser'])){
    $courseStatus = null;
    if (isset($globalUserData['courses'][0]) && isset($globalUserData['courses'][0]['status']))
        $courseStatus = $globalUserData['courses'][0]['status'];
    
    $URI = $serverURI . "/DB/DBUser/user/course/{$cid}/status/0";
    $courseUser = http_get($URI, true);
    $courseUser = User::decodeUser($courseUser);
    $URI = $serverURI . "/DB/DBExerciseSheet/exercisesheet/course/{$cid}/";
    $courseSheets = http_get($URI, true);
    $courseSheets = ExerciseSheet::decodeExerciseSheet($courseSheets);
    $courseSheets = array_reverse($courseSheets);

    if (!$privileged){
        foreach ($courseSheets as $key => $sheet){
            if ($sheet->getEndDate()!==null && $sheet->getStartDate()!==null){
                // bool if endDate of sheet is greater than the actual date
                $isExpired = date('U') > date('U', $sheet->getEndDate()); 

                // bool if startDate of sheet is greater than the actual date
                $hasStarted = date('U') > date('U', $sheet->getStartDate());
                if ($isExpired){
                    $allowed = 0;

                    if (isset($user_course_data['courses'][0]['course'])){
                        $obj = Course::decodeCourse(Course::encodeCourse($user_course_data['courses'][0]['course']));
                        $allowed = Course::containsSetting($obj,'AllowLateSubmissions');
                    }

                    if ($allowed  === null || $allowed==1){
                    } else {
                        unset($courseSheets[$key]);
                    }

                } elseif (!$hasStarted){
                    unset($courseSheets[$key]);
                }

            } else {
                unset($courseSheets[$key]);
            }
        }
    }

    $userNavigation = MakeUserNavigationElement($globalUserData,
                                                $courseUser,
                                                $privileged,
                                                PRIVILEGE_LEVEL::LECTURER,
                                                $sid,
                                                $courseSheets,
                                                false,
                                                false,
                                                array('page/admin/studentMode','studentMode.md'),
                                                array(array('title'=>Language::Get('main','leaveStudent', $langTemplate),'target'=>PRIVILEGE_LEVEL::$SITES[$courseStatus].'?cid='.$cid)));
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array('name' => $user_course_data['courses'][0]['course']['name'],
               'backTitle' => Language::Get('main','backToCourse', $langTemplate),
               'backURL' => "Student.php?cid={$cid}",
               'notificationElements' => $notifications,
               'navigationElement' => $menu,
               'userNavigationElement' => $userNavigation));


/**
 * @todo detect when the form was changed by the user, this could be done by
 * hashing the form elements before handing them to the user:
 * - hash the form (simple hash/hmac?)
 * - save the calculated has in a hidden form input
 * - when the form is posted recalculate the hash and compare to the previous one
 * - log the user id?
 *
 * @see http://www.php.net/manual/de/function.hash-hmac.php
 * @see http://php.net/manual/de/function.hash.php
 */

$t = Template::WithTemplateFile('include/Upload/Upload.template.html');
$t->bind($upload_data);
$t->bind(array('privileged' => $privileged));

$w = new HTMLWrapper($h, $t);
$w->set_config_file('include/configs/config_upload_exercise.json');
$w->show();
