<?php
/**
 * @file Helpers.php
 * A collection of helper methods that can be used by classes
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.1.0
 *
 * @author Max Brauer <ma.brauer@live.de>
 * @date 2016
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2014
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2013-2014
 */
 
 
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Language.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/fileUtils.php' );
include_once ( dirname(__FILE__) . '/Helpers/FILE_TYPE.php' );
include_once ( dirname(__FILE__) . '/Helpers/PRIVILEGE_LEVEL.php' );

// wandelt einen Wert (in Byte) in eine lesbare Form um
function parse_size($size) {
  $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
  $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
  if ($unit) {
    // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
    return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
  }
  else {
    return round($size);
  }
}

// erzeuge einen Downloadlink aus einer Dateiadresse
function generateDownloadURL($fileObject, $dur = 1800){
    return fileUtils::generateDownloadURL($fileObject, $dur);
}

// erzeugt eine Umleitungsschaltfläche
function createRedirectButton($redirect,$esid=null){
    $text = '';
    $text.= '<button formaction="" title="'.$redirect['title'].'" name="redirect" value="'.(isset($esid) ? $esid.'_' : '_').$redirect['id'].'" class="text-button body-option-color">';
    $text.= $redirect['title'];
    $text.= '</button>';
    return $text;
}

// erzeugt eine Umleitungsschaltfläche (für den Bereich der Navigationsleiste)
function createRedirectButtonHeader($redirect,$esid=null){
    $text = '';
    $text.= '<button formaction="" style="text-decoration: none;color: #2B648F;" title="'.$redirect['url'].'" name="redirect" value="'.(isset($esid) ? $esid.'_' : '_').$redirect['id'].'" class="text-button-simple">';
    $text.= $redirect['title'];
    $text.= '</button>';
    return $text;
}

// führt eine Umleitung aus (Typ: Redirect)
function executeRedirect($redirect, $uid, $cid, $esid){
    $auth = $redirect->getAuthentication();
    if ($auth === 'none' or $auth === ''){
        // wir müssen nur umleiten
        header_remove();
        header('Location: ' . $redirect->getUrl());
        exit();
    } elseif ($auth == 'transaction'){
        // wir hängen die Daten als GET an die URL
        global $serverURI;
        $URI = $serverURI . '/DB/DBCourseStatus/coursestatus/course/'.$cid.'/user/'.$uid;
        $student_data = http_get($URI, true, $message);
        
        if ($message!=200) return false;
        
        $URI = $serverURI . '/DB/DBSession/session/user/'.$uid;
        $session_data = http_get($URI, true, $message);
        
        if ($message!=200) return false;
    
        // erzeuge nun den Inhalt
        $data = array('user'=>json_decode($student_data), 'session'=>json_decode($session_data));
        if (isset($esid) && trim($esid) != ''){
            $data['esid'] = $esid;
        }
            
        $URI = $serverURI . "/DB/DBTransaction/transaction/course/".Redirect::getCourseFromRedirectId($redirect->getId());
        $newTransaction = Transaction::createTransaction(null,time()+180, 'redirect', json_encode($data));
        $transaction = http_post_data($URI, Transaction::encodeTransaction($newTransaction), true, $message);

        if ($message != "201") {
            // es gab einen Fehler
            return false;
        } else {
            $transaction = Transaction::decodeTransaction($transaction);
            header('Location: ' . $redirect->getUrl().'?tid='.$transaction->getTransactionId());
            exit();
        }
    }
    return true;
}
                    
/**
 * Remove a value fom an array
 *
 * @param $array The array from which to remove the value
 * @param $value The value that should be removed
 * @param $strict If array_search should be strict
 * @see http://php.net/manual/de/function.array-search.php
 */
function unsetValue(array $array, $value, $strict = TRUE)
{
    if(($key = array_search($value, $array, $strict)) !== FALSE) {
        unset($array[$key]);
    }
    return $array;
}

/**
 * tests if an array is associative.
 *
 * An array is treated as associative as soon as one of its keys is a string.
 */
function is_assoc($array)
{
  return (bool)count(array_filter(array_keys($array), 'is_string'));
}

/**
 * Redirect to errorpage with given errormessage
 *
 * @param int $errormsg Errormessage e.g. 404.
 */
function set_error($errormsg)
{
    header('Location: Error.php?msg='.$errormsg);
}

