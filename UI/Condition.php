<?php
/**
 * @file Condition.php
 * Constructs the page that is displayed when managing exam conditions.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';
include_once '../Assistants/Structures.php';

$notifications = array();

if (isset($_POST['action'])) {
    // creates a new course
    if ($_POST['action'] == "SetCondition") {
        // bool which is true if any error occured
        $RequestError = false;

        foreach ($_POST as $key => $value) {
            // skips the first POST which includes the 'action' type
            if($key == "action") {
                continue;
            }

            // changes the percentage for each exercise type
            $approvalConditionId = $key;
            $percentage = cleanInput($value);

            if (is_numeric($percentage) && $percentage >= 0 && $percentage <= 100) {
                $percentage = $percentage / 100;

                $newApprovalCondition = ApprovalCondition::createApprovalCondition($approvalConditionId, $cid, null, $percentage);
                $newApprovalConditionSettings = ApprovalCondition::encodeApprovalCondition($newApprovalCondition);
                $URI = $databaseURI . "/approvalcondition/approvalcondition/" . $approvalConditionId;
                http_put_data($URI, $newApprovalConditionSettings, true, $message);

                if ($message != "201") {
                    $RequestError = true;
                }
            }
            else {
                $notifications[] = MakeNotification("warning", "Ungültige Eingabe!");
                $RequestError = true;
            }


        }

        // creates a notification depending on RequestError
        if ($RequestError) {
            $notifications[] = MakeNotification("error", "Beim Speichern ist ein Fehler aufgetreten!");
        }
        else {
            $notifications[] = MakeNotification("success", "Die Zulassungsbedingungen wurden erfolgreich gespeichert!");
        }

    }
}

// load user data from the database
$URL = $getSiteURI . "/condition/user/{$uid}/course/{$cid}";
$condition_data = http_get($URL, false);
$condition_data = json_decode($condition_data, true);

$user_course_data = $condition_data['user'];

// manages table sort
if (isset($_GET['sortby'])) {
    $sortBy = cleanInput($_GET['sortby']);

    switch ($sortBy) {
        case "firstName":
            function compare_firstName($a, $b) {
                return strnatcmp($a['firstName'], $b['firstName']);
            }
            usort($condition_data['users'], 'compare_firstName');
            break;

        case "lastName":
            function compare_lastName($a, $b) {
                return strnatcmp($a['lastName'], $b['lastName']);
            }
            usort($condition_data['users'], 'compare_lastName');
            break;

        /**
         * @todo Change when 'Matrikelnummer' is included in database.
         */
        case "userName":
            function compare_userName($a, $b) {
                return $a['userName'] < $b['userName'];
            }
            usort($condition_data['users'], 'compare_userName');
            break;

        case "isApproved":
            function compare_isApproved($a, $b) {
                return strnatcmp($a['isApproved'], $b['isApproved']);
            }
            usort($condition_data['users'], 'compare_isApproved');
            break;
    }
}


 $menu = MakeNavigationElement($user_course_data,
                               PRIVILEGE_LEVEL::ADMIN,
                               true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "notificationElements" => $notifications,
               "navigationElement" => $menu));

// construct a content element for setting exam paper conditions
$setCondition = Template::WithTemplateFile('include/Condition/SetCondition.template.html');
$setCondition->bind($condition_data);

$userList = Template::WithTemplateFile('include/Condition/UserList.template.html');
$userList->bind($condition_data);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $setCondition, $userList);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $setCondition);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
