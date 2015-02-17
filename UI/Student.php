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

$sheetNotifications = array();

if (isset($_POST['deleteSubmissionWarning'])) {
    $notifications[] = MakeNotification("warning", "Soll die Einsendung wirklich gelöscht werden?");
} elseif (isset($_POST['deleteSubmission'])) {
    $suid = cleanInput($_POST['deleteSubmission']);
    
    // extractes the studentId of the submission
    $URI = $databaseURI . "/submission/" . $suid;
    $submission = http_get($URI, true);                  
    $submission = json_decode($submission, true);
                    
    // only deletes the submission if it belongs to the user
    if ($submission['studentId'] == $uid) {
        $URI = $databaseURI . "/selectedsubmission/submission/" . $suid;
        http_delete($URI, true, $message);

        if ($message == "201") {
            $notifications[] = MakeNotification("success", "Die Einsendung wurde gelöscht!");
        } else {
            $notifications[] = MakeNotification("error", "Beim Löschen ist ein Fehler aufgetreten!");
        }
    }

} elseif (isset($_POST['downloadMarkings'])) {
    downloadMarkingsForSheet($uid, $_POST['downloadMarkings']);
}

// load tutor data from GetSite
$URI = $getSiteURI . "/student/user/{$uid}/course/{$cid}";
$student_data = http_get($URI, true);
$student_data = json_decode($student_data, true);
$student_data['filesystemURI'] = $filesystemURI;
$student_data['cid'] = $cid;
$student_data['uid'] = $uid;

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
$h->bind($student_data);

/*$hp = Template::WithTemplateFile('include/Header/Extra-Info-Student.template.html');
$hp->bind($user_course_data);
$hp->bind(array("name" => $user_course_data['courses'][0]['course']['name']));
$hp->bind($student_data);*/
               
$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetStudent.template.html');
$t->bind($student_data);

$w = new HTMLWrapper($h, $t);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $t);
$w->set_config_file('include/configs/config_student_tutor.json');
$w->show();

?>
