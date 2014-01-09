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
     * type: Course[]
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
           'U_flag' => 'flag'
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
    public function __construct($data=array()) 
    {
        foreach ($data AS $key => $value) {
            if (isset($key)){
                if (is_array($value)) {
                    $sub = Course::decodeCourse($value);
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
      /*  return array(
            'id' => $this->id,
            'userName' => $this->userName,
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'title' => $this->title,
            'courses' => $this->courses
        );*/
        
         if ($this->id!==null) $list['id'] = $this->id;
         if ($this->userName!==null) $list['userName'] = $this->userName;
         if ($this->email!==null) $list['email'] = $this->email;
         if ($this->firstName!==null) $list['firstName'] = $this->firstName;
         if ($this->lastName!==null) $list['lastName'] = $this->lastName;
         if ($this->title!==null) $list['title'] = $this->title;
         if ($this->courses!==array()) $list['courses'] = $this->courses;
         if ($this->flag!==null) $list['flag'] = $this->flag;
       return $list;
    }
}
?>