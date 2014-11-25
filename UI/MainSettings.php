<?php
/**
 * @file MainSettings.php
 * Constructs the page that is used to create and delete users and
 * to create new courses.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 *
 * @todo POST Request to logic instead of DB
 * @todo check rights for whole page
 * @todo create a navigation bar for super admins
 * @todo unset $_POST on success
 */

include_once 'include/Boilerplate.php';
include_once '../Assistants/Structures.php';
include_once 'include/FormEvaluator.php';

// load Plugins data from LogicController
$URI = $serverURI . "/logic/LExtension/link/extension";
$temp = http_get($URI, true);
$plugins_data = json_decode($temp, true);

if (isset($_POST['action'])) {
    // creates a new course
    if ($_POST['action'] == "CreateCourse") {

        $f = new FormEvaluator($_POST);
        $f->checkStringForKey('courseName',
                              FormEvaluator::REQUIRED,
                              'warning',
                              'Ungültiger Kursname.',
                              array('min' => 1));

        $f->checkStringForKey('semester',
                              FormEvaluator::REQUIRED,
                              array('min' => 1),
                              'warning',
                              'Ungültiges Semester.');

        $f->checkIntegerForKey('defaultGroupSize',
                              FormEvaluator::REQUIRED,
                              'warning',
                              'Ungültige Gruppengröße.',
                              array('min' => 0));

        $f->checkArrayOfIntegersForKey('exerciseTypes',
                                       FormEvaluator::OPTIONAL,
                                       'warning',
                                       'Ungültige Aufgabentypen.');
                                       
        $f->checkArrayOfIntegersForKey('plugins',
                               FormEvaluator::OPTIONAL,
                               'warning',
                               'keine Erweiterungen gewählt.');

        if($f->evaluate(true)) {
            // bool which is true if any error occured
            $RequestError = false;

            $foundValues = $f->foundValues;

            // extracts the php POST data
            $courseName = $foundValues['courseName'];
            $semester = $foundValues['semester'];
            $defaultGroupSize = $foundValues['defaultGroupSize'];
            $plugins = $foundValues['plugins'];
            $exerciseTypes = $foundValues['exerciseTypes'];

            // creates a new course
            $newCourse = Course::createCourse(null, $courseName, $semester, $defaultGroupSize);
            $newCourseSettings = Course::encodeCourse($newCourse);
            $URI = $logicURI . "/course";
            $newCourse = http_post_data($URI, $newCourseSettings, true, $messageNewCourse);

            // extracts the id of the new course
            $newCourse = json_decode($newCourse, true);
            $newCourseId = $newCourse['id'];

            // creates a new approvalCondition for every selected exerciseType
            if (isset($exerciseTypes) && !empty($exerciseTypes)){
                foreach ($exerciseTypes as $exerciseType) {
                    $newApprovalCondition = ApprovalCondition::createApprovalCondition(null,
                                                                                       $newCourseId,
                                                                                       $exerciseType,
                                                                                       0);
                    $newApprovalConditionSettings = ApprovalCondition::encodeApprovalCondition($newApprovalCondition);
                    $URI = $databaseURI . "/approvalcondition";
                    http_post_data($URI, $newApprovalConditionSettings, true, $messageNewAc);

                    if ($messageNewAc != "201") {
                        $RequestError = true;
                        break;
                    }

                }
            }

            // create Plugins
            if (isset($plugins) && !empty($plugins)){
                foreach ($plugins as $plugin) {
                    $URI = $serverURI . "/logic/LExtension/link/course/{$newCourseId}/extension/{$plugin}";
                    http_post_data($URI, '', true, $messageNewAc);
                    if ($messageNewAc != "201") {
                        $RequestError = true;
                        break;
                    }
                }
            }

            // creates a notification depending on RequestError
            if ($messageNewCourse == "201"
                && $RequestError == false) {
                $notifications[] = MakeNotification("success",
                                                    "Die Veranstaltung wurde erstellt!");
            } else {
                $notifications[] = MakeNotification("error",
                                                    "Beim Speichern ist ein Fehler aufgetreten!");
            }
        } else {
            $notifications = $notifications + $f->notifications;
        }
    }

    if ($_POST['action'] == "SetAdmin") {
        // check if POST data is send
        if(isset($_POST['courseID']) && isset($_POST['userName'])) {
            // clean Input
            $courseID = cleanInput($_POST['courseID']);
            $userName = cleanInput($_POST['userName']);

            // extracts the userID
            $URI = $databaseURI . "/user/user/{$userName}";
            $user_data = http_get($URI, true);
            $user_data = json_decode($user_data, true);

            // sets admin rights for the user
            if (empty($user_data)) {
                $notifications[] = MakeNotification("error", "Ungültiges Kürzel.");
            } else {
                $userID = $user_data['id'];
                $status = 3;

                $data = User::encodeUser(User::createCourseStatus($userID, $courseID, $status));
                $url = $databaseURI . "/coursestatus";
                http_post_data($url, $data, true, $message);

                if ($message != "201") {
                    $data = User::encodeUser(User::createCourseStatus($userID, $courseID, $status));
                    $url = $databaseURI . "/coursestatus/course/{$courseID}/user/{$userID}";
                    http_put_data($url, $data, true, $message);

                    if ($message == "201") {
                        $notifications[] = MakeNotification("success", "Der Admin wurde eingetragen.");
                    } else {
                        $notifications[] = MakeNotification("error", "Beim Eintragen ist ein Fehler aufgetreten.");
                    }
                } else {
                    $notifications[] = MakeNotification("success", "Der Admin wurde eingetragen.");
                }
            }
        }
    }

    // creates a new user
    if ($_POST['action'] == "CreateUser") {

        $f = new FormEvaluator($_POST);
        $f->checkStringForKey('lastName',
                              FormEvaluator::REQUIRED,
                              'warning',
                              'Ungültiger Nachname.',
                              array('min' => 1));

        $f->checkStringForKey('firstName',
                              FormEvaluator::REQUIRED,
                              'warning',
                              'Ungültiger Vorname.',
                              array('min' => 1));

        $f->checkStringForKey('userName',
                              FormEvaluator::REQUIRED,
                              'warning',
                              'Ungültiger Benutzername.',
                              array('min' => 1));

        $f->checkEmailForKey('email',
                              FormEvaluator::OPTIONAL,
                              false,
                              'warning',
                              'Ungültige E-Mail-Adresse.');

        $f->checkStringForKey('password',
                              FormEvaluator::REQUIRED,
                              'warning',
                              'Ungültiges Passwort.',
                              array('min' => 6));

        $f->checkStringForKey('passwordRepeat',
                              FormEvaluator::REQUIRED,
                              'warning',
                              'Ungültige Passwortwiederholung.',
                              array('min' => 6));

        if($f->evaluate(true)) {

            $foundValues = $f->foundValues;

            $lastName = $foundValues['lastName'];
            $firstName = $foundValues['firstName'];
            $email = isset($foundValues['email']) ? $foundValues['email'] : null;
            $userName = $foundValues['userName'];

            $password = $foundValues['password'];
            $passwordRepeat = $foundValues['passwordRepeat'];

            // both passwords are equal
            if($password == $passwordRepeat) {

                $salt = $auth->generateSalt();
                $passwordHash = $auth->hashPassword($password, $salt);

                $newUser = User::createUser(null,
                                            $userName,
                                            $email,
                                            $firstName,
                                            $lastName,
                                            null,
                                            1,
                                            $passwordHash,
                                            $salt,
                                            0);

                $newUserSettings = User::encodeUser($newUser);

                $URI = $databaseURI . "/user";
                http_post_data($URI, $newUserSettings, true, $message);

                if ($message == "201") {
                    $notifications[] = MakeNotification("success",
                                                         "Der Nutzer wurde erstellt!");
                }
            } else {
                $notifications[] = MakeNotification("error",
                                                    "Die Passwörter stimmen nicht überein!");
            }
        } else {
            $notifications = $notifications + $f->notifications;
        }
    }

    // deletes an user
    if ($_POST['action'] == "DeleteUser") {
        if(isset($_POST['userName'])) {
            // clean Input
            $userName = cleanInput($_POST['userName']);

            // extracts the userID
            $URI = $databaseURI . "/user/user/{$userName}";
            $user_data = http_get($URI, true);
            $user_data = json_decode($user_data, true);

            if (empty($user_data)) {
                $notifications[] = MakeNotification("error", "Ungültiges Kürzel.");
            } else {
                $userID = $user_data['id'];

                // deletes the user
                $url = $databaseURI . "/user/{$userID}";
                http_delete($url, true, $message);

                if ($message == "201") {
                    $notifications[] = MakeNotification("success", "Der Nutzer wurde erfolgreich gelöscht.");
                } else {
                    $notifications[] = MakeNotification("error", "Beim Löschen ist ein Fehler aufgetreten.");
                }
            }
        }
    }
}

