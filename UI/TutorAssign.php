<?php
/**
 * @file TutorAssign.php
 * Constructs the page for managing tutor assignments.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';
include_once 'include/FormEvaluator.php';

if (isset($_POST['action'])) {
    // automatically assigns all unassigned submissions to the selected tutors
    if ($_POST['action'] == "AssignAutomatically") {

        $f = new FormEvaluator($_POST);

        $f->checkArrayOfIntegersForKey('tutorIds',
                                       FormEvaluator::REQUIRED,
                                       'warning',
                                       'Ungültige Tutoren.');

        if ($f->evaluate(true)) {
            // extracts the php POST data
            $foundValues = $f->foundValues;
            $selectedTutorIDs = $foundValues['tutorIds'];

            $data = array('tutors' => array(),
                          'unassigned' => array());

            // load user data from the database for the first time
            $URL = $getSiteURI . "/tutorassign/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
            $tutorAssign_data = http_get($URL, false);
            $tutorAssign_data = json_decode($tutorAssign_data, true);

            // adds all tutors that are selected in the form to the request body
            foreach ($selectedTutorIDs as $tutorID) {
                $newTutor = array('tutorId' => $tutorID);
                $data['tutors'][] = $newTutor;
            }

            // adds all unassigned submissions to the request body
            if (!empty($tutorAssign_data['tutorAssignments'])) {
                foreach ($tutorAssign_data['tutorAssignments'] as $tutorAssignment) {
                    if ($tutorAssignment['tutor']['userName'] == "unassigned") {
                        foreach ($tutorAssignment['submissions'] as $submission) {
                            $submission['id'] = $submission['submissionId'];

                            unset($submission['submissionId']);
                            unset($submission['unassigned']);

                            $data['unassigned'][] = $submission;
                        }
                    }
                }
            }

            $data = json_encode($data);

            $URI = $logicURI . "/tutor/auto/group/course/{$cid}/exercisesheet/{$sid}";
            http_post_data($URI, $data, true, $message);

            if ($message == "201" || $message == "200") {
                $msg = "Die Zuweisungen wurden erfolgreich geändert.";
                $notifications[] = MakeNotification("success", $msg);
            } else {
                $msg = "Bei der Zuweisung ist ein Fehler aufgetreten.";
                $notifications[] = MakeNotification("error", $msg);
            }
        }  else {
            $notifications = $notifications + $f->notifications;
        }
    }

    // removes all tutor assignments by deleting all markings of the exercisesheet
    if ($_POST['action'] == "AssignRemove") {
        $URI = $databaseURI . "/marking/exercisesheet/" . $sid;
        http_delete($URI, true, $message);

        if ($message == "201") {
            $msg = "Die Zuweisungen wurden erfolgreich aufgehoben.";
            $notifications[] = MakeNotification("success", $msg);
        } else {
            $msg = "Beim Aufheben der Zuweisungen ist ein Fehler aufgetreten.";
            $notifications[] = MakeNotification("error", $msg);
        }
    }
}

// load user data from the database
$URL = $getSiteURI . "/tutorassign/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
$tutorAssign_data = http_get($URL, false);
$tutorAssign_data = json_decode($tutorAssign_data, true);

$user_course_data = $tutorAssign_data['user'];

// check userrights for course
Authentication::checkRights(1, $cid, $uid, $user_course_data);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "notificationElements" => $notifications));

// construct a content element for assigning tutors automatically
$assignAutomatically = Template::WithTemplateFile('include/TutorAssign/AssignAutomatically.template.html');
$assignAutomatically->bind($tutorAssign_data);

// construct a content element for assigning tutors manually
$assignManually = Template::WithTemplateFile('include/TutorAssign/AssignManually.template.html');
$assignManually->bind($tutorAssign_data);

// construct a content element for removing assignments from tutors
$assignRemove = Template::WithTemplateFile('include/TutorAssign/AssignRemove.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $assignAutomatically, $assignManually, $assignRemove);
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $assignAutomatically);
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $assignRemove);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
