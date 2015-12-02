<?php

// automatically assigns all unassigned submissions to the selected tutors
set_time_limit(180);
$f->addSet('tutorIds',
           ['satisfy_exists',
            'satisfy_not_empty',
            'is_array',
            'on_error'=>['type'=>'warning',
                         'text'=>Language::Get('main','???', $langTemplate)]])
  ->addSet('tutorIds',
           ['perform_this_array'=>[[['key_all'],
                                    ['valid_identifier']]],
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidTutors', $langTemplate)]]);
$valResults = $f->validate();
$notifications = array_merge($notifications,$f->getPrintableNotifications('MakeNotification'));
$f->resetNotifications()->resetErrors();

if ($f->isValid()) {
    // extracts the php POST data
    $selectedTutorIDs = $valResults['tutorIds'];

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
            if ($tutorAssignment['tutor']['userName'] === 'unassigned') {
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

    if ($message === 201 || $message === 200) {
        $msg = Language::Get('main','successAssignment', $langTemplate);
        $assignAutomaticallyNotifications[] = MakeNotification('success', $msg);
    } else {
        $msg = Language::Get('main','errorAssignment', $langTemplate);
        $assignAutomaticallyNotifications[] = MakeNotification('error', $msg);
    }
}