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
 */

include_once 'include/Boilerplate.php';
include_once '../Assistants/Structures.php';

$notifications = array();

if (isset($_POST['action'])) {
    // creates a new course
    if ($_POST['action'] == "CreateCourse") {
        if(isset($_POST['courseName']) && isset($_POST['semester']) && isset($_POST['defaultGroupSize'])
            && isset($_POST['exerciseTypes'])) {

            // bool which is true if any error occured
            $RequestError = false;

            // extracts the php POST data
            $courseName = cleanInput($_POST['courseName']);
            $semester = cleanInput($_POST['semester']);
            $defaultGroupSize = cleanInput($_POST['defaultGroupSize']);
            $exerciseTypes = cleanInput($_POST['exerciseTypes']);

            // creates a new course
            $newCourse = new Course();
            $newCourseSettings = Course::encodeCourse($newCourse->createCourse(null, $courseName, $semester, $defaultGroupSize));
            $URI = $databaseURI . "/course";
            $newCourseId = http_post_data($URI, $newCourseSettings, true, $messageNewCourse);

            // extracts the id of the new course
            $newCourseId = json_decode($newCourseId, true);
            $newCourseId = $newCourseId['id'];

            // creates a new approvalCondition for every selected exerciseType
            foreach ($exerciseTypes as $exerciseType)
            {
                $newApprovalCondition = new ApprovalCondition();
                $newApprovalConditionSettings = ApprovalCondition::encodeApprovalCondition(
                    $newApprovalCondition->createApprovalCondition(null, $newCourseId, $exerciseType, 0));
                $URI = $databaseURI . "/approvalcondition";
                http_post_data($URI, $newApprovalConditionSettings, true, $messageNewAc);

                if ($messageNewAc != "201") {
                    $RequestError = true;
                }

            }

            // creates a notification depending on RequestError
            if ($messageNewCourse == "201" && $RequestError == false) {
                $notifications[] = MakeNotification("success", "Die Veranstaltung wurde erstellt!");
            }
            else {
                $notifications[] = MakeNotification("error", "Beim Speichern ist ein Fehler aufgetreten!");
            }
        }
        else {
            $notifications[] = MakeNotification("error", "Es wurden nicht alle Felder ausgefüllt!");
        }
    }

    // creates a new user
    if ($_POST['action'] == "CreateUser") {
         if(isset($_POST['lastName']) && isset($_POST['firstName']) && isset($_POST['email']) &&
             isset($_POST['userName']) && isset($_POST['password']) && isset($_POST['passwordRepeat'])) {
            $lastName = cleanInput($_POST['lastName']);
            $firstName = cleanInput($_POST['firstName']);
            $email = cleanInput($_POST['email']);
            $userName = cleanInput($_POST['userName']);

            /**
            * @todo Hash passwords.
            */
            $password = cleanInput($_POST['password']);
            $passwordRepeat = cleanInput($_POST['passwordRepeat']);

            // both passwords are equal
            if($password == $passwordRepeat) {
                $newUser = new User();

                /**
                * @todo What's needed? Flag, salt, failedLogins?
                * @todo Add the user's title.
                */
                $newUserSettings = User::encodeUser($newUser->createUser(null, $userName, $email, $firstName, $lastName,
                     null, null, $password));
                $URI = $databaseURI . "/user";
                http_post_data($URI, $newUserSettings, true, $message);

                if ($message == "201") {
                     $notifications[] = MakeNotification("success", "Der User wurde erstellt!");
                }
            }
            else {
                $notifications[] = MakeNotification("error", "Die Passwörter stimmen nicht überein!");
            }
         }
    }
}

// load mainSettings data from GetSite
$databaseURI = $getSiteURI . "/mainsettings/user/{$uid}/course/{$cid}";
$mainSettings_data = http_get($databaseURI);
$mainSettings_data = json_decode($mainSettings_data, true);

$user_course_data = $mainSettings_data['user'];

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => "Einstellungen",
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Admin.php?cid={$cid}",
               "notificationElements" => $notifications));

// construct a content element for creating new courses
$createCourse = Template::WithTemplateFile('include/MainSettings/CreateCourse.template.html');
$createCourse->bind($mainSettings_data);

// construct a content element for creating new users
$createUser = Template::WithTemplateFile('include/MainSettings/CreateUser.template.html');

// construct a content element for deleting users
$deleteUser = Template::WithTemplateFile('include/MainSettings/DeleteUser.template.html');

/**
 * @todo combine the templates into a single file
 */

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $createCourse, $createUser, $deleteUser);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $createCourse);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $createUser);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $deleteUser);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
