<?php   
set_time_limit(180);
        
$URI = $databaseURI . "/group/exercisesheet/{$sid}"; /// !!! gehört die SID zur CID ??? ///
$groups = http_get($URI, true, $message);
$groups = Group::decodeGroup($groups);

if ($message === 200) {
    $URI = $databaseURI . "/exercisesheet/{$sid}/exercise";
    $exerciseSheet = http_get($URI, true, $message);
    $exerciseSheet = ExerciseSheet::decodeExerciseSheet($exerciseSheet);
    
    if ($message === 200) {
        if ($exerciseSheet->getCourseId() == $cid){
            $exercises=array();
            foreach($exerciseSheet->getExercises() as $exercise){
                $exercises[] = $exercise->getId();
            }
        
            $URI = $databaseURI . "/submission/exercisesheet/{$sid}/selected";
            $submissions = http_get($URI, true, $message);
            $submissions = Submission::decodeSubmission($submissions);
            
            if ($message === 200) {
                $leaderGroups = array();
                foreach ($exercises as $exercise){
                    foreach($groups as $group){
                        $leaderGroups[] = $group->getLeader()->getId();
                    }
                }
                $postAssignMakeValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
                ->addSet('make',
                         ['set_default'=>array(),
                          'perform_this_array'=>[['exercises',
                                                  ['perform_this_array'=>[[['key_all'],
                                                                          ['satisfy_in_list'=>$exercises,
                                                                           'on_error'=>['type'=>'error',
                                                                                        'text'=>Language::Get('main','???', $langTemplate)]]]],
                                                   'on_error'=>['type'=>'error',
                                                                'text'=>Language::Get('main','???', $langTemplate)]]],
                                                 ['groups',
                                                  ['perform_this_array'=>[[['key_all'],
                                                                          ['satisfy_in_list'=>$leaderGroups,
                                                                           'on_error'=>['type'=>'error',
                                                                                        'text'=>Language::Get('main','???', $langTemplate)]]]],
                                                   'on_error'=>['type'=>'error',
                                                                'text'=>Language::Get('main','???', $langTemplate)]]]],
                          'on_error'=>['type'=>'warning',
                                       'text'=>Language::Get('main','???', $langTemplate)]]);

                $foundValues = $postAssignMakeValidation->validate();
                $assignMakeNotifications = array_merge($assignMakeNotifications,$postAssignMakeValidation->getPrintableNotifications('MakeNotification'));
                $postAssignMakeValidation->resetNotifications()->resetErrors();

                if ($postAssignMakeValidation->isValid()) {
                    $exercises = $foundValues['make']['exercises'];
                    $leaderGroups = $foundValues['make']['groups'];
                    
                    $users = array();
                    foreach ($exercises as $exercise){
                        $users[$exercise] = array();
                        foreach($leaderGroups as $leader){
                            $users[$exercise][] = $leader;
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
                        $assignMakeNotifications[] = MakeNotification('success', Language::Get('main','successMake', $langTemplate));
                    } else {
                        $assignMakeNotifications[] = MakeNotification('error', Language::Get('main','errorMake', $langTemplate));
                    }
                }
                
            } else {
                $assignMakeNotifications[] = MakeNotification('error', Language::Get('main','errorMake', $langTemplate));
            }
        } else {
            $assignMakeNotifications[] = MakeNotification('error', Language::Get('main','???', $langTemplate));
        }
    } else {
        $assignMakeNotifications[] = MakeNotification('error', Language::Get('main','errorMake', $langTemplate));
    }
} else {
    $assignMakeNotifications[] = MakeNotification('error', Language::Get('main','errorMake', $langTemplate));
}