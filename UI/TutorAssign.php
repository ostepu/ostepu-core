<?php
/**
 * @file TutorAssign.php
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

if (isset($_POST['action'])) {
    Logger::Log($_POST, LogLevel::INFO);
    /**
     * @todo assign tutors based on the selected method
     */

    // redirect, so the user can reload the page without a warning
    header("Location: TutorAssign.php");
} else {
    Logger::Log("No Assignment Data", LogLevel::INFO);
}

/**
 * @todo Combine user course and status request into GetSite request
 */
// load user and course data from the database
$databaseURL = $databaseURI . "/coursestatus/course/{$cid}/user/{$uid}";
$user_course_data = http_get($databaseURL, true, $message);

$user_course_data = json_decode($user_course_data, true);

Logger::Log("Course request done");

// check userrights for course
Authentication::checkRights(1, $cid, $uid, $user_course_data);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "zur Veranstaltung",
               "backURL" => "Tutor.php?cid={$cid}",
               "notificationElements" => $notifications));

$databaseURL = $databaseURI . "/tutorassignment/course/{$cid}/exercisesheet/{$sid}";
$data = http_get($databaseURL, true);
$data = json_decode($data, true);

$tutorAssignment = array("tutorAssignments" => $data);

// construct a content element for managing groups
$assignAutomatically = Template::WithTemplateFile('include/TutorAssign/AssignAutomatically.template.html');
$assignAutomatically->bind($tutorAssignment);

// construct a content element for creating groups
$assignManually = Template::WithTemplateFile('include/TutorAssign/AssignManually.template.html');
$assignManually->bind($tutorAssignment);

// construct a content element for joining groups
$assignCancel = Template::WithTemplateFile('include/TutorAssign/AssignRemove.template.html');

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $assignAutomatically, $assignManually, $assignCancel);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
