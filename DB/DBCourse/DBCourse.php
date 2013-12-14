<?php
/**
 * @file (filename)
 * (description)
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

$com = new CConfig(DBCourse::getPrefix());

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
        $this->query = array(CConfig::getLink($conf->getLinks(),"out"));
        
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');

        // PUT EditCourse
        $this->_app->put('/' . $this->getPrefix() . '/course/:courseid',
                        array($this,'editCourse'));
        
        // DELETE DeleteCourse
        $this->_app->delete('/' . $this->getPrefix() . '/course/:courseid',
                           array($this,'deleteCourse'));
        
        // POST SetCourse
        $this->_app->post('/' . $this->getPrefix(),
                         array($this,'setCourse'));
        
        // GET GetCourses
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid',
                        array($this,'getCourses'));
                        
        // GET GetCourseMember
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid/user',
                        array($this,'getCourseMember'));
        
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
       /* $obj = new Query();
        eval("\$sql = \"" . implode('\n',file("Sql/EditCourse.sql")) . "\";");
        $obj->setRequest($sql);
        
        $result = Request::routeRequest("GET",
                                      '/query',
                                      $this->_app->request->headers->all(),
                                      Query::encodeQuery($obj),
                                      $this->query,
                                      "query");
        
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                header($result['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }*/
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // DELETE DeleteCourse
    public function deleteCourse($courseid)
    {
        /*$obj = new Query();
        eval("\$sql = \"" . implode('\n',file("Sql/DeleteCourse.sql")) . "\";");
        $obj->setRequest($sql);
        
        $result = Request::routeRequest("GET",
                                      '/query',
                                      $this->_app->request->headers->all(),
                                      Query::encodeQuery($obj),
                                      $this->query,
                                      "query");
        
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                header($result['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }*/
    }
    
    /**
     * (description)
     *
     * @param $path (description)
     */
    // POST SetCourse
    public function setCourse()
    {
        /*$obj = new Query();
        eval("\$sql = \"" . implode('\n',file("Sql/SetCourse.sql")) . "\";");
        $obj->setRequest($sql);
        
        $result = Request::routeRequest("GET",
                                      '/query',
                                      $this->_app->request->headers->all(),
                                      Query::encodeQuery($obj),
                                      $this->query,
                                      "query");
        
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                header($result['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }*/
    }
        
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // GET GetCourses
    public function getCourses($userid)
    {    
        $obj = new Query();
        eval("\$sql = \"" . implode('\n',file("Sql/GetCourses.sql")) . "\";");
        $obj->setRequest($sql);
        
        $result = Request::routeRequest("GET",
                                      '/query',
                                      $this->_app->request->headers->all(),
                                      Query::encodeQuery($obj),
                                      $this->query,
                                      "query");

        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $courses = DBJson::getResultObjectsByAttributes($query->getResponse(), Course::getDBPrimaryKey(), Course::getDBConvert());
            $this->_app->response->setBody(Course::encodeCourse($courses));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                header($result['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Course::encodeCourse(new Course()));
            $this->_app->stop();
        }
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // GET GetCourseMember
    public function getCourseMember($courseid)
    {             
        $obj = new Query();
        eval("\$sql = \"" . implode('\n',file("Sql/GetCourseMember.sql")) . "\";");
        $obj->setRequest($sql);
        
        $result = Request::routeRequest("GET",
                                      '/query',
                                      $this->_app->request->headers->all(),
                                      Query::encodeQuery($obj),
                                      $this->query,
                                      "query");

        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $member = DBJson::getResultObjectsByAttributes($query->getResponse(), User::getDBPrimaryKey(), User::getDBConvert());
            $this->_app->response->setBody(User::encodeUser($member));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                header($result['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->stop();
        }

    }

}
?>