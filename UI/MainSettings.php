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
 * @todo add function for creating users
 * @todo add function for deleting users
 * @todo create a navigation bar for super admins
 * @todo unset $_POST on success
 */

include_once 'include/Boilerplate.php';
include_once '../Assistants/Structures.php';
include_once 'include/FormEvaluator.php';

if (isset($_POST['action'])) {
    // creates a new course
    if ($_POST['action'] == "CreateCourse") {

        $f = new FormEvaluator($_POST);
        $f->checkStringForKey('courseName',
                              FormEvaluator::REQUIRED,
                              true,
                              'warning',
                              'Ungültiger Kursname.');

        $f->checkStringForKey('semester',
                              FormEvaluator::REQUIRED,
                              true,
                              'warning',
                              'Ungültiges Semester.');

        $f->checkIntegerForKey('defaultGroupSize',
                              FormEvaluator::REQUIRED,
                              'warning',
                              'Ungültige Gruppengröße.',
                              array('min' => 0));

        $f->checkArrayForKey('exerciseTypes',
                             FormEvaluator::REQUIRED,
                             true,
                             'warning',
                             'Ungültige Aufgabentypen.');

        if($f->evaluate(true)) {

            // bool which is true if any error occured
            $RequestError = false;

            $foundValues = $f->foundValues;

            // extracts the php POST data
            $courseName = $foundValues['courseName'];
            $semester = $foundValues['semester'];
            $defaultGroupSize = $foundValues['defaultGroupSize'];
            $exerciseTypes = $foundValues['exerciseTypes'];

            // creates a new course
            $newCourse = Course::createCourse(null, $courseName, $semester, $defaultGroupSize);
            $newCourseSettings = Course::encodeCourse($newCourse);
            $URI = $databaseURI . "/course";
            $newCourse = http_post_data($URI, $newCourseSettings, true, $messageNewCourse);

            // extracts the id of the new course
            $newCourse = json_decode($newCourse, true);
            $newCourseId = $newCourse['id'];

            // creates a new approvalCondition for every selected exerciseType
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

    // creates a new user
    if ($_POST['action'] == "CreateUser") {

        $f = new FormEvaluator($_POST);
        $f->checkStringForKey('lastName',
                              FormEvaluator::REQUIRED,
                              true,
                              'warning',
                              'Ungüliger Nachname.');

        $f->checkStringForKey('firstName',
                              FormEvaluator::REQUIRED,
                              true,
                              'warning',
                              'Ungüliger Vorname.');

        $f->checkStringForKey('userName',
                              FormEvaluator::REQUIRED,
                              true,
                              'warning',
                              'Ungüliger Benutzername.');

        $f->checkEmailForKey('email',
                              FormEvaluator::REQUIRED,
                              true,
                              'warning',
                              'Ungülige E-Mail-Adresse.');

        $f->checkStringForKey('password',
                              FormEvaluator::REQUIRED,
                              true,
                              'warning',
                              'Ungüliges Passwort.');

        $f->checkStringForKey('passwordRepeat',
                              FormEvaluator::REQUIRED,
                              true,
                              'warning',
                              'Ungüliges Passwort.');

        if($f->evaluate(true)) {

            $foundValues = $f->foundValues;

            $lastName = $foundValues['lastName'];
            $firstName = $foundValues['firstName'];
            $email = $foundValues['email'];
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
                                                         "Der User wurde erstellt!");
                }
            } else {
                $notifications[] = MakeNotification("error",
                                                    "Die Passwörter stimmen nicht überein!");
            }
        } else {
            $notifications = $notifications + $f->notifications;
        }
    }
}

// load mainSettings data from GetSite
$databaseURI = $getSiteURI . "/mainsettings/user/{$uid}";
$mainSettings_data = http_get($databaseURI, true);
$mainSettings_data = json_decode($mainSettings_data, true);

$user_course_data = $mainSettings_data['user'];

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => "Einstellungen",
               "backTitle" => "Veranstaltungen",
               "backURL" => "index.php",
               "notificationElements" => $notifications));

// construct a content element for creating new courses
$createCourse = Template::WithTemplateFile('include/MainSettings/CreateCourse.template.html');
$createCourse->bind($mainSettings_data);
if (count($notifications) != 0) {
    $createCourse->bind($_POST);
}


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
$w = new HTMLWrapper($h, $createCourse, $createUser, $deleteUser);
$w->defineForm(basename(__FILE__), false, $createCourse);
$w->defineForm(basename(__FILE__), false, $createUser);
$w->defineForm(basename(__FILE__), false, $deleteUser);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
