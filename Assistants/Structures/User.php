<?php 


/**
 * @file User.php contains the User class
 */

/**
 * the user structure
 *
 * @author Till Uhlig
 * @author Florian Lücke
 */
class User extends Object implements JsonSerializable
{

    /**
     * @var string $id a id that identifies the user
     */
    private $id = null;

    /**
     * the $id getter
     *
     * @return the value of $id
     */
    public function getId( )
    {
        return $this->id;
    }

    /**
     * the $id setter
     *
     * @param string $value the new value for $id
     */
    public function setId( $value = null )
    {
        $this->id = $value;
    }

    /**
     * @var string $userName a string that identifies the user
     */
    private $userName = null;

    /**
     * the $userName getter
     *
     * @return the value of $userName
     */
    public function getUserName( )
    {
        return $this->userName;
    }

    /**
     * the $userName setter
     *
     * @param string $value the new value for $userName
     */
    public function setUserName( $value = null )
    {
        $this->userName = $value;
    }

    /**
     * @var string $email The user's email address.
     */
    private $email = null;

    /**
     * the $email getter
     *
     * @return the value of $email
     */
    public function getEmail( )
    {
        return $this->email;
    }

    /**
     * the $email setter
     *
     * @param string $value the new value for $email
     */
    public function setEmail( $value = null )
    {
        $this->email = $value;
    }

    /**
     * @var string $firstName The user's first name(s)
     */
    private $firstName = null;

    /**
     * the $firstName getter
     *
     * @return the value of $firstName
     */
    public function getFirstName( )
    {
        return $this->firstName;
    }

    /**
     * the $firstName setter
     *
     * @param string $value the new value for $firstName
     */
    public function setFirstName( $value = null )
    {
        $this->firstName = $value;
    }

    /**
     * @var string $lastName The user's last name(s)
     */
    private $lastName = null;

    /**
     * the $lastName getter
     *
     * @return the value of $lastName
     */
    public function getLastName( )
    {
        return $this->lastName;
    }

    /**
     * the $lastName setter
     *
     * @param string $value the new value for $lastName
     */
    public function setLastName( $value = null )
    {
        $this->lastName = $value;
    }

    /**
     * @var string $title possibly a title the user holds
     */
    private $title = null;

    /**
     * the $title getter
     *
     * @return the value of $title
     */
    public function getTitle( )
    {
        return $this->title;
    }

    /**
     * the $title setter
     *
     * @param string $value the new value for $title
     */
    public function setTitle( $value = null )
    {
        $this->title = $value;
    }

    /**
     * @var CourseStatus[] $courses an array of CourseStatus objects that represents the courses
     * the user is enlisted in and which role she/he has in that course
     */
    private $courses = array( );

    /**
     * the $courses getter
     *
     * @return the value of $courses
     */
    public function getCourses( )
    {
        return $this->courses;
    }

    /**
     * the $courses setter
     *
     * @param CourseStatus[] $value the new value for $courses
     */
    public function setCourses( $value = null )
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
    public function getFlag( )
    {
        return $this->flag;
    }

    /**
     * the $flag setter
     *
     * @param short $value the new value for $flag
     */
    public function setFlag( $value = null )
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
    public function getPassword( )
    {
        return $this->password;
    }

    /**
     * the $password setter
     *
     * @param string $value the new value for $password
     */
    public function setPassword( $value = null )
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
    public function getSalt( )
    {
        return $this->salt;
    }

    /**
     * the $salt setter
     *
     * @param string $value the new value for $salt
     */
    public function setSalt( $value = null )
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
    public function getFailedLogins( )
    {
        return $this->failedLogins;
    }

    /**
     * the $failedLogins setter
     *
     * @param int $value the new value for $failedLogins
     */
    public function setFailedLogins( $value = null )
    {
        $this->failedLogins = $value;
    }

    /**
     * @var string $externalId represents an alias for a user. For example, for access via Studip.
     */
    private $externalId = null;

    /**
     * the $externalId getter
     *
     * @return the value of $externalId
     */
    public function getExternalId( )
    {
        return $this->externalId;
    }

    /**
     * the $externalId setter
     *
     * @param string $value the new value for $externalId
     */
    public function setExternalId( $value = null )
    {
        $this->externalId = $value;
    }

    /**
     * @var string $studentNumber represents a matriculation/student number
     */
    private $studentNumber = null;

    /**
     * the $studentNumber getter
     *
     * @return the value of $studentNumber
     */
    public function getStudentNumber( )
    {
        return $this->studentNumber;
    }

