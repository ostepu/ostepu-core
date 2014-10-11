<?php
/**
 * @file Dowload.php
 * Contains code that handles download requests.
 *
 * @todo support downloads of csv files
 */

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Request.php';

// get the download token
if (isset($_GET['t'])) {
    $token = $_GET['t'];
} else {
    set_error(400);
}

// check if otions for the token are set
if (isset($_SESSION['downloads'])) {
    if (isset($_SESSION['downloads'][$token])) {
        $options = $_SESSION['downloads'][$token];
    }
} else {

    // the user must have "entered" an invaid token
    set_error(404);
}

if ($options['download'] == 'attachments') {
    if (isset($options['URL'])) {
        // if the user has downloaded all attachments in this session, reuse the location
        $location = $options['URL'];
    } else {
        $sid = $options['sid'];

        $URL = "{$logicURI}/DB/attachment/exercisesheet/{$sid}";
        $attachments = http_get($URL, true);
        $attachments = json_decode($attachments, true);

        $files = array();
        foreach ($attachments as $attachment) {
            $files[] = $attachment['file'];
        }

        $fileString = json_encode($files);

        $zipfile = http_post_data($filesystemURI . '/zip',  $fileString, true);
        $zipfile = json_decode($zipfile, true);

        $location = "../FS/FSBinder/{$zipfile['address']}/attachments.zip"; //{$filesystemURI}
        $_SESSION['downloads'][$token]['URL'] = $location;
    }

} elseif ($options['download'] == 'markings') {

    if (isset($options['URL']) && false) {
        // if the user has downloaded all markings in this session, reuse the location
        $location = $options['URL'];
    } else {
        $sid = $options['sid'];
        $uid = $options['uid'];

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
            if (isset($marking['file']) && isset($marking['submission']['selectedForGroup']) && $marking['submission']['selectedForGroup']){                   
                $exerciseId = $marking['submission']['exerciseId'];
                
                // marking
                $marking['file']['displayName'] = "{$namesOfExercises[$exerciseId]}/K_{$marking['file']['hash']}_{$marking['file']['displayName']}";
                $files[] = $marking['file'];
                
                // submission
                $marking['submission']['file']['displayName'] = "{$namesOfExercises[$exerciseId]}/{$marking['submission']['file']['hash']}_{$marking['submission']['file']['displayName']}";
                $files[] = $marking['submission']['file']; 
                
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
        $zipfile = json_decode($zipfile, true);

        $location = "../FS/FSBinder/{$zipfile['address']}/markings.zip"; //{$filesystemURI}
        $_SESSION['downloads'][$token]['URL'] = $location;
    }

}

header("Location: {$location}");
?>