<?php
/**
 * @file TutorAssign.php
 * Constructs the page for managing tutor assignments.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/include/FormEvaluator.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/LArraySorter.php';

if (!isset($_POST['actionSortUsers']))
if (isset($_POST['action'])) {

    if (isset($_POST['action']) && $_POST['action'] == "AssignManually") {
    
        // automatically assigns all unassigned proposed submissions to tutors
        if (isset($_POST['actionAssignAllProposals'])){
            // load user data from the database
            $URL = $getSiteURI . "/tutorassign/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
            $tutorAssign_data = http_get($URL, true);
            $tutorAssign_data = json_decode($tutorAssign_data, true);
            $markings = array();
            if (!empty($tutorAssign_data['tutorAssignments'])) {
                foreach ($tutorAssign_data['tutorAssignments'] as $tutorAssignment) {
                    if (isset($tutorAssignment['proposalSubmissions'])){
                        foreach ($tutorAssignment['proposalSubmissions'] as $submission){
                            $sub = Submission::decodeSubmission(Submission::encodeSubmission($submission));
                            $marking = new Marking();
                            $marking->setSubmission($sub);
                            $marking->setStatus(1);
                            $marking->setTutorId($tutorAssignment['tutor']['id']);
                            $markings[] = $marking;
                        }
                    }
                }
            }
                        
            $URI = $serverURI . "/logic/LMarking/marking";
            http_post_data($URI, Marking::encodeMarking($markings), true, $message);

            if ($message == "201" || $message == "200") {
                $msg = "Die Zuweisungen wurden erfolgreich geändert.";
                $assignManuallyNotifications[] = MakeNotification("success", $msg);
            } else {
                $msg = "Bei der Zuweisung ist ein Fehler aufgetreten.";
                $assignManuallyNotifications[] = MakeNotification("error", $msg);
            }        
        }
        
        // assigns manually chosen submissions to the selected tutor
        if (isset($_POST['actionAssignManually'])){
            $f = new FormEvaluator($_POST);

            $f->checkIntegerForKey('tutorId',
                                   FormEvaluator::REQUIRED,
                                   'warning',
                                   'Ungültiger Tutor.');
                                           
            $f->checkArrayOfArraysForKey('assign',
                                       FormEvaluator::REQUIRED,
                                       'warning',
                                       'Ungültige Auswahl.');

            if ($f->evaluate(true)) {
                // extracts the php POST data
                $foundValues = $f->foundValues;
                $selectedTutorID = $foundValues['tutorId'];
                $assigns=cleanInput($_POST['assign']);
                $markings = array();
                foreach($assigns as $owner => $ass){
                    $markingList = isset($ass['marking']) ? $ass['marking'] : array();
                    $proposals = isset($ass['proposal']) ? $ass['proposal'] : array();

                    // change assignment only if different source and target
                    if ($owner!=$selectedTutorID){
                        foreach ($markingList as $markingId => $subs){
                            $subs = $subs[0];
                            $sub = new Submission();
                            $sub->setId($subs);
                            if ($owner==-1){
                                // from unassigned to tutor (creates new marking)
                                $marking = new Marking();
                                $marking->setSubmission($sub);
                                $marking->setStatus(1);
                                $marking->setTutorId($selectedTutorID);
                                $markings[] = $marking;
                            } else {
                                if ($selectedTutorID==-1){
                                    // remove assignment from tutor (removes the specified marking)
                                    $URI = $serverURI . "/logic/LMarking/marking/marking/".$markingId;
                                    http_delete($URI, true, $message);
                                } else {
                                    // move assignment from tutor to tutor
                                    $marking = new Marking();
                                    $marking->setId($markingId);
                                    $marking->setTutorId($selectedTutorID);
                                    $markings[] = $marking;
                                }
                            }
                        }
                    }
                    
                    // "unassigned" can't obtain proposals (-1 -> "unassiged")
                    if ($selectedTutorID!=-1){
                        foreach ($proposals as $props){
                            // assign to selected tutor
                            $sub = new Submission();
                            $sub->setId($props);
                            $marking = new Marking();
                            $marking->setSubmission($sub);
                            $marking->setStatus(1);
                            $marking->setTutorId($selectedTutorID);
                            $markings[] = $marking;
                        }
                    }
                }
                
                $URI = $serverURI . "/logic/LMarking/marking";
                http_post_data($URI, Marking::encodeMarking($markings), true, $message);

                if ($message == "201" || $message == "200") {
                    $msg = "Die Zuweisungen wurden erfolgreich geändert.";
                    $assignManuallyNotifications[] = MakeNotification("success", $msg);
                } else {
                    $msg = "Bei der Zuweisung ist ein Fehler aufgetreten.";
                    $assignManuallyNotifications[] = MakeNotification("error", $msg);
                }         
            }  else {
                if (!isset($assignManuallyNotifications))
                    $assignManuallyNotifications = array();
                $assignManuallyNotifications = $assignManuallyNotifications + $f->notifications;
            }
        }
    }
    
    
    // automatically assigns all unassigned submissions to the selected tutors
    if (isset($_POST['action']) && $_POST['action'] == "AssignAutomatically") {

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
                $assignAutomaticallyNotifications[] = MakeNotification("success", $msg);
            } else {
                $msg = "Bei der Zuweisung ist ein Fehler aufgetreten.";
                $assignAutomaticallyNotifications[] = MakeNotification("error", $msg);
            }
        }  else {
            if (!isset($assignAutomaticallyNotifications))
                $assignAutomaticallyNotifications = array();
            $assignAutomaticallyNotifications = $assignAutomaticallyNotifications + $f->notifications;
        }
    }

    // removes all tutor assignments by deleting all markings of the exercisesheet
    if ($_POST['action'] == "AssignRemoveWarning") {
        $assignRemoveNotifications[] = MakeNotification("warning", "Sollen die Zuweisungen wirklich aufgehoben werden?<br>Dabei werden alle bisherigen Korrekturen entfernt!!!");
    } elseif ($_POST['action'] == "AssignRemove") {
        $URI = $databaseURI . "/marking/exercisesheet/" . $sid;
        http_delete($URI, true, $message);

        if ($message == "201") {
            $msg = "Die Zuweisungen wurden erfolgreich aufgehoben.";
            $assignRemoveNotifications[] = MakeNotification("success", $msg);
        } else {
            $msg = "Beim Aufheben der Zuweisungen ist ein Fehler aufgetreten.";
            $assignRemoveNotifications[] = MakeNotification("error", $msg);
        }
    }
}

// load user data from the database
$URL = $getSiteURI . "/tutorassign/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
$tutorAssign_data = http_get($URL, true);
$tutorAssign_data = json_decode($tutorAssign_data, true);

foreach ($tutorAssign_data['tutorAssignments'] as $key2 => $tutorAssignment){
    if (count($tutorAssign_data['tutorAssignments'][$key2]['submissions'])>0){
        $assignments = $tutorAssignment['submissions'];
        $dataList = array();
        foreach ($assignments as $key => $submission)
            $dataList[] = array('pos' => $key,'userName'=>$submission['user']['userName'],'lastName'=>$submission['user']['lastName'],'firstName'=>$submission['user']['firstName']);
        $sortTypes = array('lastName','firstName','userName');
        if (!isset($_POST['sortUsers'])) $_POST['sortUsers'] = null;
        $_POST['sortUsers'] = (in_array($_POST['sortUsers'],$sortTypes) ? $_POST['sortUsers'] : $sortTypes[0]);
        $sortTypes = array('lastName','firstName','userName');
        $dataList=LArraySorter::orderby($dataList, $_POST['sortUsers'], SORT_ASC,$sortTypes[(array_search($_POST['sortUsers'],$sortTypes)+1)%count($sortTypes)], SORT_ASC);
        $tempData = array();
        foreach($dataList as $data)
            $tempData[] = $tutorAssign_data['tutorAssignments'][$key2]['submissions'][$data['pos']];
            
        $tutorAssign_data['tutorAssignments'][$key2]['submissions'] = $tempData;
    }
    if (!isset($tutorAssign_data['tutorAssignments'][$key2]['proposalSubmissions'])) continue;
    $assignments = $tutorAssignment['proposalSubmissions'];
    $dataList = array();
    foreach ($assignments as $key => $submission)
        $dataList[] = array('pos' => $key,'userName'=>$submission['user']['userName'],'lastName'=>$submission['user']['lastName'],'firstName'=>$submission['user']['firstName']);
    $sortTypes = array('lastName','firstName','userName');
    if (!isset($_POST['sortUsers'])) $_POST['sortUsers'] = null;
    $_POST['sortUsers'] = (in_array($_POST['sortUsers'],$sortTypes) ? $_POST['sortUsers'] : $sortTypes[0]);
    $sortTypes = array('lastName','firstName','userName');
    $dataList=LArraySorter::orderby($dataList, $_POST['sortUsers'], SORT_ASC,$sortTypes[(array_search($_POST['sortUsers'],$sortTypes)+1)%count($sortTypes)], SORT_ASC);
    $tempData = array();
    foreach($dataList as $data)
        $tempData[] = $tutorAssign_data['tutorAssignments'][$key2]['proposalSubmissions'][$data['pos']];
        
    $tutorAssign_data['tutorAssignments'][$key2]['proposalSubmissions'] = $tempData;

}

function custom_sort($a,$b) {
    if (!isset($a['tutor']['courses']['0']['status'])) return 1;
    if (!isset($b['tutor']['courses']['0']['status'])) return 1;
    if ($a['tutor']['courses']['0']['status']==$b['tutor']['courses']['0']['status']){
        if (!isset($a['tutor']['lastName']) || !isset($a['tutor']['lastName'])) return 0;
        return $a['tutor']['lastName']>$b['tutor']['lastName'];
    } else
        return $a['tutor']['courses']['0']['status']>$b['tutor']['courses']['0']['status'];
}
usort($tutorAssign_data['tutorAssignments'], "custom_sort");

$user_course_data = $tutorAssign_data['user'];

if (isset($_POST['sortUsers'])) {
    $tutorAssign_data['sortUsers'] = $_POST['sortUsers'];
}

// check userrights for course
Authentication::checkRights(1, $cid, $uid, $user_course_data);
Authentication::checkRights(PRIVILEGE_LEVEL::TUTOR, $cid, $uid, $user_course_data);
$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::TUTOR,true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "notificationElements" => $notifications,
               "navigationElement" => $menu));

// construct a content element for assigning tutors automatically
$assignAutomatically = Template::WithTemplateFile('include/TutorAssign/AssignAutomatically.template.html');
$assignAutomatically->bind($tutorAssign_data);
if (isset($assignAutomaticallyNotifications))
    $assignAutomatically->bind(array("AssignAutomaticallyNotificationElements" => $assignAutomaticallyNotifications));

// construct a content element for assigning tutors manually
$assignManually = Template::WithTemplateFile('include/TutorAssign/AssignManually.template.html');
$assignManually->bind($tutorAssign_data);
if (isset($assignManuallyNotifications))
    $assignManually->bind(array("AssignManuallyNotificationElements" => $assignManuallyNotifications));

// construct a content element for removing assignments from tutors
$assignRemove = Template::WithTemplateFile('include/TutorAssign/AssignRemove.template.html');
if (isset($assignRemoveNotifications))
    $assignRemove->bind(array("AssignRemoveNotificationElements" => $assignRemoveNotifications));

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $assignAutomatically, $assignManually, $assignRemove);
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $assignAutomatically);
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $assignManually);
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $assignRemove);
$w->set_config_file('include/configs/config_tutor_assign.json');
//$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