    /**
     * the $studentNumber setter
     *
     * @param string $value the new value for $studentNumber
     */
    public function setStudentNumber( $value = null )
    {
        $this->studentNumber = $value;
    }

    /**
     * @var string $isSuperAdmin is this user a super-admin
     */
    private $isSuperAdmin = null;

    /**
     * the $isSuperAdmin getter
     *
     * @return the value of $isSuperAdmin
     */
    public function getIsSuperAdmin( )
    {
        return $this->isSuperAdmin;
    }

    /**
     * the $isSuperAdmin setter
     *
     * @param string $value the new value for $isSuperAdmin
     */
    public function setIsSuperAdmin( $value = null )
    {
        $this->isSuperAdmin = $value;
    }

    /**
     * @var string $comment the user comment
     */
    private $comment = null;

    /**
     * the $comment getter
     *
     * @return the value of $comment
     */
    public function getComment( )
    {
        return $this->comment;
    }

    /**
     * the $comment setter
     *
     * @param string $value the new value for $comment
     */
    public function setComment( $value = null )
    {
        $this->comment = $value;
    }

    /**
     * Creates an User object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $userId The id of the user.
     * @param string $userName The user name.
     * @param string $email The email address.
     * @param string $firstName The first name.
     * @param string $lastName The last name.
     * @param string $title The title.
     * @param string $flag The user flag.
     * @param string $password The password hash.
     * @param string $salt The password salt.
     * @param string $failedLogins The failed logins counter.
     * @param string $externalId The external ID.
     * @param string $studentNumber The student number.
     * @param string $isSuperAdmin The super admin flag.
     * @param string $comment The user comment.
     *
     * @return an user object
     */
    public static function createUser( 
                                      $userId,
                                      $userName,
                                      $email,
                                      $firstName,
                                      $lastName,
                                      $title,
                                      $flag,
                                      $password,
                                      $salt,
                                      $failedLogins,
                                      $externalId = null,
                                      $studentNumber = null,
                                      $isSuperAdmin = null,
                                      $comment = null
                                      )
    {
        return new User( array( 
                               'id' => $userId,
                               'userName' => $userName,
                               'email' => $email,
                               'firstName' => $firstName,
                               'lastName' => $lastName,
                               'title' => $title,
                               'flag' => $flag,
                               'password' => $password,
                               'salt' => $salt,
                               'failedLogins' => $failedLogins,
                               'externalId' => $externalId,
                               'studentNumber' => $studentNumber,
                               'isSuperAdmin' => $isSuperAdmin,
                               'comment' => $comment
                               ) );
    }

