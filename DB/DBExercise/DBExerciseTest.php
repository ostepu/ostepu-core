<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBExerciseTest extends PHPUnit_Framework_TestCase
{

    public function testGetSheetExercises()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExercise/exercise/exercisesheet/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetExercises call");
        $this->assertContains('"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExercise/exercise/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetExercises call");
    }
    
    public function testGetCourseExercises()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExercise/exercise/course/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseExercises call");
        $this->assertContains('"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExercise/exercise/course/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseExercises call");
    }  
    
    public function testGetAllExercises()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExercise/exercise',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllExercises call");
        $this->assertContains('"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"',$result['content']);
        

    }
    
    public function testGetExercise()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExercise/exercise/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExercise call");
        $this->assertContains('"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExercise/exercise/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExercise call");
    }
    
    public function testSetExercise()
    {

    }
    
    public function testDeleteExercise()
    {

    }
    
    public function testEditExercise()
    {

    }
}