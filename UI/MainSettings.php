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
    if ($_POST['action'] == "CreateCourse") {
        if(isset($_POST['courseName']) && isset($_POST['semester']) && isset($_POST['defaultGroupSize'])) {
            $courseName = cleanInput($_POST['courseName']);
            $semester = cleanInput($_POST['semester']);
            $defaultGroupSize = cleanInput($_POST['defaultGroupSize']);

            $newCourse = new Course();
            $newCourseSettings = Course::encodeCourse($newCourse->createCourse(null, $courseName, $semester, $defaultGroupSize));
            $URI = $databaseURI . "/course";
            http_post_data($URI, $newCourseSettings, true, $message);

            if ($message == "201") {
                $notifications[] = MakeNotification("success", "Die Veranstaltung wurde erstellt!");
            }
        }
    }
}

// load mainSettings data from GetSite
$databaseURI = $getSiteURI . "/mainsettings/user/{$uid}/course/{$cid}";
$mainSettings_data = http_get($databaseURI);
$mainSettings_data = json_decode($mainSettings_data, true);

$user_course_data = $mainSettings_data;

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Admin.php?cid={$cid}",
               "notificationElements" => $notifications));

// construct a content element for creating new courses
$createCourse = Template::WithTemplateFile('include/MainSettings/CreateCourse.template.html');

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
