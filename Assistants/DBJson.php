<?php
/**
* @file (filename)
* %(description)
*/ 


/**
 * (description)
 */
class DbJson
{

    /**
     * (description)
     *
     * @param $object (description)
     */
    public static function getJson($object)
    {
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
     * @param $data (description)
     */
    public static function getRows($data)
    {
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
     * @param $object (description)
     * @param $object (description)
     */
    public static function getObjectsByAttributes($data, $id, $attributes, $extension = "")
    {
        $res = array();
        foreach ($data as $row) {       
            foreach ($attributes as $attrib => $value) {  
                if (isset($row[$attrib.$extension])){          
                    $res[$row[$id.$extension]][$value] =  $row[$attrib.$extension];
                }
            }
        }
        return $res;
    }
    
    /**
     * (description)
     *
     * @param $object (description)
     * @param $object (description)
     * @param $object (description)
     */
    public static function getResultObjectsByAttributes($data, $id, $attributes, $extension = "")
    {
        $res = array();
        foreach ($data as $row) {  
            $temp = NULL;
            foreach ($attributes as $attrib => $value) {  
                if (isset($row[$attrib.$extension])){              
                    $temp[$value] =  $row[$attrib.$extension];
                }
            }
            array_push($res,$temp);
        }
    
        return $res;
    }
    
    /**
     * (description)
     *
     * @param $object (description)
     * @param $object (description)
     * @param $object (description)
     */
    public static function getInsertDataFromInput($data, $attributes, $seperator)
    {
        $data = json_decode($data);
        if (!is_array($data))
            $data = array($data);
        
        $res = array();
        foreach ($data as $row) {  
            $row = get_object_vars($row);
            $temp = array();
            $t1 = "";
            $t2 = "";
            foreach ($attributes as $attrib => $value) {  
                if (isset($row[$value]) && !is_array($row[$value])){    
                    $t1 = $t1 . $seperator . $attrib;
                    $t2 = $t2 . $seperator . "'" . $row[$value] . "'";
                }
            }
            if ($t1 != "" && $t2 != ""){
                $t1=substr($t1,1);  
                $t2=substr($t2,1);
                array_push($temp, $t1);
                array_push($temp, $t2);
                array_push($res,$temp);
            }
        }
    
        return $res;
    }
    
     /**
     * (description)
     *
     * @param $object (description)
     * @param $object (description)
     * @param $object (description)
     */
    public static function getUpdateDataFromInput($data, $attributes, $seperator)
    {
        $data = json_decode($data,true);
        
            $temp = "";
            foreach ($attributes as $attrib => $value) {  
                if (isset($data[$value]) && !is_array($data[$value])){    
                    $temp = $temp . $seperator . $attrib . '=' . "'" . $data[$value] . "'";
                }
            }
            if ($temp != ""){
                $temp=substr($temp,1);  
            }
    
        return $temp;
    } 
    
    /**
     * (description)
     *
     * @param $object (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     */
    public static function concatResultObjectLists($data, $prim, $primKey, $primAttrib, $sec, $secKey, $extension = "")
    {
        foreach ($prim as &$row){
            $row[$primAttrib] = array();
        }
    
        foreach ($data as $rw){
            if (isset($sec[$rw[$secKey.$extension]])){
                array_push($prim[$rw[$primKey]][$primAttrib], $sec[$rw[$secKey.$extension]]);
            }
        }
    
        $arr = array();
        foreach ($prim as $rw){
            array_push($arr, $rw);
        }
    
        return $arr;
    }
    
    /**
     * (description)
     *
     * @param $object (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     */
    public static function concatObjectLists($data, $prim, $primKey, $primAttrib, $sec, $secKey, $extension = "")
    {
        foreach ($prim as &$row){
            $row[$primAttrib] = array();
        }
    
        foreach ($data as $rw){
            if (isset($sec[$rw[$secKey.$extension]])){
                if (!isset($prim[$rw[$primKey]][$primAttrib]))
                    $prim[$rw[$primKey]][$primAttrib] = array();
        
            $prim[$rw[$primKey]][$primAttrib][$rw[$secKey.$extension]] =  $sec[$rw[$secKey.$extension]];
            }
        }
    
        return $prim;
    }
    
        /**
     * (description)
     *
     * @param $object (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     */
    public static function concatObjectListsSingleResult($data, $prim, $primKey, $primAttrib, $sec, $secKey, $extension = "")
    {
    
        foreach ($data as $rw){
            if (isset($sec[$rw[$secKey]])){
                if (!isset($prim[$rw[$primKey]][$primAttrib]))
                    $prim[$rw[$primKey]][$primAttrib] =  $sec[$rw[$secKey]];
            }
        }
   
        return $prim;
    }
    
    /**
     * (description)
     *
     * @param $object (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     * @param $param (description)
     */
    public static function concatResultObjectListAsArray($data, $prim, $primKey, $primAttrib, $sec, $secKey, $extension = "")
    {
        foreach ($prim as &$row){
            $row[$primAttrib] = array();
        }
    
        foreach ($data as $rw){
            if (isset($sec[$rw[$secKey]])){
                if (!in_array($rw[$secKey], $prim[$rw[$primKey]][$primAttrib]))
                    array_push($prim[$rw[$primKey]][$primAttrib], $rw[$secKey]);
            }
        }
    
        $arr = array();
        foreach ($prim as $rw){
            array_push($arr, $rw);
        }
        
        return $arr;
    }
}
?>