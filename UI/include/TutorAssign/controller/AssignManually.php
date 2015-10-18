<?php
if (isset($_POST['action']) && isset($_POST['action']) && $_POST['action'] == "AssignManually") {
    set_time_limit(180);
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
                        $sub = Submission::decodeSubmission(json_encode($submission));
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
            $msg = Language::Get('main','successAssignment', $langTemplate);
            $assignManuallyNotifications[] = MakeNotification("success", $msg);
        } else {
            $msg = Language::Get('main','errorAssignment', $langTemplate);
            $assignManuallyNotifications[] = MakeNotification("error", $msg);
        }        
    }
    
    // assigns manually chosen submissions to the selected tutor
    if (isset($_POST['actionAssignManually'])){
        $f = new FormEvaluator($_POST);

        $f->checkIntegerForKey('tutorId',
                               FormEvaluator::REQUIRED,
                               'warning',
                               Language::Get('main','invalidTutor', $langTemplate));
                                       
        $f->checkArrayOfArraysForKey('assign',
                                   FormEvaluator::REQUIRED,
                                   'warning',
                                   Language::Get('main','invalidSelection', $langTemplate));

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
                $msg = Language::Get('main','successAssignment', $langTemplate);
                $assignManuallyNotifications[] = MakeNotification("success", $msg);
            } else {
                $msg = Language::Get('main','errorAssignment', $langTemplate);
                $assignManuallyNotifications[] = MakeNotification("error", $msg);
            }         
        }  else {
            if (!isset($assignManuallyNotifications))
                $assignManuallyNotifications = array();
            $assignManuallyNotifications = $assignManuallyNotifications + $f->notifications;
        }
    }
}