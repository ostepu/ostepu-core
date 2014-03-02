<?php
/**
 * @file Dowload.php
 * Contains code that handles download requests.
 *
 * @todo support downloads of all markings
 * @todo support downloads of csv files
 */

include_once 'include/Boilerplate.php';

if (isset($_GET['t'])) {
    $token = $_GET['t'];
} else {
    set_error(400);
}

print_r($_SESSION);

if (isset($_SESSION['downloads'])) {
    if (isset($_SESSION['downloads'][$token])) {
        $options = $_SESSION['downloads'][$token];
    }
} else {
    set_error(404);
}

if ($options['download'] == 'attachments') {

    if (isset($options['URL'])) {
        $location = $options['URL'];
    } else {
        $sid = cleanInput($_POST['downloadAttachments']);

        $URL = "{$serverURI}/logic/Controller/DB/attachment/exercisesheet/{sid}";
        $attachments = http_get($URL);
        $attachments = json_decode($attachments, true);

        $files = array();
        foreach ($attachments as $attachment) {
            $files[] = $attachment['file'];
        }

        $fileString = json_encode($files);

        $zipfile = http_post_data($filesystemURI . '/zip',  $fileString);
        $zipfile = json_decode($zipfile, true);

        $location = "{$filesystemURI}/{$zipfile['address']}/attachments.zip";

        $_SESSION['downloads'][$token]['URL'] = $location;
    }

    header("Location: {$location}");
}

?>