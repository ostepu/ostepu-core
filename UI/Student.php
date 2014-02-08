<?php
/**
 * @file Student.php
 * Constructs the page that is displayed to a student.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

// load tutor data from GetSite
$URI = $getSiteURI . "/student/user/{$uid}/course/{$cid}";
$student_data = http_get($URI, true);
$student_data = json_decode($student_data, true);
$student_data['filesystemURI'] = $filesystemURI;

$user_course_data = $student_data['user'];

// check userrights for course
Authentication::checkRights(PRIVILEGE_LEVEL::STUDENT, $cid, $uid, $user_course_data);

$menu = MakeNavigationElementForCourseStatus($user_course_data['courses'],
                                             PRIVILEGE_LEVEL::STUDENT);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "notificationElements" => $notifications,
               "navigationElement" => $menu));

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetStudent.template.html');
$t->bind($student_data);

$w = new HTMLWrapper($h, $t);
$w->set_config_file('include/configs/config_student_tutor.json');
$w->show();

?>
