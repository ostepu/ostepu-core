<?php
/**
 * @file (filename)
 * (description)
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/DbJson.php' );
include_once( 'Include/DbRequest.php' );
include_once( 'Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

$com = new CConfig("");

if (!$com->used())
    new DbCourse();
    
/**
 * (description)
 */
class DbCourse
{
    private static $_prefix = "course";
    public static function getPrefix(){
        return DbCourse::$_prefix;
    }
    public static function setPrefix($value){
        DbCourse::$_prefix = $value;
    }
    
    public function __construct(){
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // PUT EditCourse
        $this->app->put('/course/course/:courseid',
                        array($this,'editCourse'));
        
        // DELETE DeleteCourse
        $this->app->delete('/course/course/:courseid',
                           array($this,'deleteCourse'));

        // DELETE RemoveCourseMember
        $this->app->delete('/course/course/:courseid/user/:userid', 
                            array($this,'removeCourseMember'));
        
        // POST SetCourse
        $this->app->post('/course',
                         array($this,'setCourse'));
        
        // POST AddCourseMember
        $this->app->post('/course/course/:courseid/user/:userid',
                         array($this,'addCourseMember'));
        
        // GET GetCourseMember
        $this->app->get('/course/course/:courseid/user',
                        array($this,'getCourseMember'));
        
        // GET GetCourses
        $this->app->get('/course/user/:userid',
                        array($this,'getCourses'));
        
        if (strpos ($this->app->request->getResourceUri(),'/' . $this->_prefix) === 0){
            // run Slim
            $this->app->run();
        }
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // PUT EditCourse
    public function editCourse($courseid){
        $this->app->response->setStatus(200);
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // DELETE DeleteCourse
    public function deleteCourse($courseid){
        $this->app->response->setStatus(252);
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     * @param $userid (description)
     */
    // DELETE RemoveCourseMember
    public function removeCourseMember($courseid,$userid){
        $this->app->response->setStatus(252);
    }
    
    /**
     * (description)
     *
     * @param $path (description)
     */
    // POST SetCourse
    public function setCourse(){
        $this->app->response->setStatus(201);
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     * @param $userid (description)
     */
    // POST AddCourseMember
    public function addCourseMember($courseid,$userid){
        $this->app->response->setStatus(201);
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // GET GetCourses
    public function getCourses($userid){
        eval("\$sql = \"".implode('\n',file("include/Sql/GetCourses.sql"))."\";");
        $query_result = DbRequest::request($sql);
        $this->app->response->setStatus(200);
        $Courses = DbJson::getJson($query_result);
        $this->app->response->setBody($Courses);
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // GET GetCourseMember
    public function getCourseMember($courseid){
        eval("\$sql = \"".implode('\n',file("include/Sql/GetCourseMember.sql"))."\";");
        $query_result = DbRequest::request($sql);
        $this->app->response->setStatus(200);
        $Courses = DbJson::getJson($query_result);

        $this->app->response->setBody($Courses);
    }

}
?>