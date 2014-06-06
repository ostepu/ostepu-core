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

// load Plugins data from LogicController
$URI = $serverURI . "/logic/LExtension/link/extension";
$temp = http_get($URI, true);
$plugins_data=array();
$plugins_data['plugins'] = json_decode($temp, true);

$URI = $serverURI . "/logic/LExtension/link/course/{$cid}/extension";
$temp = http_get($URI, true);
$temp = json_decode($temp, true);
$installedPlugins = array();
foreach ($plugins_data['plugins'] as &$plugin){
    foreach ($temp as &$installed){
        if ($plugin['target'] === $installed['target']){
            $installedPlugins[] = $installed['target'];
            unset($installed);
            $plugin['isInstalled'] = 1;
            break;
        }
    }  
}

if (isset($_POST['action'])) {
    if ($_POST['action'] == "Plugins") {
        
        $plugins = cleanInput($_POST['plugins']);
        
        // bool which is true if any error occured
        $RequestError=false;
        
        // which need to be deleted
        if (!empty($plugins)) {
            $etDelete = array_diff($installedPlugins, $plugins);
        } else {
            $etDelete = $installedPlugins;
        }
        
        // which need to be installed
        if (!empty($installedPlugins)) {
            if (!is_array($plugins)) $plugins = array($plugins);
            $etCreate = array_diff($plugins, $installedPlugins);
        } else {
            $etCreate = $plugins;
        }

        // install Plugins
        if (isset($etCreate) && !empty($etCreate)){
            foreach ($etCreate as $plugin2) {
                if ($plugin2 === '') continue;
                $URI = $serverURI . "/logic/LExtension/link/course/{$cid}/extension/{$plugin2}";
                http_post_data($URI, '', true, $messageNewAc);
                if ($messageNewAc != "201") {
                    $RequestError = true;
                    break;
                }
            }
        }
     
        // uninstall Plugins
        if (isset($etDelete) && !empty($etDelete)){
            foreach ($etDelete as $plugin3) {
                if ($plugin3 === '') continue;
                $URI = $serverURI . "/logic/LExtension/link/course/{$cid}/extension/{$plugin3}";
                http_delete($URI, true, $messageNewAc);
                if ($messageNewAc != "201") {
                    $RequestError = true;
                    break;
                }
            }
        }
        
        // load Plugins data from LogicController
        $URI = $serverURI . "/logic/LExtension/link/extension";
        $temp = http_get($URI, true);
        $plugins_data=array();
        $plugins_data['plugins'] = json_decode($temp, true);

        $URI = $serverURI . "/logic/LExtension/link/course/{$cid}/extension";
        $temp = http_get($URI, true);
        $temp = json_decode($temp, true);
        $installedPlugins = array();
        foreach ($plugins_data['plugins'] as &$plugin){
            foreach ($temp as &$installed){
                if ($plugin['target'] === $installed['target']){
                    $installedPlugins[] = $installed['target'];
                    unset($installed);
                    $plugin['isInstalled'] = 1;
                    break;
                }
            }  
        }

        // show notification
        if ($RequestError == false) {
            $notifications[] = MakeNotification("success", "Die Veranstaltung wurde bearbeitet!");
        }
        else {
            $notifications[] = MakeNotification("error", "Beim Speichern ist ein Fehler aufgetreten!");
        }
    
    } elseif ($_POST['action'] == "CourseSettings") {
        // check if POST data is send
        if(isset($_POST['courseName']) && isset($_POST['semester']) && isset($_POST['defaultGroupSize'])) {

            // bool which is true if any error occured
            $RequestError = false;

            // extracts the php POST data
            $courseName = cleanInput((isset($_POST['courseName']) ? $_POST['courseName'] : '' ));
            $semester = cleanInput((isset($_POST['semester']) ? $_POST['semester'] : '' ));
            $defaultGroupSize = cleanInput((isset($_POST['defaultGroupSize']) ? $_POST['defaultGroupSize'] : '' ));
            $selectedExerciseTypes = cleanInput((isset($_POST['exerciseTypes']) ? $_POST['exerciseTypes'] : '' ));

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
            if(isset($currentExerciseTypes)){
                if (!empty($selectedExerciseTypes)) {
                    if (!is_array($currentExerciseTypes)) $currentExerciseTypes = array($currentExerciseTypes);
                    $etDelete = array_diff($currentExerciseTypes, $selectedExerciseTypes);
                } else {
                    $etDelete = $currentExerciseTypes;
                }
            }

            // exercises types which don't exist in the database and need to be created
            if (isset($currentExerciseTypes) && !empty($currentExerciseTypes)) {
                if (!is_array($selectedExerciseTypes)) $selectedExerciseTypes = array($selectedExerciseTypes);
                $etCreate = array_diff($selectedExerciseTypes, $currentExerciseTypes);
            } else {
                $etCreate = $selectedExerciseTypes;
            }

            if (isset($etDelete)){
                if ($etDelete == null) $etDelete = array();
                if (!is_array($etDelete)) $etDelete = array($etDelete);
                // deletes approvalConditions
                if (isset($currentExerciseTypesByApprovalId)){
                    foreach($etDelete as $exerciseType2) {
                        if ($exerciseType2==='')continue;
                        $URI = $databaseURI . "/approvalcondition/" . $currentExerciseTypesByApprovalId[$exerciseType2];
                        http_delete($URI, true, $message);

                        if ($message != "201") {
                            $RequestError = true;
                        }
                    }
                }
            }

            if (isset($etCreate)){
                if ($etCreate == null) $etCreate = array();
                if (!is_array($etCreate)) $etCreate = array($etCreate);
                // adds approvalConditions
                foreach($etCreate as $exerciseType3) {
                    if ($exerciseType3==='')continue;
                    $newApprovalConditionSettings = ApprovalCondition::encodeApprovalCondition(
                        ApprovalCondition::createApprovalCondition(null, $cid, $exerciseType3, 0));
                    $URI = $databaseURI . "/approvalcondition";
                    http_post_data($URI, $newApprovalConditionSettings, true, $message);

                    if ($message != "201") {
                        $RequestError = true;
                    }
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
                              'warning',
                              'Ungültiger Nutzername.',
                              array('min' => 1));

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
                                                    'Der Nutzer wurde'
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

// construct a content element for plugins
$plugins = Template::WithTemplateFile('include/CourseManagement/Plugins.template.html');
$plugins->bind($plugins_data);

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
$w = new HTMLWrapper($h, $courseSettings, $plugins, $addExerciseType, $editExerciseType, $grantRights, $revokeRights, $addUser);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $courseSettings);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $plugins);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $addExerciseType);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $editExerciseType);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $grantRights);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $revokeRights);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $addUser);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
