<?php
/**
 * @file Group.php
 * Constructs the page that is displayed to a student, when managing a group.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

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
            $notifications[] = MakeNotification("error", "Beim Entfernen ist ein Fehler aufgetreten.");
        } else {
            $notifications[] = MakeNotification("success", "Der Nutzer wurde aus der Gruppe entfernt.");
        }
    }

    // removes an invitation from the group
    if ($_POST['action'] == "RemoveGroupMember" && isset($_POST['removeInvitation'])) {
        // extracts the member whose invitation is being removed
        $userID = cleanInput($_POST['removeInvitation']);
        
        // deletes the invitation
        $URI = $databaseURI . "/invitation/user/{$uid}/exercisesheet/{$sid}/user/{$userID}";
        http_delete($URI, true, $message);

        if ($message == "201") {
            $notifications[] = MakeNotification("success", "Die Einladung wurde gelöscht.");
        } else {
            $notifications[] = MakeNotification("error", "Beim Löschen der Einladung ist ein Fehler aufgetreten.");
        }
    }

    // removes all group members and deletes the group
    if ($_POST['action'] == "RemoveGroupMember" && isset($_POST['leaveGroup'])) {
        // extracts the leader of the group
        $leaderID = cleanInput($_POST['leaveGroup']);

        // checks if the user that wants to leave is the leader of the group
        if ($leaderID == $uid) {
            // bool which is true if any error occured
            $RequestError = false;

            // returns all invitations from the groups
            $URI = $databaseURI . "/invitation/leader/exercisesheet/{$sid}/user/{$leaderID}";
            $invitations = http_get($URI, true);
            $invitations = json_decode($invitations, true);

            // returns the leader and all members of the group
            $URI = $databaseURI . "/group/user/{$uid}/exercisesheet/{$sid}";
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
                if (!empty($group['members'])) {
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
                    $notifications[] = MakeNotification("error", "Beim Verlassen ist ein Fehler aufgetreten!");
                }
                else {
                    $notifications[] = MakeNotification("success", "Sie haben die Gruppe verlassen.");
                }

            } else {
                $notifications[] = MakeNotification("error", "Beim Verlassen ist ein Fehler aufgetreten.");
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
                $notifications[] = MakeNotification("error", "Fehler else.");
            }

            // shows notification
            if ($RequestError) {
                $notifications[] = MakeNotification("error", "Beim Verlassen ist ein Fehler aufgetreten.");
            } else {
                $notifications[] = MakeNotification("success", "Sie haben die Gruppe verlassen.");
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
                                     $uid,
                                     $submissionID,
                                     $exerciseID,
                                     $message);

            if ($message != "201") {
                $RequestError = true;
            }
        }

        // shows notification
        if ($RequestError == false) {
            $notifications[] = MakeNotification("success", "Die Einsendungen wurden ausgewählt.");
        }
        else {
            $notifications[] = MakeNotification("error", "Beim Speichern ist ein Fehler aufgetreten!");
        }
    }

    // invites users to the group
    if ($_POST['action'] == "InviteGroup") {
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
                if (empty($user_data) || $user_data['id'] == $uid) {
                    $notifications[] = MakeNotification("error", "Ungültiges Kürzel.");
                } else {
                    $memberID = $user_data['id'];

                    $newInvitation = Invitation::encodeInvitation(Invitation::createInvitation($uid, $memberID, $sid));
                    $URI = $databaseURI . "/invitation";
                    http_post_data($URI, $newInvitation, true, $message);

                    if ($message == "201") {
                        $notifications[] = MakeNotification("success", "Der Nutzer {$memberName} wurde eingeladen.");
                    } else {
                        $notifications[] = MakeNotification("error", "Bei der Einladung ist ein Fehler aufgetreten. 
                            Eventuell wurde schon eine Einladung an {$memberName} gesendet.");
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
        $URI = $databaseURI . "/invitation/user/{$leaderID}/exercisesheet/{$sid}/user/{$uid}";
        http_delete($URI, true, $message);

        if ($message == "201") {
            $notifications[] = MakeNotification("success", "Die Einladung wurde abgelehnt.");
        } else {
            $notifications[] = MakeNotification("error", "Beim Ablehnen der Einladung ist ein Fehler aufgetreten.");
        }
    }

    // accepts an invitation to a group
    if ($_POST['action'] == "ManageInvitations" && isset($_POST['acceptInvitation'])) {
        // bool which is true if any error occured
        $RequestError = false;

        // extracts the leader of the invitation that is being removed
        $leaderID = cleanInput($_POST['acceptInvitation']);

        // adds the user to the group
        $newGroupSettings = Group::encodeGroup(Group::createGroup($leaderID, $uid, $sid));
        $URI = $databaseURI . "/group/user/{$uid}/exercisesheet/{$sid}";
        http_put_data($URI, $newGroupSettings, true, $message);

        if ($message != "201") {
            $RequestError = true;
        }

        // deletes the invitation
        $URI = $databaseURI . "/invitation/user/{$leaderID}/exercisesheet/{$sid}/user/{$uid}";
        http_delete($URI, true, $message);

        if ($message != "201") {
            $RequestError = true;
        }

        // deletes all selectedSubmissions
        if (!removeSelectedSubmission($uid, $sid)) {
            $RequestError = true;
        }

        // shows notification
        if ($RequestError == false) {
            $notifications[] = MakeNotification("success", "Sie haben die Einladung angenommen.");
        }
        else {
            $notifications[] = MakeNotification("error", "Beim Annehmen der Einladung ist ein Fehler aufgetreten!");
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


// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "notificationElements" => $notifications));

$isInGroup = (!empty($group_data['group']['members']) || !empty($group_data['invitationsFromGroup']));
$isLeader = $group_data['group']['leader']['id'] == $uid;
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
$w = new HTMLWrapper($h, $groupMembers, (isset($groupManagement) ? $groupManagement : null), (isset($invitationsFromGroup) ? $invitationsFromGroup : null), (isset($invitationsToGroup) ? $invitationsToGroup : null));
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $groupMembers);

if (isset($groupManagement))
    $w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $groupManagement);
if (isset($invitationsFromGroup))
    $w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $invitationsFromGroup);
if (isset($invitationsToGroup))
    $w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $invitationsToGroup);
$w->set_config_file('include/configs/config_group.json');
$w->show();

?>