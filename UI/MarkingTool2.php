<?php
include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/LArraySorter.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/include/FormEvaluator.php';

global $globalUserData;
//Überprüft ob mindestens ein Tutor diese Seite abruft.
Authentication::checkRights(PRIVILEGE_LEVEL::TUTOR, $cid, $uid, $globalUserData);

$URI = $getSiteURI . "/markingtool/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";

if (isset($tutorID)) {
    $URI .= "/tutor/{$tutorID}";
}

if (isset($statusID) && $statusID != 'all' && $statusID != 'notAccepted') {
    $URI .= "/status/{$statusID}";
}

$markingTool_data = http_get($URI, true);
$markingTool_data = json_decode($markingTool_data, true);

$user_course_data = $markingTool_data['user'];

//Gibt den HTML Kopf aus, der dann alles nachlädt

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::TUTOR,true);

// $h = Template::WithTemplateFile('include/Header/Header.template.html');
// $h->bind($user_course_data);
// $h->bind(array('name' => $user_course_data['courses'][0]['course']['name'],
               // 'navigationElement' => $menu));
			   
$c = Template::WithTemplateFile('include/MarkingTool2/MarkingTool2.template.html');
$c->bind($markingTool_data);


$w = new HTMLWrapper(/*$h, */$c);

$w->set_config_file('include/configs/config_marking_tool2.json');
if (isset($maintenanceMode) && $maintenanceMode === '1')
    $w->add_config_file('include/configs/config_maintenanceMode.json');
$w->show();
//echo "<pre>"; echo json_encode($markingTool_data, JSON_PRETTY_PRINT); echo "</pre>";