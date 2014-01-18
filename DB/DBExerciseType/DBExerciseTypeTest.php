<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBExerciseTypeTest extends PHPUnit_Framework_TestCase
{    
    private $url = "";
    
    public function testDBExerciseType()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
             
        $this->SetPossibleType();
        $this->EditPossibleType();
        $this->DeletePossibleType();
        $this->GetPossibleType();
        $this->GetAllPossibleTypes();
    }
    
    public function GetAllPossibleTypes()
    {
        $result = Request::get($this->url . 'DBExerciseType/exercisetype',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllPossibleTypes call");
        $this->assertContains('{"id":"1","name":"Theorie"}',$result['content']);     
    }
    
    public function GetPossibleType()
    {
        $result = Request::get($this->url . 'DBExerciseType/exercisetype/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetPossibleType call");
        $this->assertContains('{"id":"1","name":"Theorie"}',$result['content']); 
        
        $result = Request::get($this->url . 'DBExerciseType/exercisetype/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetPossibleType call");
    }
    
    public function SetPossibleType()
    {

    }
    
    public function DeletePossibleType()
    {

    }
    
    public function EditPossibleType()
    {

    }
}