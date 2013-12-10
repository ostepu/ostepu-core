<?php 
/**
 * 
 */
class User extends Object implements JsonSerializable
{

    /**
     * a id that identifies the user
     *
     * type: int
     */
    private $_id;
    
    /**
     * (description)
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setId($value)
    {
        $this->_id = $value;
    }
    
    
    
    
    /**
     * a string that identifies the user
     *
     * type: string
     */
    private $_userName; 
    
    /**
     * (description)
     */
    public function getUserName()
    {
        return $this->_userName;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setUserName($value)
    {
        $this->_userName = $value;
    }

    
    
    
    /**
     * The user's email address.
     *
     * type: string
     */
    private $_email;
    
    /**
     * (description)
     */
    public function getEmail()
    {
        return $this->_email;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setEmail($value)
    {
        $this->_email = $value;
    }

    
    
    
    /**
     * The user's first name(s)
     *
     * type: string
     */
    private $_firstName;
    
    /**
     * (description)
     */
    public function getFirstName()
    {
        return $this->_firstName;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setFirstName($value)
    {
        $this->_firstName = $value;
    }

    
    
    
    
    /**
     * The user's last name(s)
     *
     * type: string
     */
    private $_lastName;
    
    /**
     * (description)
     */
    public function getLastName()
    {
        return $this->_lastName;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setLastName($value)
    {
        $this->_lastName = $value;
    }

    
    
    
    
    /**
     * possibly a title the user holds
     *
     * type: string
     */
    private $_title; 
    
    /**
     * (description)
     */
    public function getTitle()
    {
        return $this->_title;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setTitle($value)
    {
        $this->_title = $value;
    }

    
    
    
    
    /**
     * an array of CourseStatus objects that represents the courses
     * the user is enlisted in and which role she/he has in that course
     *
     * type: Course[]
     */
    private $_courses = array();
    
    /**
     * (description)
     */
    public function getCourses()
    {
        return $this->_courses;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setCourses($value)
    {
        $this->_courses = $value;
    }
    
    
    
    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'U_id' => '_id',
           'U_username' => '_userName',
           'U_email' => '_email',
           'U_firstName' => '_firstName',
           'U_lastName' => '_lastName',
           'U_title' => '_title',
           'U_courses' => '_courses'
        );
    }
    
    /**
     * (description)
     */
    public static function getDbPrimaryKey()
    {
        return 'U_id';
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public function __construct($_data=array()) 
    {
        foreach ($_data AS $_key => $value) {
            if (isset($_key)){
                if (is_array($value)) {
                    $_sub = Course::decodeCourse($value);
                    $value = $_sub;
                }
                $this->{$_key} = $value;
            }
        }
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeUser($_data){
        return json_encode($_data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeUser($_data, $decode=true)
    {
        if ($decode)
            $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $value) {
                array_push($result, new User($value));
            }
            return $result;   
        } else
            return new User($_data);
    }
    
    /**
     * (description)
     */
    public function jsonSerialize() 
    {
      /*  return array(
            '_id' => $this->_id,
            '_userName' => $this->_userName,
            '_email' => $this->_email,
            '_firstName' => $this->_firstName,
            '_lastName' => $this->_lastName,
            '_title' => $this->_title,
            '_courses' => $this->_courses
        );*/
        
         if ($this->_id!==null) $list['_id'] = $this->_id;
         if ($this->_userName!==null) $list['_userName'] = $this->_userName;
         if ($this->_email!==null) $list['_email'] = $this->_email;
         if ($this->_firstName!==null) $list['_firstName'] = $this->_firstName;
         if ($this->_lastName!==null) $list['_lastName'] = $this->_lastName;
         if ($this->_title!==null) $list['_title'] = $this->_title;
         if ($this->_courses!==array()) $list['_courses'] = $this->_courses;
       return $list;
    }
}
?>