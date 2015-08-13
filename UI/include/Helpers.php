<?php
/**
 * @file Helpers.php
 * A collection of helper methods that can be used by classes
 *
 * @author Florian Lücke
 * @author Ralf Busch
 */
 
 
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );
include_once ( dirname(__FILE__) . '/Helpers/FILE_TYPE.php' );
include_once ( dirname(__FILE__) . '/Helpers/PRIVILEGE_LEVEL.php' );

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
 * Read file contents as a string
 *
 * @param $filename The name of the file that should be read
 * @see http://php.net/manual/en/function.include.php
 */
function getIncludeContents($filename)
{
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
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
function MakeNotification($notificationType, $notificationText)
{
    return <<<EOF
<div class="notification-bar {$notificationType}">
    $notificationText
</div>
EOF;
}

/**
 * Converts bytes into a readable file size.
 *
 * @param int $size bytes that need to be converted
 * @return string readable file size
 */
function formatBytes($size)
{
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
            $input = htmlspecialchars(trim(($input)),ENT_QUOTES, 'UTF-8');     //stripcslashes       
        } else {
            $input = htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }

    return $input;
}

function MakeNavigationElement($user,
                               $requiredPrivilege,
                               $switchDisabled = false,
                               $forIndex = false)
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
                                   'forIndex' => $forIndex));

    return $navigationElement;
}

/**
 * Saves a file to an instance of a filesystem server
 *
 * @param string $filesystemURI The url at which the filesystem server is running.
 * @param string $filePath The local path at which the file is located.
 * @param string $displayName The displayname of the uploaded file
 * @param int $timestamp The UNIX timestamp of the upload
 * @param string &$message A reference to a variable that will contain the HTTP
 * status code on return.
 *
 * @return string Om success rturns a json object, representing the file in the
 * filesystem. NULL otherwise.
 */
function uploadFileToFileSystem($filesystemURI,
                                $filePath,
                                $displayName,
                                $timestamp,
                                &$message)
{
    $data = file_get_contents($filePath);
    $data = base64_encode($data);

    $file = array('timeStamp' => $timestamp,
                  'displayName' => $displayName,
                  'body' => $data);

    // upload the file to the filesystem
    $URL = $filesystemURI . '/file';
    $jsonFile = http_post_data($URL,
                              json_encode($file),
                              true,
                              $message);

    return $jsonFile;
}

/**
 * Saves a reference to a file in the Database.
 *
 * @param string $databaseURI The url at which the database server is running.
 * @param array $file An associative array or file object representing a file
 * @param string &$message A reference to a variable that will contain the HTTP
 * status code on return.
 *
 * @return string On success rturns a json object, representing the file in the
 * database. NULL otherwise.
 */
function saveFileInDatabase($databaseURI,
                            $file,
                            &$message)
{
    $URL = $databaseURI . '/file';
//echo json_encode($file);
    $jsonFile = http_post_data($URL, json_encode($file), true, $message);
///echo "OK: ".$jsonFile;
    if ($message != "201") {
        //POST failed, check if the file already exists
        $hash = $file['hash'];
        $URL = $databaseURI . '/file/hash/' . $hash;
        $jsonFile = http_get($URL, true, $message);
        ///echo "hash: ".$jsonFile;
    }
///echo "<br>";
    return $jsonFile;
}

/**
 * Stores a file in filesystem and database.
 *
 * @param string $filesystemURI The url at which the filesystem server is running.
 * @param string $databaseURI The url at which the database server is running.
 * @param string $filePath The local path at which the file is located.
 * @param string $displayName The displayname of the uploaded file
 * @param int $timestamp The UNIX timestamp of the upload
 * @param string &$message A reference to a variable that will contain the HTTP
 * status code on return.
 */
function fullUpload($filesystemURI,
                    $databaseURI,
                    $filePath,
                    $displayName,
                    $timestamp,
                    &$message)
{
    $jsonFile = uploadFileToFileSystem($filesystemURI,
                                       $filePath,
                                       $displayName,
                                       $timestamp,
                                       $message);

    if ($message != "201") {
        return NULL;
    }

    $fileObj = json_decode($jsonFile, true);
    $fileObj['timeStamp'] = $timestamp;

    $jsonFile = saveFileInDatabase($databaseURI,
                                   $fileObj,
                                   $message);

    return $jsonFile;
}

/**
 * Updates the selected submission of a group.
 *
 * @param string $databaseURI The url at which the database server is running.
 * @param int $leaderId The id of the the group's leader
 * @param int $submissionUd The new selected submission
 * @param int $exerciseId The submission's exercise id.
 * @param string &$message A reference to a variable that will contain the HTTP
 * status code on return.
 *
 * @return string On success rturns a json object, representing the selected
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
 * Creates a submission for a file.
 *
 * @param string $databaseURI The url at which the database server is running.
 * @param int $userid The id of the user that submitted the file.
 * @param int $fileId The id of the file that the user submitted.
 * @param int $exerciseId The id of the exercise the submission is for.
 * @param string $comment A comment the uder left on the submission.
 * @param int $timestapm The UNIX timestamp of the submission
 * @param string &$message A reference to a variable that will contain the HTTP
 * status code on return.
 *
 * @return string On success returns a json object, representing the selected
 * submission in the database. NULL otherwise.
 */
function submitFile($databaseURI,
                    $userid,
                    $fileId,
                    $exerciseId,
                    $comment,
                    $timestamp,
                    &$message)
{
    $submission = Submission::createSubmission(NULL,
                                               $userid,
                                               $fileId,
                                               $exerciseId,
                                               $comment,
                                               1,
                                               $timestamp,
                                               NULL,
                                               NULL);
    $URL = $databaseURI . '/submission';
    $returnedSubmission = http_post_data($URL,
                                         json_encode($submission),
                                         true,
                                         $message);

    return $returnedSubmission;
}

/**
 * Starts download of all attachments of a sheet.
 *
 * @param $sheetId The id of the sheet whose attachments are downloaded.
 */
function downloadAttachmentsOfSheet($sheetId)
{
    $sid = cleanInput($sheetId);
    
    $tokenString = "{$sid}_AttachmentsDownload";
    $token = md5($tokenString);

    if (!isset($_SESSION['downloads'][$token])) {
        $_SESSION['downloads'][$token] = array('download' => 'attachments',
                                               'sid' => $sid);
    }

    header("Location: Download.php?t={$token}");
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