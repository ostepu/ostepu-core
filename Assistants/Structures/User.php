<?php 
/**
 * @file User.php contains the User class
 */
 
/**
 * the user structure
 *
 * @author Till Uhlig, Florian Lücke
 */
class User extends Object implements JsonSerializable
{

    /**
     * @var string $id a id that identifies the user
     */
    private $id;
    
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
     * @var string $userName a string that identifies the user
     */
    private $userName; 
    
    /**
     * the $userName getter
     *
     * @return the value of $userName
     */ 
    public function getUserName()
    {
        return $this->userName;
    }
    
    /**
     * the $userName setter
     *
     * @param string $value the new value for $userName
     */ 
    public function setUserName($value)
    {
        $this->userName = $value;
    }

    
    
    
    /**
     * @var string $email The user's email address.
     */
    private $email;
    
    /**
     * the $email getter
     *
     * @return the value of $email
     */ 
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * the $email setter
     *
     * @param string $value the new value for $email
     */ 
    public function setEmail($value)
    {
        $this->email = $value;
    }

    
    
    
    /**
     * @var string $firstName The user's first name(s)
     */
    private $firstName;
    
    /**
     * the $firstName getter
     *
     * @return the value of $firstName
     */ 
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    /**
     * the $firstName setter
     *
     * @param string $value the new value for $firstName
     */ 
    public function setFirstName($value)
    {
        $this->firstName = $value;
    }

    
    
    
    
    /**
     * @var string $lastName The user's last name(s)
     */
    private $lastName;
    
    /**
     * the $lastName getter
     *
     * @return the value of $lastName
     */ 
    public function getLastName()
    {
        return $this->lastName;
    }
    
    /**
     * the $lastName setter
     *
     * @param string $value the new value for $lastName
     */ 
    public function setLastName($value)
    {
        $this->lastName = $value;
    }

    
    
    
    
    /**
     * @var string $title possibly a title the user holds
     */
    private $title; 
    
    /**
     * the $title getter
     *
     * @return the value of $title
     */ 
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * the $title setter
     *
     * @param string $value the new value for $title
     */ 
    public function setTitle($value)
    {
        $this->title = $value;
    }

    
    
    
    
    /**
     * @var CourseStatus[] $courses an array of CourseStatus objects that represents the courses
     * the user is enlisted in and which role she/he has in that course
     */
    private $courses = array();
    
    /**
     * the $courses getter
     *
     * @return the value of $courses
     */ 
    public function getCourses()
    {
        return $this->courses;
    }
    
    /**
     * the $courses setter
     *
     * @param CourseStatus[] $value the new value for $courses
     */ 
    public function setCourses($value)
    {
        $this->courses = $value;
    }
    
    
    
    
    /**
     * @var short $flag the account status (removed, active, locked) 
     *
     * type: short
     */
    private $flag = null;
    
    /**
     * the $flag getter
     *
     * @return the value of $flag
     */ 
    public function getFlag()
    {
        return $this->flag;
    }
    
    /**
     * the $flag setter
     *
     * @param short $value the new value for $flag
     */ 
    public function setFlag($value)
    {
        $this->flag = $value;
    }
    
    
    
    
    /**
     * @var string $password the sha256 hashed password 
     */
    private $password = null;
    
    /**
     * the $password getter
     *
     * @return the value of $password
     */ 
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * the $password setter
     *
     * @param string $value the new value for $password
     */ 
    public function setPassword($value)
    {
        $this->password = $value;
    }
    
    /**
     * @var string $salt is used for logins/password hashing 
     */
    private $salt = null;
    
    /**
     * the $salt getter
     *
     * @return the value of $salt
     */ 
    public function getSalt()
    {
        return $this->salt;
    }
    
    /**
     * the $salt setter
     *
     * @param string $value the new value for $salt
     */ 
    public function setSalt($value)
    {
        $this->salt = $value;
    }
    
    /**
     * @var int $failedLogins a counter, to check how much failed logins detected
     */
    private $failedLogins = null;
    
    /**
     * the $failedLogins getter
     *
     * @return the value of $failedLogins
     */ 
    public function getFailedLogins()
    {
        return $this->failedLogins;
    }
    
    /**
     * the $failedLogins setter
     *
     * @param int $value the new value for $failedLogins
     */ 
    public function setFailedLogins($value)
    {
        $this->failedLogins = $value;
    }
    
    
    

    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
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
           'U_password' => 'password',
           'U_salt' => 'salt',
           'U_failed_logins' => 'failedLogins'
        );
    }
       
    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'U_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->userName != null) $this->addInsertData($values, 'U_username', DBJson::mysql_real_escape_string($this->userName));
        if ($this->email != null) $this->addInsertData($values, 'U_email', DBJson::mysql_real_escape_string($this->email));
        if ($this->firstName != null) $this->addInsertData($values, 'U_firstName', DBJson::mysql_real_escape_string($this->firstName));
        if ($this->lastName != null) $this->addInsertData($values, 'U_lastName', DBJson::mysql_real_escape_string($this->lastName));
        if ($this->title != null) $this->addInsertData($values, 'U_title', DBJson::mysql_real_escape_string($this->title));
        if ($this->flag != null) $this->addInsertData($values, 'U_flag', DBJson::mysql_real_escape_string($this->flag));
        if ($this->password != null) $this->addInsertData($values, 'U_password', DBJson::mysql_real_escape_string($this->password));
        if ($this->salt != null) $this->addInsertData($values, 'U_salt', DBJson::mysql_real_escape_string($this->salt));
        if ($this->failedLogins != null) $this->addInsertData($values, 'U_failed_logins', DBJson::mysql_real_escape_string($this->failedLogins));
        
        
        if ($values != ""){
            $values=substr($values,1);
        }
        return $values;
    } 
    
    /**
     * converts a course status to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
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
     * returns a sting/string[] of the database primary key/keys
     * 
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey()
    {
        return 'U_id';
    }
    
    /**
     * defines the flag attribut
     *
     * @return an mapping array
     */
    public static function getFlagDefinition(){
        return array(
            '0' => 'inactive', // <- removes all private user data, account removed
            '1' => 'active', // <- the account is active
            '2' => 'locked' // <- login locked
        );
    }
    
    /**
     * the constructor
     * 
     * @param $data an assoc array with the object informations
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
     * encodes an object to json
     * 
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeUser($data){
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
     * the json serialize function
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
         if ($this->salt!==null) $list['salt'] = $this->salt;
         if ($this->failedLogins!==null) $list['failedLogins'] = $this->failedLogins;
       return $list;
    }
}
?>