<?php
/**
 * @file LArraySorter.php contains the LArraySorter class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2014
 */

/**
 * A class, to sort an array properly.
 */
class LArraySorter
{
    public static function multidimensional_search($parents, $searched) {
      if (empty($searched) || empty($parents)) {
        return false;
      }

      foreach ($parents as $key => $value) {
        $exists = true;
        foreach ($searched as $skey => $svalue) {
          $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
        }
        if($exists){ return $key; }
      }

      return false;
    } 
    
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
     * @param string $key The key of $data.
     * @param mixed $sortorder Either SORT_ASC to sort ascendingly or SORT_DESC to sort descendingly.
     *
     * @return array An array ordered by given parameters.
     */
    public static function orderBy()
    {
        $args = func_get_args();
        $data = array_shift($args);
        if ($data === null) $data = array();
        $isArray=true;
        reset($data);
        if (gettype(current($data)) == 'object') $isArray=false;
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                if ($isArray){
                    foreach ($data as $key => $row){
                        $tmp[$key] = (isset($row[$field]) ? strtolower($row[$field]) : null);
                    }
                } else {
                    foreach ($data as $key => $row){
                        $tmp[$key] = (isset($row->{$field}) ? strtolower($row->{$field}) : null);
                    }                 
                }
                $args[$n] = $tmp;   
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
}