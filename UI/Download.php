<?php
/**
 * @file Dowload.php
 * Contains code that handles download requests.
 *
 * @todo support downloads of csv files
 */

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Request.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/LArraySorter.php';

function checkPermission($permission){
    global $getSiteURI;
    global $cid;
    global $uid;
    $URL = $getSiteURI . "/index/user/{$uid}";
    $data = http_get($URL, true);
    $data = json_decode($data,true);
    $found=false;
    foreach ($data['courses'] as $key=>$course){
        if ($course['course']['id']==$cid){
            $data['courses']=array($course);$found=true;
            break;
        }
    }
    if (!$found)
        $data['courses']=array();
    $user_course_data = $data;
    Authentication::checkRights($permission, $cid, $uid, $user_course_data);
}

$_GET=cleanInput($_GET);

$types = Marking::getStatusDefinition();
$status = null;
foreach ($types as $type){
    if (isset($_GET['downloadCSV_'.$type['id']])){
        $status = $type['id'];
        $_GET['downloadCSV']=$_GET['downloadCSV_'.$type['id']];
        break;
    }
}
if (isset($_GET['downloadCSV'])) {
    checkPermission(PRIVILEGE_LEVEL::TUTOR);
    $sid = cleanInput($_GET['downloadCSV']);
    $location = $logicURI . '/tutor/user/' . $uid . '/exercisesheet/' . $sid.(isset($status) ? '/status/'.$status : '');
    $result = http_get($location, true);
    echo $result;
    exit(0);
}

if (isset($_GET['downloadAttachments'])) {
    checkPermission(PRIVILEGE_LEVEL::STUDENT);
    $sid = $_GET['downloadAttachments'];
    $URL = "{$logicURI}/DB/attachment/exercisesheet/{$sid}";
    $attachments = http_get($URL, true);
    $attachments = json_decode($attachments, true);

    $files = array();
    foreach ($attachments as $attachment) {
        $files[] = $attachment['file'];
    }

    $fileString = json_encode($files);

    $zipfile = http_post_data($filesystemURI . '/zip',  $fileString, true);
    $zipfile = File::decodeFile($zipfile);
    $zipfile->setDisplayName('attachments.zip');
    $zipfile = File::encodeFile($zipfile);
    echo $zipfile;
    exit(0);

} elseif (isset($_GET['downloadMarkings'])) {
    checkPermission(PRIVILEGE_LEVEL::STUDENT);
    $sid = $_GET['downloadMarkings'];

    $multiRequestHandle = new Request_MultiRequest();
    
    //request to database to get the markings
    $handler = Request_CreateRequest::createCustom('GET', "{$logicURI}/DB/marking/exercisesheet/{$sid}/user/{$uid}", array(),'');
    $multiRequestHandle->addRequest($handler);
    
    $handler = Request_CreateRequest::createCustom('GET', "{$logicURI}/DB/exercisesheet/exercisesheet/{$sid}/exercise", array(),'');
    $multiRequestHandle->addRequest($handler);

    $answer = $multiRequestHandle->run();
    $markings = json_decode($answer[0]['content'], true);

    $sheet = json_decode($answer[1]['content'], true);
    $exercises = $sheet['exercises'];
    
    //an array to descripe the subtasks
    $alphabet = range('a', 'z');
    $count = 0;
    $namesOfExercises = array();
    $attachments = array();
    
    $count=null;
    foreach ($exercises as $key => $exercise){
        $exerciseId = $exercise['id'];
        
        if (isset($exercise['attachments']))
            $attachments[$exerciseId] = $exercise['attachments'];

        if ($count===null || $exercises[$count]['link'] != $exercise['link']){
            $count=$key;
            $namesOfExercises[$exerciseId] = 'Aufgabe_'.$exercise['link'];
            $subtask = 0;
        }else{
            $subtask++;
            $namesOfExercises[$exerciseId] = 'Aufgabe_'.$exercise['link'].$alphabet[$subtask];
            $namesOfExercises[$exercises[$count]['id']] = 'Aufgabe_'.$exercises[$count]['link'].$alphabet[0];
        }
    }

    $files = array();
    foreach ($markings as $marking) {
        if (isset($marking['submission']['selectedForGroup']) && $marking['submission']['selectedForGroup']){                   
            $exerciseId = $marking['submission']['exerciseId'];
            
            // marking
            if (isset($marking['file']) && (!isset($marking['hideFile']) || !$marking['hideFile']) ){
                $marking['file']['displayName'] = "{$namesOfExercises[$exerciseId]}/K_{$marking['file']['hash']}_{$marking['file']['displayName']}";
                $files[] = $marking['file'];
            }
            
            // submission
            if (isset($marking['submission']['file']) && (!isset($marking['submission']['hideFile']) || !$marking['submission']['hideFile'])){
                $marking['submission']['file']['displayName'] = "{$namesOfExercises[$exerciseId]}/{$marking['submission']['file']['hash']}_{$marking['submission']['file']['displayName']}";
                $files[] = $marking['submission']['file']; 
            }
            
            // attachments
            if (isset($attachments[$exerciseId])){
                foreach ($attachments[$exerciseId] as $attachment){
                    if (isset($attachment['file']['address'])){
                        $attachment['file']['displayName'] = "{$namesOfExercises[$exerciseId]}/A_{$attachment['file']['hash']}_{$attachment['file']['displayName']}";
                        $files[] = $attachment['file'];     
                    }
                }
            }
        }
    }
    unset($attachments, $markings, $exercises);
    
    // sheetFile
    if (isset($sheet['sheetFile']['address'])){
        $sheet['sheetFile']['displayName'] = "{$sheet['sheetFile']['displayName']}";
        $files[] = $sheet['sheetFile'];
    }
    
    // sampleSolution
    if (isset($sheet['sampleSolution']['address'])){
        $sheet['sampleSolution']['displayName'] = "{$sheet['sampleSolution']['displayName']}";
        $files[] = $sheet['sampleSolution'];
    }

    $fileString = json_encode($files);
    $zipfile = http_post_data($filesystemURI . '/zip',  $fileString, true);
    $zipfile = File::decodeFile($zipfile);
    $zipfile->setDisplayName('markings.zip');
    $zipfile = File::encodeFile($zipfile);
    echo $zipfile;
    exit(0);
}