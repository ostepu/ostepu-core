<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once 'include/Helpers.php';

// construct a new Header
$h = new Header("Übungsplattform",
                "",
                "",
                "");

$h->setBackURL("index.php")
  ->setBackTitle("zur Veranstaltung");

if (isset($_GET['uid'])) {
    $userid = $_GET['uid'];
} else {
    $uid = 0;
}

$databaseURI = 'http://141.48.9.92/uebungsplattform/DB/DBCourse/DBCourse.php/course/user/';

$courses = http_get($databaseURI . $userid);
$courses = json_decode($courses, TRUE);
$courses = array('courses' => $courses, 'uid' => $userid );

// construct a login element
$courseSelect = Template::WithTemplateFile('include/CourseSelect/CourseSelect.template.html');
$courseSelect->bind($courses);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $courseSelect);
$w->set_config_file('include/configs/config_default.json');
$w->show();
?>

