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
     * @param $app (description)
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
     * @param $object (description)
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
    public static function getObjectsByAttributes($data, $id, $attributes)
    {
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
     * @param $object (description)
     * @param $object (description)
     */
    public static function getResultObjectsByAttributes($data, $id, $attributes)
    {
        $res = array();
        foreach ($data as $row) {  
            $temp = NULL;
            foreach ($attributes as $attrib => $value) {  
                if (isset($row[$attrib])){              
                    $temp[$value] =  $row[$attrib];
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
     */
    public static function concatObjectLists($data, $prim, $primKey, $primAttrib, $sec, $secKey)
    {
    foreach ($prim as &$row){
        $row[$primAttrib] = array();
    }
    
    foreach ($data as $rw){
        if (isset($sec[$rw[$secKey]])){
            array_push($prim[$rw[$primKey]][$primAttrib], $sec[$rw[$secKey]]);
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