<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBSelectedSubmissionTest extends PHPUnit_Framework_TestCase
{    
    
    public function testGetSheetSelected()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSelectedSubmission/selectedsubmission/exercisesheet/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetSelected call");
        $this->assertContains('{"leaderId":"1","submissionId":"2","exerciseId":"1"}',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSelectedSubmission/selectedsubmission/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetSelected call"); 
    }
    
    public function testGetExerciseSelected()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSelectedSubmission/selectedsubmission/exercise/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExerciseSelected call");
        $this->assertContains('{"leaderId":"1","submissionId":"2","exerciseId":"1"}',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSelectedSubmission/selectedsubmission/exercise/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExerciseSelected call"); 
    }
    
    public function testSetSelectedSubmission()
    {

    }
    
    public function testDeleteSelectedSubmission()
    {

    }
    
    public function testEditSelectedSubmission()
    {

    }
}