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

if (isset($_POST['action'])){ 
    $types = Marking::getStatusDefinition();
    $status = null;
    foreach ($types as $type){
        if (isset($_POST['downloadCSV_'.$type['id']])){
            $status = $type['id'];
            $_POST['downloadCSV']=$_POST['downloadCSV_'.$type['id']];
            break;
        }
    }

    if ($_POST['action'] == "ExerciseSheetLecturer" && isset($_POST['downloadAttachments'])) {
        downloadAttachmentsOfSheet($_POST['downloadAttachments']);

    }
    if ($_POST['action'] == "ExerciseSheetLecturer" && isset($_POST['downloadCSV'])) 
        $sid = cleanInput($_POST['downloadCSV']);
        $location = $logicURI . '/tutor/user/' . $uid . '/exercisesheet/' . $sid.(isset($status) ? '/status/'.$status : '');
        $result = http_get($location, true);
        $zipfile = json_decode($result, true);
        $location = "../FS/FSBinder/{$zipfile['address']}/".$zipfile['displayName'];
        header("Location: {$location}");
    }
    if ($_POST['action'] == "ExerciseSheetLecturer" && isset($_POST['deleteSheet'])) {
        $URL = $logicURI . "/exercisesheet/exercisesheet/{$_POST['deleteSheet']}";
        $result = http_delete($URL, true, $message);
        
        if ($message == 201){
            array_push($notifications, MakeNotification('success', 'Die Übungsserie wurde gelöscht.'));
        } else 
            array_push($notifications, MakeNotification('warning', 'Die Übungsserie konnte nicht gelöscht werden.'));
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

$w = new HTMLWrapper($h, $t);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $t);
$w->set_config_file('include/configs/config_admin_lecturer.json');
$w->show();

?>
