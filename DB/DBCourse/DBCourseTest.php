<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBCourseTest extends PHPUnit_Framework_TestCase
{

    public function testGetCourse()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBCourse/course/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourse call");
        $this->assertContains('"name":"Fachschaftsseminar fuer Mathematik',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBCourse/course/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourse call");  
    }
    
    public function testGetAllCourses()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBCourse/course',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for Courses call");
        $this->assertContains('"name":"Fachschaftsseminar fuer Mathematik',$result['content']);  
    }
    
    public function testGetUserCourses()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBCourse/course/user/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for UserCourses call");
        $this->assertContains('"name":"Fachschaftsseminar fuer Mathematik',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBCourse/course/user/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for UserCourses call");  
    }
    
    public function testSetCourse()
    {

    }
    
    public function testDeleteCourse()
    {

    }
    
    public function testEditCourse()
    {

    }
}