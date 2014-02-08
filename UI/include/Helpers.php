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

    if ($message == "409") {
        set_error("409");
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

    if ($message == "409") {
        set_error("409");
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

    if ($message == "409") {
        set_error("409");
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

    if ($message == "409") {
        set_error("409");
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
                      "Date : {$date}"));
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

function MakeNavigationElementForCourseStatus($courses, $requiredPrivilege, $switchDisabled = false) {
    if (count($courses) > 1) {
        return "";
    }

    $courseStatus = $courses[0]['status'];
    $course = $courses[0]['course'];

    $navigationElement = NULL;

    // chooses the menu depending on the user's status in the course
    switch ($courseStatus) {
        case 2:
            // status = Lecturer
            $file = 'include/Navigation/NavigationLecturer.template.html';
            $navigationElement = Template::WithTemplateFile($file);
            break;
        case 3:
            // status = Admin
            $file = 'include/Navigation/NavigationAdmin.template.html';
            $navigationElement = Template::WithTemplateFile($file);
            break;
    }

    if (isset($navigationElement)) {
        $navigationElement->bind(array('cid' => $course['id'],
                                       'requiredPrivilege' => $requiredPrivilege,
                                       'courseStatus' => $courseStatus,
                                       'switchDisabled' => $switchDisabled));
    }

    return $navigationElement;
}

?>
