<?php 
/**
 * @file Link.php contains the Link class
 */
 
/**
 * the link structure
 *
 * @author Till Uhlig
 */
class Link extends Object implements JsonSerializable
{
    /**
     * @var string $id the db id of the link
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
     * @var string $target the target component id 
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
     * @var string $id the link owner component id
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
    
    /**
     * @var string $name the link name
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
     * (description)
     *
     * @param $conf (description)
     */
    public function setName($value)
    {
        $this->name = $value;
    }
    
    
    
    
    /**
     * @var string $address the URL/address of the target component 
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
     * @var string $relevanz an optional attribute for components who want to differentiate their links
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
     *  @var string $prefix the prefix with which the component operates
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
     * Creates an Link object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $id The id of the link.
     * @param string $owner The id of the owner.
     * @param string $target The id of the target.
     * @param string $name The link name.
     * @param string $relevanz The relevanz.
     *
     * @return an link object
     */
    public function createLink($id,$owner,$target,$name,$relevanz)
    {
        return new Link(array('id' => $id,
        'owner' => $owner,
        'target' => $target,
        'name' => $name,
        'relevanz' => $relevanz));
    }
    
    /**
     * the constructor
     * 
     * @param $data an assoc array with the object informations
     */
    public function __construct($data=array())
    {
        if ($data==null)
            $data = array();
        
        foreach ($data AS $key => $value) {
            if (isset($key)){
                $this->{$key} = $value;
            }
        }
    }
    
    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
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
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData()
    {
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'CL_id', DBJson::mysql_real_escape_string($this->id));
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
     * returns a sting/string[] of the database primary key/keys
     * 
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey()
    {
        return 'CL_id';
    }
    
    /**
     * encodes an object to json
     * 
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeLink($data)
    {
        return json_encode($data);
    }
    
    /**
     * decodes $data to an object
     * 
     * @param string $data json encoded data (decode=true) 
     * or json decoded data (decode=false)
     * @param bool $decode specifies whether the data must be decoded
     *
     * @return the object
     */
    public static function decodeLink($data, $decode=true)
    {
        if ($decode && $data==null) 
            $data = "{}";
            
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
        $list = array();
        if ($this->id!==null) $list['id'] = $this->id;
        if ($this->name!==null) $list['name'] = $this->name;
        if ($this->address!==null) $list['address'] = $this->address;
        if ($this->target!==null) $list['target'] = $this->target;
        if ($this->prefix!==null) $list['prefix'] = $this->prefix;
        if ($this->owner!==null) $list['owner'] = $this->owner;
        if ($this->relevanz!==null) $list['relevanz'] = $this->relevanz;
        return $list;  
    }
}
?>