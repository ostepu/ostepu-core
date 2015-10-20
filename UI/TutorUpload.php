<?php
/**
 * @file TutorUpload.php
 * Shows a form for tutors for uploading markings.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/Language.php';
include_once dirname(__FILE__) . '/../Assistants/MimeReader.php';

$langTemplate='TutorUpload_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

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
                $file = new File();
                $file->setBody( Reference::createReference($filePath) );
                $file->setTimeStamp(time());
                $file->setDisplayName($displayName);

                $file = File::encodeFile($file);

                // sends the JSON object to the logic
                $URI = $logicURI . "/tutor/user/{$uid}/course/{$cid}";

                $error = http_post_data($URI, $file, true, $message);

                if ($message == "201" || $message == "200") {
                    $successmsg = Language::Get('main','sucessFileUpload', $langTemplate);
                    $notifications[] = MakeNotification('success',
                                                        $successmsg);
                } else {
                    $notifications[] = MakeNotification('error',
                                                        $error);
                }
            } else {
                $errormsg = Language::Get('main','invalidFileType', $langTemplate);
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
