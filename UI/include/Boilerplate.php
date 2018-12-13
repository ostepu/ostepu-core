<?php
/**
 * @file Boilerplate.php
 * Contains common code.
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2014
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2014
 *
 * @todo Configuration for logic controller uri could go here.
 */

$notifications = array();
$startup_error = error_get_last();

include_once ( dirname(__FILE__) . '/Authorization.php' );
include_once ( dirname(__FILE__) . '/HTMLWrapper.php' );
include_once ( dirname(__FILE__) . '/Template.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );
include_once ( dirname(__FILE__) . '/Helpers.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Language.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/pageLib.php' );
if (file_exists(dirname(__FILE__) . '/Config.php')) include_once dirname(__FILE__) . '/Config.php';

if ($startup_error !== null){
    $notifications[] = MakeNotification('error',$startup_error['message']);
    unset($startup_error);
}

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

$globalUserData = null;
if (isset($uid)){
    initPage($uid,(isset($cid) ? $cid : null));
}