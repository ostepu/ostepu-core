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
    private $target = null;
    
    /**
     * (description)
     */
    public function getTarget()
    {
        return $this->target;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
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
     * (description)
     */
    public function getOwner()
    {
        return $this->owner;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function setOwner($value)
    {
        $this->owner = $value;
    }
    
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
    private $relevanz = null;
    
    /**
     * (description)
     */
    public function getRelevanz()
    {
        return $this->relevanz;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
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
        
        if ($this->id != null) $this->addInsertData($values, 'ES_id', mysql_real_escape_string($this->id));
        if ($this->name != null) $this->addInsertData($values, 'CL_name', mysql_real_escape_string($this->name));
        if ($this->owner != null) $this->addInsertData($values, 'CO_id_owner', mysql_real_escape_string($this->owner));
        if ($this->target != null) $this->addInsertData($values, 'CO_id_target', mysql_real_escape_string($this->target));
        if ($this->relevanz != null) $this->addInsertData($values, 'CL_relevanz', mysql_real_escape_string($this->relevanz));
        
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
     * (description)
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