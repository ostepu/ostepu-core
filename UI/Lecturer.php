<?php
/**
 * @file Lecturer.php
 * Constructs the page that is displayed to a lecturer.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once dirname(__FILE__).'/include/Boilerplate.php';
include_once dirname(__FILE__).'/../Assistants/Structures.php';
include_once dirname(__FILE__).'/../Assistants/LArraySorter.php';

$sheetNotifications = array();

if (isset($_POST['action'])){ 
    if ($_POST['action'] == "ExerciseSheetLecturer" && isset($_POST['deleteSheetWarning'])) {
        $sheetNotifications[$_POST['deleteSheetWarning']][] = MakeNotification("warning", "Soll die Übungsserie wirklich gelöscht werden?");
    } elseif ($_POST['action'] == "ExerciseSheetLecturer" && isset($_POST['deleteSheet'])) {
        $URL = $logicURI . "/exercisesheet/exercisesheet/{$_POST['deleteSheet']}";
        $result = http_delete($URL, true, $message);
        
        if ($message == 201){
            $sheetNotifications[$_POST['deleteSheet']][] = MakeNotification('success', 'Die Übungsserie wurde gelöscht.');
        } else 
            $sheetNotifications[$_POST['deleteSheet']][] = MakeNotification('error', 'Die Übungsserie konnte nicht gelöscht werden.');
    }
}

// load GetSite data for Lecturer.php
$URL = $getSiteURI . "/lecturer/user/{$uid}/course/{$cid}";
$lecturer_data = http_get($URL, true);
$lecturer_data = json_decode($lecturer_data, true);
$lecturer_data['filesystemURI'] = $filesystemURI;
$lecturer_data['cid'] = $cid;

$user_course_data = $lecturer_data['user'];

// check userrights for course
Authentication::checkRights(PRIVILEGE_LEVEL::LECTURER, $cid, $uid, $user_course_data);

if (is_null($user_course_data)) {
    $user_course_data = array();
}

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::LECTURER);
// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "notificationElements" => $notifications,
               "navigationElement" => $menu));


$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetLecturer.template.html');
$t->bind($lecturer_data);
if (isset($sheetNotifications))
    $t->bind(array("SheetNotificationElements" => $sheetNotifications));

$w = new HTMLWrapper($h, $t);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $t);
$w->set_config_file('include/configs/config_admin_lecturer.json');
$w->show();

?>
