<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBExternalIdTest extends PHPUnit_Framework_TestCase
{    
    public function testGetAllExternalIds()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExternalId/externalid',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllExternalIds call");
        $this->assertContains('"name":"Fachschaftsseminar fuer Mathematik"',$result['content']);
        

    }
    
    public function testGetCourseExternalIds()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExternalId/externalid/course/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseExternalIds call");
        $this->assertContains('"name":"Fachschaftsseminar fuer Mathematik"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExternalId/externalid/course/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseExternalIds call");
   }
    
    public function testGetExternalId()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExternalId/externalid/Ver2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExternalId call");
        $this->assertContains('"name":"Fachschaftsseminar fuer Mathematik"',$result['content']);
    }
    
    public function testSetExternalId()
    {

    }
    
    public function testDeleteExternalId()
    {

    }
    
    public function testEditExternalId()
    {

    }
}