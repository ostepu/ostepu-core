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
 * @todo add function to edit Rights for specific user
 */

include_once 'include/Boilerplate.php';
include_once '../Assistants/Structures.php';

$notifications = array();

if (isset($_POST['action'])) {
    if ($_POST['action'] == "CourseSettings") {
        if(isset($_POST['courseName']) && isset($_POST['semester']) && isset($_POST['defaultGroupSize'])) {
            $courseName = cleanInput($_POST['courseName']);
            $semester = cleanInput($_POST['semester']);
            $defaultGroupSize = cleanInput($_POST['defaultGroupSize']);

            $newCourse = new Course();
            $newCourseSettings = Course::encodeCourse($newCourse->createCourse($cid,$courseName,$semester,$defaultGroupSize));
            $URI = $databaseURI . "/course/course/{$cid}";
            $courseManagement_data = http_put_data($URI, $newCourseSettings, true, $message);

            if ($message == "201") {
                /**
                 * @todo Notification for succesful edit
                 */
            }
        }
    }
}

// load CourseManagement data from GetSite
$URI = $getSiteURI . "/coursemanagement/user/{$uid}/course/{$cid}";
$courseManagement_data = http_get($URI, true);
$courseManagement_data = json_decode($courseManagement_data, true);

$user_course_data = $courseManagement_data['user'];

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Admin.php?cid={$cid}",
               "notificationElements" => $notifications));

// construct a content element for changing course settings
$courseSettings = Template::WithTemplateFile('include/CourseManagement/CourseSettings.template.html');
$courseSettings->bind($courseManagement_data['course'][0]);

// construct a content element for granting user-rights
$grantRights = Template::WithTemplateFile('include/CourseManagement/GrantRights.template.html');

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
