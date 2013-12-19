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
     */
    private $complete = null;
    
    /**
     * (description)
     */
    public function getcomplete()
    {
        return $this->complete;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function setComplete($value)
    {
        $this->complete = $value;
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
           'CL_complete' => 'complete',
           'CL_id_owner' => 'owner',
           'CL_id_target' => 'target',
           'CL_relevanz' => 'relevanz'
        );
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
            'complete' => $this->complete,
            'relevanz' => $this->relevanz
        );
    }
}
?>