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
    private $id;
    
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
     * a string that identifies the user
     *
     * type: string
     */
    private $userName; 
    
    /**
     * (description)
     */
    public function getUserName()
    {
        return $this->userName;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function setUserName($value)
    {
        $this->userName = $value;
    }

    
    
    
    /**
     * The user's email address.
     *
     * type: string
     */
    private $email;
    
    /**
     * (description)
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function setEmail($value)
    {
        $this->email = $value;
    }

    
    
    
    /**
     * The user's first name(s)
     *
     * type: string
     */
    private $firstName;
    
    /**
     * (description)
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function setFirstName($value)
    {
        $this->firstName = $value;
    }

    
    
    
    
    /**
     * The user's last name(s)
     *
     * type: string
     */
    private $lastName;
    
    /**
     * (description)
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function setLastName($value)
    {
        $this->lastName = $value;
    }

    
    
    
    
    /**
     * possibly a title the user holds
     *
     * type: string
     */
    private $title; 
    
    /**
     * (description)
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function setTitle($value)
    {
        $this->title = $value;
    }

    
    
    
    
    /**
     * an array of CourseStatus objects that represents the courses
     * the user is enlisted in and which role she/he has in that course
     *
     * type: CourseStatus[]
     */
    private $courses = array();
    
    /**
     * (description)
     */
    public function getCourses()
    {
        return $this->courses;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setCourses($value)
    {
        $this->courses = $value;
    }
    
    
    
    
    /**
     * (description)
     *
     * type: short
     */
    private $flag = null;
    
    /**
     * (description)
     */
    public function getFlag()
    {
        return $this->flag;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setFlag($value)
    {
        $this->flag = $value;
    }
    
    
    
    
    /**
     * (description)
     *
     * type: string
     */
    private $password = null;
    
    /**
     * (description)
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setPassword($value)
    {
        $this->password = $value;
    }
    
    
    
    
    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'U_id' => 'id',
           'U_username' => 'userName',
           'U_email' => 'email',
           'U_firstName' => 'firstName',
           'U_lastName' => 'lastName',
           'U_title' => 'title',
           'U_courses' => 'courses',
           'U_flag' => 'flag',
           'U_password' => 'password'
        );
    }
       
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'U_id', $this->id );
        if ($this->userName != null) $this->addInsertData($values, 'U_username', $this->userName );
        if ($this->email != null) $this->addInsertData($values, 'U_email', $this->email );
        if ($this->firstName != null) $this->addInsertData($values, 'U_firstName', $this->firstName );
        if ($this->lastName != null) $this->addInsertData($values, 'U_lastName', $this->lastName );
        if ($this->title != null) $this->addInsertData($values, 'U_title', $this->title );
        if ($this->flag != null) $this->addInsertData($values, 'U_flag', $this->flag );
        if ($this->password != null) $this->addInsertData($values, 'U_password', $this->password );
        
        if ($values != ""){
            $values=substr($values,1);
        }
        return $values;
    } 
    
    /**
     * (description)
     */
    public function getCourseStatusInsertData(){
        $values = "";
                
        if ($this->id != null) $this->addInsertData($values, 'U_id', $this->id );
        if ($this->courses != array()) $this->addInsertData($values, 'CS_status', $this->courses[0]->getStatus() );
        if ($this->courses != array() && $this->courses->getCourse() != null) $this->addInsertData($values, 'C_id', $this->courses[0]->getCourse()->getId() );
        
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
        return 'U_id';
    }
    
    /**
     * (description)
     */
    public static function getFlagDefinition(){
        return array(
            '0' => 'inactive',
            '1' => 'active',
        );
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
                    $this->{$key} = CourseStatus::decodeCourseStatus($value, false);

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
    public static function encodeUser($data){
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeUser($data, $decode=true)
    {
        if ($decode)
            $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new User($value));
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
         $list = array();
         if ($this->id!==null) $list['id'] = $this->id;
         if ($this->userName!==null) $list['userName'] = $this->userName;
         if ($this->email!==null) $list['email'] = $this->email;
         if ($this->firstName!==null) $list['firstName'] = $this->firstName;
         if ($this->lastName!==null) $list['lastName'] = $this->lastName;
         if ($this->title!==null) $list['title'] = $this->title;
         if ($this->courses!==array()) $list['courses'] = $this->courses;
         if ($this->flag!==null) $list['flag'] = $this->flag;
         if ($this->password!==null) $list['password'] = $this->password; 
       return $list;
    }
}
?>