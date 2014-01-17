<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBCourseStatusTest extends PHPUnit_Framework_TestCase
{    
    public function testGetCourseRights()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBCourseStatus/coursestatus/course/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseRights call");
        $this->assertContains('{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"2","courses":',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBCourseStatus/coursestatus/course/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseRights call");   
    }
    
    public function testGetMemberRights()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBCourseStatus/coursestatus/user/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetMemberRights call");
        $this->assertContains('{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"2","courses":',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBCourseStatus/coursestatus/user/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetMemberRights call");   
    }
    
    public function testGetMemberRight()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBCourseStatus/coursestatus/course/1/user/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetMemberRight call");
        $this->assertContains('{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"2","courses":',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBCourseStatus/coursestatus/course/AAA/user/2',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetMemberRight call");   
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBCourseStatus/coursestatus/course/1/user/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetMemberRight call");   
    }
    
    public function testAddCourseMember()
    {

    }
    
    public function testRemoveCourseMember()
    {

    }
    
    public function testEditMemberRight()
    {

    }
}