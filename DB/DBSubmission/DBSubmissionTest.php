<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBSubmissionTest extends PHPUnit_Framework_TestCase
{   

    public function testGetSelectedSheetSubmissions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/exercisesheet/1/selected',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSelectedSheetSubmissions call");
        $this->assertContains('{"id":"2","studentId":"1","exerciseId":"1","comment":"zwei","accepted":"1","date":"1389643115","selectedForGroup":"2","file":',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/exercisesheet/AAA/selected',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSelectedSheetSubmissions call");   
    }
    
    public function testGetSheetSubmissions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/exercisesheet/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetSubmissions call");
        $this->assertContains('{"id":"2","studentId":"1","exerciseId":"1","comment":"zwei","accepted":"1","date":"1389643115","selectedForGroup":"2","file":',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetSubmissions call");
    }
    
    public function testGetSelectedExerciseSubmissions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/exercise/1/selected',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSelectedExerciseSubmissions call");
        $this->assertContains('{"id":"2","studentId":"1","exerciseId":"1","comment":"zwei","accepted":"1","date":"1389643115","selectedForGroup":"2","file":',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/exercise/AAA/selected',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSelectedExerciseSubmissions call");
    }
    
    public function testGetAllSubmissions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllSubmissions call");
        $this->assertContains('{"id":"1","studentId":"1","exerciseId":"1","comment":"eins","accepted":"1","date":"1389643115","file":',$result['content']);   
    }
    
    public function testGetExerciseSubmissions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/exercise/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExerciseSubmissions call");
        $this->assertContains('{"id":"1","studentId":"1","exerciseId":"1","comment":"eins","accepted":"1","date":"1389643115","file":',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/exercise/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExerciseSubmissions call");
   }
    
    public function testGetSubmission()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSubmission call");
        $this->assertContains('{"id":"1","studentId":"1","exerciseId":"1","comment":"eins","accepted":"1","date":"1389643115","file":',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSubmission call");
    }
    
    public function testGetGroupSelectedExerciseSubmissions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/group/user/4/exercise/2/selected',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetGroupSelectedExerciseSubmissions call");
        $this->assertContains('{"id":"6","studentId":"4","exerciseId":"2","comment":"sechs","accepted":"1","date":"1389643115","selectedForGroup":"6","file":',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/group/user/4/exercise/AAA/selected',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetGroupSelectedExerciseSubmissions call");
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/group/user/AAA/exercise/2/selected',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetGroupSelectedExerciseSubmissions call");
   }
    
    public function testGetGroupExerciseSubmissions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/group/user/4/exercise/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetGroupExerciseSubmissions call");
        $this->assertContains('{"id":"6","studentId":"4","exerciseId":"2","comment":"sechs","accepted":"1","date":"1389643115","selectedForGroup":"6","file":',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/group/user/2/exercise/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetGroupExerciseSubmissions call");
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/group/user/AAA/exercise/1',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetGroupExerciseSubmissions call");
    }
    
    public function testGetGroupSelectedSubmissions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/group/user/4/exercisesheet/1/selected',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetGroupSelectedSubmissions call");
        $this->assertContains('{"id":"6","studentId":"4","exerciseId":"2","comment":"sechs","accepted":"1","date":"1389643115","selectedForGroup":"6","file":',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/group/user/4/exercisesheet/AAA/selected',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetGroupSelectedSubmissions call");
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/group/user/AAA/exercisesheet/1/selected',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetGroupSelectedSubmissions call");
    }
    
    public function testGetGroupSubmissions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/group/user/2/exercisesheet/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetGroupSubmissions call");
        $this->assertContains('{"id":"3","studentId":"2","exerciseId":"1","comment":"drei","accepted":"1","date":"1389643115","file":',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/group/user/2/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetGroupSubmissions call");
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/group/user/AAA/exercisesheet/1',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetGroupSubmissions call");
    }
    
    public function testGetUserExerciseSubmissions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/user/1/exercise/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUserExerciseSubmissions call");
        $this->assertContains('{"id":"1","studentId":"1","exerciseId":"1","comment":"eins","accepted":"1","date":"1389643115","file":',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/user/1/exercise/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserExerciseSubmissions call");
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBSubmission/submission/user/AAA/exercise/1',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserExerciseSubmissions call");
    }
    
    public function testSetSubmission()
    {

    }
    
    public function testDeleteSubmission()
    {

    }
    
    public function testEditSubmission()
    {

    }
}