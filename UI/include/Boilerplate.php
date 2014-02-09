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

$serverURI = "http://141.48.9.92/uebungsplattform/";
$databaseURI = $serverURI . "DB/DBControl";
$filesystemURI = $serverURI . "FS/FSControl/";
$getSiteURI = $serverURI . "logic/GetSite";


/**
 * An enumeration of different privilege levels.
 */
class PRIVILEGE_LEVEL
{
    const STUDENT = 0;
    const TUTOR = 1;
    const LECTURER = 2;
    const ADMIN = 3;
    const SUPER_ADMIN = 4;

    static $NAMES = array(
        self::STUDENT => 'Student',
        self::TUTOR => 'Tutor',
        self::LECTURER => 'Dozent',
        self::ADMIN => 'Admin');

    static $SITES = array(
        self::STUDENT => 'Student.php',
        self::TUTOR => 'Tutor.php',
        self::LECTURER => 'Lecturer.php',
        self::ADMIN => 'Admin.php');
}

?>