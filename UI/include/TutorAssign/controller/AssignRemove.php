<?php

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