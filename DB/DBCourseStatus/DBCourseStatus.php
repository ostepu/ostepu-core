<?php
/**
 * @file DBCourseStatus.php contains the DBCourseStatus class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBCourseStatus/CourseStatusSample.json
 */ 

require_once( '../../Assistants/Slim/Slim.php' );
include_once( '../../Assistants/Structures.php' );
include_once( '../../Assistants/Request.php' );
include_once( '../../Assistants/DBJson.php' );
include_once( '../../Assistants/CConfig.php' );
include_once( '../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader();

// runs the CConfig
$com = new CConfig(DBCourseStatus::getPrefix());

// runs the DBUser
if (!$com->used())
    new DBCourseStatus($com->loadConfig());  


/**
 * A class, to abstract the "CourseStatus" table from database
 */
class DBCourseStatus
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
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBCourseStatus::$_prefix = $value;
    }


    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
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
        $this->_app->put('/' . $this->getPrefix() . '/course/:courseid/user/:userid(/)',
                        array($this, 'editMemberRight'));
                        
        // DELETE RemoveCourseMember
        $this->_app->delete('/' . $this->getPrefix() . '/course/:courseid/user/:userid(/)', 
                            array($this,'removeCourseMember'));
                            
        // POST AddCourseMember
        $this->_app->post('/' . $this->getPrefix() . '(/)', ///course/:courseid/user/:userid
                         array($this,'addCourseMember'));
        
        // GET GetMemberRight
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid/user/:userid(/)',
                        array($this,'getMemberRight'));
                        
        // GET GetMemberRights
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid(/)',
                        array($this,'getMemberRights'));  
                        
        // GET GetCourseRights
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid(/)',
                        array($this,'getCourseRights'));  
                        
        // starts slim only if the right prefix was received              
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
                    
            // run Slim
            $this->_app->run();
        }
    }


    /**
     * Edits the course status of a user in a specific course.
     *
     * Called when this component receives an HTTP PUT request to
     * /coursestatus/course/$courseid/user/$userid(/).
     * The request body should contain a JSON object representing the user's new
     * course status.
     *
     * @param int $courseid The id of the course.
     * @param int $userid The id of the user whose status is being updated.
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
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
                $this->_app->stop();
            }
        }
    }


    /**
     * Deletes the course status of a user in a specific course.
     *
     * Called when this component receives an HTTP DELETE request to
     * /coursestatus/course/$courseid/user/$userid(/).
     *
     * @param int $courseid The id of the course.
     * @param int $userid The id of the user whose status is being deleted.
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
            $this->_app->response->setStatus(201);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE RemoveCourseMember failed",LogLevel::ERROR);
            $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->stop();
        }
    }


    /**
     * Adds a course status to a user in a specific course.
     *
     * Called when this component receives an HTTP POST request to
     * /coursestatus(/).
     * The request body should contain a JSON object representing the user's 
     * course status.
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
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
                $this->_app->stop();
            }
        }
    }


    /**
     * Returns the course status of a user in a specific course.
     *
     * Called when this component receives an HTTP GET request to
     * /coursestatus/course/$courseid/user/$userid(/).
     *
     * @param int $courseid The id of the course.
     * @param int $userid The id of the user whose status is being returned.
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
                                CourseStatus::getDBPrimaryKey(), 
                                CourseStatus::getDBConvert());
            
            // generates an assoc array of courses by using a defined list of 
            // its attributes
            $courses = DBJson::getObjectsByAttributes($query->getResponse(), 
                                                    Course::getDBPrimaryKey(), 
                                                    Course::getDBConvert());
                                
            // concatenates the course stats and the associated courses
            $res = DBJson::concatObjectListsSingleResult($data, 
                                    $courseStatus,
                                    CourseStatus::getDBPrimaryKey(),
                                    CourseStatus::getDBConvert()['CS_course'], 
                                    $courses,Course::getDBPrimaryKey());              

            // concatenates the users and the associated course stats
            $res = DBJson::concatResultObjectLists($data, 
                                $user,
                                User::getDBPrimaryKey(),
                                User::getDBConvert()['U_courses'],
                                $res,CourseStatus::getDBPrimaryKey());     
            //  to reindex
            //$res = array_merge($res);
            
            // only one object as result
            if (count($res)>0)
                $res = $res[0];    
 
            $this->_app->response->setBody(User::encodeUser($res));

            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetMemberRight failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->stop();
        }
    }


    /**
     * Returns all course status objects of a user.
     *
     * Called when this component receives an HTTP GET request to
     * /coursestatus/user/$userid(/).
     *
     * @param int $userid The id of the user.
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
                                CourseStatus::getDBPrimaryKey(), 
                                CourseStatus::getDBConvert());
            
            // generates an assoc array of courses by using a defined list of 
            // its attributes
            $courses = DBJson::getObjectsByAttributes($query->getResponse(), 
                                                    Course::getDBPrimaryKey(), 
                                                    Course::getDBConvert());
                                
            // concatenates the course stats and the associated courses
            $res = DBJson::concatObjectListsSingleResult($data, 
                                    $courseStatus,
                                    CourseStatus::getDBPrimaryKey(),
                                    CourseStatus::getDBConvert()['CS_course'], 
                                    $courses,Course::getDBPrimaryKey());              

            // concatenates the users and the associated course stats
            $res = DBJson::concatResultObjectLists($data, 
                                $user,
                                User::getDBPrimaryKey(),
                                User::getDBConvert()['U_courses'],
                                $res,CourseStatus::getDBPrimaryKey()); 
                                //  to reindex
            //$res = array_merge($res);
            
            // only one object as result
            if (count($res)>0)
                $res = $res[0];    
                
            $this->_app->response->setBody(User::encodeUser($res));

            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetMemberRights failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->stop();
        }
    }


    /**
     * Returns all course status objects of a course.
     *
     * Called when this component receives an HTTP GET request to
     * /coursestatus/course/$courseid(/).
     *
     * @param int $courseid The id of the course.
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
                                CourseStatus::getDBPrimaryKey(), 
                                CourseStatus::getDBConvert());
            
            // generates an assoc array of courses by using a defined list of 
            // its attributes
            $courses = DBJson::getObjectsByAttributes($query->getResponse(), 
                                                    Course::getDBPrimaryKey(), 
                                                    Course::getDBConvert());
                                
            // concatenates the course stats and the associated courses
            $res = DBJson::concatObjectListsSingleResult($data, 
                                    $courseStatus,
                                    CourseStatus::getDBPrimaryKey(),
                                    CourseStatus::getDBConvert()['CS_course'], 
                                    $courses,Course::getDBPrimaryKey());              

            // concatenates the users and the associated course stats
            $res = DBJson::concatResultObjectLists($data, 
                                $user,
                                User::getDBPrimaryKey(),
                                User::getDBConvert()['U_courses'],
                                $res,CourseStatus::getDBPrimaryKey()); 
                                
            //  to reindex
            //$res = array_merge($res);
                
            $this->_app->response->setBody(User::encodeUser($res));

            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetCourseRights failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->stop();
        }
    }
}
?>