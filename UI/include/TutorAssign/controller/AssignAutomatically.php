<?php
/**
 * @file AssignAutomatically.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */


// automatically assigns all unassigned submissions to the selected tutors
set_time_limit(180);
$postAssignAutomaticallyValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('tutorIds',
           ['satisfy_exists',
            'satisfy_not_empty',
            'is_array',
            'on_error'=>['type'=>'warning',
                         'text'=>Language::Get('main','noTutorSelected', $langTemplate)]])
  ->addSet('tutorIds',
           ['perform_this_array'=>[[['key_all'],
                                    ['valid_identifier']]],
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidTutors', $langTemplate)]]);
$foundValues = $postAssignAutomaticallyValidation->validate();
$notifications = array_merge($notifications,$postAssignAutomaticallyValidation->getPrintableNotifications('MakeNotification'));
$postAssignAutomaticallyValidation->resetNotifications()->resetErrors();

if ($postAssignAutomaticallyValidation->isValid()) {
// extracts the php POST data
$selectedTutorIDs = $foundValues['tutorIds'];

$data = array('tutors' => array(),
              'unassigned' => array(),
              'assigned' => array());

    // load user data from the database for the first time
    $URL = $getSiteURI . "/tutorassign/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
    $tutorAssign_data = http_get($URL, false);
    $tutorAssign_data = json_decode($tutorAssign_data, true);

    // adds all tutors that are selected in the form to the request body
    foreach ($selectedTutorIDs as $tutorID) {
        $newTutor = array('tutorId' => $tutorID);
        $data['tutors'][] = $newTutor;
    }
    
    $fromTutor = isset($_POST['fromTutor']) ? $_POST['fromTutor'] : null;
    
    if (isset($fromTutor) && !empty($tutorAssign_data['tutorAssignments'])){
        if ($fromTutor == -1){ // "unzugeordneter" Kontrolleur
            // adds all unassigned submissions to the request body
            foreach ($tutorAssign_data['tutorAssignments'] as $tutorAssignment) {
                if ($tutorAssignment['tutor']['userName'] === 'unassigned') {
                    foreach ($tutorAssignment['submissions'] as $submission) {
                        unset($submission['unassigned']);
                        $data['unassigned'][] = $submission;
                    }
                }
            }
        } elseif($fromTutor == 'u') { // unbekannter Kontrolleur
            foreach ($tutorAssign_data['tutorAssignments'] as $tutorAssignment) {
                if (!isset($tutorAssignment['tutor']['id']) && $tutorAssignment['tutor']['userName'] !== 'unassigned') {
                    foreach ($tutorAssignment['submissions'] as $submission) {
                        unset($submission['unassigned']);
                        $data['assigned'][] = array('id'=>$submission['markingId'],'submission'=>$submission);
                    }
                }
            }
        } else { // ein richtiger Kontrolleur
            foreach ($tutorAssign_data['tutorAssignments'] as $tutorAssignment) {
                if (isset($tutorAssignment['tutor']['id']) && $tutorAssignment['tutor']['id'] == $fromTutor) {
                    foreach ($tutorAssignment['submissions'] as $submission) {
                        unset($submission['unassigned']);
                        $data['assigned'][] = array('id'=>$submission['markingId'],'submission'=>$submission);
                    }
                }
            }
        }
    }
}

if ($postAssignAutomaticallyValidation->isValid()) {
    $data = json_encode($data);

    $URI = $logicURI . "/tutor/auto/group/course/{$cid}/exercisesheet/{$sid}";
    http_post_data($URI, $data, true, $message);

    if ($message === 201 || $message === 200) {
        $msg = Language::Get('main','successAssignment', $langTemplate);
        $assignAutomaticallyNotifications[] = MakeNotification('success', $msg);
    } else {
        $msg = Language::Get('main','errorAssignment', $langTemplate);
        $assignAutomaticallyNotifications[] = MakeNotification('error', $msg);
    }
}