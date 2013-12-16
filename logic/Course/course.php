<?php

require 'Slim/Slim.php';
include 'include/Assistants/request/createRequest.php';    
    
\Slim\Slim::registerAutoloader();
	
class Course
{
    //URL of the logiccontroller    
    private $lURL = "";				//Einlesen aus config.ini

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
    
    
    
    /**
     * set a new course
     * 
     * @param (param)
     */
    private function setCourse($data){
        $body = $this->app->request->getBody();
        $header = $this->app->request->getHeader();
        $URL = $lURL.'/DB/course';
        $status = createPost($URL, $header, $body);
        $this->appapp->response->setStatus($status);        
    }
    
    /**
     * edit an existing course
     * 
     * @param (param)
     */    
    private function editCourse($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->getHeader();
        $URL = $lURL.'/DB/course/course/'.$courseid;
        $status = createPut($URL, $header, $body);
        $this->app->response->setStatus($status);        
    }

    /**
     * delete an existing course
     * 
     * @param (param)
     */
    private function deleteCourse($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->getHeader();
        $URL = $lURL.'/DB/course/course/'.$courseid;
        $status = createDelete($URL, $header, $body);
        $this->app->response->setStatus($status);        
    }        

    /**
     * add a user to a course
     * 
     * @param (param)
     */
    private function addCourseMember($courseid, $userid){        
        $body = $this->app->request->getBody();
        $header = $this->app->request->getHeader();
        $URL = $lURL.'/DB/course/'.$courseid.'/course/'.$userid;
        $status = createPut($URL, $header, $body);
        $this->app->response->setStatus($status);        
    }

    /**
     * returns a list of users who are added to the course
     * 
     * @param (param)
     */
    private function getCourseMember($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->getHeader();
        $URL = $lURL.'/DB/course/'.$courseid.'/user';
        $dbAnswer = createGet($URL, $header, $body);            //createGet(...).getBody?
        $this->app->response->setStatus(200);                   //status aus createGet auslesen!
        $this->app->response->setBody($dbAnswer);
    }
    
    /**
     * returns a list of all courses the user is added to.
     * 
     * @param (param)
     */
    private function getCourses($userid){

        $body = $this->app->request->getBody();
        $header = $this->app->request->getHeader();        
        $URL = $lURL.'/DB/course/user/'.$userid;        
        $dbAnswer = createGet($URL, $header, $body);            //createGet(...).getBody?
        $this->app->response->setStatus(200);                   //status aus createGet auslesen!
        $this->app->response->setBody($dbAnswer);
    }
}
?>   