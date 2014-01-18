<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBCourseStatusTest extends PHPUnit_Framework_TestCase
{    
    private $url = "";

    public function testDBCourseStatus()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
            
        $this->AddCourseMember();
        $this->EditMemberRight();
        $this->RemoveCourseMember();
        $this->GetMemberRight();
        $this->GetMemberRights();
        $this->GetCourseRights();
    }
    
    public function GetCourseRights()
    {
        $result = Request::get($this->url . 'DBCourseStatus/coursestatus/course/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseRights call");
        $this->assertContains('{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1","courses":',$result['content']);
        
        $result = Request::get($this->url . 'DBCourseStatus/coursestatus/course/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseRights call");   
    }
    
    public function GetMemberRights()
    {
        $result = Request::get($this->url . 'DBCourseStatus/coursestatus/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetMemberRights call");
        $this->assertContains('{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1","courses":',$result['content']);
        
        $result = Request::get($this->url . 'DBCourseStatus/coursestatus/user/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetMemberRights call");   
    }
    
    public function GetMemberRight()
    {
        $result = Request::get($this->url . 'DBCourseStatus/coursestatus/course/1/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetMemberRight call");
        $this->assertContains('{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1","courses":',$result['content']);
        
        $result = Request::get($this->url . 'DBCourseStatus/coursestatus/course/AAA/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetMemberRight call");   
        
        $result = Request::get($this->url . 'DBCourseStatus/coursestatus/course/1/user/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetMemberRight call");   
    }
    
    public function AddCourseMember()
    {

    }
    
    public function RemoveCourseMember()
    {

    }
    
    public function EditMemberRight()
    {

    }
}