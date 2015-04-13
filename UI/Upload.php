<?php
/**
 * @file Upload.php
 * Shows a form to upload solutions.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';
include_once '../Assistants/Structures.php';

if (isset($_POST['action']) && $_POST['action'] == 'submit') {
    // handle uploading files
    /**
     * @todo don't automatically accept the submission
     */
    $timestamp = time();

    $URL = $databaseURI . '/group/user/' . $uid . '/exercisesheet/' . $sid;
    $group = http_get($URL, true);
    $group = json_decode($group, true);

    if (!isset($group['leader'])) {
        $errormsg = "500: Internal Server Error. <br />Zur Zeit können keine Aufgaben eingesendet werden.";
        $notifications[] = MakeNotification('error',
                                            $errormsg);
        Logger::Log('error', "No group set for user {$uid} in course {$cid}!");
    } else {

        $leaderId = $group['leader']['id'];

        foreach ($_POST['exercises'] as $key => $exercise) {
            $exerciseId = cleanInput($exercise['exerciseID']);
            $fileName = "file{$exerciseId}";
            
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
                if (isset($_FILES[$fileName])){
                    $file = $_FILES[$fileName];
                    $error = $file['error'];
                }
                else
                {    
                    $file = null;
                    $error = 0;
                }

                if ($error === 0) {
                    $errormsg = '';
                    if (isset($_FILES[$fileName])){
                        $filePath = $file['tmp_name'];
                        $uploadFile = File::createFile(null,$file['name'],null,$timestamp,null,null);
                        $uploadFile->setBody(base64_encode(file_get_contents($file['tmp_name'])));
                    } else {
                        $uploadFile = File::createFile(null,null,null,$timestamp,null,null);
                        $uploadFile->setBody(base64_encode(Form::encodeForm($formdata)));
                    }

                    $uploadSubmission = Submission::createSubmission(null,$uid,null,$exerciseId,$exercise['comment'],1,$timestamp,null,$leaderId);
                    $uploadSubmission->setFile($uploadFile);
                    $uploadSubmission->setExerciseName(isset($exercise['name']) ? $exercise['name'] : null);
                    $uploadSubmission->setSelectedForGroup('1');

                    $URL = $serverURI.'/logic/LProcessor/submission';
///echo Submission::encodeSubmission($uploadSubmission);return;

                    $result = http_post_data($URL, Submission::encodeSubmission($uploadSubmission), true, $message);
//echo $result;
                    if ($message != "201") {
                        $result = Submission::decodeSubmission($result);
                        $exercise = $key + 1;
                        $errormsg = "{$message}: Aufgabe ".$exercise['name']." konnte nicht hochgeladen werden.";
                        
                        if ($result!==null && !empty($result)){
                            $errormsg .= "<br><br>";
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
                                
                                    if ($message != "201") {
                                        $result2 = Choice::decodeChoice($result2);
                                        $exercise = $key + 1;
                                        $errormsg = "{$message}: Aufgabe ".$exercise['name']." konnte nicht hochgeladen werden.";
                                        
                                        if ($result2!==null){
                                            $errormsg .= "<br><br>";
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
                  

                    $msg = "Aufgabe ".$exercise['name']." wurde erfolgreich eingesendet.<br>".$errormsg;
                    $notifications[] = MakeNotification('success',
                                                        $msg);
                }
            }
        }
    }
}

// load user data from the database
$URL = $getSiteURI . "/upload/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
///echo $URL;return;
$upload_data = http_get($URL, true);
$upload_data = json_decode($upload_data, true);
$upload_data['filesystemURI'] = $filesystemURI;
$upload_data['cid'] = $cid;
$upload_data['sid'] = $sid;

if (!isset($group)){
    $URL = $databaseURI . "/group/user/{$uid}/exercisesheet/{$sid}";
    $group = http_get($URL, true);
    $group = json_decode($group, true);
    $upload_data['group'] = $group;
}

$user_course_data = $upload_data['user'];
Authentication::checkRights(PRIVILEGE_LEVEL::STUDENT, $cid, $uid, $user_course_data);

if (isset($upload_data['exerciseSheet']['endDate']) && isset($upload_data['exerciseSheet']['startDate'])){
    // bool if endDate of sheet is greater than the actual date
    $isExpired = date('U') > date('U', $upload_data['exerciseSheet']['endDate']); 

    // bool if startDate of sheet is greater than the actual date
    $hasStarted = date('U') > date('U', $upload_data['exerciseSheet']['startDate']);
    if ($isExpired){
        set_error("Der Übungszeitraum ist am ".date('d.m.Y  -  H:i', $upload_data['exerciseSheet']['endDate'])." abgelaufen!");
    } elseif (!$hasStarted){
        set_error("Der Übungszeitraum beginnt am ".date('d.m.Y  -  H:i', $upload_data['exerciseSheet']['startDate'])."!");
    }
    
} else
    set_error("Kein Übungszeitraum gefunden!");

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

$user_course_data = $upload_data['user'];
$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::STUDENT);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Student.php?cid={$cid}",
               "notificationElements" => $notifications,
               "navigationElement" => $menu));


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

$w = new HTMLWrapper($h, $t);
$w->set_config_file('include/configs/config_upload_exercise.json');
$w->show();
?>
