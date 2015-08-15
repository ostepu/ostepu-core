<?php
// removes all tutor assignments by deleting all markings of the exercisesheet
if (isset($_POST['action']) && $_POST['action'] == "AssignMakeWarning") {
    $assignMakeNotifications[] = MakeNotification("warning", Language::Get('main','askMake', $langTemplate));
} elseif (isset($_POST['action']) && $_POST['action'] == "AssignMake") {
    $URI = $databaseURI . "/group/exercisesheet/{$sid}";
    $groups = http_get($URI, true, $message);
    $groups = Group::decodeGroup($groups);
    
    if ($message == "200") {
        $URI = $databaseURI . "/exercisesheet/{$sid}/exercise";
        $exerciseSheet = http_get($URI, true, $message);
        $exerciseSheet = ExerciseSheet::decodeExerciseSheet($exerciseSheet);
        
        if ($message == "200") {
            $exercises=array();
            foreach($exerciseSheet->getExercises() as $exercise){
                $exercises[] = $exercise->getId();
            }
        
            $URI = $databaseURI . "/submission/exercisesheet/{$sid}/selected";
            $submissions = http_get($URI, true, $message);
            $submissions = Submission::decodeSubmission($submissions);
            
            if ($message == "200") {
                $users = array();
                foreach ($exercises as $exercise){
                    $users[$exercise] = array();
                    foreach($groups as $group){
                        $users[$exercise][] = $group->getLeader()->getId();
                        
                    }
                }
                unset($groups);
                
                $submitted=array();
                foreach ($exercises as $exercise){
                    $submitted[$exercise] = array();
                }
                foreach($submissions as $submission){
                    $submitted[$submission->getExerciseId()][] = $submission->getLeaderId();
                }
                $noSubmission=array();
                foreach ($exercises as $exercise){
                    $noSubmission[$exercise] = array_diff($users[$exercise],$submitted[$exercise]);
                }
                unset($submitted);
                unset($users);
                
                $failure = false;
                foreach ($noSubmission as $exercise => $exerciseUsers){
                    foreach ($exerciseUsers as $user){
                        if (createSubmission($user, $exercise) === null){
                            $failure = true;
                            break;
                        }
                    }
                    if ($failure) break;
                }
                
                if (!$failure){
                    $assignMakeNotifications[] = MakeNotification("success", Language::Get('main','successMake', $langTemplate));
                } else {
                    $assignMakeNotifications[] = MakeNotification("error", Language::Get('main','errorMake', $langTemplate));
                }
                
            } else {
                $assignMakeNotifications[] = MakeNotification("error", Language::Get('main','errorMake', $langTemplate));
            }
        } else {
            $assignMakeNotifications[] = MakeNotification("error", Language::Get('main','errorMake', $langTemplate));
        }
    } else {
        $assignMakeNotifications[] = MakeNotification("error", Language::Get('main','errorMake', $langTemplate));
    }
}