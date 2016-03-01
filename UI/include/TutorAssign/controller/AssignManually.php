<?php
/**
 * @file AssignManually.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

$postAssignManuallyTypeValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('actionAssignAllProposals',
       ['set_default'=>null,
        'on_error'=>['type'=>'error',
                     'text'=>Language::Get('main','errorValidateAssignAllProposals', $langTemplate)]])
  ->addSet('actionAssignManually',
       ['set_default'=>null,
        'on_error'=>['type'=>'error',
                     'text'=>Language::Get('main','errorValidateAssignManually', $langTemplate)]]);
$foundValues = $postAssignManuallyTypeValidation->validate();
$notifications = array_merge($notifications,$postAssignManuallyTypeValidation->getPrintableNotifications('MakeNotification'));
$postAssignManuallyTypeValidation->resetNotifications()->resetErrors();
    
// automatically assigns all unassigned proposed submissions to tutors
set_time_limit(180);
if (isset($foundValues['actionAssignAllProposals'])){
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
                
    $URI = $serverURI . '/logic/LMarking/marking';
    http_post_data($URI, Marking::encodeMarking($markings), true, $message);

    if ($message === 201 || $message === 200) {
        $msg = Language::Get('main','successAssignment', $langTemplate);
        $assignManuallyNotifications[] = MakeNotification('success', $msg);
    } else {
        $msg = Language::Get('main','errorAssignment', $langTemplate);
        $assignManuallyNotifications[] = MakeNotification('error', $msg);
    }        
}

// assigns manually chosen submissions to the selected tutor
set_time_limit(180);
if (isset($foundValues['actionAssignManually'])){
    $postAssignManuallyValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
      ->addSet('tutorId',
               ['satisfy_exists',
                'satisfy_not_empty',
                'valid_identifier',
                'on_error'=>['type'=>'warning',
                             'text'=>Language::Get('main','invalidTutor', $langTemplate)]])
      ->addSet('assign',
               ['satisfy_exists',
                'satisfy_not_empty',
                'on_error'=>['type'=>'warning',
                             'text'=>Language::Get('main','noAssignment', $langTemplate)]])
                             
      // structure: $_POST['assign'][-1|id]['proposal'] = array(id,id,id,...)
      //            $_POST['assign'][-1|id]['marking'][id] = array(id,id,id,...)
      ->addSet('assign',
               ['perform_this_foreach'=>[['key',
                                          ['logic_or'=>[['satisfy_value'=>'-1'],
                                                        ['valid_identifier']]]], 
                                         ['elem',
                                          ['perform_switch_case'=>[['proposal',
                                                                    ['perform_this_array'=>[[['key_all'],
                                                                                             ['valid_identifier']]]]],
                                                                   ['marking',
                                                                    ['perform_this_foreach'=>[['key',
                                                                                               ['valid_identifier']], 
                                                                                              ['elem',
                                                                                               ['perform_this_array'=>[[['key_all'],
                                                                                                                        ['valid_identifier']]]]]]]]]]]],
                'on_error'=>['type'=>'warning',
                             'text'=>Language::Get('main','invalidAssignment', $langTemplate)]]);
                             
    $foundData = $postAssignManuallyValidation->validate();
    $assignManuallyNotifications = array_merge($assignManuallyNotifications,$postAssignManuallyValidation->getPrintableNotifications('MakeNotification'));
    $postAssignManuallyValidation->resetNotifications()->resetErrors();

    if ($postAssignManuallyValidation->isValid()) {
        // extracts the php POST data
        $selectedTutorID = $foundData['tutorId'];
        $assigns=$foundData['assign'];
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
        
        $URI = $serverURI . '/logic/LMarking/marking';
        http_post_data($URI, Marking::encodeMarking($markings), true, $message);

        if ($message === 201 || $message === 200) {
            $msg = Language::Get('main','successAssignment', $langTemplate);
            $assignManuallyNotifications[] = MakeNotification('success', $msg);
        } else {
            $msg = Language::Get('main','errorAssignment', $langTemplate);
            $assignManuallyNotifications[] = MakeNotification('error', $msg);
        }         
    }
}