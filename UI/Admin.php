<?php
/**
 * @file Admin.php
 * Constructs the page that is displayed to an admin.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';

if (isset($_POST['action'])) {
    if ($_POST['action'] == "ExerciseSheetLecturer" && isset($_POST['downloadAttachments'])) {
        $sid = cleanInput($_POST['downloadAttachments']);

        $attachments = http_get($serverURI . '/logic/Controller/DB/attachment/exercisesheet/' . $sid);
        $attachments = json_decode($attachments, true);

        $files = array();
        foreach ($attachments as $attachment) {
            $files[] = $attachment['file'];
        }

        $fileString = json_encode($files);

        $zipfile = http_post_data($filesystemURI . '/' . 'zip',  $fileString);
        $zipfile = json_decode($zipfile, true);

        $location = $filesystemURI . '/' . $zipfile['address'];
        header("Location: {$location}/attachments.zip");
    }
    if ($_POST['action'] == "ExerciseSheetLecturer" && isset($_POST['downloadCSV'])) {
        $sid = cleanInput($_POST['downloadCSV']);
        $location = $logicURI . '/tutor/user/' . $uid . '/exercisesheet/' . $sid;
        header("Location: {$location}");
    }
}

// load GetSite data for Admin.php
$URL = $getSiteURI . "/admin/user/{$uid}/course/{$cid}";
$admin_data = http_get($URL, false);
$admin_data = json_decode($admin_data, true);
$admin_data['filesystemURI'] = $filesystemURI;
$admin_data['cid'] = $cid;

$user_course_data = $admin_data['user'];

Authentication::checkRights(PRIVILEGE_LEVEL::ADMIN, $cid, $uid, $user_course_data);

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::ADMIN);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltung wechseln",
               "backURL" => "index.php",
               "notificationElements" => $notifications,
               "navigationElement" => $menu));


$t = Template::WithTemplateFile('include/ExerciseSheet/ExerciseSheetLecturer.template.html');
$t->bind($admin_data);

$w = new HTMLWrapper($h, $t);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $t);
$w->set_config_file('include/configs/config_admin_lecturer.json');
$w->show();

?>