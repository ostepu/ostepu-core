<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBCourseTest extends PHPUnit_Framework_TestCase
{
    private $url = "";
    
    public function testDBCourse()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
            
        $this->AddCourse();
        $this->EditCourse();
        $this->DeleteCourse();
        $this->GetUserCourses();
        $this->GetAllCourses();
        $this->GetCourse();
    }
    
    public function GetCourse()
    {
        $result = Request::get($this->url . 'DBCourse/course/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourse call");
        $this->assertContains('"name":"Fachschaftsseminar fuer Mathematik',$result['content']);
        
        $result = Request::get($this->url . 'DBCourse/course/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourse call");  
    }
    
    public function GetAllCourses()
    {
        $result = Request::get($this->url . 'DBCourse/course',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for Courses call");
        $this->assertContains('"name":"Fachschaftsseminar fuer Mathematik',$result['content']);  
    }
    
    public function GetUserCourses()
    {
        $result = Request::get($this->url . 'DBCourse/course/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for UserCourses call");
        $this->assertContains('"name":"Fachschaftsseminar fuer Mathematik',$result['content']);
        
        $result = Request::get($this->url . 'DBCourse/course/user/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for UserCourses call");  
    }
    
    public function AddCourse()
    {
        $result = Request::delete($this->url . 'DBCourse/course/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddCourse call");
        
        //createCourse($courseId,$name,$semester,$defaultGroupSize)
        $obj = Course::createCourse("100","NeueVeranstaltung","1/2","2");
        
        $result = Request::post($this->url . 'DBCourse/course',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),Course::encodeCourse($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddCourse call");      
        $this->assertContains('{"id":100}',$result['content']);
    }
    
    public function DeleteCourse()
    {
        $result = Request::delete($this->url . 'DBCourse/course/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for DeleteCourse call"); 
        
        $result = Request::delete($this->url . 'DBCourse/course/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for DeleteCourse call");
        
        $result = Request::delete($this->url . 'DBCourse/course/100',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for DeleteCourse call");
    }
    
    public function EditCourse()
    {
        //createCourse($courseId,$name,$semester,$defaultGroupSize)
        $obj = Course::createCourse(null,"NeuNeueVeranstaltung","1/2","2");
        
        $result = Request::put($this->url . 'DBCourse/course/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),Course::encodeCourse($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for EditCourse call");  
        
        $result = Request::put($this->url . 'DBCourse/course/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for EditCourse call");
        
        $result = Request::put($this->url . 'DBCourse/course/100',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for EditCourse call");
        
        $result = Request::get($this->url . 'DBCourse/course/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for EditCourse call");
        $this->assertContains('"name":"NeuNeueVeranstaltung"',$result['content']);
    }
}