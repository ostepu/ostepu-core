<?php
/**
 * @file Group.php
 * Constructs the page that is displayed to a student, when managing a group.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Validation/Validation.php';

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::STUDENT, $cid, $uid, $globalUserData);

$langTemplate='Group_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

$f = new Validation($_POST, array('preRules'=>array('sanitize')));

$f->addSet('action',
           array('set_default'=>'noAction',
                 'satisfy_in_list'=>array('noAction', 'RemoveGroupMember', 'ManageGroup', 'InviteGroup', 'ManageInvitations'),
                 'on_error'=>array('type'=>'error',
                                   'text'=>'???1')));
$valResults = $f->validate();
$notifications = array_merge($notifications,$f->getPrintableNotifications());
$f->resetNotifications()->resetErrors();

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
 * a particular sheet.
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

if ($f->isValid() && $valResults['action'] !== 'noAction') {
    // removes a group member
    if ($valResults['action'] === 'RemoveGroupMember') {
        $f->addSet('removeMember',
                   array('valid_identifier',
                         'set_default'=>null,
                         'on_error'=>array('type'=>'error',
                                           'text'=>'???1')))
          ->addSet('removeInvitation',
                   array('valid_identifier',
                         'set_default'=>null,
                         'on_error'=>array('type'=>'error',
                                           'text'=>'???1')))
          ->addSet('leaveGroup',
                   array('valid_identifier',
                         'set_default'=>null,
                         'on_error'=>array('type'=>'error',
                                           'text'=>'???1')));
                                           
        $valResults = $f->validate();
        $notifications = array_merge($notifications,$f->getPrintableNotifications());
        $f->resetNotifications()->resetErrors();

        if ($f->isValid()){
            if (isset($valResults['removeMember'])){
                // bool which is true if any error occured
                $RequestError = false;

                // removes the user from the group
                if (removeUserFromGroup($valResults['removeMember'], $sid)) {
                    if (!removeSelectedSubmission($valResults_POST['removeMember'], $sid)) {
                        $RequestError = true;
                    }
                } else {
                    $RequestError = true;
                }

                if ($RequestError) {
                    $notifications[] = MakeNotification('error', Language::Get('main','errorRemoveMember', $langTemplate));
                } else {
                    $notifications[] = MakeNotification('success', Language::Get('main','successRemoveMember', $langTemplate));
                }
            } elseif (isset($valResults['removeInvitation'])){
                // removes an invitation from the group
                
                // deletes the invitation
                $URI = $databaseURI . "/invitation/user/{$uid}/exercisesheet/{$sid}/user/{$valResults['removeInvitation']}";
                http_delete($URI, true, $message);

                if ($message === 201) {
                    $notifications[] = MakeNotification('success', Language::Get('main','successRemoveInvitation', $langTemplate));
                } else {
                    $notifications[] = MakeNotification('error', Language::Get('main','errorRemoveInvitation', $langTemplate));
                }
            } elseif (isset($valResults['leaveGroup'])){
                // removes all group members and deletes the group
                
                // checks if the user that wants to leave is the leader of the group
                if ($valResults['leaveGroup'] == $uid) {
                    // bool which is true if any error occured
                    $RequestError = false;

                    // returns all invitations from the groups
                    $URI = $databaseURI . "/invitation/leader/exercisesheet/{$sid}/user/{$valResults['leaveGroup']}";
                    $invitations = http_get($URI, true);
                    $invitations = json_decode($invitations, true);

                    // returns the leader and all members of the group
                    $URI = $databaseURI . "/group/user/{$uid}/exercisesheet/{$sid}";
                    $group = http_get($URI, true);
                    $group = json_decode($group, true);

                    // removes all invitations of the group
                    if (!empty($invitations)) {
                        foreach ($invitations as $invitation) {
                            $URI = $databaseURI . "/invitation/user/{$valResults['leaveGroup']}/";
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
                                if (!removeUserFromGroup($member['id'], $sid)) {
                                    $RequestError = true;
                                }
                                if (!removeSelectedSubmission($member['id'], $sid)) {
                                    $RequestError = true;
                                }
                            }
                        }

                        // removes the selectedSubmissions of the leader
                        if (!removeSelectedSubmission($group['leader']['id'], $sid)) {
                            $RequestError = true;
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

                    // removes the user from the group
                    if (removeUserFromGroup($uid, $sid)) {
                        if (!removeSelectedSubmission($uid, $sid)) {
                            $RequestError = true;
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
    if ($valResults['action'] === 'ManageGroup') {
        $f->addSet('exercises',
                   ['default'=>array(),
                    'perform_foreach'=>[['key',
                                         ['valid_identifier']],
                                        ['elem',
                                         ['valid_identifier']]],
                    'on_error'=>['type'=>'error',
                                 'text'=>'???1']]);
                                           
        $valResults = $f->validate();
        $notifications = array_merge($notifications,$f->getPrintableNotifications());
        $f->resetNotifications()->resetErrors();

        if ($f->isValid()){
            // bool which is true if any error occured
            $RequestError = false;
            
            // extracts the exerciseIDs and the submissionIDs and updates
            // the selectedSubmissions
            foreach ($valResults['exercises'] as $key => $value) {
                $exerciseID = $key; // !!! darf er diese IDs nutzen ??? ///
                $submissionID = $value; // !!! darf er diese IDs nutzen ??? ///

                updateSelectedSubmission($databaseURI,
                                         $uid,
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

    if ($valResults['action'] === 'InviteGroup') {
        $f->addSet('applyGroup',
                   ['default'=>false,
                    'to_boolean',
                    'on_error'=>['type'=>'error',
                                 'text'=>'???1']]);
                                           
        $valResults = $f->validate();
        $notifications = array_merge($notifications,$f->getPrintableNotifications());
        $f->resetNotifications()->resetErrors();
        
        if ($f->isValid()){
            $RequestError = false;
            
            if ($valResults['applyGroup'] === true){
                // apply last group
                
                $f->addSet('members',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'is_array',
                    'perform_array'=>[['key_all',
                                       ['valid_identifier']]],
                    'on_error'=>['type'=>'error',
                                 'text'=>'???1']]);
                                 
                $valResults = $f->validate();
                $notifications = array_merge($notifications,$f->getPrintableNotifications());
                $f->resetNotifications()->resetErrors();
                                 
                if ($f->isValid()) {
                    // invite old members
                    foreach ($valResults['members'] as $member){ /// !!! dürfen diese Mitglieder eingeladen werden ??? ///
                        $newInvitation = Invitation::encodeInvitation(Invitation::createInvitation($uid, $member, $sid));
                        $URI = $databaseURI . '/invitation';
                        http_post_data($URI, $newInvitation, true, $message);

                        if ($message !== 201) {
                            $RequestError=true;
                        }
                    }
                    if (!$RequestError){
                        $notifications[] = MakeNotification('success', Language::Get('main','successInviteMembers', $langTemplate));
                        // accept invitations
                        foreach ($valResults['members'] as $member){
                            
                            // adds the user to the group
                            $newGroupSettings = Group::encodeGroup(Group::createGroup($uid, $member, $sid));
                            $URI = $databaseURI . "/group/user/{$member}/exercisesheet/{$sid}";
                            $answ = http_put_data($URI, $newGroupSettings, true, $message);
                            if ($message !== 201) {
                                $RequestError = true;
                                continue;
                            }
                            
                            // deletes the invitation
                            $URI = $databaseURI . "/invitation/user/{$uid}/exercisesheet/{$sid}/user/{$member}";
                            http_delete($URI, true, $message);

                            if ($message !== 201) {
                                $RequestError = true;
                                continue;
                            }

                            // deletes all selectedSubmissions
                            if (!removeSelectedSubmission($member, $sid)) {
                                $RequestError = true;
                                continue;
                            }
                        }
                        
                        if (!$RequestError){
                            $notifications[] = MakeNotification('success', Language::Get('main','successJoinMembers', $langTemplate));
                        } else {
                            $notifications[] = MakeNotification('error', Language::Get('main','errorJoinMember', $langTemplate));
                        }
                
                    } else {
                       $notifications[] = MakeNotification('error', Language::Get('main','errorInviteMember', $langTemplate));
                    }
                } else {
                    $notifications[] = MakeNotification('error', Language::Get('main','errorApplyGroup', $langTemplate));
                    $RequestError = true;
                }
            } else {
                // invites users to the group
                
                $f->addSet('userName',
                   ['satisfy_exists',
                    'satisfy_not_empty',
                    'is_array',
                    'perform_array'=>[['key_all',
                                       ['satisfy_not_empty',
                                        'valid_userName']]],
                    'on_error'=>['type'=>'error',
                                 'text'=>'???1']]);
                                 
                $valResults = $f->validate();
                $notifications = array_merge($notifications,$f->getPrintableNotifications());
                $f->resetNotifications()->resetErrors();
                
                if ($f->isValid()) {
                    foreach ($valResults['userName'] as $key => $memberName) {
                        // extracts the userID
                        $URI = $databaseURI . "/user/user/{$memberName}";
                        $user_data = http_get($URI, true);
                        $user_data = json_decode($user_data, true);

                        // invites the user to the current group
                        if (!isset($user_data['id']) || empty($user_data) || $user_data['id'] == $uid) {
                            $notifications[] = MakeNotification('error', Language::Get('main','invalidUserId', $langTemplate));
                        } else {
                            $memberID = $user_data['id'];

                            $newInvitation = Invitation::encodeInvitation(Invitation::createInvitation($uid, $memberID, $sid));
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

    if ($valResults['action'] === 'ManageInvitations') {
        $f->addSet('denyInvitation',
                   ['valid_identifier',
                    'set_default'=>null,
                    'on_error'=>['type'=>'error',
                                 'text'=>'???1']])
          ->addSet('acceptInvitation',
                   ['valid_identifier',
                    'set_default'=>null,
                    'on_error'=>['type'=>'error',
                                 'text'=>'???1']]);
                                 
        $valResults = $f->validate();
        $notifications = array_merge($notifications,$f->getPrintableNotifications());
        $f->resetNotifications()->resetErrors();

        if ($f->isValid()){
            
            if (isset($valResults['denyInvitation'])){
                // removes an invitation to a group
    
                // deletes the invitation
                $URI = $databaseURI . "/invitation/user/{$valResults['denyInvitation']}/exercisesheet/{$sid}/user/{$uid}";
                http_delete($URI, true, $message);

                if ($message === 201) {
                    $notifications[] = MakeNotification('success', Language::Get('main','successRejectInvitation', $langTemplate));
                } else {
                    $notifications[] = MakeNotification('error', Language::Get('main','errorRejectInvitation', $langTemplate));
                }
            } elseif (isset($valResults['acceptInvitation'])){
                // accepts an invitation to a group
                
                // bool which is true if any error occured
                $RequestError = false;

                // adds the user to the group
                if ($RequestError === false){
                    $newGroupSettings = Group::encodeGroup(Group::createGroup($valResults['acceptInvitation'], $uid, $sid));
                    $URI = $databaseURI . "/group/user/{$uid}/exercisesheet/{$sid}";
                    http_put_data($URI, $newGroupSettings, true, $message);

                    if ($message !== 201) {
                        $RequestError = true;
                    }
                }

                // deletes the invitation
                if ($RequestError === false){
                    $URI = $databaseURI . "/invitation/user/{$valResults['acceptInvitation']}/exercisesheet/{$sid}/user/{$uid}";
                    http_delete($URI, true, $message);

                    if ($message !== 201) {
                        $RequestError = true;
                    }
                }

                // deletes all selectedSubmissions
                if ($RequestError === false){
                    if (!removeSelectedSubmission($uid, $sid)) {
                        $RequestError = true;
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
                $notifications[] = MakeNotification('error', '');
            }
        }
    }
}

// load mainSettings data from GetSite
$URI = $getSiteURI . "/group/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
///echo $URI;return;
$group_data = http_get($URI, true);
$group_data = json_decode($group_data, true);
$group_data['filesystemURI'] = $filesystemURI;
$group_data['uid'] = $uid;

$user_course_data = $group_data['user'];


if (isset($group_data['exerciseSheet']['endDate']) && isset($group_data['exerciseSheet']['startDate'])){
    // bool if endDate of sheet is greater than the actual date
    $isExpired = date('U') > date('U', $group_data['exerciseSheet']['endDate']); 

    // bool if startDate of sheet is greater than the actual date
    $hasStarted = date('U') > date('U', $group_data['exerciseSheet']['startDate']);
    if ($isExpired){
        set_error(Language::Get('main','expiredExercisePerion', $langTemplate,array('endDate'=>date('d.m.Y  -  H:i', $group_data['exerciseSheet']['endDate']))));
    } elseif (!$hasStarted){
        set_error(Language::Get('main','noStartedExercisePeriod', $langTemplate,array('startDate'=>date('d.m.Y  -  H:i', $group_data['exerciseSheet']['startDate']))));
    }
    
} else
    set_error(Language::Get('main','noExercisePeriod', $langTemplate));


$user_course_data = $group_data['user'];
$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::STUDENT);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array('name' => $user_course_data['courses'][0]['course']['name'],
               'backTitle' => 'zur Veranstaltung',
               'backURL' => "Student.php?cid={$cid}",
               'notificationElements' => $notifications,
               'navigationElement' => $menu));

$isInGroup = (!empty($group_data['group']['members']) || !empty($group_data['invitationsFromGroup']));
$isLeader = isset($group_data['group']['leader']['id']) && $group_data['group']['leader']['id'] == $uid;
$hasInvitations = !empty($group_data['invitationsToGroup']);

$group_data['isInGroup'] = $isInGroup;
$group_data['isLeader'] = $isLeader;

// construct a content element for group information
$groupMembers = Template::WithTemplateFile('include/Group/GroupMembers.template.html');
$groupMembers->bind($group_data);

// construct a content element for managing groups
if ($isInGroup) {
    $groupManagement = Template::WithTemplateFile('include/Group/GroupManagement.template.html');
    $groupManagement->bind($group_data);
}

// construct a content element for creating groups
if ($isLeader) {
    $invitationsFromGroup = Template::WithTemplateFile('include/Group/InvitationsFromGroup.template.html');
    $invitationsFromGroup->bind($group_data);
}

// construct a content element for joining groups
if ($hasInvitations) {
    $invitationsToGroup = Template::WithTemplateFile('include/Group/InvitationsToGroup.template.html');
    $invitationsToGroup->bind($group_data);
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
$w->show();
