<?php
/**
 * @file DBCourse.php contains the DBCourse class
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/DBRequest.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/Logger.php' );

\Slim\Slim::registerAutoloader();

// runs the CConfig
$com = new CConfig(DBCourse::getPrefix());

// runs the DBExerciseSheet
if (!$com->used())
    new DBCourse($com->loadConfig());  
    
/**
 * A class, to abstract the "DBCourse" table from database
 *
 * @author Till Uhlig
 */
class DBCourse
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
    private static $_prefix = "course";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBCourse::$_prefix;
    }
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBCourse::$_prefix = $value;
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
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');

        // PUT EditCourse
        $this->_app->put('/' . $this->getPrefix() . '(/course)/:courseid(/)',
                        array($this,'editCourse'));
        
        // DELETE DeleteCourse
        $this->_app->delete('/' . $this->getPrefix() . '(/course)/:courseid(/)',
                           array($this,'deleteCourse'));
        
        // POST SetCourse
        $this->_app->post('/' . $this->getPrefix(),
                         array($this,'setCourse'));
                         
        // GET GetCourse
        $this->_app->get('/' . $this->getPrefix() . '(/course)/:courseid(/)',
                        array($this,'getCourse'));
                        
        // GET GetAllCourses
        $this->_app->get('/' . $this->getPrefix() . '(/course)(/)',
                        array($this,'getAllCourses'));
                        
        // GET GetUserCourses
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid(/)',
                        array($this,'getUserCourses'));
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
                    
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * PUT EditCourse
     *
     * @param int $courseid a database course identifier
     */
    public function editCourse($courseid)
    {
        Logger::Log("starts PUT EditCourse",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($courseid));
                            
        // decode the received course data, as an object
        $insert = Course::decodeCourse($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/EditCourse.sql", 
                                            array("courseid" => $esid, 
                                            "values" => $data));                   

            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditCourse failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * DELETE DeleteCourse
     *
     * @param int $courseid a database course identifier
     */
    public function deleteCourse($courseid)
    {
        Logger::Log("starts DELETE DeleteCourse",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($courseid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteCourse.sql", 
                                        array("courseid" => $courseid));    
        
        // checks the correctness of the query                              
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteCourse failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
    
    /**
     * POST SetCourse
     */ 
    public function setCourse()
    {
        Logger::Log("starts POST SetCourse",LogLevel::DEBUG);
        
        // decode the received course data, as an object
        $insert = Course::decodeCourse($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/SetCourse.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new Course();
                $obj->setId($queryResult->getInsertId());
            
                $this->_app->response->setBody(Course::encodeCourse($obj)); 
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST SetCourse failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * GET GetCourse
     *
     * @param int $courseid a database course identifier
     */
    public function getCourse($courseid)
    {    
        Logger::Log("starts GET GetCourse",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($courseid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetCourse.sql", 
                                        array("courseid" => $courseid));
        
        // checks the correctness of the query                                   
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of an course by using a defined list of 
            // its attributes
            $course = DBJson::getObjectsByAttributes($data, 
                                    Course::getDBPrimaryKey(), 
                                    Course::getDBConvert());
            
            // generates an assoc array of exercise sheets by using a defined list of 
            // its attributes
            $exerciseSheets = DBJson::getObjectsByAttributes($data, 
                ExerciseSheet::getDBPrimaryKey(), 
                array(ExerciseSheet::getDBPrimaryKey() => ExerciseSheet::getDBConvert()[ExerciseSheet::getDBPrimaryKey()]));
            
            // concatenates the course and the associated exercise sheet IDs
            $res = DBJson::concatResultObjectListAsArray($data, 
                            $course,
                            Course::getDBPrimaryKey(),
                            Course::getDBConvert()['C_exerciseSheets'], 
                            $exerciseSheets,ExerciseSheet::getDBPrimaryKey());  
            
            $this->_app->response->setBody(Course::encodeCourse($res));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetCourse failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Course::encodeCourse(new Course()));
            $this->_app->stop();
        }
    }   
    
    /**
     * GET GetAllCourses
     */
    public function getAllCourses()
    {    
        Logger::Log("starts GET GetAllCourses",LogLevel::DEBUG);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAllCourses.sql", 
                                        array());
        
        // checks the correctness of the query                                       
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of courses by using a defined list of 
            // its attributes
            $courses = DBJson::getObjectsByAttributes($data, 
                                    Course::getDBPrimaryKey(), 
                                    Course::getDBConvert());
            
            // generates an assoc array of exercise sheets by using a defined list of 
            // its attributes
            $exerciseSheets = DBJson::getObjectsByAttributes($data, 
                ExerciseSheet::getDBPrimaryKey(), 
                array(ExerciseSheet::getDBPrimaryKey() => ExerciseSheet::getDBConvert()[ExerciseSheet::getDBPrimaryKey()]));
            
            // concatenates the courses and the associated exercise sheet IDs
            $res = DBJson::concatResultObjectListAsArray($data, 
                        $courses,
                        Course::getDBPrimaryKey(),
                        Course::getDBConvert()['C_exerciseSheets'],
                        $exerciseSheets,ExerciseSheet::getDBPrimaryKey());  
            
            $this->_app->response->setBody(Course::encodeCourse($res));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllCourses failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Course::encodeCourse(new Course()));
            $this->_app->stop();
        }
    }    
    
    /**
     * GET GetUserCourses
     *
     * @param int $userid a database user identifier
     */
    public function getUserCourses($userid)
    {    
        Logger::Log("starts GET GetUserCourses",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetUserCourses.sql", 
                                        array("userid" => $userid));
        
        // checks the correctness of the query                                       
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of courses by using a defined list of 
            // its attributes
            $courses = DBJson::getObjectsByAttributes($data, 
                                    Course::getDBPrimaryKey(), 
                                    Course::getDBConvert());
            
            // generates an assoc array of exercise sheets by using a defined list of 
            // its attributes
            $exerciseSheets = DBJson::getObjectsByAttributes($data, 
                ExerciseSheet::getDBPrimaryKey(), 
                array(ExerciseSheet::getDBPrimaryKey() => ExerciseSheet::getDBConvert()[ExerciseSheet::getDBPrimaryKey()]));
            
            // concatenates the courses and the associated exercise sheet IDs
            $res = DBJson::concatResultObjectListAsArray($data, 
                            $courses,Course::getDBPrimaryKey(),
                            Course::getDBConvert()['C_exerciseSheets'],
                            $exerciseSheets,ExerciseSheet::getDBPrimaryKey());  
            
            $this->_app->response->setBody(Course::encodeCourse($res));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetUserCourses failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Course::encodeCourse(new Course()));
            $this->_app->stop();
        }
    }
    
}
?>