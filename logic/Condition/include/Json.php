<?php
/**
* @file (filename)
* %(description)
*/ 


/**
 * (description)
 */
class Json
{

    /**
     * (description)
     *
     * @param $app (description)
     * @param $object (description)
     */
    public static function get_json($app,$object){
        if (!$object){
            throw new Exception("Invalid query. Error: " . mysql_error());
        }
        $res = array();
       // $temp = array();
       // while ($row = $object->fetch_assoc()) {
        while ($row = mysql_fetch_assoc($object)) {
        //$temp = $row;
            array_push($res, $row);
        //     echo "|||".json_encode($row)."|||";
        }
       // echo var_dump($res);
                  //  echo json_encode($res);
        return json_encode($res);
           // return '{"'.$name.'": ' . json_encode($res) . '}';
    }
    
    /**
     * (description)
     *
     * @param $object (description)
     */
    public static function getObjectArray($object){
        $res = array();
        // while ($row = fetch_assoc($object)) {
         while ($row = mysqli_fetch_assoc($object)) {
            array_push($res, new Course($row));
        }
        return $res; 
    }

}
?>