/**
 * Sends an HTTP GET request.
 *
 * Uses HTTP GET request to get contents a $url
 * @param string $url The URL that should be opnened.
 * @param bool $authbool If true then send sessioninformation in header.
 * @param string $message The Response Message e.g. 404. Argument is optional.
 */
function http_get($url, $authbool, &$message = 0)
{
    $answer = Request::get($url, array(), '', $authbool);
    
    if (isset($answer['status']))
        $message = $answer['status'];     
  
    if ($message == "401") {
        Authentication::logoutUser();
    }

    return (isset($answer['content']) ? $answer['content'] : '');
}

/**
 * Sends an HTTP POST request.
 *
 * Uses HTTP POST request to post $data to a $url
 * @param string $url The URL that should be opnened.
 * @param arrray $data An associative array that contains the fields that should
 * be postet to $url
 * @param bool $auth If true then send sessioninformation in header.
 * @param string $message The Response Message e.g. 404. Argument is optional.
 */
function http_post_data($url, $data, $authbool, &$message = 0)
{
    $answer = Request::post($url, array(), $data, $authbool);
    
    if (isset($answer['status']))
        $message = $answer['status'];     
  
    if ($message == "401") {
        Authentication::logoutUser();
    }

    return (isset($answer['content']) ? $answer['content'] : '');
}

/**
 * Sends an HTTP PUT request.
 *
 * Uses HTTP PUT request to post $data to a $url
 * @param string $url The URL that should be opnened.
 * @param arrray $data An associative array that contains the fields that should
 * be postet to $url
 * @param bool $authbool If true then send sessioninformation in header.
 * @param string $message The Response Message e.g. 404. Argument is optional.
 */
function http_put_data($url, $data, $authbool, &$message = 0)
{
    $answer = Request::put($url, array(), $data, $authbool);
    
    if (isset($answer['status']))
        $message = $answer['status'];     
  
    if ($message == "401") {
        Authentication::logoutUser();
    }

    return (isset($answer['content']) ? $answer['content'] : '');
}

/**
 * Sends an HTTP DELETE request.
 *
 * Uses HTTP DELETE request to get contents a $url
 * @param string $url The URL that should be opnened.
 * @param bool $authbool If true then send sessioninformation in header.
 * @param string $message The Response Message e.g. 404. Argument is optional.
 * @param bool $sessiondelete If true then send a new timestamp. Only necessary
 * if $authbool true.
 */
function http_delete($url, $authbool, &$message = 0, $sessiondelete = false)
{
    $answer = Request::delete($url, array(), '', $authbool);
    
    if (isset($answer['status']))
        $message = $answer['status'];     
  
    if ($message == "401" && $sessiondelete == false) {
        Authentication::logoutUser();
    }

    return (isset($answer['content']) ? $answer['content'] : '');
}

/**
 * Sets HTTP header fields required for authentication
 *
 * @param resource $curl A curl resource, that needs an authentication header.
 */
function curlSetAuthentication($curl, $sessiondelete = false)
{
    if ($sessiondelete) {
        $date = $_SERVER['REQUEST_TIME'];
    } else {
        $date = $_SESSION['LASTACTIVE'];
    }

    $session = $_SESSION['SESSION'];
    $user = $_SESSION['UID'];
    curl_setopt($curl,
                CURLOPT_HTTPHEADER,
                array("User: {$user}",
                      "Session: {$session}",
                      "Date: {$date}")
                );
}

/**
 * Creates a new Notification bar item
 *
 * @param string $notificationType The type of the new notification. Should be
 * one of the following:
 *     - error
 *     - warning
 *     - success
 * @param $notificationText The text that should be displayed in the
 * notification.
 * @see Notifications.css
 */
function MakeNotification($notificationType, $notificationText, $collapsible=false, $rows=null)
{
    if (!$collapsible){
        return "<div class='{$notificationType} notification-bar'>{$notificationText}</div>";
    } else {
        $addRows = (isset($rows) ? "rows='{$rows}'" : '');
        return "<textarea readonly class='link-sim notification-bar {$notificationType}' {$addRows}>{$notificationText}</textarea>";
    }
}

// wandelt eine relative URL auf eine allgemeine Datei aus UI/CContent/content/ in eine aufrufbare URL um
function generateCommonFileUrl($path){
    global $externalURI;
    return $externalURI.'/UI/CContent/content/common/'.$path;
}

