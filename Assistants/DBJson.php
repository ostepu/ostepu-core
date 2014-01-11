<?php
/**
* @file DBJson.php contains the DbJson class
*/ 


/**
 * the DBJson class is written for several tasks
 *
 * @author Till Uhlig
 */
class DBJson
{

    function mysql_real_escape_string($inp) {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);

        if(!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    } 

    /**
     * The function checks whether an input list of arguments, has the correct data type
     * If not, the slim instance terminates with a 412 error code
     *
     * @param $app a running slim instance
     */
    public static function checkInput(){
        $args = func_get_args();
        
        // the first argument ist the slim instance, remove from the test list
        $app = &$args[0];
        $args = array_slice ( $args, 1, count($args) );

        foreach ($args as &$a) {
            // search a argument, which is not true
            if (!$a){
                // one of the arguments isn't true, abort progress
                Logger::Log("access denied",LogLevel::ERROR);
                $app->response->setBody("[]");
                $app->response->setStatus(412);
                $app->stop();
                break;
            }
        }
    }

    /**
     * the function reads the passed mysql object and converts direktly into json
     *
     * @param $object a mysql answer
     *
     * @return an array of json (string[]) 
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
     * the function reads the passed mysql object content
     *
     * @param $data a mysql answer
     *
     * @return an array, which represents the received columns (string[][])
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
     * extract an array of attributes from a data array, using a list of object attributes
     *
     * @param $data an array, which represents the data, received sql data
     * @param $id the primary key/keys (string/string[])
     * @param $attributes the object attributes (string[])
     * @param $extension optional, a const postfix for the column names (string)
     *
     * @return an array of assoc arrays
     */
    public static function getObjectsByAttributes($data, $id, $attributes, $extension = "")
    {
        $res = array();
        
        foreach ($data as $row) { 
            $key = "";
            if (is_array($id)){
                foreach ($id as $di){
                    $key = $key . $row[$di.$extension] . ',';
                }
            } else{
                $key = $row[$id.$extension];
            }

            foreach ($attributes as $attrib => $value) {  
                if (isset($row[$attrib.$extension])){          
                    $res[$key][$value] =  $row[$attrib.$extension];
                }
            }
        }
        return $res;
    }
    
    /**
     * extract an array of attributes from a data array, using a list of object attributes
     *
     * @param $data (description)
     * @param $id (description)
     * @param $attributes (description)
     * @param $extension (description)
     *
     * @return an array of arrays
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
     * generates insert data from input
     *
     * @param $data (description)
     * @param $attributes (description)
     * @param $seperator (description)
     *
     * @return
     */
    public static function getInsertDataFromInput($data, $attributes, $seperator)
    {
        $row = $data;

        $temp = array();
        $t1 = "";
        $t2 = "";
        foreach ($attributes as $attrib => $value) {  
            if (isset($row[$value]) && !is_array($row[$value]) && gettype($row[$value])!= 'object'){    
                $t1 = $t1 . $seperator . $attrib;
                $t2 = $t2 . $seperator . "'" . $row[$value] . "'";
            }
        }

        if ($t1 != "" && $t2 != ""){
            $t1=substr($t1,1);  
            $t2=substr($t2,1);
        }    
        return array ('columns' => $t1, 'values' => $t2);
    }
    
    /**
     * generates update data from input
     *
     * @param $data (description)
     * @param $attributes (description)
     * @param $seperator (description)
     *
     * @return
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
     * concatenates two arrays by using attribute lists, 
     *
     * @param $data (description)
     * @param $prim (description)
     * @param $primKey (description)
     * @param $primAttrib (description)
     * @param $sec (description)
     * @param $secKey (description)
     * @param $extension (description)
     *
     * @return
     */
    public static function concatResultObjectLists($data, $prim, $primKey, $primAttrib, $sec, $secKey, $extension = "")
    {
        foreach ($prim as &$row){
            $row[$primAttrib] = array();
        }
    
        foreach ($data as $rw){
            $key = "";
            if (is_array($primKey)){
                foreach ($primKey as $di){
                    $key = $key . $rw[$di] . ',';
                }
            } else{
                $key = $rw[$primKey];
            }
            
            if (isset($sec[$rw[$secKey.$extension]])){
                $prim[$key][$primAttrib][$rw[$secKey.$extension]] = $sec[$rw[$secKey.$extension]];
            }
        }
        
        foreach ($prim as &$row){
            $row[$primAttrib] = array_merge($row[$primAttrib]);
        }
        
        $prim = array_values($prim);

        return $prim;
    }
    
