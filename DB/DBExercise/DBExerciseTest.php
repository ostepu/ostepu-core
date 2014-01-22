<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBExerciseTest extends PHPUnit_Framework_TestCase
{
    private $url = "";
    
    public function testDBExercise()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
            
       /* $this->AddExercise();
        $this->EditExercise();
        $this->DeleteExercise();*/
        $this->GetSheetExercises();
        $this->GetCourseExercises();
        $this->GetAllExercises();
        $this->GetExercise();
    }
    
    public function GetSheetExercises()
    {
        $result = Request::get($this->url . 'DBExercise/exercise/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetExercises call");
        $this->assertContains('"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"',$result['content']);
        
        $result = Request::get($this->url . 'DBExercise/exercise/exercisesheet/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetExercises call");
    }
    
    public function GetCourseExercises()
    {
        $result = Request::get($this->url . 'DBExercise/exercise/course/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseExercises call");
        $this->assertContains('"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"',$result['content']);
        
        $result = Request::get($this->url . 'DBExercise/exercise/course/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseExercises call");
    }  
    
    public function GetAllExercises()
    {
        $result = Request::get($this->url . 'DBExercise/exercise',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllExercises call");
        $this->assertContains('"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"',$result['content']);
        

    }
    
    public function GetExercise()
    {
        $result = Request::get($this->url . 'DBExercise/exercise/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExercise call");
        $this->assertContains('"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"',$result['content']);
        
        $result = Request::get($this->url . 'DBExercise/exercise/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExercise call");
    }
    
    public function AddExercise()
    {
        $result = Request::delete($this->url . 'DBExercise/exercise/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddExercise call");
        
        //createExercise($exerciseId,$courseId,$sheetId,$maxPoints,$type,$link,$bonus)
        $obj = Exercise::createExercise("100",null,"1","10","1","100","0");

        $result = Request::post($this->url . 'DBExercise/exercise',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),Exercise::encodeExercise($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddExercise call");   
        
        $result = Request::post($this->url . 'DBExercise/exercise',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for AddExercise call"); 
    }
    
    public function DeleteExercise()
    {
        $result = Request::delete($this->url . 'DBExercise/exercise/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for DeleteExercise call");
        
        $result = Request::delete($this->url . 'DBExercise/exercise/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for DeleteExercise call");
        
        $result = Request::delete($this->url . 'DBExercise/exercise/100',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for DeleteExercise call");
    }
    
    public function EditExercise()
    {
        //createExercise($exerciseId,$courseId,$sheetId,$maxPoints,$type,$link,$bonus)
        $obj = Exercise::createExercise("100",null,"1","10","1","100","1");

        $result = Request::put($this->url . 'DBExercise/exercise/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),Exercise::encodeExercise($obj));
       // $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for EditExercise call"); 
        
        $result = Request::put($this->url . 'DBExercise/exercise/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for EditExercise call");  
        
        $result = Request::put($this->url . 'DBExercise/exercise/100',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for EditExercise call"); 
        
        $result = Request::get($this->url . 'DBExercise/exercise/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExercise call");
        $this->assertContains('"bonus":"1"',$result['content']);
    }
}