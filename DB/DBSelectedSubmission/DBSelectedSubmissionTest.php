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
            
        $this->SetSelectedSubmission();
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
    
    public function SetSelectedSubmission()
    {

    }
    
    public function DeleteSelectedSubmission()
    {

    }
    
    public function EditSelectedSubmission()
    {

    }
}