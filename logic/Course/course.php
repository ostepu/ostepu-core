<?php

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';   
include_once( 'include/CConfig.php' ); 
    
\Slim\Slim::registerAutoloader();
/**
 * The Course class
 *
 * This class handles everything belongs to a Course
 */
class Course
{
    /**
     *Values needed for conversation with other components
     */
    private $_conf=null;
    
    private static $_prefix = "course";
    
    public static function getPrefix()
    {
        return Course::$_prefix;
    }
    public static function setPrefix($value)
    {
        Course::$_prefix = $value;
    }
    
    
    /**
     *Address of the Logic-Controller
     *dynamic set by CConf below
     */ 
    private $lURL = "";
        
    public function __construct($conf)
    {
        /**
         *Initialise the Sli-Framework
         */
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        /**
         *Set the Logiccontroller-URL
         */
        $this->_conf = $conf;
        $this->query = array();
        
        $this->query = array(CConfig::getLink($conf->getLinks(),"controller"));
        $this->lURL = $querry['address'];
        
        //POST SetCourse
        $this->app->post(':data+', array($this, 'setCourse')); //keine URL: ''?
        
        //PUT EditCourse
        $this->app->put('/course/:courseid', array($this, 'editCourse'));    
        
        //DELETE DeleteCourse
        $this->app->delete('/course/:courseid', array($this, 'deleteCourse'));   
        
        //POST AddCourseMember
        $this->app->post('/course/:courseid/user/:userid', array($this, 'addCourseMember'));   
        
        //GET GetCourseMember
        $this->app->get('/course/:courseid/user', array($this, 'getCourseMember'));    
        
        //GET GetCourses
        $this->app->get('/user/:userid', array($this, 'getCourses'));    
       
        //run Slim
        $this->app->run();
    }
    
    /**
     * Funktion to set a new course and save in DB 
     * takes one argument and retunrs a Status-Code
     * @param $data an string-array containing the URI-arguments (shout be empty)
     */
    public function setCourse($data){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course';
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);        
    }
    
    /**
     * Funktion to edit an existing course
     * takes one argument and retunrs a Status-Code
     * @param $courseid an integer identifies the Course
     */    
    public function editCourse($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/course/'.$courseid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);        
    }

    /**
     * Funktion to delete an existing course
     * takes one argument and retunrs a Status-Code
     * @param $courseid an integer identifies the Course
     */
    public function deleteCourse($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/course/'.$courseid;
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);        
    }        

    /**
     * Funktion to add a User to a course
     * takes two arguments and retunrs a Status-Code
     * @param $courseid an integer identifies the Course
     * @param $userid an integer identifies the user
     */
    public function addCourseMember($courseid, $userid){        
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/'.$courseid.'/course/'.$userid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);        
    }

    /**
     * Funktion to get the Members of a course
     * takes one argument and retunrs a Status-Code
     * @param $courseid an integer identifies the Course
     */
    public function getCourseMember($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/'.$courseid.'/user';
        $answer = Request::custom('GET', $URL, $header, $body);            
        $this->app->response->setStatus($answer['status']);                
        $this->app->response->setBody($answer['content']);
    }
    
    /**
     * Funktion to get all courses of a user
     * takes one argument and retunrs a Status-Code
     * @param $userid an integer identifies the user
     */
    public function getCourses($userid){

        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();        
        $URL = $this->lURL.'/DB/course/user/'.$userid;        
        $answer = Request::custom('GET', $URL, $header, $body);            
        $this->app->response->setStatus($answer['status']);                
        $this->app->response->setBody($answer['content']);
    }
}
/**
 * get new Config-Datas from DB 
 */
$com = new CConfig(Course::getPrefix());

/**
 * make a new instance of Course-Class with the Config-Datas
 */
if (!$com->used())
    new Course($com->loadConfig());
?>   