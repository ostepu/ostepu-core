<?php
/**
 * @file Tutor.php
 * Constructs the page that is displayed to a tutor.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

if (isset($_POST['action'])) {
    if ($_POST['action'] == "ExerciseSheetTutor" && isset($_POST['downloadAttachments'])) {
        $sid = cleanInput($_POST['downloadAttachments']);

        $attachments = http_get($serverURI . '/logic/Controller/DB/attachment/exercisesheet/' . $sid);
        $attachments = json_decode($attachments, true);

        $files = array();
        foreach ($attachments as $attachment) {
            $files[] = $attachment['file'];
        }

        $fileString = json_encode($files);

        $zipfile = http_post_data($filesystemURI . '/' . 'zip',  $fileString);
        $zipfile = json_decode($zipfile, true);

        $location = $filesystemURI . '/' . $zipfile['address'];
        header("Location: {$location}/attachments.zip");
    }
}

$requiredPrivilege = PRIVILEGE_LEVEL::TUTOR;

// load tutor data from GetSite
$URI = $getSiteURI . "/tutor/user/{$uid}/course/{$cid}";
$tutor_data = http_get($URI, true);
$tutor_data = json_decode($tutor_data, true);
$tutor_data['filesystemURI'] = $filesystemURI;
$tutor_data['cid'] = $cid;

$user_course_data = $tutor_data['user'];

// check userrights for course
Authentication::checkRights(PRIVILEGE_LEVEL::TUTOR, $cid, $uid, $user_course_data);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "notificationElements" => $notifications));

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetTutor.template.html');
$t->bind($tutor_data);

$w = new HTMLWrapper($h, $t);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $t);
$w->set_config_file('include/configs/config_student_tutor.json');
$w->show();

?>
