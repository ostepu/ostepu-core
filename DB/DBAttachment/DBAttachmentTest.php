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
            
        $this->SetAttachment();
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
    
    public function SetAttachment()
    {

    }
    
    public function DeleteAttachment()
    {

    }
    
    public function EditAttachment()
    {

    }
}