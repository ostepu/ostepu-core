<?php
/**
 * @file Helpers.php
 * A collection of helper methods that can be used by classes
 *
 * @author Florian Lücke
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
 * Sends an HTTP GET request.
 *
 * Uses HTTP GET request to get contents a $url
 * @param string $url The URL that should be opnened.
 * @param string $message The Response Message e.g. 404. Argument ist optional.
 */
function http_get($url, &$message = 0)
{
    $c = curl_init();

    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_HTTPGET, 1);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);

    $retData = curl_exec($c);
    $message = curl_getinfo($c, CURLINFO_HTTP_CODE);
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
 */
function http_post_data($url, $data)
{
    $c = curl_init();

    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_POST, 1);
    curl_setopt($c, CURLOPT_POSTFIELDS, $data);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);

    $retData = curl_exec($c);
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
 */
function http_put_data($url, $data)
{
    $c = curl_init();

    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_POSTFIELDS, $data);
    curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);

    $retData = curl_exec($c);
    curl_close($c);

    return $retData;
}

/**
 * Sends an HTTP DELETE request.
 *
 * Uses HTTP DELETE request to get contents a $url
 * @param string $url The URL that should be opnened.
 */
function http_delete($url)
{
    $c = curl_init();

    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);

    $retData = curl_exec($c);
    curl_close($c);

    return $retData;
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
 * @sa Notifications.css
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
 * @param $input An associative array that contains inputstrings or a string
 */
function cleanInput($input)
{
    if (is_array($input)) {
        foreach ($input as $element) {
            if (!get_magic_quotes_gpc()) {
                $element = htmlspecialchars(trim(stripcslashes($element)), ENT_QUOTES);
            } else {
                $element = htmlspecialchars(trim($element), ENT_QUOTES);
            } 
        }
    } else {
        if (!get_magic_quotes_gpc()) {
            $input = htmlspecialchars(trim(stripcslashes($input)), ENT_QUOTES);
        } else {
            $input = htmlspecialchars(trim($input), ENT_QUOTES);
        } 
    }
    return $input;
}
?>
