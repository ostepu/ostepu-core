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
            
            #region Form to PDF
            if(isset($exercise['choices']) && $exercise['choices']!==''){
                $formdata = file_get_contents('FormSample.json');
                $formdata = Form::decodeForm($formdata);
                
                if (!is_array($formdata))$formdata=array($formdata);
                $data = array();
                foreach ($formdata as $value){
                    $data[$value->getExerciseId()] = $value;
                }
                
                $answer="";
                if ($data[$exerciseId]->getType()==0) $answer = cleanInput($exercise['choices']);
                if ($data[$exerciseId]->getType()==1) $answer = cleanInput($data[$exerciseId]->getChoices()[$exercise['choices']]->getText());
                if ($data[$exerciseId]->getType()==2)
                    foreach($exercise['choices'] as $chosen)
                        $answer.=cleanInput($data[$exerciseId]->getChoices()[intval($chosen)]->getText())."<br>";
                
                //$exerciseId
                $Text=    "<h1>AUFGABE ".$exercise['name']."</h1>".
                        "<hr>".
                        "<p>".
                        "<h2>Aufgabenstellung:</h2>".
                        $data[$exerciseId]->getTask().
                        "</p>".
                        "<p>".
                        "<h2>Antwort:</h2>".
                        $answer.
                        "</p>";
                        
                $pdf = Pdf::createPdf($Text);
                $URL = $filesystemURI."/pdf";
                $pdf = File::decodeFile(http_post_data($URL, Pdf::encodePdf($pdf), false, $message));
                $pdf->setDisplayName($exercise['name'].".pdf");
                $pdf->setTimeStamp($timestamp);
            }
            #endregion

            if (isset($_FILES[$fileName]) || (isset($exercise['choices']) && $exercise['choices']!=='')) {
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
                    if (isset($_FILES[$fileName])){
                        $filePath = $file['tmp_name'];
                        $uploadFile = File::createFile(null,$file['name'],null,$timestamp,null,null);
                        $uploadFile->setBody(base64_encode(file_get_contents($file['tmp_name'])));
                    }
                    else
                        $uploadFile=$pdf;

                    $uploadSubmission = Submission::createSubmission(null,$uid,null,$exerciseId,$exercise['comment'],1,$timestamp,null,$leaderId);
                    $uploadSubmission->setFile($uploadFile);
                    $uploadSubmission->setSelectedForGroup('1');

                    $URL = "http://localhost/uebungsplattform/logic/LProcessor/submission";
                    $result = http_post_data($URL, Submission::encodeSubmission($uploadSubmission), true, $message);

                    if ($message != "201") {
                        $exercise = $key + 1;
                        $errormsg = "{$message}: Aufgabe ".$exercise['name']." konnte nicht hochgeladen werden.";
                        $notifications[] = MakeNotification('error',
                                                            $errormsg);
                        continue;
                    }

                    $msg = "Aufgabe ".$exercise['name']." wurde erfolgreich eingesendet.";
                    $notifications[] = MakeNotification('success',
                                                        $msg);
                }
            }
        }
    }
}

// load user data from the database
$URL = $getSiteURI . "/upload/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
$upload_data = http_get($URL, true);
$upload_data = json_decode($upload_data, true);
$upload_data['filesystemURI'] = $filesystemURI;
$upload_data['cid'] = $cid;
$upload_data['sid'] = $sid;

//$formdata = file_get_contents('FormSample.json');
$URL = "http://localhost/uebungsplattform/DB/DBForm/form/exercisesheet/{$sid}";
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

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Student.php?cid={$cid}",
               "notificationElements" => $notifications));


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