// Erzeugt ein Knopf für Hilfemeldungen
function MakeInfoButton($helpPath)
{
    global $externalURI;
    $helpPath = implode('/',func_get_args());
    $URL = "{$externalURI}/DB/CHelp/help/".Language::$selectedLanguage."/{$helpPath}";
    return "<a href='{$URL}' class='plain image-button exercise-sheet-images' target='popup' onclick=\"window.open('{$URL}', 'popup', 'width=700,height=600,scrollbars=yes,location=no,directories=no,menubar=no,toolbar=no,status=no,resizable=yes')\" title='info' target='_blank'><img src='".generateCommonFileUrl('img/Info.png')."' /></a>";
}

/**
 * Converts bytes into a readable file size.
 *
 * @param int $size bytes that need to be converted
 * @return string readable file size
 */
function formatBytes($size)
{
    if ($size<=0) return '0B';
    $base = log($size) / log(1024);
    $suffixes = array('', 'K', 'M', 'G', 'T');

    return round(pow(1024, $base - floor($base)), 2) . $suffixes[floor($base)] . "B";
}

/**
 * Delete masked slashes from array and trim it.
 *
 * @param mixed $input Some input that needs to be
 */
function cleanInput($input)
{
    if (is_array($input)) {

        foreach ($input as &$element) {
            // pass $element as reference so $input will be modified
            $element = cleanInput($element);
        }
    } else {

        if (get_magic_quotes_gpc() == 0) {
            // magic quotes is turned off
            $input = htmlspecialchars(trim($input),ENT_QUOTES, 'UTF-8');    
        } else {
            $input = htmlspecialchars(stripslashes(trim($input)), ENT_QUOTES, 'UTF-8');
        }
    }

    return $input;
}

//Erzeugt ein Navigationselement (1. Leiste in der Übersicht unter dem Titel)
function MakeNavigationElement($user,
                               $requiredPrivilege,
                               $switchDisabled = false,
                               $forIndex = false,
                               $links = array())
{
    $courses = isset($user['courses']) ? $user['courses'] : null;

    $isSuperAdmin = isset($user['isSuperAdmin']) ? ($user['isSuperAdmin'] == 1) : null;

    /*if ($forIndex == true && $isSuperAdmin == false) {
        return "";
    }*/

    $courseStatus = null;
    if (isset($courses[0]) && isset($courses[0]['status']))
        $courseStatus = $courses[0]['status'];
      
    $course = null;    
    if (isset($courses[0]) && isset($courses[0]['course']))
        $course = $courses[0]['course'];

    $file = 'include/Navigation/Navigation.template.html';
    $navigationElement = Template::WithTemplateFile($file);

    $navigationElement->bind(array('cid' => (isset($course['id']) ? $course['id'] : null),
                                   'requiredPrivilege' => $requiredPrivilege,
                                   'courseStatus' => $courseStatus,
                                   'switchDisabled' => $switchDisabled,
                                   'sites' => PRIVILEGE_LEVEL::$SITES,
                                   'isSuperAdmin' => $isSuperAdmin,
                                   'forIndex' => $forIndex,
                                   'links'=>$links));

    return $navigationElement;
}

//Erzeugt ein Navigationselement (2. Leiste für Studentenrolle etc.)
function MakeUserNavigationElement($user,
                               $courseUser,
                               $privileged,
                               $requiredPrivilege,
                               $sid = null,
                               $courseSheets = null,
                               $switchDisabled = false,
                               $forIndex = false,
                               $helpPath = null,
                               $buttons = array(),
                               $stid = null)
{
    $courses = isset($user['courses']) ? $user['courses'] : null;

    $isSuperAdmin = isset($user['isSuperAdmin']) ? ($user['isSuperAdmin'] == 1) : null;

    /*if ($forIndex == true && $isSuperAdmin == false) {
        return "";
    }*/

    $courseStatus = null;
    if (isset($courses[0]) && isset($courses[0]['status']))
        $courseStatus = $courses[0]['status'];
      
    $course = null;    
    if (isset($courses[0]) && isset($courses[0]['course']))
        $course = $courses[0]['course'];

    $file = 'include/Navigation/UserNavigation.template.html';
    $navigationElement = Template::WithTemplateFile($file);

    if ($courseUser!==null){
        function compare_lastName($a, $b) {
            if ($a->getLastName() === null) return 1;
            if ($b->getLastName() === null) return 1;
            return strnatcmp(strtolower($a->getLastName()), strtolower($b->getLastName()));
        }
        usort($courseUser, 'compare_lastName');
    }

    $navigationElement->bind(array('uid' => $user['id'], 'cid' => (isset($course['id']) ? $course['id'] : null),
                                   'requiredPrivilege' => $requiredPrivilege,
                                   'courseStatus' => $courseStatus,
                                   'switchDisabled' => $switchDisabled,
                                   'sites' => PRIVILEGE_LEVEL::$SITES,
                                   'isSuperAdmin' => $isSuperAdmin,
                                   'forIndex' => $forIndex,
                                   'stid' => $stid,
                                   'sid' => $sid,
                                   'courseUser' => $courseUser,
                                   'courseSheets' => $courseSheets,
                                   'privileged' => $privileged,
                                   'helpPath' => $helpPath,
                                   'buttons' => $buttons));

    return $navigationElement;
}

