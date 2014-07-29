<?php
/**
 * @file Boilerplate.php
 * Contains common code.
 *
 * @todo Configuration for logic controller uri could go here.
 */

include_once 'include/Authorization.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';
include_once '../Assistants/Logger.php';
include_once 'include/Helpers.php';
include_once 'include/Config.php';

$notifications = array();

if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
} else {
    Logger::Log('no course id!\n');
}

if (isset($_SESSION['UID'])) {
    $uid = $_SESSION['UID'];
} else {
    Logger::Log('no user id!\n');
}

if (isset($_GET['sid'])) {
    $sid = $_GET['sid'];
} else {
    Logger::Log('no sheet id!\n');
}

if (isset($_GET['suid'])) {
    $suid = $_GET['suid'];
} else {
    Logger::Log('no submission id!\n');
}

// global $serverURI;
// global $databaseURI;
// global $logicURI;
// global $filesystemURI;
// global $getSiteURI;

?>