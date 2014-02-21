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
include_once 'include/FormEvaluator.php';

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
    } elseif ($_POST['action'] == "AddExerciseType") {
        // check if POST data is send
        if(isset($_POST['exerciseTypeName'])) {
            // clean Input
            $exerciseTypeName = cleanInput($_POST['exerciseTypeName']);

            // create new exerciseType
            $data = ExerciseType::encodeExerciseType(ExerciseType::createExerciseType(null, $exerciseTypeName));

            $url = $databaseURI . "/exercisetype";
            http_post_data($url, $data, true, $message);

            // show notification
            if ($message == "201") {
                $notifications[] = MakeNotification("success", "Die Punkteart wurden erfolgreich angelegt!");
            } else {
                $notifications[] = MakeNotification("error", "Beim Speichern ist ein Fehler aufgetreten!");
            }
        } else {
            $notifications[] = MakeNotification("error", "Es wurden nicht alle Felder ausgefüllt!");
        }
    } elseif ($_POST['action'] == "EditExerciseType") {
        // check if POST data is send
        if(isset($_POST['exerciseTypeID']) && isset($_POST['exerciseTypeName'])) {
            // clean Input
            $exerciseTypeID = cleanInput($_POST['exerciseTypeID']);
            $exerciseTypeName = cleanInput($_POST['exerciseTypeName']);

            // create new exerciseType
            $data = ExerciseType::encodeExerciseType(ExerciseType::createExerciseType($exerciseTypeID, $exerciseTypeName));

            $url = $databaseURI . "/exercisetype/" . $exerciseTypeID;
            http_put_data($url, $data, true, $message);

            // show notification
            if ($message == "201") {
                $notifications[] = MakeNotification("success", "Die Punkteart wurden erfolgreich geändert!");
            } else {
                $notifications[] = MakeNotification("error", "Beim Speichern ist ein Fehler aufgetreten!");
            }
        } else {
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
                $data = User::encodeUser(User::createCourseStatus($userID, $cid, $Rights));

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
    } elseif ($_POST['action'] == "AddUser") {

        $f = new FormEvaluator($_POST);

        $f->checkStringForKey('userName',
                              FormEvaluator::REQUIRED,
                              true,
                              'warning',
                              'Ungültiger Nutzername.');

        $f->checkIntegerForKey('rights',
                               FormEvaluator::REQUIRED,
                               'warning',
                               'Ungültige Rechte-ID.',
                               array('min' => 0, 'max' => 2));

        if ($f->evaluate(true)) {
            $foundValues = $f->foundValues;

            $userName = $foundValues['userName'];
            $rights = $foundValues['rights'];

            $URL = $databaseURI . '/user/user/' . $userName;
            $user = http_get($URL, true);
            $user = json_decode($user, true);

            $userId = $user['id'];

            $newUser = User::createCourseStatus($userId, $cid, $rights);
            $newUser = User::encodeUser($newUser);

            $URL = $databaseURI . '/coursestatus';
            http_post_data($URL, $newUser, true, $message);

            if ($message == "201") {
                $notifications[] = MakeNotification('success',
                                                    'Der Beutzer wurde'
                                                    .' erfolgreich in die'
                                                    .' Veranstaltung eingetragem.');
            } else {
                set_error("409");
                exit;
            }
        } else {
            $notifications = $notifications + $f->notifications;
        }
    } else {
        $notifications[] = MakeNotification('error',
                                            'Unbekannte Aktion.');
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
               "notificationElements" => $notifications,
               "navigationElement" => $menu));

// construct a content element for changing course settings
$courseSettings = Template::WithTemplateFile('include/CourseManagement/CourseSettings.template.html');
$courseSettings->bind($courseManagement_data);

// construct a content element for adding exercise types
$addExerciseType = Template::WithTemplateFile('include/CourseManagement/AddExerciseType.template.html');

// construct a content element for editing exercise types
$editExerciseType = Template::WithTemplateFile('include/CourseManagement/EditExerciseType.template.html');
$editExerciseType->bind($courseManagement_data);

// construct a content element for granting user-rights
$grantRights = Template::WithTemplateFile('include/CourseManagement/GrantRights.template.html');
$grantRights->bind($courseManagement_data);

// construct a content element for taking away a user's user-rights
$revokeRights = Template::WithTemplateFile('include/CourseManagement/RevokeRights.template.html');
$revokeRights->bind($courseManagement_data);

// construct a content element for adding users
$addUser = Template::WithTemplateFile('include/CourseManagement/AddUser.template.html');

/**
 * @todo combine the templates into a single file
 */

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $courseSettings, $addExerciseType, $editExerciseType, $grantRights, $revokeRights, $addUser);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $courseSettings);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $addExerciseType);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $editExerciseType);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $grantRights);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $revokeRights);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $addUser);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