/**
 * Updates the selected submission of a group.
 *
 * @param string $databaseURI The url at which the database server is running.
 * @param int $leaderId The id of the the group's leader
 * @param int $submissionId The new selected submission
 * @param int $exerciseId The submission's exercise id.
 * @param string &$message A reference to a variable that will contain the HTTP
 * status code on return.
 *
 * @return string On success returns a json object, representing the selected
 * submission in the database. NULL otherwise.
 */
function updateSelectedSubmission($databaseURI,
                                  $leaderId,
                                  $submissionId,
                                  $exerciseId,
                                  &$message,
                                  $newFlag=null)
{
    $selectedSubmission = SelectedSubmission::createSelectedSubmission($leaderId,
                                                                       $submissionId,
                                                                       $exerciseId);
    $URL = $databaseURI . '/selectedsubmission';
    $returnedSubmission = http_post_data($URL,
                                         json_encode($selectedSubmission),
                                         true,
                                         $message);
        
    if ($message != "201") {
        $URL = $databaseURI . '/selectedsubmission/leader/' . $leaderId
               . '/exercise/' . $exerciseId;
        $returnedSubmission = http_put_data($URL,
                                            json_encode($selectedSubmission),
                                            true,
                                            $message);
    }

    if ($newFlag!==null){
        // todo: treat the case if the previous operation failed
        if ($submissionId===null){
            $ret = Submission::decodeSubmission($returnedSubmission);
            $submissionId = $ret->getId();
        }

        $URL = $databaseURI . '/submission/submission/'.$submissionId;
        $submissionUpdate = Submission::createSubmission($submissionId,null,null,null,null,null,null,$newFlag);
        $returnedSubmission2 = http_put_data($URL,
                                             json_encode($submissionUpdate),
                                             true,
                                             $message2);   
    }
    
    return $returnedSubmission;
}

/**
 * Setzt die Sprache des Nutzers (es sollen noch weitere Aspekte folgen)
 */
function initPage($uid, $courseid=null){
    global $getSiteURI;
    global $globalUserData;
    
    // load user data from the database
    $databaseURI = $getSiteURI . "/accountsettings/user/{$uid}".(isset($courseid) ? '/course/'.$courseid : '');
    $accountSettings_data = http_get($databaseURI, true);
    $accountSettings_data = json_decode($accountSettings_data, true);

    if (isset($accountSettings_data['lang'])){
        Language::setPreferedLanguage($accountSettings_data['lang']);
    }
    
    $globalUserData = $accountSettings_data;
}

/**
 * Starts download of all markings of a sheet.
 *
 * @param $sheetId The id of the sheet whose markings are downloaded.
 */
function downloadMarkingsForSheet($userId, $sheetId)
{
    $sid = cleanInput($sheetId);
    $uid = cleanInput($userId);
    
    $tokenString = "{$sid}_{$uid}_MarkingsDownload";
    $token = md5($tokenString);

    if (!isset($_SESSION['downloads'][$token])) {
        $_SESSION['downloads'][$token] = array('download' => 'markings',
                                               'sid' => $sid,
                                               'uid' => $uid);
    }

    header("Location: Download.php?t={$token}");
}

/**
 * Creates a submission.
 * The submission contains a dummy file for consistency reasons
 * which isn't shown to anyone by setting the 'hideFile' flag 
 *
 * @param $leaderID The userID of the group leader
 * @param $eID The id of the exercisesheet
 *
 * @return Returns the submission on success, NULL otherwise 
 */
