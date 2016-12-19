<?php
/**
 * @file AssignRemove.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.3.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */


// removes all tutor assignments by deleting all markings of the exercisesheet
$URI = $databaseURI . "/marking/exercisesheet/" . $sid; /// !!! gehört die SID zur CID ??? ///
http_delete($URI, true, $message);

if ($message === 201) {
    $msg = Language::Get('main','successUnassign', $langTemplate);
    $assignRemoveNotifications[] = MakeNotification('success', $msg);
} else {
    $msg = Language::Get('main','errorUnassign', $langTemplate);
    $assignRemoveNotifications[] = MakeNotification('error', $msg);
}