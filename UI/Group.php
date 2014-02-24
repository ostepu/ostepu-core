<?php
/**
 * @file Group.php
 * Constructs the page that is displayed to a student, when managing a group.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 *
 * @todo load data from the database and fill the page
 */

include_once 'include/Boilerplate.php';

if (isset($_POST['action'])) {
    // removes all group members and deletes the group
    if ($_POST['action'] == "RemoveGroupMember" && isset($_POST['removeMember'])) {
        // extracts the member that is being removed
        $userID = cleanInput($_POST['removeMember']);

        // creates a new group for the user that is being removed
        $newGroupSettings = Group::encodeGroup(Group::createGroup($userID, $userID, $sid));
        $URI = $databaseURI . "/group/user/{$userID}/exercisesheet/{$sid}";
        http_put_data($URI, $newGroupSettings, true, $message);

        if ($message == "201") {
            $notifications[] = MakeNotification("success", "Der Nutzer {$_POST['kick']} wurde aus der Gruppe entfernt.");
        } else {
            $notifications[] = MakeNotification("error", "Beim Entfernen ist ein Fehler aufgetreten.");
        }
    }

    // removes a group member
    if ($_POST['action'] == "RemoveGroupMember" && isset($_POST['leaveGroup'])) {
        // extracts the leader of the group
        $leaderID = cleanInput($_POST['leaveGroup']);

        // check if the user that wants to leave is the leader of the group
        if ($leaderID == $uid) {
            /**
             * @todo Finish this. 
             */
        } else {
            // creates a new group for the user that wants to leave
            $newGroupSettings = Group::encodeGroup(Group::createGroup($uid, $uid, $sid));
            $URI = $databaseURI . "/group/user/{$uid}/exercisesheet/{$sid}";
            http_put_data($URI, $newGroupSettings, true, $message);

            if ($message == "201") {
                $notifications[] = MakeNotification("success", "Sie haben die Gruppe verlassen.");
            } else {
                $notifications[] = MakeNotification("error", "Beim Verlassen ist ein Fehler aufgetreten.");
            }
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
                if (empty($user_data)) {
                    $notifications[] = MakeNotification("error", "Der User existiert nicht.");
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
}

// load mainSettings data from GetSite
$databaseURI = $getSiteURI . "/group/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
$group_data = http_get($databaseURI, true);
$group_data = json_decode($group_data, true);
$group_data['filesystemURI'] = $filesystemURI;
$group_data['uid'] = $uid;

$user_course_data = $group_data['user'];


// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "notificationElements" => $notifications));

$isInGroup = !empty($group_data['group']['members']);
$isLeader = $group_data['group']['leader']['id'] == $uid;
$hasInvitations = !empty($group_data['invitations']);

$group_data['isInGroup'] = $isInGroup;
$group_data['isLeader'] = $isLeader;

// construct a content element for group information
$groupMembers = Template::WithTemplateFile('include/Group/GroupMembers.template.html');
$groupMembers->bind($group_data);

// construct a content element for managing groups
if ($isInGroup && $isLeader) {
    $manageGroup = Template::WithTemplateFile('include/Group/ManageGroup.template.html');
    $manageGroup->bind($group_data);
} elseif ($isInGroup) {
    /**
     * @todo Construct a different page for the members
     */
}

// construct a content element for creating groups
if ($isLeader) {
    $createGroup = Template::WithTemplateFile('include/Group/InviteGroup.template.html');
    $createGroup->bind($group_data);
}

// construct a content element for joining groups
if ($hasInvitations) {
    $invitations = Template::WithTemplateFile('include/Group/Invitations.template.html');
    $invitations->bind($group_data);
}

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $groupMembers, $manageGroup, $createGroup, $invitations);
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, $groupMembers);
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, $createGroup);
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, $invitations);
$w->set_config_file('include/configs/config_group.json');
$w->show();

?>