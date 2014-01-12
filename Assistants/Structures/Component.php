<?php
/**
 * 
 */
class Component extends Object implements JsonSerializable
{
    /**
     * (description)
     */
    private $id = null;
    
    /**
     * the $id getter
     *
     * @return the value of $id
     */ 
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * the $id setter
     *
     * @param string $value the new value for $id
     */ 
    public function setId($value)
    {
        $this->id = $value;
    }
    
    
    
    
    /**
     * (description)
     */
    private $name = null;
    
    /**
     * the $name getter
     *
     * @return the value of $name
     */ 
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * the $name setter
     *
     * @param string $value the new value for $name
     */ 
    public function setName($value)
    {
        $this->name = $value;
    }
    
    
    
    
    /**
     * (description)
     */
    private $address = null;
    
    /**
     * the $address getter
     *
     * @return the value of $address
     */ 
    public function getAddress()
    {
        return $this->address;
    }
    
    /**
     * the $address setter
     *
     * @param string $value the new value for $address
     */ 
    public function setAddress($value)
    {
        $this->address = $value;
    }
    
    
    
    
    /**
     * (description)
     */ 
    private $option = null;
    
    /**
     * the $option getter
     *
     * @return the value of $option
     */ 
    public function getOption()
    {  
        return $this->option;
    }
    
    /**
     * the $option setter
     *
     * @param string $value the new value for $option
     */ 
    public function setOption($value)
    {
        $this->option = $value;
    }
    
    
    
    
    /**
     * (description)
     */
    private $prefix = null;
    
    /**
     * the $prefix getter
     *
     * @return the value of $prefix
     */ 
    public function getPrefix()
    {
        return $this->prefix;
    }
    
    /**
     * the $prefix setter
     *
     * @param string $value the new value for $prefix
     */ 
    public function setPrefix($value)
    {
        $this->prefix = $value;
    }
    
    
    
    
    /**
     * (description)
     */
    private $links = array();
    
    /**
     * the $links getter
     *
     * @return the value of $links
     */ 
    public function getLinks()
    {
        return $this->links;
    }
    
    /**
     * the $links setter
     *
     * @param string $value the new value for $links
     */ 
    public function setLinks($value)
    {
        $this->links = $value;
    }


    
    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'CO_id' => 'id',
           'CO_name' => 'name',
           'CO_address' => 'address',
           'CO_option' => 'option',
           'CO_prefix' => 'prefix',
           'CO_links' => 'links'
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'CO_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->name != null) $this->addInsertData($values, 'CO_name', DBJson::mysql_real_escape_string($this->name));
        if ($this->address != null) $this->addInsertData($values, 'CO_address', DBJson::mysql_real_escape_string($this->address));
        if ($this->option != null) $this->addInsertData($values, 'CO_option', DBJson::mysql_real_escape_string($this->option));
        
        if ($values != ""){
            $values=substr($values,1);
        }
        return $values;
    }
    
    /**
     * (description)
     */
    public static function getDbPrimaryKey()
    {
        return 'CO_id';
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
                if ($key == 'links') {
                    $this->{$key} = Link::decodeLink($value,false);
                }
                else
                    $this->{$key} = $value;
            }
        }
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeComponent($data)
    {
        return json_encode($data);
    }

    /**
     * decodes the string
     * 
     * @param string $data the jseon encoded object data
     * @param bool $decode (description)
     *
     * @return an object
     */
    public static function decodeComponent($data, $decode=true)
    {
        if ($decode)
            $data = json_decode($data);
            
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Component($value));
            }
            return $result;   
        } else
            return new Component($data);
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize() 
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'option' => $this->option,
            'prefix' => $this->prefix,
            'links' => $this->links
        );
    }

}
?>