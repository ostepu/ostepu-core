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
            
        $this->SetCourse();
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
    
    public function SetCourse()
    {

    }
    
    public function DeleteCourse()
    {

    }
    
    public function EditCourse()
    {

    }
}