    /**
     * (description)
     *
     * @param $data (description)
     * @param $prim (description)
     * @param $primKey (description)
     * @param $primAttrib (description)
     * @param $sec (description)
     * @param $secKey (description)
     * @param $extension (description)
     *
     * @return
     */
    public static function concatObjectLists($data, $prim, $primKey, $primAttrib, $sec, $secKey, $extension = "")
    {
        foreach ($prim as &$row){
            $row[$primAttrib] = array();
        }
    
        foreach ($data as $rw){
            $key = "";
            if (is_array($primKey)){
                foreach ($primKey as $di){
                    $key = $key . $rw[$di] . ',';
                }
            } else{
                $key = $rw[$primKey];
            }
        
            if (isset($sec[$rw[$secKey.$extension]])){       
                $prim[$key][$primAttrib][$rw[$secKey.$extension]] =  $sec[$rw[$secKey.$extension]];
            }
        }
    
        return $prim;
    }
    
    /**
     * (description)
     *
     * @param $data (description)
     * @param $prim (description)
     * @param $primKey (description)
     * @param $primAttrib (description)
     * @param $sec (description)
     * @param $secKey (description)
     * @param $extension (description)
     *
     * @return
     */
    public static function concatObjectListResult($data, $prim, $primKey, $primAttrib, $sec, $secKey, $extension = "")
    {
        foreach ($prim as &$row){
            $row[$primAttrib] = array();
        }
    
        foreach ($data as $rw){
            $key = "";
            if (is_array($primKey)){
                foreach ($primKey as $di){
                    $key = $key . $rw[$di] . ',';
                }
            } else{
                $key = $rw[$primKey];
            }
            
            if (isset($sec[$rw[$secKey.$extension]])){        
                $prim[$key][$primAttrib][$rw[$secKey.$extension]] =  $sec[$rw[$secKey.$extension]];
            }
        }
    
    
        foreach ($prim as &$row){
            $row[$primAttrib] = array_merge($row[$primAttrib]);
        }
        
        return $prim;
    }
    
    /**
     * (description)
     *
     * @param $data (description)
     * @param $prim (description)
     * @param $primKey (description)
     * @param $primAttrib (description)
     * @param $sec (description)
     * @param $secKey (description)
     * @param $extension (description)
     *
     * @return
     */
    public static function concatObjectListsSingleResult($data, $prim, $primKey, $primAttrib, $sec, $secKey, $extension = "")
    {
        foreach ($data as $rw){
            $key = "";
            if (is_array($primKey)){
                foreach ($primKey as $di){
                    $key = $key . $rw[$di] . ',';
                }
            } else{
                $key = $rw[$primKey];
            }

            if (isset($sec[$rw[$secKey.$extension]])){
                if (!isset($prim[$key][$primAttrib]))
                    $prim[$key][$primAttrib] = $sec[$rw[$secKey.$extension]];
            }
        }
   
        return $prim;
    }
    
    /**
     * (description)
     *
     * @param $data (description)
     * @param $prim (description)
     * @param $primKey (description)
     * @param $primAttrib (description)
     * @param $sec (description)
     * @param $secKey (description)
     * @param $extension (description)
     *
     * @return
     */
    public static function concatResultObjectListAsArray($data, $prim, $primKey, $primAttrib, $sec, $secKey, $extension = "")
    {
        foreach ($prim as &$row){
            $row[$primAttrib] = array();
        }
    
        foreach ($data as $rw){
            if (isset($sec[$rw[$secKey]])){
                array_push($prim[$rw[$primKey]][$primAttrib], $rw[$secKey]);
            }
        }
        
        $prim = array_merge($prim);
        return $prim;
    }
}
?>