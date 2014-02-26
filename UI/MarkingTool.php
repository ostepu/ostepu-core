<?php
/**
 * @file MarkingTool.php
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

if (isset($_POST['action'])) {
    if ($_POST['action'] == "ShowMarkingTool") {
        if (isset($_POST['sheetID']) && isset($_POST['tutorID']) && isset($_POST['statusID'])) {
            $sid = cleanInput($_POST['sheetID']);
            
            if ($_POST['tutorID'] != "all") {
                $tutorID = cleanInput($_POST['tutorID']);
            }
            if ($_POST['statusID'] != "all") {
                $statusID = cleanInput($_POST['statusID']);
            }
        }
    }
}

// create URI for GetSite
$URI = $getSiteURI . "/markingtool/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";

if (isset($tutorID)) {
    $URI .= "/tutor/{$tutorID}";
}

if (isset($statusID)) {
    $URI .= "/status/{$statusID}";
}

// load MarkingTool data from GetSite
$markingTool_data = http_get($URI, true);
$markingTool_data = json_decode($markingTool_data, true);
$markingTool_data['filesystemURI'] = $filesystemURI;

// adds the selected sheetID, tutorID and statusID
$markingTool_data['sheetID'] = $sid;
$markingTool_data['tutorID'] = $tutorID;
$markingTool_data['statusID'] = $statusID;

$markingTool_data['URI'] = $URI;

$user_course_data = $markingTool_data['user'];


// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "notificationElements" => $notifications,
               "navigationElement" => $menu));


$searchSettings = Template::WithTemplateFile('include/MarkingTool/MarkingToolSettings.template.html');
$searchSettings->bind($markingTool_data);

$markingElement = Template::WithTemplateFile('include/MarkingTool/MarkingTool.template.html');
$markingElement->bind($markingTool_data);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $searchSettings, $markingElement);
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $searchSettings);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>