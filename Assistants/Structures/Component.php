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
     * (description)
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
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
     * (description)
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
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
     * (description)
     */
    public function getAddress()
    {
        return $this->address;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
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
     * (description)
     */
    public function getOption()
    {  
        return $this->option;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
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
     * (description)
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
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
     * (description)
     */
    public function getLinks()
    {
        return $this->links;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
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
        
        if ($this->id != null) $this->addInsertData($values, 'CO_id', $this->id );
        if ($this->name != null) $this->addInsertData($values, 'CO_name', $this->name );
        if ($this->address != null) $this->addInsertData($values, 'CO_address', $this->address );
        if ($this->option != null) $this->addInsertData($values, 'CO_option', $this->option );
        
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
                if (is_array($value)) {
                    $sub =  Link::decodeLink($value,false);
                    $value = $sub;
                }
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
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
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
     * (description)
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