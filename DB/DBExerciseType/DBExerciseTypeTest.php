<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBExerciseTypeTest extends PHPUnit_Framework_TestCase
{    
    public function testGetAllPossibleTypes()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExerciseType/exercisetype',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllPossibleTypes call");
        $this->assertContains('{"id":"1","name":"Theorie"}',$result['content']);     
    }
    
    public function testGetPossibleType()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExerciseType/exercisetype/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetPossibleType call");
        $this->assertContains('{"id":"1","name":"Theorie"}',$result['content']); 
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExerciseType/exercisetype/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetPossibleType call");
    }
    
    public function testSetPossibleType()
    {

    }
    
    public function testDeletePossibleType()
    {

    }
    
    public function testEditPossibleType()
    {

    }
}