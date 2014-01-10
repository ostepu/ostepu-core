<?php
/**
 * @file DBCourseStatus.php contains the DBCourseStatus class
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/Logger.php' );

\Slim\Slim::registerAutoloader();

// runs the CConfig
$com = new CConfig(DBCourseStatus::getPrefix());

// runs the DBUser
if (!$com->used())
    new DBCourseStatus($com->loadConfig());  
    
/**
 * A class, to abstract the "CourseStatus" table from database
 *
 * @author Till Uhlig
 */
class DBCourseStatus
{
    /**
     * @var $_app the slim object
     */
    private $_app=null;
    
    /**
     * @var $_conf the component data object
     */ 
    private $_conf=null;
    
    /**
     * @var $query a list of links to a query component
     */ 
    private $query=array();
    
    /**
     * @var $_prefix the prefix, the class works with
     */ 
    private static $_prefix = "coursestatus";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBCourseStatus::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBCourseStatus::$_prefix = $value;
    }
    
    /**
     * the component constructor
     *
     * @param $conf component data
     */ 
    public function __construct($conf)
    {
        // initialize component
        $this->_conf = $conf;
        $this->query = array(CConfig::getLink($conf->getLinks(),"out"));
        
        // initialize slim
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');

        // PUT EditMemberRight
        $this->_app->put('/' . $this->getPrefix() . '/course/:courseid/user/:userid',
                        array($this, 'editMemberRight'));
                        
        // DELETE RemoveCourseMember
        $this->_app->delete('/' . $this->getPrefix() . '/course/:courseid/user/:userid', 
                            array($this,'removeCourseMember'));
                            
        // POST AddCourseMember
        $this->_app->post('/' . $this->getPrefix() . '/course/:courseid/user/:userid',
                         array($this,'addCourseMember'));
        
        // GET GetMemberRight
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid/user/:userid',
                        array($this,'getMemberRight'));
                        
        // GET GetMemberRights
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid',
                        array($this,'getMemberRights'));  
                        
        // GET GetCourseRights
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid',
                        array($this,'getCourseRights'));  
                        
        // starts slim only if the right prefix was received              
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
                    
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * PUT EditMemberRight
     *
     * @param $userid a database user identifier
     */
    public function editMemberRight($courseid,$userid)
    {
        Logger::Log("starts PUT EditMemberRight",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($courseid),
                            ctype_digit($userid));
                            
        // decode the received user data, as an object
        $insert = User::decodeUser($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getCourseStatusInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/EditMemberRight.sql", 
                                            array("courseid" => $courseid,"userid" => $userid, "values" => $data));                   
           
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditMemberRight failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * DELETE RemoveCourseMember
     *
     * @param $courseid a database course identifier
     * @param $userid a database user identifier
     */
    public function removeCourseMember($courseid,$userid)
    {
        Logger::Log("starts DELETE RemoveCourseMember",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($courseid),
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/RemoveCourseMember.sql", 
                                        array("courseid" => $courseid,"userid" => $userid));    
                                        
        // checks the correctness of the query                          
        if ($result['status']>=200 && $result['status']<=299){
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE RemoveCourseMember failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
    
    /**
     * POST AddCourseMember
     *
     * @param $courseid a database course identifier
     * @param $userid a database user identifier
     */
    public function addCourseMember()
    {
        Logger::Log("starts POST AddCourseMember",LogLevel::DEBUG);
        
        // decode the received user data, as an object
        $insert = User::decodeUser($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getCourseStatusInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/AddCourseMember.sql", 
                                            array("values" => $data));                   
           
            // checks the correctness of the query    
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST AddCourseMember failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * GET GetMemberRight
     *
     * @param $courseid a database course identifier
     * @param $userid a database user identifier
     */
    public function getMemberRight($courseid,$userid)
    {
        Logger::Log("starts GET GetMemberRight",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($courseid),
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
         $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetMemberRight.sql", 
                                        array("userid" => $userid,"courseid" => $courseid));
        
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
            Logger::Log("GET GetMemberRight failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetMemberRights
     *
     * @param $userid a database user identifier
     */
    public function getMemberRights($userid)
    {
        Logger::Log("starts GET GetMemberRights",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
         $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetMemberRights.sql", 
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
            Logger::Log("GET GetMemberRights failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetCourseRights
     *
     * @param $courseid a database course identifier
     */
    public function getCourseRights($courseid)
    {
        Logger::Log("starts GET GetCourseRights",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($courseid));
                            
        // starts a query, by using a given file
         $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetCourseRights.sql", 
                                        array("courseid" => $courseid));
        
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
                
            $this->_app->response->setBody(User::encodeUser($res));

            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetCourseRights failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->stop();
        }
    }
}
?>