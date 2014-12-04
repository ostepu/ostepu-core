<?php
/**
 * @file Tutor.php
 * Constructs the page that is displayed to a tutor.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once dirname(__FILE__).'/include/Boilerplate.php';
include_once dirname(__FILE__).'/../Assistants/Structures.php';

if (isset($_POST['downloadAttachments'])) {
    downloadAttachmentsOfSheet($_POST['downloadAttachments']);
}
 
$types = Marking::getStatusDefinition();
$status = null;
foreach ($types as $type){
    if (isset($_POST['downloadCSV_'.$type['id']])){
        $status = $type['id'];
        $_POST['downloadCSV']=$_POST['downloadCSV_'.$type['id']];
        break;
    }
}

if (isset($_POST['downloadCSV'])) {
    $sid = cleanInput($_POST['downloadCSV']);
    $URI = $logicURI . '/tutor/user/' . $uid . '/exercisesheet/' . $sid.(isset($status) ? '/status/'.$status : '');

    $csvFile = http_get($URI, true);
    $csvFile = json_decode($csvFile, true);

    if (isset($csvFile['address']) && isset($csvFile['displayName'])){
        $fileAddress = $csvFile['address'];
        $displayName = $csvFile['displayName'];
        header("Location: ../FS/FSBinder/{$fileAddress}/{$displayName}");
    }
}

// load tutor data from GetSite
$URI = $getSiteURI . "/tutor/user/{$uid}/course/{$cid}";
$tutor_data = http_get($URI, true);
$tutor_data = json_decode($tutor_data, true);
$tutor_data['filesystemURI'] = $filesystemURI;
$tutor_data['cid'] = $cid;

// check userrights for course
$user_course_data = $tutor_data['user'];
Authentication::checkRights(PRIVILEGE_LEVEL::TUTOR, $cid, $uid, $user_course_data);
$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::TUTOR);
                              
// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "notificationElements" => $notifications,
               "navigationElement" => $menu));

$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetTutor.template.html');
$t->bind($tutor_data);

$w = new HTMLWrapper($h, $t);
$w->defineForm(basename(__FILE__)."?cid=".$cid, false, $t);
$w->set_config_file('include/configs/config_student_tutor.json');
$w->show();

?>
