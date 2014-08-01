<?php
/**
 * @file Dowload.php
 * Contains code that handles download requests.
 *
 * @todo support downloads of csv files
 */

include_once 'include/Boilerplate.php';

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

        $location = "{$filesystemURI}/{$zipfile['address']}/attachments.zip";
        $_SESSION['downloads'][$token]['URL'] = $location;
    }

} elseif ($options['download'] == 'markings') {

    if (isset($options['URL'])) {
        // if the user has downloaded all markings in this session, reuse the location
        $location = $options['URL'];
    } else {
        $sid = $options['sid'];
        $uid = $options['uid'];

        $URL = "{$logicURI}/DB/marking/exercisesheet/{$sid}/user/{$uid}";
        $markings = http_get($URL, true);
        $markings = json_decode($markings, true);

        $files = array();
        foreach ($markings as $marking) {
            $files[] = $marking['file'];
        }

        $fileString = json_encode($files);

        $zipfile = http_post_data($filesystemURI . '/zip',  $fileString, true);
        $zipfile = json_decode($zipfile, true);

        $location = "{$filesystemURI}/{$zipfile['address']}/markings.zip";
        $_SESSION['downloads'][$token]['URL'] = $location;
    }

}

header("Location: {$location}");
?>