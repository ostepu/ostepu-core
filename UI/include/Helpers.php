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
 * Delete masked slashes from array and trim it.
 *
 * @param array $input An associative array that contains inputstrings
 */
function cleanInput($input)
{
    foreach ($input as $element) {
        if (get_magic_quotes_gpc()) {
            $element = trim(stripcslashes($element));
        } else {
            $element = trim($element);
        } 
    }

    return $input;
}
/**
 * check if user is logged in
 */
function checkLogin()
{
    session_regenerate_id(true);
    if (!isset($_SESSION['signed']) || !$_SESSION['signed']) {return false;}
    // check for timeout (after 10 minutes of inactivity)
    if (!isset($_SESSION['lastactive']) || ($_SESSION['lastactive'] + 10*60) <= $_SERVER['REQUEST_TIME']) {return false;}
    /**
     * @todo check if sessionid is on the DBserver and userid on the DB ist equal to SessionUID, session from DB only if flag is 1
     * @todo check rights
     */

    // update last activity 
    $_SESSION['lastactive'] = $_SERVER['REQUEST_TIME'];
    return true;
}

?>
