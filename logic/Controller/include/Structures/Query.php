<?php 
/**
 * 
 */
class Query extends Object implements JsonSerializable
{

    /**
     * (description)
     *
     * type: String
     */
    private $content = null;
    
    /**
     * (description)
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setContent($value)
    {
        $this->content = $value;
    }
    
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public function __construct($data=array()) 
    {
        foreach ($data AS $key => $value) {
            if (isset($key)){
                $this->{$key} = $value;
            }
        }
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeQuery($data){
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeQuery($data, $decode=true)
    {
        if ($decode)
            $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Query($value));
            }
            return $result;   
        } else
            return new User($data);
    }
    
    /**
     * (description)
     */
    public function jsonSerialize() 
    {       
         if ($this->content!==null) $list['content'] = $this->content;
         
       return $list;
    }
}
?>