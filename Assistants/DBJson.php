<?php
/**
* @file (filename)
* %(description)
*/ 


/**
 * (description)
 */
class DBJson
{

    /**
     * (description)
     *
     * @param $app (description)
     * @param $object (description)
     */
    public static function get_json($object){
        if (!$object){
            throw new Exception("Invalid query. Error: " . mysql_error());
        }
        $res = array();
        while ($row = mysql_fetch_assoc($object)) {
            array_push($res, $row);
        }
        return json_encode($res);
    }
        
    /**
     * (description)
     *
     * @param $object (description)
     */
    public static function GetRows($data){
        $res = array();
        while ($row = mysql_fetch_assoc($data)) {                   
            array_push($res,$row);
        }
        return $res;
    }
    
    /**
     * (description)
     *
     * @param $object (description)
     */
    public static function GetObjectsByAttributes($data, $id, $attributes){
        $res = array();
        foreach ($data as $row) {       
            foreach ($attributes as $attrib => $value) {  
                if (isset($row[$attrib])){          
                    $res[$row[$id]][$value] =  $row[$attrib];
                }
            }
        }

        return $res;
    }
    
    /**
     * (description)
     *
     * @param $object (description)
     */
    public static function ConcatObjectLists($data, $prim, $primKey, $primAttrib, $sec, $secKey){
    foreach ($prim as &$row){
        $row[$primAttrib] = array();
    }
    
    foreach ($data as $rw){
    array_push($prim[$rw[$primKey]][$primAttrib], $sec[$rw[$secKey]]);
    }
    
    return $prim;
    }

}
?>