    /**
     * Creates an CourseStatus object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $userId The id of the user.
     * @param string $courseId The id of the course.
     * @param string $status The status.
     *
     * @return an course status object
     */
    public static function createCourseStatus( 
                                              $userId,
                                              $courseId,
                                              $status
                                              )
    {
        return new User( array( 
                               'id' => $userId,
                               'courses' => array( array( 
                                                         'status' => $status,
                                                         'course' => new Course( array( 'id' => $courseId ) )
                                                         ) )
                               ) );
    }

    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert( )
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
                     'U_failed_logins' => 'failedLogins',
                     'U_studentNumber' => 'studentNumber',
                     'U_externalId' => 'externalId',
                     'U_isSuperAdmin' => 'isSuperAdmin',
                     'U_comment' => 'comment'
                     );
    }

    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData( )
    {
        $values = '';

        if ( $this->id != null )
            $this->addInsertData( 
                                 $values,
                                 'U_id',
                                 DBJson::mysql_real_escape_string( $this->id )
                                 );
        if ( $this->userName != null )
            $this->addInsertData( 
                                 $values,
                                 'U_username',
                                 DBJson::mysql_real_escape_string( $this->userName )
                                 );
        if ( $this->email != null )
            $this->addInsertData( 
                                 $values,
                                 'U_email',
                                 DBJson::mysql_real_escape_string( $this->email )
                                 );
        if ( $this->firstName != null )
            $this->addInsertData( 
                                 $values,
                                 'U_firstName',
                                 DBJson::mysql_real_escape_string( $this->firstName )
                                 );
        if ( $this->lastName != null )
            $this->addInsertData( 
                                 $values,
                                 'U_lastName',
                                 DBJson::mysql_real_escape_string( $this->lastName )
                                 );
        if ( $this->title != null )
            $this->addInsertData( 
                                 $values,
                                 'U_title',
                                 DBJson::mysql_real_escape_string( $this->title )
                                 );
        if ( $this->flag != null )
            $this->addInsertData( 
                                 $values,
                                 'U_flag',
                                 DBJson::mysql_real_escape_string( $this->flag )
                                 );
        if ( $this->password != null )
            $this->addInsertData( 
                                 $values,
                                 'U_password',
                                 DBJson::mysql_real_escape_string( $this->password )
                                 );
        if ( $this->salt != null )
            $this->addInsertData( 
                                 $values,
                                 'U_salt',
                                 DBJson::mysql_real_escape_string( $this->salt )
                                 );
        if ( $this->failedLogins != null )
            $this->addInsertData( 
                                 $values,
                                 'U_failed_logins',
                                 DBJson::mysql_real_escape_string( $this->failedLogins )
                                 );
        if ( $this->externalId != null )
            $this->addInsertData( 
                                 $values,
                                 'U_externalId',
                                 DBJson::mysql_real_escape_string( $this->externalId )
                                 );
        if ( $this->studentNumber != null )
            $this->addInsertData( 
                                 $values,
                                 'U_studentNumber',
                                 DBJson::mysql_real_escape_string( $this->studentNumber )
                                 );
        if ( $this->isSuperAdmin != null )
            $this->addInsertData( 
                                 $values,
                                 'U_isSuperAdmin',
                                 DBJson::mysql_real_escape_string( $this->isSuperAdmin )
                                 );
        if ( $this->comment != null )
            $this->addInsertData( 
                                 $values,
                                 'U_comment',
                                 DBJson::mysql_real_escape_string( $this->comment )
                                 );

        if ( $values != '' ){
            $values = substr( 
                             $values,
                             1
                             );
        }
        return $values;
    }

    /**
     * converts a course status to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getCourseStatusInsertData( )
    {
        $values = '';

        if ( $this->id != null )
            $this->addInsertData( 
                                 $values,
                                 'U_id',
                                 $this->id
                                 );
        if ( $this->courses != null && 
             $this->courses != array( ) )
            $this->addInsertData( 
                                 $values,
                                 'CS_status',
                                 $this->courses[0]->getStatus( )
                                 );
        if ( $this->courses != null && 
             $this->courses != array( ) && 
             $this->courses[0]->getCourse( ) != null )
            $this->addInsertData( 
                                 $values,
                                 'C_id',
                                 $this->courses[0]->getCourse( )->getId( )
                                 );

        if ( $values != '' ){
            $values = substr( 
                             $values,
                             1
                             );
        }
        return $values;
    }

    /**
     * returns a sting/string[] of the database primary key/keys
     *
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey( )
    {
        return'U_id';
    }

    /**
     * defines the flag attribut
     *
     * @return an mapping array
     */
    public static function getFlagDefinition( )
    {
        return array( 
                     '0' => 'inactive',

        // <- removes all private user data, account removed
        '1' => 'active',

        // <- the account is active
        '2' => 'locked'// <- login locked
        
                     );
    }

    /**
     * the constructor
     *
     * @param $data an assoc array with the object informations
     */
    public function __construct( $data = array( ) )
    {
        if ( $data == null )
            $data = array( );

        foreach ( $data AS $key => $value ){
            if ( isset( $key ) ){
                if ( $key == 'courses' ){
                    $this->{
                        $key
                        
                    } = CourseStatus::decodeCourseStatus( 
                                                         $value,
                                                         false
                                                         );
                    
                } else {
                    $key = strtoupper($key[0]).substr($key,1);
                    $func = "set".$key;
                    $this->$func($value);
                }
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
    public static function encodeUser( $data )
    {
        return json_encode( $data );
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
    public static function decodeUser( 
                                      $data,
                                      $decode = true
                                      )
    {
        if ( $decode && 
             $data == null )
            $data = '{}';

        if ( $decode )
            $data = json_decode( $data );

        if ( is_array( $data ) ){
            $result = array( );
            foreach ( $data AS $key => $value ){
                $result[] = new User( $value = null );
            }
            return $result;
            
        } else 
            return new User( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->id !== null )
            $list['id'] = $this->id;
        if ( $this->userName !== null )
            $list['userName'] = $this->userName;
        if ( $this->email !== null )
            $list['email'] = $this->email;
        if ( $this->firstName !== null )
            $list['firstName'] = $this->firstName;
        if ( $this->lastName !== null )
            $list['lastName'] = $this->lastName;
        if ( $this->title !== null )
            $list['title'] = $this->title;
        if ( $this->courses !== array( ) && 
             $this->courses !== null )
            $list['courses'] = $this->courses;
        if ( $this->flag !== null )
            $list['flag'] = $this->flag;
        if ( $this->password !== null )
            $list['password'] = $this->password;
        if ( $this->salt !== null )
            $list['salt'] = $this->salt;
        if ( $this->failedLogins !== null )
            $list['failedLogins'] = $this->failedLogins;
        if ( $this->externalId !== null )
            $list['externalId'] = $this->externalId;
        if ( $this->studentNumber !== null )
            $list['studentNumber'] = $this->studentNumber;
        if ( $this->isSuperAdmin !== null )
            $list['isSuperAdmin'] = $this->isSuperAdmin;
        if ( $this->comment !== null )
            $list['comment'] = $this->comment;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractUser( 
                                       $data,
                                       $singleResult = false,
                                       $UserExtension = '',
                                       $CourseStatusExtension = '',
                                       $CourseExtension = '',
                                       $isResult = true
                                       )
    {

        // generates an assoc array of users by using a defined list of its
        // attributes
        $users = DBJson::getObjectsByAttributes( 
                                                $data,
                                                User::getDBPrimaryKey( ),
                                                User::getDBConvert( ),
                                                $UserExtension
                                                );

        // generates an assoc array of course stats by using a defined list of
        // its attributes
        $courseStatus = DBJson::getObjectsByAttributes( 
                                                       $data,
                                                       CourseStatus::getDBPrimaryKey( ),
                                                       CourseStatus::getDBConvert( ),
                                                       $CourseStatusExtension
                                                       );

        // generates an assoc array of courses by using a defined list of
        // its attributes
        $courses = DBJson::getObjectsByAttributes( 
                                                  $data,
                                                  Course::getDBPrimaryKey( ),
                                                  Course::getDBConvert( ),
                                                  $CourseExtension
                                                  );

        // concatenates the course stats and the associated courses
        $res = DBJson::concatObjectListsSingleResult( 
                                                     $data,
                                                     $courseStatus,
                                                     CourseStatus::getDBPrimaryKey( ),
                                                     CourseStatus::getDBConvert( )['CS_course'],
                                                     $courses,
                                                     Course::getDBPrimaryKey( ),
                                                     $CourseExtension,
                                                     $CourseStatusExtension
                                                     );

        // concatenates the users and the associated course stats
        $res = DBJson::concatResultObjectLists( 
                                               $data,
                                               $users,
                                               User::getDBPrimaryKey( ),
                                               User::getDBConvert( )['U_courses'],
                                               $res,
                                               CourseStatus::getDBPrimaryKey( ),
                                               $CourseStatusExtension,
                                               $UserExtension
                                               );

        if ($isResult){ 
            // to reindex
            // $res = array_merge($res);
            if ( $singleResult ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }

    public static function ExtractCourseStatus( 
                                               $data,
                                               $singleResult = false,
                                               $UserExtension = '',
                                               $CourseStatusExtension = '',
                                               $CourseExtension = '',
                                               $isResult = true
                                               )
    {

        // generates an assoc array of a user by using a defined list of its
        // attributes
        $user = DBJson::getObjectsByAttributes( 
                                               $data,
                                               User::getDBPrimaryKey( ),
                                               User::getDBConvert( ),
                                               $UserExtension
                                               );

        // generates an assoc array of course stats by using a defined list of
        // its attributes
        $courseStatus = DBJson::getObjectsByAttributes( 
                                                       $data,
                                                       CourseStatus::getDBPrimaryKey( ),
                                                       CourseStatus::getDBConvert( ),
                                                       $CourseStatusExtension
                                                       );

        // generates an assoc array of courses by using a defined list of
        // its attributes
        $courses = DBJson::getObjectsByAttributes( 
                                                  $data,
                                                  Course::getDBPrimaryKey( ),
                                                  Course::getDBConvert( ),
                                                  $CourseExtension
                                                  );

        // concatenates the course stats and the associated courses
        $res = DBJson::concatObjectListsSingleResult( 
                                                     $data,
                                                     $courseStatus,
                                                     CourseStatus::getDBPrimaryKey( ),
                                                     CourseStatus::getDBConvert( )['CS_course'],
                                                     $courses,
                                                     Course::getDBPrimaryKey( ),
                                                     $CourseExtension,
                                                     $CourseStatusExtension
                                                     );

        // concatenates the users and the associated course stats
        $res = DBJson::concatResultObjectLists( 
                                               $data,
                                               $user,
                                               User::getDBPrimaryKey( ),
                                               User::getDBConvert( )['U_courses'],
                                               $res,
                                               CourseStatus::getDBPrimaryKey( ),
                                               $CourseStatusExtension,
                                               $UserExtension
                                               );

        if ($isResult){                    
            // to reindex
            // $res = array_merge($res);
            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 
?>

