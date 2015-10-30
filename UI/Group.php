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

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::STUDENT, $cid, $uid, $globalUserData);

$langTemplate='Group_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

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

    if ($message == "201") {
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

    if ($message == "201") {
        return true;
    } else {
        return false;
    }
}

if (isset($_POST['action'])) {
    // removes a group member
    if ($_POST['action'] == "RemoveGroupMember" && isset($_POST['removeMember'])) {
        // extracts the member that is being removed
        $userID = cleanInput($_POST['removeMember']);

        // bool which is true if any error occured
        $RequestError = false;

        // removes the user from the group
        if (removeUserFromGroup($userID, $sid)) {
            if (!removeSelectedSubmission($userID, $sid)) {
                $RequestError = true;
            }
        } else {
            $RequestError = true;
        }

        if ($RequestError) {
            $notifications[] = MakeNotification("error", Language::Get('main','errorRemoveMember', $langTemplate));
        } else {
            $notifications[] = MakeNotification("success", Language::Get('main','successRemoveMember', $langTemplate));
        }
    }

    // removes an invitation from the group
    if ($_POST['action'] == "RemoveGroupMember" && isset($_POST['removeInvitation'])) {
        // extracts the member whose invitation is being removed
        $userID = cleanInput($_POST['removeInvitation']);
        
        // deletes the invitation
        $URI = $databaseURI . "/invitation/user/{$selectedUser}/exercisesheet/{$sid}/user/{$userID}";
        http_delete($URI, true, $message);

        if ($message == "201") {
            $notifications[] = MakeNotification("success", Language::Get('main','successRemoveInvitation', $langTemplate));
        } else {
            $notifications[] = MakeNotification("error", Language::Get('main','errorRemoveInvitation', $langTemplate));
        }
    }

    // removes all group members and deletes the group
    if ($_POST['action'] == "RemoveGroupMember" && isset($_POST['leaveGroup'])) {
        // extracts the leader of the group
        $leaderID = cleanInput($_POST['leaveGroup']);

        // checks if the user that wants to leave is the leader of the group
        if ($leaderID == $selectedUser) {
            // bool which is true if any error occured
            $RequestError = false;

            // returns all invitations from the groups
            $URI = $databaseURI . "/invitation/leader/exercisesheet/{$sid}/user/{$leaderID}";
            $invitations = http_get($URI, true);
            $invitations = json_decode($invitations, true);

            // returns the leader and all members of the group
            $URI = $databaseURI . "/group/user/{$selectedUser}/exercisesheet/{$sid}";
            $group = http_get($URI, true);
            $group = json_decode($group, true);

            // removes all invitations of the group
            if (!empty($invitations)) {
                foreach ($invitations as $invitation) {
                    $URI = $databaseURI . "/invitation/user/{$leaderID}/";
                    $URI .= "exercisesheet/{$sid}/user/{$invitation['member']['id']}";

                    http_delete($URI, true, $message);

                    if ($message != "201") {
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
                    $notifications[] = MakeNotification("error", Language::Get('main','errorLeaveGroup', $langTemplate));
                }
                else {
                    $notifications[] = MakeNotification("success", Language::Get('main','successLeaveGroup', $langTemplate));
                }

            } else {
                $notifications[] = MakeNotification("error", Language::Get('main','errorLeaveGroup', $langTemplate));
            }

        } else {
            // bool which is true if any error occured
            $RequestError = false;

            // removes the user from the group
            if (removeUserFromGroup($selectedUser, $sid)) {
                if (!removeSelectedSubmission($selectedUser, $sid)) {
                    $RequestError = true;
                }
            } else {
                $RequestError = true;
                ///$notifications[] = MakeNotification("error", "Fehler else.");
            }

            // shows notification
            if ($RequestError) {
                $notifications[] = MakeNotification("error", Language::Get('main','errorLeaveGroup', $langTemplate));
            } else {
                $notifications[] = MakeNotification("success", Language::Get('main','successLeaveGroup', $langTemplate));
            }
        }
    }

    // updates the selectedSubmissions for the group
    if ($_POST['action'] == "ManageGroup" && isset($_POST['exercises'])) {
        $exercises = cleanInput($_POST['exercises']);

        // bool which is true if any error occured
        $RequestError = false;
        
        // extracts the exerciseIDs and the submissionIDs and updates
        // the selectedSubmissions
        foreach ($exercises as $key => $value) {
            $exerciseID = $key;
            $submissionID = $value;

            updateSelectedSubmission($databaseURI,
                                     $selectedUser,
                                     $submissionID,
                                     $exerciseID,
                                     $message);

            if ($message != "201") {
                $RequestError = true;
            }
        }

        // shows notification
        if ($RequestError == false) {
            $notifications[] = MakeNotification("success", Language::Get('main','successSelectSubmission', $langTemplate));
        }
        else {
            $notifications[] = MakeNotification("error", Language::Get('main','errorSelectSubmission', $langTemplate));
        }
    }

    // apply last group
    if ($_POST['action'] == "InviteGroup" && isset($_POST['applyGroup'])) {
        $RequestError = false;
        
        if (isset($_POST['members'])) {
            // invite old members
            foreach ($_POST['members'] as $member){
                $newInvitation = Invitation::encodeInvitation(Invitation::createInvitation($selectedUser, $member, $sid));
                $URI = $databaseURI . "/invitation";
                http_post_data($URI, $newInvitation, true, $message);

                if ($message != "201") {
                    $RequestError=true;
                }
            }
            if (!$RequestError){
                $notifications[] = MakeNotification("success", Language::Get('main','successInviteMembers', $langTemplate));
                // accept invitations
                foreach ($_POST['members'] as $member){
                    
                    // adds the user to the group
                    $newGroupSettings = Group::encodeGroup(Group::createGroup($selectedUser, $member, $sid));
                    $URI = $databaseURI . "/group/user/{$member}/exercisesheet/{$sid}";
                    $answ = http_put_data($URI, $newGroupSettings, true, $message);
                    if ($message != "201") {
                        $RequestError = true;
                        continue;
                    }
                    
                    // deletes the invitation
                    $URI = $databaseURI . "/invitation/user/{$selectedUser}/exercisesheet/{$sid}/user/{$member}";
                    http_delete($URI, true, $message);

                    if ($message != "201") {
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
                    $notifications[] = MakeNotification("success", Language::Get('main','successJoinMembers', $langTemplate));
                } else {
                    $notifications[] = MakeNotification("error", Language::Get('main','errorJoinMember', $langTemplate));
                }
        
            } else {
               $notifications[] = MakeNotification("error", Language::Get('main','errorInviteMember', $langTemplate));
            }
        } else {
            $notifications[] = MakeNotification("error", Language::Get('main','errorApplyGroup', $langTemplate));
            $RequestError = true;
        }
    }
    
    // invites users to the group
    if ($_POST['action'] == "InviteGroup" && !isset($_POST['applyGroup'])) {
        if (isset($_POST['userName'])) {
            foreach ($_POST['userName'] as $key => $memberName) {
                
                // skips empty input fields
                if (empty($memberName)) {
                    continue;
                }

                // extracts the memberName
                $memberName = cleanInput($memberName);

                // extracts the userID
                $URI = $databaseURI . "/user/user/{$memberName}";
                $user_data = http_get($URI, true);
                $user_data = json_decode($user_data, true);

                // invites the user to the current group
                if (!isset($user_data['id']) || empty($user_data) || $user_data['id'] == $selectedUser) {
                    $notifications[] = MakeNotification("error", Language::Get('main','invalidUserId', $langTemplate));
                } else {
                    $memberID = $user_data['id'];

                    $newInvitation = Invitation::encodeInvitation(Invitation::createInvitation($selectedUser, $memberID, $sid));
                    $URI = $databaseURI . "/invitation";
                    http_post_data($URI, $newInvitation, true, $message);

                    if ($message == "201") {
                        $notifications[] = MakeNotification("success", Language::Get('main','successInviteMember', $langTemplate, array('memberName'=>$memberName)));
                    } else {
                        $notifications[] = MakeNotification("error", Language::Get('main','errorInviteMember', $langTemplate, array('memberName'=>$memberName)));
                    }
                }
            }
        }
    }

    // removes an invitation to a group
    if ($_POST['action'] == "ManageInvitations" && isset($_POST['denyInvitation'])) {
        // extracts the leader of the invitation that is being removed
        $leaderID = cleanInput($_POST['denyInvitation']);

        // deletes the invitation
        $URI = $databaseURI . "/invitation/user/{$leaderID}/exercisesheet/{$sid}/user/{$selectedUser}";
        http_delete($URI, true, $message);

        if ($message == "201") {
            $notifications[] = MakeNotification("success", Language::Get('main','successRejectInvitation', $langTemplate));
        } else {
            $notifications[] = MakeNotification("error", Language::Get('main','errorRejectInvitation', $langTemplate));
        }
    }

    // accepts an invitation to a group
    if ($_POST['action'] == "ManageInvitations" && isset($_POST['acceptInvitation'])) {
        // bool which is true if any error occured
        $RequestError = false;

        // extracts the leader of the invitation that is being removed
        $leaderID = cleanInput($_POST['acceptInvitation']);

        // adds the user to the group
        $newGroupSettings = Group::encodeGroup(Group::createGroup($leaderID, $selectedUser, $sid));
        $URI = $databaseURI . "/group/user/{$selectedUser}/exercisesheet/{$sid}";
        http_put_data($URI, $newGroupSettings, true, $message);

        if ($message != "201") {
            $RequestError = true;
        }

        // deletes the invitation
        $URI = $databaseURI . "/invitation/user/{$leaderID}/exercisesheet/{$sid}/user/{$selectedUser}";
        http_delete($URI, true, $message);

        if ($message != "201") {
            $RequestError = true;
        }

        // deletes all selectedSubmissions
        if (!removeSelectedSubmission($selectedUser, $sid)) {
            $RequestError = true;
        }

        // shows notification
        if ($RequestError == false) {
            $notifications[] = MakeNotification("success", Language::Get('main','successJoinGroup', $langTemplate));
        }
        else {
            $notifications[] = MakeNotification("error", Language::Get('main','errorJoinGroup', $langTemplate));
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
    $URI = $serverURI . "/DB/DBUser/user/course/{$cid}/status/0";
    $courseUser = http_get($URI, true);
    $courseUser = User::decodeUser($courseUser);
    $URI = $serverURI . "/DB/DBExerciseSheet/exercisesheet/course/{$cid}/";
    $courseSheets = http_get($URI, true);
    $courseSheets = ExerciseSheet::decodeExerciseSheet($courseSheets);
    $courseSheets = array_reverse($courseSheets);
    
    if (!$privileged){
        foreach ($courseSheets as $key => $sheet){
            if ($sheet->getGroupSize() === null || $sheet->getGroupSize() <= 1){
                unset($courseSheets[$key]);
                continue;
            }
            
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
    }
    
    $userNavigation = MakeUserNavigationElement($globalUserData,$courseUser,$privileged,
                                                PRIVILEGE_LEVEL::LECTURER,$sid,$courseSheets);
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Student.php?cid={$cid}",
               "notificationElements" => $notifications,
               "navigationElement" => $menu,
               "userNavigationElement" => $userNavigation));

$isInGroup = (!empty($group_data['group']['members']) || !empty($group_data['invitationsFromGroup']));
$isLeader = isset($group_data['group']['leader']['id']) && $group_data['group']['leader']['id'] == $selectedUser;
$hasInvitations = !empty($group_data['invitationsToGroup']);

$group_data['isInGroup'] = $isInGroup;
$group_data['isLeader'] = $isLeader;

// construct a content element for group information
$groupMembers = Template::WithTemplateFile('include/Group/GroupMembers.template.html');
$groupMembers->bind($group_data);
$groupMembers->bind(array("privileged" => $privileged));

// construct a content element for managing groups
if ($isInGroup) {
    $groupManagement = Template::WithTemplateFile('include/Group/GroupManagement.template.html');
    $groupManagement->bind($group_data);
    $groupManagement->bind(array("privileged" => $privileged));
}

// construct a content element for creating groups
if ($isLeader) {
    $invitationsFromGroup = Template::WithTemplateFile('include/Group/InvitationsFromGroup.template.html');
    $invitationsFromGroup->bind($group_data);
    $invitationsFromGroup->bind(array("privileged" => $privileged));
}

// construct a content element for joining groups
if ($hasInvitations) {
    $invitationsToGroup = Template::WithTemplateFile('include/Group/InvitationsToGroup.template.html');
    $invitationsToGroup->bind($group_data);
    $invitationsToGroup->bind(array("privileged" => $privileged));
}

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $groupMembers, (isset($invitationsToGroup) ? $invitationsToGroup : null), (isset($groupManagement) ? $groupManagement : null), (isset($invitationsFromGroup) ? $invitationsFromGroup : null));
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $groupMembers);

if (isset($groupManagement))
    $w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $groupManagement);
if (isset($invitationsFromGroup))
    $w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $invitationsFromGroup);
if (isset($invitationsToGroup))
    $w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $invitationsToGroup);
$w->set_config_file('include/configs/config_group.json');
$w->show();
