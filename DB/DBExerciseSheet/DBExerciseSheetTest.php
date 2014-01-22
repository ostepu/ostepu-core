<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBExerciseSheetTest extends PHPUnit_Framework_TestCase
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
            
            
        $this->AddExerciseSheet();
        $this->EditExerciseSheet();
        $this->DeleteExerciseSheet();
        $this->GetExerciseSheet();
        $this->GetCourseSheets();
        $this->GetCourseSheetURLs();
        $this->GetExerciseSheetURL();
    }
    
    public function GetExerciseSheet()
    {
        $result = Request::get($this->url . 'DBExerciseSheet/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExerciseSheet call");
        $this->assertContains('"id":"1","courseId":"1","endDate":"1389643115","startDate":"1394913515","groupSize":"3","sampleSolution"',$result['content']);
        
        $result = Request::get($this->url . 'DBExerciseSheet/exercisesheet/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExerciseSheet call");
    }
    
    public function GetCourseSheets()
    {
        $result = Request::get($this->url . 'DBExerciseSheet/exercisesheet/course/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseSheets call");
        $this->assertContains('"id":"1","courseId":"1","endDate":"1389643115","startDate":"1394913515","groupSize":"3","sheetName":"Serie 1","sampleSolution"',$result['content']);
        
        $result = Request::get($this->url . 'DBExerciseSheet/exercisesheet/course/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseSheets call");
    }  
    
    public function GetCourseSheetURLs()
    {
        $result = Request::get($this->url . 'DBExerciseSheet/exercisesheet/course/1/url',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseSheetURLs call");
        $this->assertContains('"fileId":"8","displayName":"h.pdf","address":"file\/abcdefgh","timeStamp":"1389643115","fileSize":"800","hash":"abcdefgh"',$result['content']);
        
        $result = Request::get($this->url . 'DBExerciseSheet/exercisesheet/course/AAA/url',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseSheetURLs call");
    }
    
    public function GetExerciseSheetURL()
    {
        $result = Request::get($this->url . 'DBExerciseSheet/exercisesheet/1/url',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExerciseSheetURL call");
        $this->assertContains('"fileId":"8","displayName":"h.pdf","address":"file\/abcdefgh","timeStamp":"1389643115","fileSize":"800","hash":"abcdefgh"',$result['content']);
        
        $result = Request::get($this->url . 'DBExerciseSheet/exercisesheet/AAA/url',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExerciseSheetURL call");
    }
    
    public function AddExerciseSheet()
    {
        $result = Request::delete($this->url . 'DBExerciseSheet/exercisesheet/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        //$this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddExerciseSheet call");
        
        //createExerciseSheet($sheetId,$courseId,$endDate,$startDate,$groupSize,$sampleSolutionId,$sheetFileId,$sheetName)
        $obj = ExerciseSheet::createExerciseSheet("100","1",null,null,null,null,null,null);

        $result = Request::post($this->url . 'DBExerciseSheet/exercisesheet',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),ExerciseSheet::encodeExerciseSheet($obj));
       // $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddExerciseSheet call");   
        
        $result = Request::post($this->url . 'DBExerciseSheet/exercisesheet',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for AddExerciseSheet call");  
    }
    
    public function DeleteExerciseSheet()
    {
        $result = Request::delete($this->url . 'DBExerciseSheet/exercisesheet/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
       // $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for DeleteExerciseSheet call");
       
        $result = Request::delete($this->url . 'DBExerciseSheet/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for DeleteExerciseSheet call");
       
        $result = Request::delete($this->url . 'DBExerciseSheet/exercisesheet/100',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for DeleteExerciseSheet call");
    }
    
    public function EditExerciseSheet()
    {
        //createExerciseSheet($sheetId,$courseId,$endDate,$startDate,$groupSize,$sampleSolutionId,$sheetFileId,$sheetName)
        $obj = ExerciseSheet::createExerciseSheet("100","1",null,null,null,null,null,"Neu");

        $result = Request::put($this->url . 'DBExerciseSheet/exercisesheet/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),ExerciseSheet::encodeExerciseSheet($obj));
        //$this->assertEquals(201, $result['status'], "Unexpected HTTP status code for EditExerciseSheet call"); 
        
        $result = Request::put($this->url . 'DBExerciseSheet/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for EditExerciseSheet call"); 
        
        $result = Request::put($this->url . 'DBExerciseSheet/exercisesheet/100',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for EditExerciseSheet call"); 
        
        $result = Request::get($this->url . 'DBExerciseSheet/exercisesheet/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for EditExerciseSheet call");
        $this->assertContains('"sheetName":"Neu"',$result['content']);
    }
}