// load mainSettings data from GetSite
$databaseURI = $getSiteURI . "/mainsettings/user/{$uid}";
$mainSettings_data = http_get($databaseURI, true);
$mainSettings_data = json_decode($mainSettings_data, true);

$mainSettings_data['plugins'] = $plugins_data;

$user_course_data = $mainSettings_data['user'];
$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::SUPER_ADMIN,true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => "Einstellungen",
               "backTitle" => "Veranstaltungen",
               "backURL" => "index.php",
               "notificationElements" => $notifications,
               "navigationElement" => $menu));

// construct a content element for creating new courses
$createCourse = Template::WithTemplateFile('include/MainSettings/CreateCourse.template.html');
$createCourse->bind($mainSettings_data);
if (count($notifications) != 0) {
    $createCourse->bind($_POST);
}

// construct a content element for setting admins
$setAdmin = Template::WithTemplateFile('include/MainSettings/SetAdmin.template.html');
$setAdmin->bind($mainSettings_data);

// construct a content element for creating new users
$createUser = Template::WithTemplateFile('include/MainSettings/CreateUser.template.html');
if (count($notifications) != 0) {
    $createUser->bind($_POST);
}

// construct a content element for deleting users
$deleteUser = Template::WithTemplateFile('include/MainSettings/DeleteUser.template.html');

/**
 * @todo combine the templates into a single file
 */

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $createCourse, $setAdmin, $createUser, $deleteUser);
$w->defineForm(basename(__FILE__), false, $createCourse);
$w->defineForm(basename(__FILE__), false, $setAdmin);
$w->defineForm(basename(__FILE__), false, $createUser);
$w->defineForm(basename(__FILE__), false, $deleteUser);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