function createSubmission($leaderID, $eID)
{
    global $databaseURI;
    global $filesystemURI;

    // creates the new submission including the dummy file
    $newSubmission = Submission::createSubmission(null,
                                                  $leaderID,
                                                  null,
                                                  $eID,
                                                  null,
                                                  1,
                                                  time(),
                                                  null,
                                                  null,
                                                  true);

    $newSubmission = Submission::encodeSubmission($newSubmission);

    $URI = $databaseURI . "/submission";
    $submission = http_post_data($URI, $newSubmission, true, $message);

    if ($message != "201") {
        return NULL;
    }

    $submission = json_decode($submission, true);
    $submissionID = $submission['id'];

    // makes the currently created submission selected
    updateSelectedSubmission($databaseURI,
                             $leaderID,
                             $submissionID,
                             $eID,
                             $message);

    if ($message != "201") {
        return NULL;
    }

    return $submission;
}


/**
 * Creates a marking to an already existing submission.
 * The marking contains a dummy file for consistency reasons
 * which isn't shown to anyone by setting the 'hideFile' flag 
 *
 * @param $points The points of the marking
 * @param $tutorComment The tutor's comment
 * @param $status The status of the marking
 * @param $submissionID The id of the submission that belongs to the marking
 * @param $tutorID The id of the tutor who creates the marking
 *
 * @return bool Returns the marking on success, NULL otherwise 
 */
function createMarking($points, $tutorComment, $status, $submissionID, $tutorID)
{
    global $databaseURI;
    global $filesystemURI;

        // creates the new marking including the dummy file
        $newMarking = Marking::createMarking(null,
                                             $tutorID,
                                             null,
                                             $submissionID,
                                             $tutorComment,
                                             null,
                                             $status,
                                             $points,
                                             time(),
                                             true);

        $newMarking = Marking::encodeMarking($newMarking);
        $URI = $databaseURI . "/marking";
        $marking = http_post_data($URI, $newMarking, true, $message);

        if ($message != "201") {
            return NULL;
        }

    return $marking;
}


/**
 * Stores a marking in the database.
 *
 * @param $points The points of the marking
 * @param $tutorComment The tutor's comment
 * @param $status The status of the marking
 * @param $submissionID The id of the submission, if set, -1 otherwise
 * @param $markingID The id of the marking, if set, -1 otherwise
 * @param $leaderID The id of the group leader
 * @param $tutorID The id of the tutor who creates the marking
 * @param $eID The id of the exercisesheet
 *
 * @return bool Returns true on success, false otherwise 
 */
function saveMarking($points, $tutorComment, $status, $submissionID, $markingID, $leaderID, $tutorID, $eID)
{
    global $databaseURI;

    // submission and marking already exist and don't 
    // need to be created before adding the marking data
    if (($submissionID != -1 && $markingID != -1)) {
        $newMarking = Marking::createMarking($markingID, 
                                             $tutorID, 
                                             null, 
                                             null,
                                             $tutorComment,
                                             null,
                                             $status,
                                             $points,
                                             time());

        $newMarking = Marking::encodeMarking($newMarking);
        $URI = $databaseURI . "/marking/marking/{$markingID}";
        http_put_data($URI, $newMarking, true, $message);

        if ($message != 201) {
            return false;
        } else {
            return true;
        }
    } elseif ($submissionID != -1 && $markingID == -1) {
        // only the submission exists, the marking still
        // needs to be created before adding the marking data

        // creates the marking in the database
        $marking = createMarking($points, $tutorComment, $status, $submissionID, $tutorID);
        if (empty($marking)) {
            return false;
        } else {
            return true;
        }
    } elseif (($submissionID == -1 && $markingID == -1)) {
        // neither the submission nor the marking exist - they both
        // need to be created before adding the marking data

        // creates the submission in the database
        $submission = createSubmission($leaderID, $eID);

        if (!empty($submission)) {
            // creates the marking in the database
            $submissionID = $submission['id'];
            $marking = createMarking($points, $tutorComment, $status, $submissionID, $tutorID);
            if (!empty($marking)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

//Aktualisiert den Status einer Einsendung
function updateSubmission($submissionId, $accepted)
{
    global $databaseURI;
    $newSubmission = Submission::createSubmission( 
                                                    null,
                                                    null,
                                                    null,
                                                    null,
                                                    null,
                                                    $accepted,
                                                    null,
                                                    null
                                                    );

    $newSubmission = Submission::encodeSubmission($newSubmission);
    $URI = $databaseURI . "/submission/submission/{$submissionId}";
    http_put_data($URI, $newSubmission, true, $message);

    if ($message != 201) {
        return false;
    } else {
        return true;
    }
}