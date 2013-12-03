<?php
/**
* @file (filename)
* %(description)
*/ 


/**
* (description)
*/
class DBCourse
{
    public function __construct(){
        $this->app = new \Slim\Slim();

        // PUT EditCourse
        $this->app->put('/course/course/:courseid',
                        array($this,'EditCourse'));
        
        // DELETE DeleteCourse
        $this->app->delete('/course/course/:courseid',
                           array($this,'DeleteCourse'));

        // DELETE RemoveCourseMember
        $this->app->delete('/course/course/:courseid/user/:userid', 
                            array($this,'RemoveCourseMember'));
        
        // POST SetCourse
        $this->app->post('/course',
                         array($this,'SetCourse'));
        
        // POST AddCourseMember
        $this->app->post('/course/course/:courseid/user/:userid',
                         array($this,'AddCourseMember'));
        
        // GET GetCourseMember
        $this->app->get('/course/course/:courseid/user',
                        array($this,'GetCourseMember'));
        
        // GET GetCourses
        $this->app->get('/course/course/user/:userid',
                        array($this,'GetCourses'));
        
        if (strpos ($this->app->request->getResourceUri(),"/course")==0){
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
    public function EditCourse($courseid){
    
    }
    
    /**
    * (description)
    *
    * @param $courseid (description)
    */
    // DELETE DeleteCourse
    public function DeleteCourse($courseid){
    
    }
    
    /**
    * (description)
    *
    * @param $courseid (description)
    * @param $userid (description)
    */
    // DELETE RemoveCourseMember
    public function RemoveCourseMember($courseid,$userid){
    
    }
    
    /**
    * (description)
    *
    * @param $path (description)
    */
    // POST SetCourse
    public function SetCourse(){
    
    }
    
    /**
    * (description)
    *
    * @param $courseid (description)
    * @param $userid (description)
    */
    // POST AddCourseMember
    public function AddCourseMember($courseid,$userid){
    
    }
    
    /**
    * (description)
    *
    * @param $userid (description)
    */
    // GET GetCourses
    public function GetCourses($userid){
        eval("\$sql = \"".implode('\n',file("include/sql/GetCourses.sql"))."\";");
        $query_result = DBRequest::request($sql);
        $this->app->response->setStatus(200);
        $Courses = Json::get_json($this->app,$query_result);
        
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->setBody($Courses);
    }
    
    /**
    * (description)
    *
    * @param $courseid (description)
    */
    // GET GetCourseMember
    public function GetCourseMember($courseid){
        eval("\$sql = \"".implode('\n',file("include/sql/GetCourseMember.sql"))."\";");
        $query_result = DBRequest::request($sql);
        $this->app->response->setStatus(200);
        $Courses = Json::get_json($this->app,$query_result);
        
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->setBody($Courses);
    }
    

}
?>