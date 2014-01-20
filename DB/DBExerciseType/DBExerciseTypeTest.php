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
             
        $this->AddExerciseType();
        $this->EditExerciseType();
        $this->DeleteExerciseType();
        $this->GetExerciseType();
        $this->GetAllExerciseTypes();
    }
    
    public function GetAllExerciseTypes()
    {
        $result = Request::get($this->url . 'DBExerciseType/exercisetype',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllExerciseTypes call");
        $this->assertContains('{"id":"1","name":"Theorie"}',$result['content']);     
    }
    
    public function GetExerciseType()
    {
        $result = Request::get($this->url . 'DBExerciseType/exercisetype/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExerciseType call");
        $this->assertContains('{"id":"1","name":"Theorie"}',$result['content']); 
        
        $result = Request::get($this->url . 'DBExerciseType/exercisetype/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExerciseType call");
    }
    
    public function AddExerciseType()
    {
        $result = Request::delete($this->url . 'DBExerciseType/exercisetype/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        
        //createExerciseType($typeid,$name)
        $obj = ExerciseType::createExerciseType("100","Sonderpunkte");
        
        $result = Request::post($this->url . 'DBExerciseType/exercisetype',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),ExerciseType::encodeExerciseType($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddExerciseType call");      
        $this->assertContains('{"id":100}',$result['content']);
    }
    
    public function DeleteExerciseType()
    {
        $result = Request::delete($this->url . 'DBExerciseType/exercisetype/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),""); 
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for DeleteExerciseType call");

        $result = Request::delete($this->url . 'DBExerciseType/exercisetype/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for DeleteExerciseType call");
        
        $result = Request::delete($this->url . 'DBExerciseType/exercisetype/100',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for DeleteExerciseType call");
    }
    
    public function EditExerciseType()
    {
        //createExerciseType($typeid,$name)
        $obj = ExerciseType::createExerciseType("100","NeuSonderpunkte");
        
        $result = Request::put($this->url . 'DBExerciseType/exercisetype/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),ExerciseType::encodeExerciseType($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for EditExerciseType call");      
        
        $result = Request::put($this->url . 'DBExerciseType/exercisetype/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for EditExerciseType call");  

        $result = Request::put($this->url . 'DBExerciseType/exercisetype/100',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for EditExerciseType call");
        
        $result = Request::get($this->url . 'DBExerciseType/exercisetype/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for EditExerciseType call");
        $this->assertContains('"name":"NeuSonderpunkte"',$result['content']);
    }
}