<?php
/**
 * @file DBUser.php contains the DBUser class
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/Logger.php' );

\Slim\Slim::registerAutoloader();

Logger::Log("begin DBUser",LogLevel::DEBUG);

// runs the CConfig
$com = new CConfig(DBUser::getPrefix());

// runs the DBUser
if (!$com->used())
    new DBUser($com->loadConfig());
    
Logger::Log("end DBUser",LogLevel::DEBUG);
    
/**
 * A class, to abstract the "User" table from database
 *
 * @author Till Uhlig
 */
class DBUser
{
    /**
     * @var Slim $_app the slim object
     */ 
    private $_app=null;
    
    /**
     * @var Component $_conf the component data object
     */ 
    private $_conf=null;
    
    /**
     * @var Link[] $query a list of links to a query component
     */ 
    private $query=array();
    
    /**
     * @var string $_prefix the prefixes, the class works with (comma separated)
     */ 
    private static $_prefix = "user";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBUser::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBUser::$_prefix = $value;
    } 
    
    /**
     * the component constructor
     *
     * @param Component $conf component data
     */ 
    public function __construct($conf)
    {
        // initialize component
        $this->_conf = $conf;
        $this->query = array(CConfig::getLink($conf->getLinks(),"out"));
        
        // initialize slim
        $this->_app = new \Slim\Slim(array('debug' => false));
        $this->_app->response->headers->set('Content-Type', 'application/json');
                        
        // PUT EditUser
        $this->_app->put('/' . $this->getPrefix() . '/user/:userid',
                        array($this, 'editUser'));
                        
        // DELETE RemoveUser
        $this->_app->delete('/' . $this->getPrefix() . '/user/:userid',
                        array($this, 'removeUser'));
                        
        // POST AddUser
        $this->_app->post('/' . $this->getPrefix(),
                        array($this, 'addUser'));
                        
        // GET GetUsers
        $this->_app->get('/' . $this->getPrefix() . '/user',
                        array($this, 'getUsers'));
                        
        // GET GetUser
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid',
                        array($this, 'getUser'));
                        
        // GET GetCourseMember
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid',
                        array($this,'getCourseMember'));
                        
        // GET GetGroupMember
        $this->_app->get('/' . $this->getPrefix() . '/group/user/:userid/exercisesheet/:esid',
                        array($this,'getGroupMember'));  
                        
        // GET GetIncreaseUserFailedLogin
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid/IncFailedLogin',
                        array($this,'getIncreaseUserFailedLogin'));  
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . $this->getPrefix()) === 0){
            // run Slim
            $this->_app->run();
        }
    }
    

    
    /**
     * PUT EditUser
     *
     * @param string $userid a database user identifier
     */
    public function editUser($userid)
    {
        Logger::Log("starts PUT EditUser",LogLevel::DEBUG);
        
        $userid = DBJson::mysql_real_escape_string($userid);
        
        // decode the received user data, as an object
        $insert = User::decodeUser($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/EditUser.sql", 
                                            array("userid" => $userid, "values" => $data));                   
           
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                    
            } else{
                Logger::Log("PUT EditUser failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * DELETE RemoveUser
     *
     * @param string $userid a database user identifier
     */
    public function removeUser($userid)
    {
        Logger::Log("starts DELETE RemoveUser",LogLevel::DEBUG);
        
        $userid = DBJson::mysql_real_escape_string($userid);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteUser.sql", 
                                        array("userid" => $userid));    
                                        
        // checks the correctness of the query                          
        if ($result['status']>=200 && $result['status']<=299){
            $this->_app->response->setBody(User::encodeUser(new User()));
            
           // if (isset($result['headers']['Content-Type']))
               // $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
          //  $this->_app->response->headers->set('Content-Type', "application/json");
          
            $this->_app->response->headers->set("Connection", "Close");
            Logger::Log("DELETE RemoveUser ok",LogLevel::DEBUG);   
            $this->_app->response->setStatus(200);
            $this->_app->stop();
        } else{
            Logger::Log("DELETE RemoveUser failed",LogLevel::ERROR);
            $this->_app->response->headers->set("Connection", "Close");
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->response->setStatus(409);   
            $this->_app->stop();            
        }
    }
    
    /**
     * POST AddUser
     */
    public function addUser()
    {
        Logger::Log("starts POST AddUser",LogLevel::DEBUG);
        
        // decode the received user data, as an object
        $insert = User::decodeUser($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/SetUser.sql", 
                                            array("values" => $data));                   
           
            // checks the correctness of the query    
            if ($result['status']>=200 && $result['status']<=299){
                $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new User();
                $obj->setId($queryResult->getInsertId());
            
                $this->_app->response->setBody(User::encodeUser($obj)); 
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST AddUser failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * GET GetUsers
     */
    public function getUsers()
    {
        Logger::Log("starts GET GetUsers",LogLevel::DEBUG);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetUsers.sql", 
                                        array());
                  
        // checks the correctness of the query      
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            $data = $query->getResponse();
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $users = DBJson::getObjectsByAttributes($data, 
                                    User::getDBPrimaryKey(), 
                                    User::getDBConvert());
            
            // generates an assoc array of course stats by using a defined list of 
            // its attributes
            $courseStatus = DBJson::getObjectsByAttributes($data, 
                                'C_id', 
                                CourseStatus::getDBConvert());
            
            // generates an assoc array of courses by using a defined list of 
            // its attributes
            $courses = DBJson::getObjectsByAttributes($query->getResponse(), 
                                                    Course::getDBPrimaryKey(), 
                                                    Course::getDBConvert());
                                
            // concatenates the course stats and the associated courses
            $res = DBJson::concatObjectListsSingleResult($data, 
                                    $courseStatus,
                                    'C_id',
                                    CourseStatus::getDBConvert()['CS_course'], 
                                    $courses,Course::getDBPrimaryKey()); 
                                    
            // concatenates the users and the associated course stats
            $res = DBJson::concatResultObjectLists($data, 
                                $users,
                                User::getDBPrimaryKey(),
                                User::getDBConvert()['U_courses'],
                                $res,'C_id'); 
                                
            //  to reindex
            $res = array_merge($res);    
            
            $this->_app->response->setBody(User::encodeUser($res));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetUsers failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->stop();
        }
    }

    /**
     * GET GetUser
     *
     * @param string $userid a database user identifier
     */
    public function getUser($userid)
    {
        Logger::Log("starts GET GetUser",LogLevel::DEBUG);
        
        $userid = DBJson::mysql_real_escape_string($userid);

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetUser.sql", 
                                        array("userid" => $userid));
        
        // checks the correctness of the query                                 
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            $data = $query->getResponse();
            
            // generates an assoc array of a user by using a defined list of its 
            // attributes
            $user = DBJson::getObjectsByAttributes($data, 
                                    User::getDBPrimaryKey(), 
                                    User::getDBConvert());
            
            // generates an assoc array of course stats by using a defined list of 
            // its attributes
            $courseStatus = DBJson::getObjectsByAttributes($data, 
                                        'C_id', 
                                        CourseStatus::getDBConvert());
            
            // generates an assoc array of courses by using a defined list of 
            // its attributes
            $courses = DBJson::getObjectsByAttributes($query->getResponse(), 
                                                    Course::getDBPrimaryKey(), 
                                                    Course::getDBConvert());
         
            // concatenates the course stats and the associated courses
            $res = DBJson::concatObjectListsSingleResult($data, 
                                    $courseStatus,
                                    'C_id',
                                    CourseStatus::getDBConvert()['CS_course'], 
                                    $courses,Course::getDBPrimaryKey()); 

            // concatenates the users and the associated course stats
            $res = DBJson::concatResultObjectLists($data,
                                $user,
                                User::getDBPrimaryKey(),
                                User::getDBConvert()['U_courses'],
                                $res,'C_id');    
            //  to reindex
            $res = array_merge($res);
            
            // only one object as result
            if (count($res)>0)
                $res = $res[0];    
                
            $this->_app->response->setBody(User::encodeUser($res));

            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);              
        } else{
            Logger::Log("GET GetUser failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(User::encodeUser(new User()));
        }
    }
    
    /**
     * GET GetIncreaseUserFailedLogin
     *
     * @param string $userid a database user identifier
     */
    public function getIncreaseUserFailedLogin($userid)
    {
        Logger::Log("starts GET GetIncreaseUserFailedLogin",LogLevel::DEBUG);
        
        $userid = DBJson::mysql_real_escape_string($userid);

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetIncreaseUserFailedLogin.sql", 
                                        array("userid" => $userid));
        
        // checks the correctness of the query                                 
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            $data = $query->getResponse();
            
            // generates an assoc array of a user by using a defined list of its 
            // attributes
            $user = DBJson::getObjectsByAttributes($data, 
                                    User::getDBPrimaryKey(), 
                                    User::getDBConvert());
            
            // generates an assoc array of course stats by using a defined list of 
            // its attributes
            $courseStatus = DBJson::getObjectsByAttributes($data, 
                                        'C_id', 
                                        CourseStatus::getDBConvert());
            
            // generates an assoc array of courses by using a defined list of 
            // its attributes
            $courses = DBJson::getObjectsByAttributes($query->getResponse(), 
                                                    Course::getDBPrimaryKey(), 
                                                    Course::getDBConvert());
         
            // concatenates the course stats and the associated courses
            $res = DBJson::concatObjectListsSingleResult($data, 
                                    $courseStatus,
                                    'C_id',
                                    CourseStatus::getDBConvert()['CS_course'], 
                                    $courses,Course::getDBPrimaryKey()); 

            // concatenates the users and the associated course stats
            $res = DBJson::concatResultObjectLists($data,
                                $user,
                                User::getDBPrimaryKey(),
                                User::getDBConvert()['U_courses'],
                                $res,'C_id');    
            //  to reindex
            $res = array_merge($res);
            
            // only one object as result
            if (count($res)>0)
                $res = $res[0];    
                
            $this->_app->response->setBody(User::encodeUser($res));

            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);              
        } else{
            Logger::Log("GET GetIncreaseUserFailedLogin failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(User::encodeUser(new User()));
        }
    }
    
    /**
     * GET GetCourseMember
     *
     * @param int $courseid a database course identifier
     */
    public function getCourseMember($courseid)
    {     
        Logger::Log("starts GET GetCourseMember",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($courseid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetCourseMember.sql", 
                                        array("courseid" => $courseid));        
        
        // checks the correctness of the query 
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            $data = $query->getResponse();
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $users = DBJson::getObjectsByAttributes($data, 
                                    User::getDBPrimaryKey(), 
                                    User::getDBConvert());
            
            // generates an assoc array of course stats by using a defined list of 
            // its attributes
            $courseStatus = DBJson::getObjectsByAttributes($data, 
                                    'C_id', 
                                    CourseStatus::getDBConvert());
            
            // generates an assoc array of courses by using a defined list of 
            // its attributes
            $courses = DBJson::getObjectsByAttributes($query->getResponse(), 
                                                    Course::getDBPrimaryKey(), 
                                                    Course::getDBConvert());
                                
            // concatenates the course stats and the associated courses
            $res = DBJson::concatObjectListsSingleResult($data, 
                                    $courseStatus,
                                    'C_id',
                                    CourseStatus::getDBConvert()['CS_course'], 
                                    $courses,Course::getDBPrimaryKey()); 
                                    
            // concatenates the users and the associated course stats
            $res = DBJson::concatResultObjectLists($data, 
                                $users,
                                User::getDBPrimaryKey(),
                                User::getDBConvert()['U_courses'],
                                $res,'C_id'); 
                                
            //  to reindex
            $res = array_merge($res);   
            
            $this->_app->response->setBody(User::encodeUser($res));
            
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetCourseMember failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetGroupMember
     *
     * @param string $userid a database user identifier
     * @param int $esid a database exercise sheet identifier
     */
    public function getGroupMember($userid, $esid)
    {   
        Logger::Log("starts GET GetGroupMember",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid));
                            
        $userid = DBJson::mysql_real_escape_string($userid);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetGroupMember.sql", 
                                        array("userid" => $userid,"esid" => $esid));        
        
        // checks the correctness of the query 
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            $data = $query->getResponse();
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $users = DBJson::getObjectsByAttributes($data, 
                                    User::getDBPrimaryKey(), 
                                    User::getDBConvert());
            
            // generates an assoc array of course stats by using a defined list of 
            // its attributes
            $courseStatus = DBJson::getObjectsByAttributes($data, 
                                    'C_id', 
                                    CourseStatus::getDBConvert());
            
            // generates an assoc array of courses by using a defined list of 
            // its attributes
            $courses = DBJson::getObjectsByAttributes($query->getResponse(), 
                                                    Course::getDBPrimaryKey(), 
                                                    Course::getDBConvert());
                                
            // concatenates the course stats and the associated courses
            $res = DBJson::concatObjectListsSingleResult($data, 
                                    $courseStatus,
                                    'C_id',
                                    CourseStatus::getDBConvert()['CS_course'], 
                                    $courses,Course::getDBPrimaryKey()); 
                                    
            // concatenates the users and the associated course stats
            $res = DBJson::concatResultObjectLists($data, 
                                $users,
                                User::getDBPrimaryKey(),
                                User::getDBConvert()['U_courses'],
                                $res,'C_id'); 
                                
            //  to reindex
            $res = array_merge($res);   
            
            $this->_app->response->setBody(User::encodeUser($res));
            
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetGroupMember failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->stop();
        }
    }
}
?>