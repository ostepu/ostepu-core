<?php
/**
 * @file (filename)
 * (description)
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/DBRequest.php' );
include_once( 'Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

$com = new CConfig(DBCourse::getPrefix());

//course/course/2/user
if (!$com->used())
    new DBCourse($com->loadConfig());  
/**
 * (description)
 */
class DBCourse
{
    private $_app=null;
    private $_conf=null;
    
    private $query=array();
    
    private static $_prefix = "course";
    
    public static function getPrefix()
    {
        return DBCourse::$_prefix;
    }
    
    public static function setPrefix($value)
    {
        DBCourse::$_prefix = $value;
    }
    
    public function __construct($conf){
        $this->_conf = $conf;
        $this->query = array(CConfig::getLink($conf->getLinks(),"query"));
        
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');

        // PUT EditCourse
        $this->_app->put('/course/course/:courseid',
                        array($this,'editCourse'));
        
        // DELETE DeleteCourse
        $this->_app->delete('/course/course/:courseid',
                           array($this,'deleteCourse'));

        // DELETE RemoveCourseMember
        $this->_app->delete('/course/course/:courseid/user/:userid', 
                            array($this,'removeCourseMember'));
        
        // POST SetCourse
        $this->_app->post('/course',
                         array($this,'setCourse'));
        
        // POST AddCourseMember
        $this->_app->post('/course/course/:courseid/user/:userid',
                         array($this,'addCourseMember'));
        
        // GET GetCourseMember
        $this->_app->get('/course/course/:courseid/user',
                        array($this,'getCourseMember'));
        
        // GET GetCourses
        $this->_app->get('/course/user/:userid',
                        array($this,'getCourses'));
        
        if (strpos ($this->_app->request->getResourceUri(),'/' . $this->getPrefix()) === 0){
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // PUT EditCourse
    public function editCourse($courseid)
    {
        $this->_app->response->setStatus(200);
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // DELETE DeleteCourse
    public function deleteCourse($courseid)
    {
        $this->_app->response->setStatus(252);
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     * @param $userid (description)
     */
    // DELETE RemoveCourseMember
    public function removeCourseMember($courseid,$userid)
    {
        $this->_app->response->setStatus(252);
    }
    
    /**
     * (description)
     *
     * @param $path (description)
     */
    // POST SetCourse
    public function setCourse()
    {
        $this->_app->response->setStatus(201);
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     * @param $userid (description)
     */
    // POST AddCourseMember
    public function addCourseMember($courseid,$userid)
    {
        $this->_app->response->setStatus(201);
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // GET GetCourses
    public function getCourses($userid)
    {
        //eval("\$sql = \"".implode('\n',file("Sql/GetCourses.sql"))."\";");         
        $obj = new Query();
        $obj->setRequest(eval("\"" . implode('\n',file("Sql/GetCourses.sql")) . "\""));
        
        $result = Request::routeRequest("GET",
                                      '/'.$filePath,
                                      $this->_app->request->headers->all(),
                                      Query::encodeQuery($obj),
                                      $this->query,
                                      "query");
        
        if ($result['status']>=200 && $result['status']<=299){
            
           // $this->_app->response->setBody($ch['content']);

            $Courses = DBJson::getResultObjectsByAttributes($ch['content'], Course::getDbPrimaryKey(), Course::getDBConvert());
            $this->_app->response->setBody(Course::encodeCourse($Courses));
        
            $this->_app->response->setStatus($ch['status']);
            if (isset($ch['headers']['Content-Type']))
                header($ch['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Course::encodeCourse(new Course()));
            $this->_app->stop();
        }
        
         /* $query_result = DbRequest::request($sql);
        $this->_app->response->setStatus(200);
        $data = DBJson::getRows($query_result);
        $Courses = DBJson::getResultObjectsByAttributes($data, Course::getDbPrimaryKey(), Course::getDBConvert());
        $this->_app->response->setBody(Course::encodeCourse($Courses));*/
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // GET GetCourseMember
    public function getCourseMember($courseid)
    {      
        eval("\$sql = \"".implode('\n',file("Sql/GetCourseMember.sql"))."\";");
        $query_result = DbRequest::request($sql);
        $this->_app->response->setStatus(200);
        $data = DBJson::getRows($query_result);
        $Member = DBJson::getResultObjectsByAttributes($data, User::getDbPrimaryKey(), User::getDBConvert());
        $this->_app->response->setBody(User::encodeUser($Member));
        
        
        /*$result = Request::routeRequest("GET",
                                      '/'.$filePath,
                                      $this->_app->request->headers->all(),
                                      "???",
                                      $this->query,
                                      "query");
        
        if ($result['status']>=200 && $result['status']<=299){
            $this->_app->response->setStatus($ch['status']);
            $this->_app->response->setBody($ch['content']);

            if (isset($ch['headers']['Content-Type']))
                header($ch['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Course::encodeCourse(new Course()));
            $this->_app->stop();
        }*/
    }

}
?>