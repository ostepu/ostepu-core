<?php
/**
 * @file AccountSettings.php
 * Constructs a page where a user can manage his account.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';
include_once '../Assistants/Structures.php';

if (isset($_POST['action'])) {
    // changes the user's password
    if ($_POST['action'] == "SetPassword") {
        if(!empty($_POST['newPassword']) && !empty($_POST['newPasswordRepeat'])) {

            // extracts the php POST data
            $oldPassword = cleanInput($_POST['oldPassword']);
            $newPassword = cleanInput($_POST['newPassword']);
            $newPasswordRepeat = cleanInput($_POST['newPasswordRepeat']);

            // loads user data from database
            $URI = $databaseURI . "/user/user/{$uid}";
            $user_data = http_get($URI, true);
            $user_data = json_decode($user_data, true);

            // generates hashs of old and new password
            $oldSalt = $user_data['salt'];
            $oldPasswordHash = $auth->hashPassword($oldPassword, $oldSalt);

            $newSalt = $auth->generateSalt();
            $newPasswordHash = $auth->hashPassword($newPassword, $newSalt);

            // check if the old password is necessary
            if ($user_data['password'] == "noPassword" || $oldPasswordHash == $user_data['password']) {
                // both passwords are equal
                if($newPassword == $newPasswordRepeat) {
                    $newUserSettings = User::encodeUser(User::createUser($uid, null, null, null, null, null, null, 
                                                        $newPasswordHash, $newSalt));
                    $URI = $databaseURI . "/user/" . $uid;
                    http_put_data($URI, $newUserSettings, true, $message);

                    if ($message == "201") {
                        $notifications[] = MakeNotification("success", "Das Passwort wurde geändert!");
                    }
                }
                else {
                    $notifications[] = MakeNotification("error", "Die Passwörter stimmen nicht überein!");
                }
            }
            else {
                $notifications[] = MakeNotification("error", "Das alte Passwort ist nicht korrekt!");
            }
        }
        else {
            $notifications[] = MakeNotification("error", "Es wurden nicht alle Felder ausgefüllt!");
        }
    }
}

// load user data from the database
$databaseURI = $getSiteURI . "/accountsettings/user/{$uid}/course/{$cid}";
$accountSettings_data = http_get($databaseURI);
$accountSettings_data = json_decode($accountSettings_data, true);

$user_course_data = $accountSettings_data['user'];

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $accountSettings_data['courses'][0]['course']['name'],
               "backTitle" => "Veranstaltungen",
               "backURL" => "index.php",
               "notificationElements" => $notifications));

// construct a content element for account information
$accountInfo = Template::WithTemplateFile('include/AccountSettings/AccountInfo.template.html');
$accountInfo->bind($accountSettings_data['user']);

// construct a content element for changing password
$changePassword = Template::WithTemplateFile('include/AccountSettings/ChangePassword.template.html');
$changePassword->bind($accountSettings_data['user']);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $accountInfo, $changePassword);
$w->defineForm(basename(__FILE__)."?cid=".$cid, $changePassword);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>
