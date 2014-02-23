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
     * @todo clean up the code a bit.
     */
    $timestamp = time();

    $URL = $databaseURI . '/group/user/' . $uid . '/exercisesheet/' . $sid;
    $group = http_get($URL, true);
    $group = json_decode($group, true)[0];

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
                $jsonFile = uploadFileToFileSystem($filesystemURI,
                                                   $filePath,
                                                   $displayName,
                                                   $timestamp,
                                                   $message);
                if ($message != "201") {
                    // upload failed generate notification and continue with
                    // the next exercise
                    $exercise = $key + 1;
                    $errormsg = "{$message}: Aufgabe {$exercise} konnte nicht hochgeladen werden.";
                    $notifications[] = MakeNotification('error',
                                                        $errormsg);
                    continue;
                }

                $fileObj = json_decode($jsonFile, true);

                $fileObj['timeStamp'] = $timestamp;

                // save the file in the datebase
                $jsonFile = saveFileInDatabase($databaseURI, $fileObj, $message);

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
                $submission = Submission::createSubmission(NULL,
                                                           $uid,
                                                           $fileId,
                                                           $exerciseId,
                                                           $comment,
                                                           1,
                                                           $timestamp,
                                                           NULL,
                                                           NULL);
                $URL = $databaseURI . '/submission';
                $returnedSubmission = http_post_data($URL,
                                                     json_encode($submission),
                                                     true,
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
            }
        }
    }
}

// load user data from the database
$URL = $getSiteURI . "/upload/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
$upload_data = http_get($URL, false);
$upload_data = json_decode($upload_data, true);
$upload_data['filesystemURI'] = $filesystemURI;
$upload_data['cid'] = $cid;
$upload_data['sid'] = $sid;

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
