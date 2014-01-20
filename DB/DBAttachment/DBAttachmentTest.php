<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBAttachmentTest extends PHPUnit_Framework_TestCase
{    
    private $url = "";
    
    public function testDBAttachment()
    {
         // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
            
        $this->AddAttachment();
        $this->EditAttachment();
        $this->DeleteAttachment();
        $this->GetAttachment();
        $this->GetAllAttachments();
        $this->GetExerciseAttachments();
        $this->GetSheetAttachments();
    }
    
    public function GetSheetAttachments()
    {
        $result = Request::get($this->url . 'DBAttachment/attachment/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetAttachments call");
        $this->assertContains('{"id":"1","exerciseId":"1","file":{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}}',$result['content']);
   
        $result = Request::get($this->url . 'DBAttachment/attachment/exercisesheet/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetAttachments call");
    }
    
    public function GetExerciseAttachments()
    {
        $result = Request::get($this->url . 'DBAttachment/attachment/exercise/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExerciseAttachments call");
        $this->assertContains('{"id":"1","exerciseId":"1","file":{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}}',$result['content']);
   
        $result = Request::get($this->url . 'DBAttachment/attachment/exercise/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExerciseAttachments call");
    }
    
    public function GetAllAttachments()
    {
        $result = Request::get($this->url . 'DBAttachment/attachment',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllAttachments call");
        $this->assertContains('{"id":"1","exerciseId":"1","file":{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}}',$result['content']);
    }
    
    public function GetAttachment()
    {
        $result = Request::get($this->url . 'DBAttachment/attachment/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAttachment call");
        $this->assertContains('{"id":"1","exerciseId":"1","file":{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}}',$result['content']);
   
        $result = Request::get($this->url . 'DBAttachment/attachment/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetAttachment call");
   }
    
    public function AddAttachment()
    {
        $result = Request::delete($this->url . 'DBAttachment/attachment/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddAttachment call");
        
        //createAttachment($attachmentId,$exerciseId,$fileId)
        $obj = Attachment::createAttachment("100","1","1");

        $result = Request::post($this->url . 'DBAttachment/attachment',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),Attachment::encodeAttachment($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddAttachment call");      
        $this->assertContains('{"id":100}',$result['content']);
    }
    
    public function DeleteAttachment()
    {
        $result = Request::delete($this->url . 'DBAttachment/attachment/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),""); 
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for DeleteAttachment call");

        $result = Request::delete($this->url . 'DBAttachment/attachment/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for DeleteAttachment call");
        
        $result = Request::delete($this->url . 'DBAttachment/attachment/100',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for DeleteAttachment call");
    }
    
    public function EditAttachment()
    {
        //createAttachment($attachmentId,$exerciseId,$fileId)
        $obj = Attachment::createAttachment("100","1","2");
        
        $result = Request::put($this->url . 'DBAttachment/attachment/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),Attachment::encodeAttachment($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for EditAttachment call");      
        
        $result = Request::put($this->url . 'DBAttachment/attachment/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for EditAttachment call");  

        $result = Request::put($this->url . 'DBAttachment/attachment/100',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for EditAttachment call");
        
        $result = Request::get($this->url . 'DBAttachment/attachment/100',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for EditAttachment call");
        $this->assertContains('"fileId":"2"',$result['content']);
    }
}