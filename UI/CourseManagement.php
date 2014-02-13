<?php
/**
 * @file CourseManagement.php
 * Constructs the page that is used to grant and revoke a user's user-rights
 * and to change basic course settings.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 *
 * @todo PUT Request to logic not to DB
 * @todo check Rights for whole page
 * @todo use logic Controller instead of database
 * @todo you have to confirm your action before deleting coursestatus
 */

include_once 'include/Boilerplate.php';
include_once '../Assistants/Structures.php';

if (isset($_POST['action'])) {
    if ($_POST['action'] == "CourseSettings") {
        // check if POST data is send
        if(isset($_POST['courseName']) && isset($_POST['semester']) && isset($_POST['defaultGroupSize'])) {

            // bool which is true if any error occured
            $RequestError = false;

            // extracts the php POST data
            $courseName = cleanInput($_POST['courseName']);
            $semester = cleanInput($_POST['semester']);
            $defaultGroupSize = cleanInput($_POST['defaultGroupSize']);
            $selectedExerciseTypes = cleanInput($_POST['exerciseTypes']);

            // loads ApprovalConditions from database
            $URI = $databaseURI . "/approvalcondition/course/{$cid}";
            $approvalCondition_data = http_get($URI, true);
            $approvalCondition_data = json_decode($approvalCondition_data, true);

            // determines all possible exercise types of the course
            foreach($approvalCondition_data as $approvalCondition) {
                $currentExerciseTypes[] = $approvalCondition['exerciseTypeId'];
                $currentExerciseTypesByApprovalId[$approvalCondition['exerciseTypeId']] = $approvalCondition['id'];
            }

            // exercise types which already exist in the database and need to be deleted
            $etDelete = array_diff($currentExerciseTypes, $selectedExerciseTypes);

            // exercises types which don't exist in the database and need to be created
            $etCreate = array_diff($selectedExerciseTypes, $currentExerciseTypes);

            // deletes approvalConditions
            foreach($etDelete as $exerciseType) {
                $URI = $databaseURI . "/approvalcondition/" . $currentExerciseTypesByApprovalId[$exerciseType];
                http_delete($URI, true, $message);

                if ($message != "201") {
                    $RequestError = true;
                }
            }

            // adds approvalConditions
            foreach($etCreate as $exerciseType) {
                $newApprovalConditionSettings = ApprovalCondition::encodeApprovalCondition(
                    ApprovalCondition::createApprovalCondition(null, $cid, $exerciseType, 0));
                $URI = $databaseURI . "/approvalcondition";
                http_post_data($URI, $newApprovalConditionSettings, true, $message);

                if ($message != "201") {
                    $RequestError = true;
                }
            }

            // create new course and edit existing one
            $newCourseSettings = Course::encodeCourse(Course::createCourse($cid,$courseName,$semester,$defaultGroupSize));
            $URI = $databaseURI . "/course/course/{$cid}";
            $courseManagement_data = http_put_data($URI, $newCourseSettings, true, $message);

            // show notification
            if ($message == "201" && $RequestError == false) {
                $notifications[] = MakeNotification("success", "Die Veranstaltung wurde bearbeitet!");
            }
            else {
                $notifications[] = MakeNotification("error", "Beim Speichern ist ein Fehler aufgetreten!");
            }
        }
        else {
            $notifications[] = MakeNotification("error", "Es wurden nicht alle Felder ausgefüllt!");
        }
    } elseif ($_POST['action'] == "GrantRights") {
        // check if POST data is send
        if(isset($_POST['userID']) && isset($_POST['Rights'])) {
            // clean Input
            $userID = cleanInput($_POST['userID']);
            $Rights = cleanInput($_POST['Rights']);

            // validate POST data
            if (is_numeric($userID) == true && is_numeric($Rights) && $Rights >= 0 && $Rights < 3) {
                // create new coursestatus and edit existing one
                $data = User::encodeUser(User::createCourseStatus($userID,$cid,$Rights));

                $url = $databaseURI . "/coursestatus/course/{$cid}/user/{$userID}";
                http_put_data($url, $data, true, $message);

                // show notification
                if ($message == "201") {
                    $notifications[] = MakeNotification("success", "Die Rechte wurden erfolgreich vergeben!");
                }
            } else {
                // otherwise show conflict page
                set_error("409");
                exit();
            }
        } else {
            $notifications[] = MakeNotification("error", "Es wurden nicht alle Felder ausgefüllt!");
        }
    } elseif ($_POST['action'] == "RevokeRights") {
        // check if POST data is send
        if(isset($_POST['userID'])) {
            // clean Input
            $userID = cleanInput($_POST['userID']);
            $Rights = cleanInput($_POST['Rights']);

            // validate POST data
            if (is_numeric($userID) == true) {
                // delete coursestatus
                $url = $databaseURI . "/coursestatus/course/{$cid}/user/{$userID}";
                http_delete($url, true, $message);

                // show notification
                if ($message == "201") {
                    $notifications[] = MakeNotification("success", "Der Nutzer wurde aus der Veranstaltung entfernt!");
                }
            } else {
                // otherwise show conflict page
                set_error("409");
                exit();
            }
        } else {
            $notifications[] = MakeNotification("error", "Es wurden nicht alle Felder ausgefüllt!");
        }
    }
}

// load CourseManagement data from GetSite
$URI = $getSiteURI . "/coursemanagement/user/{$uid}/course/{$cid}";
$courseManagement_data = http_get($URI, true);
$courseManagement_data = json_decode($courseManagement_data, true);

$user_course_data = $courseManagement_data['user'];

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::ADMIN,
                              true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Admin.php?cid={$cid}",
               "notificationElements" => $notifications,
               "navigationElement" => $menu));

// construct a content element for changing course settings
$courseSettings = Template::WithTemplateFile('include/CourseManagement/CourseSettings.template.html');
$courseSettings->bind($courseManagement_data);

// construct a content element for granting user-rights
$grantRights = Template::WithTemplateFile('include/CourseManagement/GrantRights.template.html');
$grantRights->bind($courseManagement_data);

// construct a content element for taking away a user's user-rights
$revokeRights = Template::WithTemplateFile('include/CourseManagement/RevokeRights.template.html');
$revokeRights->bind($courseManagement_data);

/**
 * @todo combine the templates into a single file
 */

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $courseSettings, $grantRights, $revokeRights);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $courseSettings);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $grantRights);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $revokeRights);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
