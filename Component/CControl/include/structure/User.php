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
    public function getId(){
        return $this->_id;
    }
    public function setId($_value){
        $this->_id = $_value;
    }
    
    /**
     * a string that identifies the user
     *
     * type: string
     */
    private $_userName; 
    public function getUserName(){
        return $this->_userName;
    }
    public function setUserName($_value){
        $this->_userName = $_value;
    }

    /**
     * The user's email address.
     *
     * type: string
     */
    private $_email;
    public function getEmail(){
        return $this->_email;
    }
    public function setWmail($_value){
        $this->_email = $_value;
    }

    /**
     * The user's first name(s)
     *
     * type: string
     */
    private $_firstName;
    public function getFirstName(){
        return $this->_firstName;
    }
    public function setFirstName($_value){
        $this->_firstName = $_value;
    }

    /**
     * The user's last name(s)
     *
     * type: string
     */
    private $_lastName;
    public function getLastName(){
        return $this->_lastName;
    }
    public function setLastName($_value){
        $this->_lastName = $_value;
    }

    /**
     * possibly a title the user holds
     *
     * type: string
     */
    private $_title; 
    public function getTitle(){
        return $this->_title;
    }
    public function setTitle($_value){
        $this->_title = $_value;
    }

    /**
     * an array of CourseStatus objects that represents the courses
     * the user is enlisted in and which role she/he has in that course
     *
     * type: Course[]
     */
    private $_courses = array();
    public function getCourses(){
        return $this->_courses;
    }
    public function setCourses($_value){
        $this->_courses = $_value;
    }
    
    
    public static function getDBConvert(){
    
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
    public static function getDBPrimaryKey(){
    
        return 'U_id';
    }
    
    
    public function __construct($_data=array()) {
        foreach ($_data AS $_key => $_value) {
            if (isset($_key)){
                if (is_array($_value)) {
                    $_sub = new Course($_value);
                    $_value = $_sub;
                }
                $this->{$_key} = $_value;
            }
        }
    }
    
    public static function encodeUser($_data){
        return json_encode($_data);
    }
    
    public static function decodeUser($_data){
        $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $_value) {
                array_push($result, new User($_value));
            }
            return $result;   
        }
        else
            return new User($_data);
    }

    public function jsonSerialize() {
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