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
 * @todo you have to confirm your action before deleting coursestatus
 */

include_once 'include/Boilerplate.php';
include_once '../Assistants/Structures.php';

if (isset($_POST['action'])) {
    if ($_POST['action'] == "CourseSettings") {
        // check if POST data is send
        if(isset($_POST['courseName']) && isset($_POST['semester']) && isset($_POST['defaultGroupSize'])) {
            // clean Input
            $courseName = cleanInput($_POST['courseName']);
            $semester = cleanInput($_POST['semester']);
            $defaultGroupSize = cleanInput($_POST['defaultGroupSize']);

            // create new course and edit existing one
            $newCourse = new Course();
            $newCourseSettings = Course::encodeCourse($newCourse->createCourse($cid,$courseName,$semester,$defaultGroupSize));
            $URI = $databaseURI . "/course/course/{$cid}";
            $courseManagement_data = http_put_data($URI, $newCourseSettings, true, $message);

            // show notification
            if ($message == "201") {
                $notifications[] = MakeNotification("success", "Die Veranstaltung wurde bearbeitet!");
            }
        } else {
            $notifications[] = MakeNotification("error", "Bitte wählen Sie alle Felder richtig aus!");
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
                $newUser = new User();
                $data = User::encodeUser($newUser->createCourseStatus($userID,$cid,$Rights));

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
            $notifications[] = MakeNotification("error", "Bitte wählen Sie alle Felder richtig aus!");
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
                    $notifications[] = MakeNotification("success", "Die Nutzer wurde aus der Veranstaltung entfernt!");
                }
            } else {
                // otherwise show conflict page
                set_error("409");
                exit();
            }
        } else {
            $notifications[] = MakeNotification("error", "Bitte wählen Sie alle Felder richtig aus!");
        }
    }
}

// load CourseManagement data from GetSite
$URI = $getSiteURI . "/coursemanagement/user/{$uid}/course/{$cid}";
$courseManagement_data = http_get($URI, true);
$courseManagement_data = json_decode($courseManagement_data, true);

$user_course_data = $courseManagement_data['user'];

$menu = MakeNavigationElementForCourseStatus($user_course_data['courses'],
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
$courseSettings->bind($courseManagement_data['course']);

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
