<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBExerciseSheetTest extends PHPUnit_Framework_TestCase
{

    public function testGetExerciseSheet()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExerciseSheet/exercisesheet/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExerciseSheet call");
        $this->assertContains('"id":"1","courseId":"1","endDate":"1389643115","startDate":"1394913515","groupSize":"1","sampleSolution"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExerciseSheet/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExerciseSheet call");
    }
    
    public function testGetCourseSheets()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExerciseSheet/exercisesheet/course/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseSheets call");
        $this->assertContains('"id":"1","courseId":"1","endDate":"1389643115","startDate":"1394913515","groupSize":"1","sheetName":"Serie 1","sampleSolution"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExerciseSheet/exercisesheet/course/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseSheets call");
    }  
    
    public function testGetCourseSheetURLs()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExerciseSheet/exercisesheet/course/1/url',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseSheetURLs call");
        $this->assertContains('"fileId":"8","displayName":"h.pdf","address":"file\/abcdefgh","timeStamp":"1389643115","fileSize":"800","hash":"abcdefgh"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExerciseSheet/exercisesheet/course/AAA/url',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseSheetURLs call");
    }
    
    public function testGetExerciseSheetURL()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExerciseSheet/exercisesheet/1/url',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExerciseSheetURL call");
        $this->assertContains('"fileId":"8","displayName":"h.pdf","address":"file\/abcdefgh","timeStamp":"1389643115","fileSize":"800","hash":"abcdefgh"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBExerciseSheet/exercisesheet/AAA/url',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExerciseSheetURL call");
    }
    
    public function testSetExerciseSheet()
    {

    }
    
    public function testDeleteExerciseSheet()
    {

    }
    
    public function testEditExerciseSheet()
    {

    }
}