<?php
/**
 * @file LArraySorter.php contains the LArraySorter class
 *
 * @author Ralf Busch
 */

/**
 * A class, to sort an array properly.
 */
class LArraySorter
{
    /**
     * Reverses an array.
     *
     * @param array $array The array which will be reversed.
     *
     * @return array $array The reversed array.
     */
    public static function reverse($array)
    {
        return array_reverse($array);
    }

    /**
     * Orders an array by given keys.
     *
     * This methods accepts multiple arguments. That means you can define more than one key.
     * e.g. orderby($data, 'key1', SORT_DESC, 'key2', SORT_ASC).
     *
     * @param array $data The array which will be sorted.
     * @param string $key The header of the request.
     * @param mixed $sortorder Either SORT_ASC to sort ascendingly or SORT_DESC to sort descendingly.
     *
     * @return array $file A file that represents the new information
     * which belongs to the added one. If there are an error, an empty array is returned.
     */
    public static function orderBy()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
                }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
}
?>