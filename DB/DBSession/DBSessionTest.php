<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBSessionTest extends PHPUnit_Framework_TestCase
{    
    public function testGetAllSessions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSession/session',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllSessions call");
        $this->assertContains('{"user":"1","session":"abcd"}',$result['content']);
    }
    
    public function testGetSessionUser()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSession/session/abcd',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSessionUser call");
        $this->assertContains('{"user":"1","session":"abcd"}',$result['content']);
    }
    
    public function testGetUserSession()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSession/session/user/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUserSession call");
        $this->assertContains('{"user":"1","session":"abcd"}',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSession/session/user/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserSession call");
   }
    
    public function testAddSession()
    {

    }
    
    public function testRemoveUserSession()
    {
    
    }
    
    public function testEditUserSession()
    {

    }
    
    public function testRemoveSession()
    {

    }
    
    public function testEditSession()
    {

    }
}