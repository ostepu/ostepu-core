<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBFileTest extends PHPUnit_Framework_TestCase
{    
    public function testGetAllFiles()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBFile/file',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllFiles call");
        $this->assertContains('{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}',$result['content']);   
    }
    
    public function testGetFileByHash()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBFile/file/hash/abcdef',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetFileByHash call");
        $this->assertContains('{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}',$result['content']);
    }
    
    public function testGetFile()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBFile/file/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetFile call");
        $this->assertContains('{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBFile/file/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetFile call");
    }
    
    public function testSetFile()
    {

    }
    
    public function testRemoveFile()
    {

    }
    
    public function testEditFile()
    {

    }
}