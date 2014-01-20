<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBMarkingTest extends PHPUnit_Framework_TestCase
{    
    private $url = "";
    
    public function testDBMarking()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
            
        $this->AddMarking();
        $this->EditMarking();
        $this->DeleteMarking();
        $this->GetMarking();
        $this->GetSubmissionMarking();
        $this->GetAllMarkings();
        $this->GetExerciseMarkings();
        $this->GetSheetMarkings();
        $this->GetUserGroupMarkings();
        $this->GetTutorSheetMarkings();
        $this->GetTutorExerciseMarkings();
    }
    
    public function GetTutorExerciseMarkings()
    {
        $result = Request::get($this->url . 'DBMarking/marking/exercise/1/tutor/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetTutorExerciseMarkings call");
        $this->assertContains('{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get($this->url . 'DBMarking/marking/exercise/AAA/tutor/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetTutorExerciseMarkings call");
        
        $result = Request::get($this->url . 'DBMarking/marking/exercise/2/tutor/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetTutorExerciseMarkings call");
    }
    
    public function GetTutorSheetMarkings()
    {
        $result = Request::get($this->url . 'DBMarking/marking/exercisesheet/1/tutor/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetTutorSheetMarkings call");
        $this->assertContains('{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get($this->url . 'DBMarking/marking/exercisesheet/AAA/tutor/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetTutorSheetMarkings call");
        
        $result = Request::get($this->url . 'DBMarking/marking/exercisesheet/2/tutor/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetTutorSheetMarkings call");
    }
    
    public function GetUserGroupMarkings()
    {
        $result = Request::get($this->url . 'DBMarking/marking/exercisesheet/1/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUserGroupMarkings call");
        $this->assertContains('{"id":"3","tutorId":"1","tutorComment":"nichts","outstanding":"0","status":"0","points":"13","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get($this->url . 'DBMarking/marking/exercisesheet/AAA/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserGroupMarkings call");
        
        $result = Request::get($this->url . 'DBMarking/marking/exercisesheet/2/user/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserGroupMarkings call");
    }
    
    public function GetSheetMarkings()
    {
        $result = Request::get($this->url . 'DBMarking/marking/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetMarkings call");
        $this->assertContains('{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get($this->url . 'DBMarking/marking/exercisesheet/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetMarkings call");
    }
    
    public function GetExerciseMarkings()
    {
        $result = Request::get($this->url . 'DBMarking/marking/exercise/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExerciseMarkings call");
        $this->assertContains('{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get($this->url . 'DBMarking/marking/exercise/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExerciseMarkings call");
    }

    public function GetAllMarkings()
    {
        $result = Request::get($this->url . 'DBMarking/marking',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllMarkings call");
        $this->assertContains('{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12","date":"1389643115","file":',$result['content']);    
    }
    
    public function GetSubmissionMarking()
    {
        $result = Request::get($this->url . 'DBMarking/marking/submission/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSubmissionMarking call");
        $this->assertContains('{"id":"1","tutorId":"2","tutorComment":"nichts","outstanding":"0","status":"0","points":"10","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get($this->url . 'DBMarking/marking/submission/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSubmissionMarking call");
    }
    
    public function GetMarking()
    {
        $result = Request::get($this->url . 'DBMarking/marking/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetMarking call");
        $this->assertContains('{"id":"1","tutorId":"2","tutorComment":"nichts","outstanding":"0","status":"0","points":"10","date":"1389643115","file":',$result['content']);  
        
        $result = Request::get($this->url . 'DBMarking/marking/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetMarking call");
    }
    
    public function AddMarking()
    {

    }
    
    public function DeleteMarking()
    {

    }
    
    public function EditMarking()
    {

    }
}