<?php
/**
 * @file Student.php
 * Constructs the page that is displayed to a student.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

if (isset($_POST['action'])) {
    if ($_POST['action'] == "ExerciseSheetStudent" && isset($_POST['downloadAttachments'])) {
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
    } elseif ($_POST['action'] == "ExerciseSheetStudent" && isset($_POST['deleteSubmission'])) {
        $suid = cleanInput($_POST['deleteSubmission']);

        $URI = $databaseURI . "/selectedsubmission/submission/" . $suid;
        http_delete($URI, true, $message);

        if ($message == "201") {
            $notifications[] = MakeNotification("success", "Die Einsendung wurde gelöscht!");
        } else {
            $notifications[] = MakeNotification("error", "Beim Löschen ist ein Fehler aufgetreten!");
        }
    } elseif ($_POST['action'] == "ExerciseSheetStudent" && isset($_POST['downloadMarkings'])) {
        $sid = cleanInput($_POST['downloadAttachments']);
        
        $markings = http_get($serverURI . '/logic/Controller/DB/marking/exercisesheet/' . $sid . '/user/' . $uid);
        $markings = json_decode($markings, true);

        $files = array();
        foreach ($markings as $marking) {
            $files[] = $marking['file'];
        }

        $fileString = json_encode($files);

        $zipfile = http_post_data($filesystemURI . '/' . 'zip',  $fileString);
        $zipfile = json_decode($zipfile, true);

        $location = $filesystemURI . '/' . $zipfile['address'];
        header("Location: {$location}/markings.zip");
    }
}

// load tutor data from GetSite
$URI = $getSiteURI . "/student/user/{$uid}/course/{$cid}";
$student_data = http_get($URI, true);
$student_data = json_decode($student_data, true);
$student_data['filesystemURI'] = $filesystemURI;
$student_data['cid'] = $cid;

$user_course_data = $student_data['user'];

// check userrights for course
Authentication::checkRights(PRIVILEGE_LEVEL::STUDENT, $cid, $uid, $user_course_data);

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::STUDENT);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "notificationElements" => $notifications,
               "navigationElement" => $menu));
/**
 * @todo also display the group leader
 * @todo fix attachment downloads
 */
$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetStudent.template.html');
$t->bind($student_data);

$w = new HTMLWrapper($h, $t);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $t);
$w->set_config_file('include/configs/config_student_tutor.json');
$w->show();

?>
