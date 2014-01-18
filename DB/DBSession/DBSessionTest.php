<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBSessionTest extends PHPUnit_Framework_TestCase
{    
    private $url = "";
    
    public function testDBSession()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
            
        $this->AddSession();
        $this->EditUserSession();
        $this->EditSession();
        $this->RemoveSession();
        $this->RemoveUserSession();
        $this->GetUserSession();
        $this->GetSessionUser();
        $this->GetAllSessions();
    }
    
    public function GetAllSessions()
    {
        $result = Request::get($this->url . 'DBSession/session',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllSessions call");
        $this->assertContains('{"user":"1","session":"abcd"}',$result['content']);
    }
    
    public function GetSessionUser()
    {
        $result = Request::get($this->url . 'DBSession/session/abcd',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSessionUser call");
        $this->assertContains('{"user":"1","session":"abcd"}',$result['content']);
    }
    
    public function GetUserSession()
    {
        $result = Request::get($this->url . 'DBSession/session/user/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUserSession call");
        $this->assertContains('{"user":"1","session":"abcd"}',$result['content']);
        
        $result = Request::get($this->url . 'DBSession/session/user/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserSession call");
   }
    
    public function AddSession()
    {

    }
    
    public function RemoveUserSession()
    {
    
    }
    
    public function EditUserSession()
    {

    }
    
    public function RemoveSession()
    {

    }
    
    public function EditSession()
    {

    }
}