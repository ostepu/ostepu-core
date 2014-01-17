<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBAttachmentTest extends PHPUnit_Framework_TestCase
{    
    public function testGetSheetAttachments()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBAttachment/attachment/exercisesheet/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetAttachments call");
        $this->assertContains('{"id":"1","exerciseId":"1","file":{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}}',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBAttachment/attachment/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetAttachments call");
    }
    
    public function testGetExerciseAttachments()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBAttachment/attachment/exercise/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetExerciseAttachments call");
        $this->assertContains('{"id":"1","exerciseId":"1","file":{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}}',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBAttachment/attachment/exercise/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetExerciseAttachments call");
    }
    
    public function testGetAllAttachments()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBAttachment/attachment',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllAttachments call");
        $this->assertContains('{"id":"1","exerciseId":"1","file":{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}}',$result['content']);
    }
    
    public function testGetAttachment()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBAttachment/attachment/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAttachment call");
        $this->assertContains('{"id":"1","exerciseId":"1","file":{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}}',$result['content']);
   
        $result = Request::get('http://localhost/uebungsplattform/DB/DBAttachment/attachment/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetAttachment call");
   }
    
    public function testSetAttachment()
    {

    }
    
    public function testDeleteAttachment()
    {

    }
    
    public function testEditAttachment()
    {

    }
}