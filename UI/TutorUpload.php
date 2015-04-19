<?php
/**
 * @file TutorUpload.php
 * Shows a form for tutors for uploading markings.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';
include_once '../Assistants/Structures.php';
include_once '../Assistants/MimeReader.php';

if (isset($_POST['action']) && $_POST['action'] == 'TutorUpload') {
    if (isset($_FILES['MarkingFile'])) {
        $file = $_FILES['MarkingFile'];
        $error = $file['error'];

        if ($error == 0) {
            $filePath = $file['tmp_name'];
            $displayName = $file['name'];
            $type = $file["type"];

            // checks file ending
            if (MimeReader::get_mime($filePath) == "application/zip") {

                // creates the JSON object containing the file
                $data = file_get_contents($filePath);

                $data = base64_encode($data);

                $file = array('timeStamp' => time(),
                              'displayName' => $displayName,
                              'body' => $data);

                $file = json_encode($file);

                // sends the JSON object to the logic
                $URI = $logicURI . "/tutor/user/{$uid}/course/{$cid}";
                ///echo $file;echo $URI;return;
                $error = http_post_data($URI, $file, true, $message);

                if ($message == "201" || $message == "200") {
                    $successmsg = "Die Datei wurde hochgeladen.";
                    $notifications[] = MakeNotification('success',
                                                        $successmsg);
                } else {
                    $notifications[] = MakeNotification('error',
                                                        $error);
                }
            } else {
                $errormsg = "Es handelt sich nicht um ein *.zip-Archiv.";
                $notifications[] = MakeNotification('error',
                                                    $errormsg);
            }
        }
    }
}

// load tutorUpload data from GetSite
$URL = $getSiteURI . "/tutorupload/user/{$uid}/course/{$cid}";
$tutorUpload_data = http_get($URL, true);
$tutorUpload_data = json_decode($tutorUpload_data, true);
$tutorUpload_data['filesystemURI'] = $filesystemURI;
$tutorUpload_data['cid'] = $cid;

$user_course_data = $tutorUpload_data['user'];
Authentication::checkRights(PRIVILEGE_LEVEL::TUTOR, $cid, $uid, $user_course_data);
$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::TUTOR,
                              true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => (isset($user_course_data['courses'][0]['course']['name']) ? $user_course_data['courses'][0]['course']['name'] : null),
               "notificationElements" => $notifications,
               "navigationElement" => $menu));

// construct a content element for uploading markings
$tutorUpload = Template::WithTemplateFile('include/TutorUpload/TutorUpload.template.html');
$tutorUpload->bind($tutorUpload_data);

$w = new HTMLWrapper($h, $tutorUpload);
$w->set_config_file('include/configs/config_upload_exercise.json');
$w->show();
