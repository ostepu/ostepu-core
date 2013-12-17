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
     * PUT EditCourse
     *
     * @param $courseid (description)
     */
    public function editCourse($courseid)
    {
        $values = DBJson::getUpdateDataFromInput($this->app->request->getBody(), Course::getDBConvert(), ',');
        
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/EditCourse.sql", 
                                        array("courseid" => $courseid,"value" => $values));   
                                        
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                header($result['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
    
    /**
     * DELETE DeleteCourse
     *
     * @param $courseid (description)
     */
    public function deleteCourse($courseid)
    {
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteCourse.sql", 
                                        array("courseid" => $courseid));    
                                        
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                header($result['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
    
    /**
     * POST SetCourse
     *
     * @param $path (description)
     */ 
    public function setCourse()
    {
        $insert = DBJson::getInsertDataFromInput($this->app->request->getBody(), Course::getDBConvert(), ',');
        foreach ($insert as $in){
            $columns = $in[0];
            $values = $in[1];

            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/SetCourse.sql", 
                                            array("columns" => $columns, "values" => $values));                   
        
            if ($result['status']>=200 && $result['status']<=299){
        
                $this->_app->response->setStatus($result['status']);
                if (isset($result['headers']['Content-Type']))
                    header($result['headers']['Content-Type']);
                
            } else{
                $this->_app->response->setStatus(409);
                $this->_app->stop();
            }
        }
    }
        
    /**
     * GET GetCourses
     *
     * @param $userid (description)
     */
    public function getCourses($userid)
    {    
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetCourses.sql", 
                                        array("userid" => $userid));
                                        
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
     * GET GetCourseMember
     *
     * @param $courseid (description)
     */
    public function getCourseMember($courseid)
    {             
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetCourseMember.sql", 
                                        array("courseid" => $courseid));        

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