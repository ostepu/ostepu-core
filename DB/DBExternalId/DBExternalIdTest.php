<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBExternalIdTest extends PHPUnit_Framework_TestCase
{    
    private $url = "";
    
    public function testDBExternalId()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
            
        $this->SetExternalId();
        $this->EditExternalId();
        $this->DeleteExternalId();
        $this->GetExternalId();
        $this->GetCourseExternalIds();
        $this->GetAllExternalIds();
    }
    
    public function GetAllExternalIds()
    {
        $result = Request::get($this->url . 'DBExternalId/externalid',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllExternalIds call");
        $this->assertContains('"name":"Fachschaftsseminar fuer Mathematik"',$result['content']);
        

    }
    
    public function GetCourseExternalIds()
    {
        $result = Request::get($this->url . 'DBExternalId/externalid/course/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseExternalIds call");
        $this->assertContains('"name":"Fachschaftsseminar fuer Mathematik"',$result['content']);
        
        $result = Request::get($this->url . 'DBExternalId/externalid/course/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseExternalIds call");
   }
    
    public function GetExternalId()
    {
        $result = Request::get($this->url . 'DBExternalId/externalid/Ver2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExternalId call");
        $this->assertContains('"name":"Fachschaftsseminar fuer Mathematik"',$result['content']);
    }
    
    public function SetExternalId()
    {

    }
    
    public function DeleteExternalId()
    {

    }
    
    public function EditExternalId()
    {

    }
}