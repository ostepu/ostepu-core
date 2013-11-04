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
function getIncludeContents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
}

?>
