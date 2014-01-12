<?php 
/**
 * 
 */
class Link extends Object implements JsonSerializable
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
    private $target = null;
    
    /**
     * the $target getter
     *
     * @return the value of $target
     */ 
    public function getTarget()
    {
        return $this->target;
    }
    
    /**
     * the $target setter
     *
     * @param string $value the new value for $target
     */ 
    public function setTarget($value)
    {
        $this->target = $value;
    }
    
    /**
     * (description)
     */
    private $owner = null;
    
    /**
     * the $owner getter
     *
     * @return the value of $owner
     */ 
    public function getOwner()
    {
        return $this->owner;
    }
    
    /**
     * the $owner setter
     *
     * @param string $value the new value for $owner
     */ 
    public function setOwner($value)
    {
        $this->owner = $value;
    }
    
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
    private $relevanz = null;
    
    /**
     * the $relevanz getter
     *
     * @return the value of $relevanz
     */ 
    public function getRelevanz()
    {
        return $this->relevanz;
    }
    
    /**
     * the $relevanz setter
     *
     * @param string $value the new value for $relevanz
     */ 
    public function setRelevanz($value)
    {
        $this->relevanz = $value;
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
     */
    public static function getDbConvert()
    {
        return array(
           'CL_id' => 'id',
           'CL_name' => 'name',
           'CL_address' => 'address',
           'CL_prefix' => 'prefix',
           'CO_id_owner' => 'owner',
           'CO_id_target' => 'target',
           'CL_relevanz' => 'relevanz'
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'ES_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->name != null) $this->addInsertData($values, 'CL_name', DBJson::mysql_real_escape_string($this->name));
        if ($this->owner != null) $this->addInsertData($values, 'CO_id_owner', DBJson::mysql_real_escape_string($this->owner));
        if ($this->target != null) $this->addInsertData($values, 'CO_id_target', DBJson::mysql_real_escape_string($this->target));
        if ($this->relevanz != null) $this->addInsertData($values, 'CL_relevanz', DBJson::mysql_real_escape_string($this->relevanz));
        
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
        return 'CL_id';
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeLink($data)
    {
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeLink($data, $decode=true)
    {
        if ($decode)
            $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Link($value));
            }
            return $result;   
        } else
            return new Link($data);
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
            'prefix' => $this->prefix,
            'target' => $this->target,
            'owner' => $this->owner,
            'relevanz' => $this->relevanz
        );
    }
}
?>