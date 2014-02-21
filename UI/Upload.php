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
include_once 'include/FormEvealuator.php';
include_once '../Assistants/Structures.php';

// load user data from the database
$URL = $getSiteURI . "/upload/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
$upload_data = http_get($URL, false);
$upload_data = json_decode($upload_data, true);
$upload_data['filesystemURI'] = $filesystemURI;
$upload_data['cid'] = $cid;
$upload_data['sid'] = $cid;

$user_course_data = $upload_data['user'];

if (isset($_POST['action'])) {
    // handle uploading files
    /**
     * @todo add error handling
     * @todo make the submission selected
     * @todo don't automatically accept the submission
     */
    foreach ($_POST['exercises'] as $key => $exercise) {
        $exerciseId = cleanInput($exercise['exerciseID']);
        $fileName = "file{$exerciseId}";

        if (isset($_FILES[$fileName])) {
            $file = $_FILES[$fileName];
            $error = $file['error'];

            if ($error === 0) {
                $filePath = $file['tmp_name'];
                $data = file_get_contents($filePath);
                $data = base64_encode($data);

                $displayName = $file['name'];

                $timestamp = time();

                $fileObj = array('timestamp' => $timestamp,
                                 'displayName' => $displayName,
                                 'body' => $data);

                // upload the file to the filesystem
                $URL = $filesystemURI . '/file';
                $fileObj = http_post_data($URL, json_encode($fileObj), true);
                print $fileObj;
                $fileObj = json_decode($fileObj, true);


                $fileObj['timestamp'] = $timestamp;

                // save the file in the datebase
                $URL = $databaseURI . '/file';
                $fileObj = http_post_data($URL, json_encode($fileObj), true);
                print $fileObj;
                $fileObj = json_decode($fileObj, true);

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
                                                     true);
            }
        }
    }
}

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
