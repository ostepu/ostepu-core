<?php

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';    
    
\Slim\Slim::registerAutoloader();
    
class Course
{
    //URL of the logiccontroller    
    private $lURL = "";                //Einlesen aus config.ini
        
    public function __construct()
    {
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        
        //SetCourse
        $this->app->post(':data+', array($this, 'setCourse')); //keine URL: ''?
        
        //EditCourse
        $this->app->put('/course/:courseid', array($this, 'editCourse'));    
        
        //DeleteCourse
        $this->app->delete('/course/:courseid', array($this, 'deleteCourse'));   
        
        //AddCourseMember
        $this->app->post('/course/:courseid/user/:userid', array($this, 'addCourseMember'));   
        
        //GetCourseMember
        $this->app->get('/course/:courseid/user', array($this, 'getCourseMember'));    
        
        //GetCourses
        $this->app->get('/user/:userid', array($this, 'getCourses'));    
       
        $this->app->run();
    }
    
    /**
     * set a new course
     * 
     * @param (param)
     */
    public function setCourse($data){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course';
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);        
    }
    
    /**
     * edit an existing course
     * 
     * @param (param)
     */    
    public function editCourse($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/course/'.$courseid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);        
    }

    /**
     * delete an existing course
     * 
     * @param (param)
     */
    public function deleteCourse($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/course/'.$courseid;
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);        
    }        

    /**
     * add a user to a course
     * 
     * @param (param)
     */
    public function addCourseMember($courseid, $userid){        
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/'.$courseid.'/course/'.$userid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);        
    }

    /**
     * returns a list of users who are added to the course
     * 
     * @param (param)
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
     * returns a list of all courses the user is added to.
     * 
     * @param (param)
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

new course();
?>   