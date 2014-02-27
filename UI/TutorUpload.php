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

            if (isset($_FILES[$fileName])) {
                $file = $_FILES[$fileName];
                $error = $file['error'];

                if ($error === 0) {
                    $filePath = $file['tmp_name'];
                    $displayName = $file['name'];

                    // upload the file to the filesystem
                    $jsonFile = fullUpload($filesystemURI,
                                           $databaseURI,
                                           $filePath,
                                           $displayName,
                                           $timestamp,
                                           $message);

                    if (($message != "201") && ($message != "200")) {
                        // saving failed
                        $exercise = $key + 1;
                        $errormsg = "{$message}: Aufgabe {$exercise} konnte nicht hochgeladen werden.";
                        $notifications[] = MakeNotification('error',
                                                            $errormsg);
                        continue;
                    } else {
                        // saving succeeded
                        $fileObj = json_decode($jsonFile, true);
                    }

                    $fileId = $fileObj['fileId'];

                    // create a new submission with the file
                    $comment = cleanInput($exercise['comment']);
                    $returnedSubmission = submitFile($databaseURI,
                                                     $uid,
                                                     $fileId,
                                                     $exerciseId,
                                                     $comment,
                                                     $timestamp,
                                                     $message);

                    if ($message != "201") {
                        $exercise = $key + 1;
                        $errormsg = "{$message}: Aufgabe {$exercise} konnte nicht hochgeladen werden.";
                        $notifications[] = MakeNotification('error',
                                                            $errormsg);
                        continue;
                    }

                    $returnedSubmission = json_decode($returnedSubmission, true);

                    // make the submission selected
                    $submissionId = $returnedSubmission['id'];
                    $returnedSubmission = updateSelectedSubmission($databaseURI,
                                                                   $leaderId,
                                                                   $submissionId,
                                                                   $exerciseId,
                                                                   $message);

                    if ($message != "201") {
                        $exercise = $key + 1;
                        $errormsg = "{$message}: Aufgabe {$exercise} konnte nicht ausgewählt werden.";
                        $notifications[] = MakeNotification('error',
                                                            $errormsg);
                        continue;
                    }

                    $exercise = $key + 1;
                    $msg = "Aufgabe {$exercise} wurde erfolgreich eingesendet.";
                    $notifications[] = MakeNotification('success',
                                                        $msg);
                }
            }
        }
    }
}

// load user data from the database
/**
 * @todo Use TutorUpload data. 
 */
$URL = $getSiteURI . "/upload/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
$tutorUpload_data = http_get($URL, false);
$tutorUpload_data = json_decode($tutorUpload_data, true);
$tutorUpload_data['filesystemURI'] = $filesystemURI;
$tutorUpload_data['sid'] = $sid;

$user_course_data = $tutorUpload_data['user'];

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::ADMIN,
                              true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "notificationElements" => $notifications));

// construct a content element for uploading markings
$tutorUpload = Template::WithTemplateFile('include/TutorUpload/TutorUpload.template.html');
$tutorUpload->bind($tutorUpload_data);

$w = new HTMLWrapper($h, $tutorUpload);
$w->set_config_file('include/configs/config_upload_exercise.json');
$w->show();
?>
