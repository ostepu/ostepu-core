<?php
/**
 * @file Helpers.php
 * A collection of helper methods that can be used by classes
 *
 * @author Florian Lücke
 * @author Ralf Busch
 */

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
    $c = curl_init();

    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_HTTPGET, 1);
    if ($authbool) {
        curlSetAuthentication($c);
    }

    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);

    $retData = curl_exec($c);
    $message = curl_getinfo($c, CURLINFO_HTTP_CODE);

    if ($message == "401") {
        Authentication::logoutUser();
    }

    curl_close($c);

    return $retData;
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
    $c = curl_init();

    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($c, CURLOPT_POSTFIELDS, $data);

    if ($authbool) {
        curlSetAuthentication($c);
    }

    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);

    $retData = curl_exec($c);
    $message = curl_getinfo($c, CURLINFO_HTTP_CODE);

    if ($message == "401") {
        Authentication::logoutUser();
    }

    curl_close($c);

    return $retData;
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
    $c = curl_init();

    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_POSTFIELDS, $data);
    curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'PUT');

    if ($authbool) {
        curlSetAuthentication($c);
    }

    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);

    $retData = curl_exec($c);
    $message = curl_getinfo($c, CURLINFO_HTTP_CODE);

    if ($message == "401") {
        Authentication::logoutUser();
    }

    curl_close($c);

    return $retData;
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
    $c = curl_init();

    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'DELETE');

    if ($authbool) {
        curlSetAuthentication($c, $sessiondelete);
    }

    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);

    $retData = curl_exec($c);
    $message = curl_getinfo($c, CURLINFO_HTTP_CODE);

    if ($message == "401" && $sessiondelete == false) {
        Authentication::logoutUser();
    }

    curl_close($c);

    return $retData;
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
                      "Date : {$date}")
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
            $input = htmlspecialchars(trim(stripcslashes($input)),
                                           ENT_QUOTES, 'UTF-8');
        } else {
            $input = htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }

    return $input;
}

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

function MakeNavigationElement($user,
                               $requiredPrivilege,
                               $switchDisabled = false)
{
    $courses = $user['courses'];

    $isSuperAdmin = ($user['userName'] == 'super-admin');

    if (count($courses) > 1 && $isSuperAdmin == false) {
        return "";
    }

    $courseStatus = $courses[0]['status'];
    $course = $courses[0]['course'];

    $file = 'include/Navigation/Navigation.template.html';
    $navigationElement = Template::WithTemplateFile($file);

    $navigationElement->bind(array('cid' => $course['id'],
                                   'requiredPrivilege' => $requiredPrivilege,
                                   'courseStatus' => $courseStatus,
                                   'switchDisabled' => $switchDisabled,
                                   'sites' => PRIVILEGE_LEVEL::$SITES,
                                   'isSuperAdmin' => $isSuperAdmin));

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
    $jsonFile = http_post_data($URL, json_encode($file), true, $message);

    if ($message != "201") {
        //POST failed, check if the file already exists
        $hash = $file['hash'];
        $URL = $databaseURI . '/file/hash/' . $hash;
        $jsonFile = http_get($URL, true, $message);
    }

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
                                  &$message)
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

    return $returnedSubmission;
}

/**
 * Creates a submission for a file
 *
 * @param string $databaseURI The url at which the database server is running.
 * @param int $userid The id of the user that submitted the file.
 * @param int $fileId The id of the file that the uder submitted.
 * @param int $exerciseId The id of the exercise the submission is for.
 * @param string $comment A comment the uder left on the submission.
 * @param int $timestapm The UNIX timestamp of the submission
 * @param string &$message A reference to a variable that will contain the HTTP
 * status code on return.
 *
 * @return string On success rturns a json object, representing the selected
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
 * An enumeration of different mime-types.
 */
class FILE_TYPE
{

    const DOC = 'doc';
    const PDF = 'pdf';
    const AI = 'ai';
    const EPS = 'eps';
    const PS = 'ps';
    const HTM = 'htm';
    const HTML = 'html';
    const SHTML = 'shtml';
    const XHTML = 'xhtml';
    const XML = 'xml';
    const GTAR = 'gtar';
    const PHP = 'php';
    const TAR = 'tar';
    const ZIP = 'zip';
    const JPG = 'jpg';
    const PNG = 'png';
    const GIF = 'gif';
    const CSV = 'csv';
    const CSS = 'css';
    const JS = 'js';
    const TXT = 'txt';

    public static $mimeType = array(
        'gz' => 'application/gzip',
        'xls' => 'application/msexcel',
        'ppt' => 'application/mspowerpoint',
        'doc' => 'application/msword',
        'pdf' => 'application/pdf',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        'htm' => 'text/html',
        'html' => 'text/html',
        'shtml' => 'application/xhtml+xml',
        'xhtml' => 'application/xhtml+xml',
        'xml' => 'text/xml',
        'gtar' => 'application/x-gtar',
        'php' => 'application/x-httpd-php',
        'tar' => 'application/x-tar',
        'zip' => 'application/zip',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'csv' => 'text/comma-separated-values',
        'css' => 'text/css',
        'js' => 'text/javascript',
        'txt' => 'text/plain');

    /**
     * Check if FileType has a given MimeType.
     *
     * @param string $end The fileending string without ".".
     *
     * @return bool Returns true if filetype is supported.
     */
    public static function checkSupportedFileType($end) {
        return array_key_exists($end, self::$mimeType);
    }

    /**
     * Returns a mime-type to given fileending.
     *
     * @param string $end The fileending string without ".".
     *
     * @return string Returns mime-type.
     */
    public static function getMimeTypeByFileEnding($end) {
        return self::$mimeType[$end];
    }
}

?>
