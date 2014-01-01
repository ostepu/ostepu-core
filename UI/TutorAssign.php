<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include_once 'include/Template.php';
?>

<?php
if (isset($_POST['action'])) {
    Logger::Log($_POST, LogLevel::INFO);
    header("Location: TutorAssign.php");
} else {
    Logger::Log("No Assignment Data", LogLevel::INFO);
}
?>

<?php
// construct a new Header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "211221492");

$h->setBackURL("index.php")
  ->setBackTitle("zur Veranstaltung");

$data = file_get_contents("http://localhost/Uebungsplattform/UI/Data/TutorAssignData");
$data = json_decode($data, true);

$tutorAssignment = $data;

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

