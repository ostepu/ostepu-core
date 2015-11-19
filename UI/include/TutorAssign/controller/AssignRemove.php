<?php
// removes all tutor assignments by deleting all markings of the exercisesheet
if (isset($_POST['action']) && $_POST['action'] == "AssignRemoveWarning") {
    $assignRemoveNotifications[] = MakeNotification("warning", Language::Get('main','askUnassign', $langTemplate));
} elseif (isset($_POST['action']) && $_POST['action'] == "AssignRemove") {
    $URI = $databaseURI . "/marking/exercisesheet/" . $sid; /// !!! gehört die SID zur CID ??? ///
    http_delete($URI, true, $message);

    if ($message == "201") {
        $msg = Language::Get('main','successUnassign', $langTemplate);
        $assignRemoveNotifications[] = MakeNotification("success", $msg);
    } else {
        $msg = Language::Get('main','errorUnassign', $langTemplate);
        $assignRemoveNotifications[] = MakeNotification("error", $msg);
    }
}