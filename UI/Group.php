<?php
/**
 * @file Group.php
 * Constructs the page that is displayed to a student, when managing a group.
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2013-2014
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2013-2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2013-2014
 */

ob_start();

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/vendor/Validation/Validation.php';

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::STUDENT, $cid, $uid, $globalUserData);

$langTemplate='Group_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

$postValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('action',
           ['set_default'=>'noAction',
            'satisfy_in_list'=>['noAction', 'RemoveGroupMember', 'ManageGroup', 'InviteGroup', 'ManageInvitations'],
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidAction', $langTemplate)]]);
$postResults = $postValidation->validate();
$notifications = array_merge($notifications,$postValidation->getPrintableNotifications('MakeNotification'));
$postValidation->resetNotifications()->resetErrors();

$selectedUser = $uid;
$privileged = 0;
if (Authentication::checkRight(PRIVILEGE_LEVEL::LECTURER, $cid, $uid, $globalUserData)){
    if (isset($_POST['selectedUser'])){
        $URI = $serverURI . "/DB/DBUser/user/course/{$cid}/status/0";
        $courseUser = http_get($URI, true);
        $courseUser = User::decodeUser($courseUser);

        $correct = false;
        foreach ($courseUser as $user){
            if ($user->getId() == $_POST['selectedUser']){
                $correct = true;
                break;
            }
        }

        if ($correct){
            $_SESSION['selectedUser'] = $_POST['selectedUser'];
        }
    }

    $selectedUser = isset($_SESSION['selectedUser']) ? $_SESSION['selectedUser'] : $uid;

    if (isset($_POST['privileged'])){
        $_SESSION['privileged'] = $_POST['privileged'];
    }
    $privileged = (isset($_SESSION['privileged']) ? $_SESSION['privileged'] : $privileged);

    if (isset($_POST['selectedSheet'])){
        $URI = $serverURI . "/DB/DBExerciseSheet/exerciseSheet/course/{$cid}";
        $courseSheets = http_get($URI, true);
        $courseSheets = ExerciseSheet::decodeExerciseSheet($courseSheets);

        $correct = false;
        foreach ($courseSheets as $sheet){
            if ($sheet->getId() == $_POST['selectedSheet']){
                $correct = true;
                break;
            }
        }

        if ($correct){
            $sid = $_POST['selectedSheet'];
        }
    }
}

/**
 * Removes a user from a group.
 *
 * @param $uid The id of the user that is being removed
 * @param $sid The id of the sheet
 */
function removeUserFromGroup($uid, $sid)
{
    global $databaseURI;

    $newGroupSettings = Group::encodeGroup(Group::createGroup($uid, $uid, $sid));
    $URI = $databaseURI . "/group/user/{$uid}/exercisesheet/{$sid}";
    http_put_data($URI, $newGroupSettings, true, $message);

    if ($message === 201) {
        return true;
    } else {
        return false;
    }
}

/**
 * Removes all selectedSubmissions of a user regarding
 * a particular sheet. (es ist egal in welcher Gruppe er ist, es werden alle entfernt)
 *
 * @param $uid The id of the user
 * @param $sid The id of the sheet
 */
function removeSelectedSubmission($uid, $sid)
{
    global $databaseURI;

    $URI = $databaseURI . "/selectedsubmission/user/{$uid}/exercisesheet/{$sid}";
    http_delete($URI, true, $message);

    if ($message === 201) {
        return true;
    } else {
        return false;
    }
}

/**
 * Ermittelt die Einsendungen welche vom Nutzer uid in seiner derzeitigen 
 * Gruppe selektiert wurden (nur die von ihm eingesendeten)
 *
 * @param $uid The id of the user
 * @param $sid The id of the sheet
 */
function collectSelectedSubmissions($uid, $sid){
    global $serverURI;
    
    $URI = $serverURI . "/submission/group/user/{$uid}/exercisesheet/{$sid}/selected";
    $selectedSubmissions = http_get($URI, true, $message);

    if ($message === 200) {
        $submissions = json_decode($submissions,true);
        $res = array();
        foreach($submissions as $sub){
            if ($sub['studentId'] == $uid){
                $res[] = $sub;
            }
        }
        return $res;
    }
    
    return array();
}

/**
 * versucht mit den Einsendungen des Nutzer uid in seiner aktuellen Gruppe einen
 * Beitrag zu leisten, indem seine Einsendungen dort selektiert werden, wenn noch
 * keine für die jeweilige Aufgabe seletiert ist (wird durch constraints der DB gelöst)
 *
 * @param $uid The id of the user
 * @param $sid The id of the sheet
 */
function contributeSelectedSubmissionsToCurrentGroup($uid, $sid, $selectedSubmissions = null){
    global $serverURI;

    $URI = $serverURI . "/DB/DBSubmission/submission/user/{$uid}/exercisesheet/{$sid}";
    $submissions = http_get($URI, true, $message);

    if ($message === 200) {
        $submissions = json_decode($submissions,true);
        
        // sortiert die Einsendungen nach dem Zeitstempel (absteigend)
        $sortedSubmissions = LArraySorter::orderby($submissions, 'date', SORT_DESC);
        $computedExercises = array();
        
        if ($selectedSubmissions !== null){
            $sortedSubmissions = array_merge($selectedSubmissions, $sortedSubmissions);
        }
        
        // jetzt wird versucht die Einsendungen als selectedSubmission einzutragen
        foreach($sortedSubmissions as $sub){
            if (!isset($computedExercises[$sub['exerciseId']])){
                $computedExercises[$sub['exerciseId']] = 1;
                $newSelectedSubmission = SelectedSubmission::createSelectedSubmission(
                                                                                      null,
                                                                                      $sub['id'],
                                                                                      $sub['exerciseId']
                                                                                      );

                $URI = $serverURI . "/DB/DBSelectedSubmission/selectedsubmission";
                http_post_data($URI, SelectedSubmission::encodeSelectedSubmission($newSelectedSubmission), true, $message);
            }
        }
        return true;
    } else {
        if ($message === 404){
            return true;
        } else {
            return false;
        }
    }
}

if ($postValidation->isValid() && $postResults['action'] !== 'noAction') {
    // removes a group member
    if ($postResults['action'] === 'RemoveGroupMember') {
        $postRemoveGroupMemberValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('removeMember',
                   ['valid_identifier',
                    'set_default'=>null,
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidUserId', $langTemplate)]])
          ->addSet('removeInvitation',
                   ['valid_identifier',
                    'set_default'=>null,
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidUserId', $langTemplate)]])
          ->addSet('leaveGroup',
                   ['valid_identifier',
                    'set_default'=>null,
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidUserId', $langTemplate)]]);

        $foundValues = $postRemoveGroupMemberValidation->validate();
        $notifications = array_merge($notifications,$postRemoveGroupMemberValidation->getPrintableNotifications('MakeNotification'));
        $postRemoveGroupMemberValidation->resetNotifications()->resetErrors();

        if ($postRemoveGroupMemberValidation->isValid()){
            if (isset($foundValues['removeMember'])){                
                // bool which is true if any error occured
                $RequestError = false;
                
                $selectedSubmissions = collectSelectedSubmissions($foundValues['removeMember'], $sid);

                // removes the user from the group
                if (removeUserFromGroup($foundValues['removeMember'], $sid)) {
                    
                    if (!removeSelectedSubmission($foundValues['removeMember'], $sid)) {
                        $RequestError = true;
                    } else {
                        // hier die letzten Einsendungen des Nutzers wieder Auswählen
                        if (!contributeSelectedSubmissionsToCurrentGroup($foundValues['removeMember'], $sid, $selectedSubmissions)){
                            $RequestError = true; 
                        }
                    }
                } else {
                    $RequestError = true;
                }

                if ($RequestError) {
                    $notifications[] = MakeNotification('error', Language::Get('main','errorRemoveMember', $langTemplate));
                } else {
                    $notifications[] = MakeNotification('success', Language::Get('main','successRemoveMember', $langTemplate));
                }
            } elseif (isset($foundValues['removeInvitation'])){
                // removes an invitation from the group

                // deletes the invitation
                $URI = $databaseURI . "/invitation/user/{$selectedUser}/exercisesheet/{$sid}/user/{$foundValues['removeInvitation']}";
                http_delete($URI, true, $message);

                if ($message === 201) {
                    $notifications[] = MakeNotification('success', Language::Get('main','successRemoveInvitation', $langTemplate));
                } else {
                    $notifications[] = MakeNotification('error', Language::Get('main','errorRemoveInvitation', $langTemplate));
                }
            } elseif (isset($foundValues['leaveGroup'])){
                // removes all group members and deletes the group

                // checks if the user that wants to leave is the leader of the group
                if ($foundValues['leaveGroup'] == $selectedUser) {
                    // bool which is true if any error occured
                    $RequestError = false;

                    // returns all invitations from the groups
                    $URI = $databaseURI . "/invitation/leader/exercisesheet/{$sid}/user/{$foundValues['leaveGroup']}";
                    $invitations = http_get($URI, true);
                    $invitations = json_decode($invitations, true);

                    // returns the leader and all members of the group
                    $URI = $databaseURI . "/group/user/{$selectedUser}/exercisesheet/{$sid}";
                    $group = http_get($URI, true);
                    $group = json_decode($group, true);

                    // removes all invitations of the group
                    if (!empty($invitations)) {
                        foreach ($invitations as $invitation) {
                            $URI = $databaseURI . "/invitation/user/{$foundValues['leaveGroup']}/";
                            $URI .= "exercisesheet/{$sid}/user/{$invitation['member']['id']}";

                            http_delete($URI, true, $message);

                            if ($message !== 201) {
                                $RequestError = true;
                            }
                        }
                    }

                    // removes all members from the group
                    if (!empty($group)) {
                        if (isset($group['members']) && !empty($group['members'])) {
                            foreach ($group['members'] as $member) {
                                $selectedSubmissions = collectSelectedSubmissions($member['id'], $sid);
                    
                                if (!removeUserFromGroup($member['id'], $sid)) {
                                    $RequestError = true;
                                }
                                if (!$RequestError && !removeSelectedSubmission($member['id'], $sid)) {
                                    $RequestError = true;
                                } else {
                                    // hier die letzten Einsendungen des Nutzers wieder Auswählen
                                    if (!contributeSelectedSubmissionsToCurrentGroup($member['id'], $sid, $selectedSubmissions)){
                                        $RequestError = true; 
                                    }
                                }
                            }
                        }

                        // removes the selectedSubmissions of the leader
                        if (!removeSelectedSubmission($group['leader']['id'], $sid)) {
                            $RequestError = true;
                        } else {
                            // hier die letzten Einsendungen des Nutzers wieder Auswählen
                            if (!contributeSelectedSubmissionsToCurrentGroup($group['leader']['id'], $sid)){
                                $RequestError = true; 
                            }
                        }

                        // shows notification
                        if ($RequestError) {
                            $notifications[] = MakeNotification('error', Language::Get('main','errorLeaveGroup', $langTemplate));
                        }
                        else {
                            $notifications[] = MakeNotification('success', Language::Get('main','successLeaveGroup', $langTemplate));
                        }

                    } else {
                        $notifications[] = MakeNotification('error', Language::Get('main','errorLeaveGroup', $langTemplate));
                    }

                } else {
                    // bool which is true if any error occured
                    $RequestError = false;
                    $selectedSubmissions = collectSelectedSubmissions($selectedUser, $sid);

                    // removes the user from the group
                    if (removeUserFromGroup($selectedUser, $sid)) {
                        if (!removeSelectedSubmission($selectedUser, $sid)) {
                            $RequestError = true;
                        } else {
                            // hier die letzten Einsendungen des Nutzers wieder Auswählen
                            if (!contributeSelectedSubmissionsToCurrentGroup($selectedUser, $sid, $selectedSubmissions)){
                                $RequestError = true; 
                            }
                        }
                    } else {
                        $RequestError = true;
                        ///$notifications[] = MakeNotification('error', "Fehler else.");
                    }

                    // shows notification
                    if ($RequestError) {
                        $notifications[] = MakeNotification('error', Language::Get('main','errorLeaveGroup', $langTemplate));
                    } else {
                        $notifications[] = MakeNotification('success', Language::Get('main','successLeaveGroup', $langTemplate));
                    }
                }
            }
        }
    }

    // updates the selectedSubmissions for the group
    if ($postResults['action'] === 'ManageGroup') {
        $postManageGroupValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('exercises',
                   ['set_default'=>array(),
                    'perform_this_foreach'=>[['key',
                                         ['valid_identifier']],
                                        ['elem',
                                         ['valid_identifier']]],
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidSelection', $langTemplate)]]);

        $foundValues = $postManageGroupValidation->validate();
        $notifications = array_merge($notifications,$postManageGroupValidation->getPrintableNotifications('MakeNotification'));
        $postManageGroupValidation->resetNotifications()->resetErrors();

        if ($postManageGroupValidation->isValid()){
            // bool which is true if any error occured
            $RequestError = false;

            // extracts the exerciseIDs and the submissionIDs and updates
            // the selectedSubmissions
            foreach ($foundValues['exercises'] as $key => $value) {
                $exerciseID = $key; // !!! darf er diese IDs nutzen ??? ///
                $submissionID = $value; // !!! darf er diese IDs nutzen ??? ///

                updateSelectedSubmission($databaseURI,
                                         $selectedUser,
                                         $submissionID,
                                         $exerciseID,
                                         $message);

                if ($message !== 201) {
                    $RequestError = true;
                    break;
                }
            }

            // shows notification
            if ($RequestError == false) {
                $notifications[] = MakeNotification('success', Language::Get('main','successSelectSubmission', $langTemplate));
            }
            else {
                $notifications[] = MakeNotification('error', Language::Get('main','errorSelectSubmission', $langTemplate));
            }
        }
    }

    if ($postResults['action'] === 'InviteGroup') {
        $postInviteGroupValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('applyGroup',
                   ['set_default'=>false,
                    'to_boolean',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidApplyGroup', $langTemplate)]]);

        $invitationType = $postInviteGroupValidation->validate();
        $notifications = array_merge($notifications,$postInviteGroupValidation->getPrintableNotifications('MakeNotification'));
        $postInviteGroupValidation->resetNotifications()->resetErrors();

        if ($postInviteGroupValidation->isValid()){
            $RequestError = false;

            if ($invitationType['applyGroup'] === true){
                // apply last group

                $postApplyGroupValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
                  ->addSet('members',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'is_array',
                    'perform_this_array'=>[[['key_all'],
                                       ['valid_identifier']]],
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidMemberIds', $langTemplate)]]);

                $foundValues = $postApplyGroupValidation->validate();
                $notifications = array_merge($notifications,$postApplyGroupValidation->getPrintableNotifications('MakeNotification'));
                $postApplyGroupValidation->resetNotifications()->resetErrors();

                if ($postApplyGroupValidation->isValid()) {
                    // invite old members
                    foreach ($foundValues['members'] as $member){ /// !!! dürfen diese Mitglieder eingeladen werden ??? ///
                        $newInvitation = Invitation::encodeInvitation(Invitation::createInvitation($selectedUser, $member, $sid));
                        $URI = $databaseURI . '/invitation';
                        http_post_data($URI, $newInvitation, true, $message);

                        if ($message !== 201) {
                            $RequestError=true;
                        }
                    }
                    if (!$RequestError){
                        $notifications[] = MakeNotification('success', Language::Get('main','successInviteMembers', $langTemplate));
                        // accept invitations
                        foreach ($foundValues['members'] as $member){
                            
                            
                            $selectedSubmissions = collectSelectedSubmissions($member, $sid);

                            // adds the user to the group
                            $newGroupSettings = Group::encodeGroup(Group::createGroup($selectedUser, $member, $sid));
                            $URI = $databaseURI . "/group/user/{$member}/exercisesheet/{$sid}";
                            $answ = http_put_data($URI, $newGroupSettings, true, $message);
                            if ($message !== 201) {
                                $RequestError = true;
                                continue;
                            }

                            // deletes the invitation
                            $URI = $databaseURI . "/invitation/user/{$selectedUser}/exercisesheet/{$sid}/user/{$member}";
                            http_delete($URI, true, $message);

                            if ($message !== 201) {
                                $RequestError = true;
                                continue;
                            }

                            // deletes all selectedSubmissions
                            if (!removeSelectedSubmission($member, $sid)) {
                                $RequestError = true;
                                continue;
                            } else {
                                if (!contributeSelectedSubmissionsToCurrentGroup($member, $sid, $selectedSubmissions)){
                                    $RequestError = true; 
                                }
                            }
                        }

                        if (!$RequestError){
                            $notifications[] = MakeNotification('success', Language::Get('main','successJoinMembers', $langTemplate));
                        } else {
                            $notifications[] = MakeNotification('error', Language::Get('main','errorJoinMember', $langTemplate));
                        }

                    } else {
                       $notifications[] = MakeNotification('error', Language::Get('main','errorInviteOldMember', $langTemplate));
                    }
                } else {
                    $notifications[] = MakeNotification('error', Language::Get('main','errorApplyGroup', $langTemplate));
                    $RequestError = true;
                }
            } else {
                // invites users to the group

                $postInviteUsersValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
                  ->addSet('userName',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'is_array',
                    'perform_this_array'=>[[['key_all'],
                                       ['valid_userName']]],
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidUserNames', $langTemplate)]]);

                $foundValues = $postInviteUsersValidation->validate();
                $notifications = array_merge($notifications,$postInviteUsersValidation->getPrintableNotifications('MakeNotification'));
                $postInviteUsersValidation->resetNotifications()->resetErrors();

                if ($postInviteUsersValidation->isValid()) {
                    foreach ($foundValues['userName'] as $key => $memberName) {
                        if (trim($memberName) == '') continue;

                        // extracts the userID
                        $URI = $databaseURI . "/user/user/{$memberName}";
                        $user_data = http_get($URI, true);
                        $user_data = json_decode($user_data, true);

                        // invites the user to the current group
                        if (!isset($user_data['id']) || empty($user_data) || $user_data['id'] === $selectedUser) {
                            $notifications[] = MakeNotification('error', Language::Get('main','invalidUserId', $langTemplate));
                        } else {
                            $memberID = $user_data['id'];

                            $newInvitation = Invitation::encodeInvitation(Invitation::createInvitation($selectedUser, $memberID, $sid));
                            $URI = $databaseURI . '/invitation';
                            http_post_data($URI, $newInvitation, true, $message);

                            if ($message === 201) {
                                $notifications[] = MakeNotification('success', Language::Get('main','successInviteMember', $langTemplate, array('memberName'=>$memberName)));
                            } else {
                                $notifications[] = MakeNotification('error', Language::Get('main','errorInviteMember', $langTemplate, array('memberName'=>$memberName)));
                            }
                        }
                    }
                }
            }
        }
    }

    if ($postResults['action'] === 'ManageInvitations') {
        $postManageInvitationsValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
          ->addSet('denyInvitation',
                   ['valid_identifier',
                    'set_default'=>null,
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidUserId', $langTemplate)]])
          ->addSet('acceptInvitation',
                   ['valid_identifier',
                    'set_default'=>null,
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidUserId', $langTemplate)]]);

        $foundValues = $postManageInvitationsValidation->validate();
        $notifications = array_merge($notifications,$postManageInvitationsValidation->getPrintableNotifications('MakeNotification'));
        $postManageInvitationsValidation->resetNotifications()->resetErrors();

        if ($postManageInvitationsValidation->isValid()){

            if (isset($foundValues['denyInvitation'])){
                // removes an invitation to a group

                // deletes the invitation
                $URI = $databaseURI . "/invitation/user/{$foundValues['denyInvitation']}/exercisesheet/{$sid}/user/{$selectedUser}";
                http_delete($URI, true, $message);

                if ($message === 201) {
                    $notifications[] = MakeNotification('success', Language::Get('main','successRejectInvitation', $langTemplate));
                } else {
                    $notifications[] = MakeNotification('error', Language::Get('main','errorRejectInvitation', $langTemplate));
                }
            } elseif (isset($foundValues['acceptInvitation'])){
                // accepts an invitation to a group

                // bool which is true if any error occured
                $RequestError = false;

                $selectedSubmissions = collectSelectedSubmissions($selectedUser, $sid);
                    
                // adds the user to the group
                if ($RequestError === false){
                    $newGroupSettings = Group::encodeGroup(Group::createGroup($foundValues['acceptInvitation'], $selectedUser, $sid));
                    $URI = $databaseURI . "/group/user/{$selectedUser}/exercisesheet/{$sid}";
                    http_put_data($URI, $newGroupSettings, true, $message);

                    if ($message !== 201) {
                        $RequestError = true;
                    }
                }

                // deletes the invitation
                if ($RequestError === false){
                    $URI = $databaseURI . "/invitation/user/{$foundValues['acceptInvitation']}/exercisesheet/{$sid}/user/{$selectedUser}";
                    http_delete($URI, true, $message);

                    if ($message !== 201) {
                        $RequestError = true;
                    }
                }

                // deletes all selectedSubmissions
                if ($RequestError === false){
                    
                    if (!removeSelectedSubmission($selectedUser, $sid)) {
                        $RequestError = true;
                    } else {
                        // hier sollen die letzten Einsendungen des neuen Nutzers
                        // in die neue Gruppe eingebracht werden
                        if (!contributeSelectedSubmissionsToCurrentGroup($selectedUser, $sid, $selectedSubmissions)){
                            $RequestError = true; 
                        }
                    }
                }

                // shows notification
                if ($RequestError === false) {
                    $notifications[] = MakeNotification('success', Language::Get('main','successJoinGroup', $langTemplate));
                }
                else {
                    $notifications[] = MakeNotification('error', Language::Get('main','errorJoinGroup', $langTemplate));
                }
            } else {
                $notifications[] = MakeNotification('error', Language::Get('main','invalidManageInvitationsAction', $langTemplate));
            }
        }
    }
}

// load mainSettings data from GetSite
$URI = $getSiteURI . "/group/user/{$selectedUser}/course/{$cid}/exercisesheet/{$sid}";
///echo $URI;return;
$group_data = http_get($URI, true);
$group_data = json_decode($group_data, true);
$group_data['filesystemURI'] = $filesystemURI;
$group_data['uid'] = $selectedUser;

$user_course_data = $group_data['user'];


if (isset($group_data['exerciseSheet']['endDate']) && isset($group_data['exerciseSheet']['startDate'])){
    // bool if endDate of sheet is greater than the actual date
    $isExpired = date('U') > date('U', $group_data['exerciseSheet']['endDate']); 

    // bool if startDate of sheet is greater than the actual date
    $hasStarted = date('U') > date('U', $group_data['exerciseSheet']['startDate']);
    if ($isExpired && !$privileged){
        set_error(Language::Get('main','expiredExercisePerion', $langTemplate,array('endDate'=>date('d.m.Y  -  H:i', $group_data['exerciseSheet']['endDate']))));
    } elseif (!$hasStarted && !$privileged){
        set_error(Language::Get('main','noStartedExercisePeriod', $langTemplate,array('startDate'=>date('d.m.Y  -  H:i', $group_data['exerciseSheet']['startDate']))));
    }

} else
    set_error(Language::Get('main','noExercisePeriod', $langTemplate));


$user_course_data = $group_data['user'];
$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::STUDENT);

$userNavigation = null;
if (isset($_SESSION['selectedUser'])){
    $courseStatus = null;
    if (isset($globalUserData['courses'][0]) && isset($globalUserData['courses'][0]['status']))
        $courseStatus = $globalUserData['courses'][0]['status'];
    
    $URI = $serverURI . "/DB/DBUser/user/course/{$cid}/status/0";
    $courseUser = http_get($URI, true);
    $courseUser = User::decodeUser($courseUser);
    $URI = $serverURI . "/DB/DBExerciseSheet/exercisesheet/course/{$cid}/";
    $courseSheets = http_get($URI, true);
    $courseSheets = ExerciseSheet::decodeExerciseSheet($courseSheets);
    $courseSheets = array_reverse($courseSheets);

    foreach ($courseSheets as $key => $sheet){
        if ($sheet->getGroupSize() === null || $sheet->getGroupSize() <= 1){
            unset($courseSheets[$key]);
            continue;
        }

        if ($privileged) continue;

        if ($sheet->getEndDate()!==null && $sheet->getStartDate()!==null){
            // bool if endDate of sheet is greater than the actual date
            $isExpired = date('U') > date('U', $sheet->getEndDate()); 

            // bool if startDate of sheet is greater than the actual date
            $hasStarted = date('U') > date('U', $sheet->getStartDate());
            if ($isExpired || !$hasStarted){
               unset($courseSheets[$key]);
            }
        } else {
            unset($courseSheets[$key]);
        }
    }

    $userNavigation = MakeUserNavigationElement($globalUserData,
                                                $courseUser,
                                                $privileged,
                                                PRIVILEGE_LEVEL::LECTURER,
                                                $sid,
                                                $courseSheets,
                                                false,
                                                false,
                                                array('page/admin/studentMode','studentMode.md'),
                                                array(array('title'=>Language::Get('main','leaveStudent', $langTemplate),'target'=>PRIVILEGE_LEVEL::$SITES[$courseStatus].'?cid='.$cid)));
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array('name' => $user_course_data['courses'][0]['course']['name'],
               'backTitle' => 'zur Veranstaltung',
               'backURL' => "Student.php?cid={$cid}",
               'notificationElements' => $notifications,
               'navigationElement' => $menu,
               'userNavigationElement' => $userNavigation));

$isInGroup = (!empty($group_data['group']['members']) || !empty($group_data['invitationsFromGroup']));
$isLeader = isset($group_data['group']['leader']['id']) && $group_data['group']['leader']['id'] == $selectedUser;
$hasInvitations = !empty($group_data['invitationsToGroup']);

$group_data['isInGroup'] = $isInGroup;
$group_data['isLeader'] = $isLeader;

// construct a content element for group information
$groupMembers = Template::WithTemplateFile('include/Group/GroupMembers.template.html');
$groupMembers->bind($group_data);
$groupMembers->bind(array('privileged' => $privileged));

// construct a content element for managing groups
if ($isInGroup) {
    $groupManagement = Template::WithTemplateFile('include/Group/GroupManagement.template.html');
    $groupManagement->bind($group_data);
    $groupManagement->bind(array('privileged' => $privileged));
}

// construct a content element for creating groups
if ($isLeader) {
    $invitationsFromGroup = Template::WithTemplateFile('include/Group/InvitationsFromGroup.template.html');
    $invitationsFromGroup->bind($group_data);
    $invitationsFromGroup->bind(array('privileged' => $privileged));
}

// construct a content element for joining groups
if ($hasInvitations) {
    $invitationsToGroup = Template::WithTemplateFile('include/Group/InvitationsToGroup.template.html');
    $invitationsToGroup->bind($group_data);
    $invitationsToGroup->bind(array('privileged' => $privileged));
}

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $groupMembers, (isset($invitationsToGroup) ? $invitationsToGroup : null), (isset($groupManagement) ? $groupManagement : null), (isset($invitationsFromGroup) ? $invitationsFromGroup : null));
$w->defineForm(basename(__FILE__).'?cid='.$cid.'&sid='.$sid, false, $groupMembers);

if (isset($groupManagement))
    $w->defineForm(basename(__FILE__).'?cid='.$cid.'&sid='.$sid, false, $groupManagement);
if (isset($invitationsFromGroup))
    $w->defineForm(basename(__FILE__).'?cid='.$cid.'&sid='.$sid, false, $invitationsFromGroup);
if (isset($invitationsToGroup))
    $w->defineForm(basename(__FILE__).'?cid='.$cid.'&sid='.$sid, false, $invitationsToGroup);
$w->set_config_file('include/configs/config_group.json');
if (isset($maintenanceMode) && $maintenanceMode === '1'){
    $w->add_config_file('include/configs/config_maintenanceMode.json');
}

$w->show();

ob_end_flush();