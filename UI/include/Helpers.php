<?php
/**
 * @file Helpers.php
 * %A collection of helper methods that can be used by classes
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

function http_get($url)
{
    $c = curl_init();

    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_HTTPGET, 1);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);

    $retData = curl_exec($c);
    curl_close($c);


    return $retData;
}

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

?>
