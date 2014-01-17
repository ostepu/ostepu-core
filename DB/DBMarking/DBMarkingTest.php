<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBMarkingTest extends PHPUnit_Framework_TestCase
{    

    public function testGetTutorExerciseMarkings()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercise/1/tutor/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetTutorExerciseMarkings call");
        $this->assertContains('{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercise/AAA/tutor/2',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetTutorExerciseMarkings call");
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercise/2/tutor/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetTutorExerciseMarkings call");
    }
    
    public function testGetTutorSheetMarkings()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercisesheet/1/tutor/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetTutorSheetMarkings call");
        $this->assertContains('{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercisesheet/AAA/tutor/2',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetTutorSheetMarkings call");
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercisesheet/2/tutor/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetTutorSheetMarkings call");
    }
    
    public function testGetUserGroupMarkings()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercisesheet/1/user/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUserGroupMarkings call");
        $this->assertContains('{"id":"3","tutorId":"1","tutorComment":"nichts","outstanding":"0","status":"0","points":"13","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercisesheet/AAA/user/2',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserGroupMarkings call");
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercisesheet/2/user/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserGroupMarkings call");
    }
    
    public function testGetSheetMarkings()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercisesheet/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetMarkings call");
        $this->assertContains('{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetMarkings call");
    }
    
    public function testGetExerciseMarkings()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercise/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExerciseMarkings call");
        $this->assertContains('{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/exercise/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExerciseMarkings call");
    }

    public function testGetAllMarkings()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllMarkings call");
        $this->assertContains('{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12","date":"1389643115","file":',$result['content']);    
    }
    
    public function testGetSubmissionMarking()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/submission/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSubmissionMarking call");
        $this->assertContains('{"id":"1","tutorId":"2","tutorComment":"nichts","outstanding":"0","status":"0","points":"10","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/submission/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSubmissionMarking call");
    }
    
    public function testGetMarking()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetMarking call");
        $this->assertContains('{"id":"1","tutorId":"2","tutorComment":"nichts","outstanding":"0","status":"0","points":"10","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBMarking/marking/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetMarking call");
    }
    
    public function testSetMarking()
    {

    }
    
    public function testDeleteMarking()
    {

    }
    
    public function testEditMarking()
    {

    }
}