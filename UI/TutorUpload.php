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

// if (isset($_POST['action']) && $_POST['action'] == 'TutorUpload') {
//     //$fileName = "MarkingFile";
//     //if (isset($_FILES[$fileName])) {
//         $file = $_FILES['MarkingFile'];
//         $error = $file['error'];

//         if ($error == 0) {
//             $filePath = $file['tmp_name'];
//             $displayName = $file['name'];

//             // creates the JSON object containing the file
//             $data = file_get_contents($filePath);
//             $data = base64_encode($data);

//             $file = array('timeStamp' => $timestamp,
//                           'displayName' => $displayName,
//                           'body' => $data);

//             $notifications[] = MakeNotification('success', $filePath);

//             // tutor/user/:userid/exercisesheet/:sheetid(/)


//             // if ($message == "201") {
//             //     $errormsg = "Die Datei wurde hochgeladen.";
//             //     $notifications[] = MakeNotification('success',
//             //                                         $errormsg);
//             // } else {
//             //     $errormsg = "Beim Hochladen ist ein Fehler aufgetreten.";
//             //     $notifications[] = MakeNotification('error',
//             //                                         $errormsg);
//             // }
//         }
//     //}
// }

// load tutorUpload data from GetSite
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
