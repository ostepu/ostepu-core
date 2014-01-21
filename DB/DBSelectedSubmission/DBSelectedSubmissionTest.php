<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBSelectedSubmissionTest extends PHPUnit_Framework_TestCase
{    
    private $url = "";
    
    public function testDBSelectedSubmission()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
            
        $this->AddSelectedSubmission();
        $this->EditSelectedSubmission();
        $this->DeleteSelectedSubmission();
        $this->GetExerciseSelected();
        $this->GetSheetSelected();
    }
    
    public function GetSheetSelected()
    {
        $result = Request::get($this->url . 'DBSelectedSubmission/selectedsubmission/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetSelected call");
        $this->assertContains('{"leaderId":"1","submissionId":"2","exerciseId":"1"}',$result['content']);
        
        $result = Request::get($this->url . 'DBSelectedSubmission/selectedsubmission/exercisesheet/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetSelected call"); 
    }
    
    public function GetExerciseSelected()
    {
        $result = Request::get($this->url . 'DBSelectedSubmission/selectedsubmission/exercise/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExerciseSelected call");
        $this->assertContains('{"leaderId":"1","submissionId":"2","exerciseId":"1"}',$result['content']);
        
        $result = Request::get($this->url . 'DBSelectedSubmission/selectedsubmission/exercise/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExerciseSelected call"); 
    }
    
    public function AddSelectedSubmission()
    {
        $result = Request::delete($this->url . 'DBSelectedSubmission/selectedsubmission/leader/4/exercise/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddSelectedSubmission call");

        //createSelectedSubmission($leaderId,$submissionId,$exerciseId)
        $obj = SelectedSubmission::createSelectedSubmission("4","1","1");

        $result = Request::post($this->url . 'DBSelectedSubmission/selectedsubmission',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),SelectedSubmission::encodeSelectedSubmission($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddSelectedSubmission call");      
   
        $result = Request::post($this->url . 'DBSelectedSubmission/selectedsubmission',array(),SelectedSubmission::encodeSelectedSubmission($obj));
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for AddSelectedSubmission call");     
    }
    
    public function DeleteSelectedSubmission()
    {
        $result = Request::delete($this->url . 'DBSelectedSubmission/selectedsubmission/leader/4/exercise/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),""); 
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for DeleteSelectedSubmission call");

        $result = Request::delete($this->url . 'DBSelectedSubmission/selectedsubmission/leader/AAA/exercise/1',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for DeleteSelectedSubmission call");
        
        $result = Request::delete($this->url . 'DBSelectedSubmission/selectedsubmission/leader/4/exercise/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for DeleteSelectedSubmission call");
        
        $result = Request::delete($this->url . 'DBSelectedSubmission/selectedsubmission/leader/4/exercise/1',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for DeleteSelectedSubmission call");
    }
    
    public function EditSelectedSubmission()
    {
        //createSelectedSubmission($leaderId,$submissionId,$exerciseId)
        $obj = SelectedSubmission::createSelectedSubmission("4","2","1");
        
        $result = Request::put($this->url . 'DBSelectedSubmission/selectedsubmission/leader/4/exercise/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),SelectedSubmission::encodeSelectedSubmission($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for EditSelectedSubmission call");      
        
        $result = Request::put($this->url . 'DBSelectedSubmission/selectedsubmission/leader/AAA/exercise/1',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for EditSelectedSubmission call"); 
        
        $result = Request::put($this->url . 'DBSelectedSubmission/selectedsubmission/leader/4/exercise/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for EditSelectedSubmission call");  
        
        $result = Request::put($this->url . 'DBSelectedSubmission/selectedsubmission/leader/4/exercise/1',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for EditSelectedSubmission call");
        
        $result = Request::get($this->url . 'DBSelectedSubmission/selectedsubmission/exercise/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for EditSelectedSubmission call");
        $this->assertContains('"submissionId":"2"',$result['content']);
    }
}