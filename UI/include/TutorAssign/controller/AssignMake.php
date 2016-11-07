<?php   
/**
 * @file AssignMake.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

set_time_limit(180);
        
$URI = $databaseURI . "/group/exercisesheet/{$sid}"; /// !!! gehört die SID zur CID ??? ///
$groups = http_get($URI, true, $message);
$groups = Group::decodeGroup($groups);

if ($message === 200) {
    $URI = $databaseURI . "/exercisesheet/exercisesheet/{$sid}/exercise";
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
                unset($groups);
                    
                $postAssignMakeValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
                ->addSet('make',
                         ['set_default'=>array(),
                          'perform_this_foreach'=>[['key',
                                                    ['satisfy_in_list'=>$exercises,
                                                     'on_error'=>['type'=>'error',
                                                                  'text'=>Language::Get('main','invalidExerciseId', $langTemplate)]]],
                                                   ['elem',
                                                    ['perform_this_array'=>[[['key_all'],
                                                                            ['satisfy_in_list'=>$leaderGroups,
                                                                             'on_error'=>['type'=>'error',
                                                                                          'text'=>Language::Get('main','invalidLeader', $langTemplate)]]]],
                                                     'on_error'=>['type'=>'error',
                                                                  'text'=>Language::Get('main','errorValidateSelection', $langTemplate)]]]],
                          'on_error'=>['type'=>'warning',
                                       'text'=>Language::Get('main','errorValidateSelection', $langTemplate)]]);

                $foundValues = $postAssignMakeValidation->validate();
                $assignMakeNotifications = array_merge($assignMakeNotifications,$postAssignMakeValidation->getPrintableNotifications('MakeNotification'));
                $postAssignMakeValidation->resetNotifications()->resetErrors();

                if ($postAssignMakeValidation->isValid()) {
                    $leaderGroups = $foundValues['make'];
                    
                    $users = array();
                    foreach ($exercises as $exercise){
                        if (!isset($leaderGroups[$exercise])) continue;
                        $users[$exercise] = array();
                        foreach($leaderGroups[$exercise] as $leader){
                            $users[$exercise][] = $leader;
                        }
                    }
                    
                    $submitted=array();
                    foreach ($exercises as $exercise){
                        $submitted[$exercise] = array();
                    }
                    foreach($submissions as $submission){
                        $submitted[$submission->getExerciseId()][] = $submission->getLeaderId();
                    }
                    $noSubmission=array();
                    foreach ($exercises as $exercise){
                        if (!isset($users[$exercise])) continue;
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
            $assignMakeNotifications[] = MakeNotification('error', Language::Get('main','invalidCourseId', $langTemplate));
        }
    } else {
        $assignMakeNotifications[] = MakeNotification('error', Language::Get('main','errorMake', $langTemplate));
    }
} else {
    $assignMakeNotifications[] = MakeNotification('error', Language::Get('main','errorMake', $langTemplate));
}