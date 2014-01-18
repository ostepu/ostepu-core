<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBFileTest extends PHPUnit_Framework_TestCase
{    
    private $url = "";
    
    public function testDBFile()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];

        $this->SetFile();
        $this->EditFile();
        $this->RemoveFile();
        $this->GetFile();
        $this->GetFileByHash();
        $this->GetAllFiles();
    }
    
    public function GetAllFiles()
    {
        $result = Request::get($this->url . 'DBFile/file',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllFiles call");
        $this->assertContains('{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}',$result['content']);   
    }
    
    public function GetFileByHash()
    {
        $result = Request::get($this->url . 'DBFile/file/hash/abcdef',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetFileByHash call");
        $this->assertContains('{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}',$result['content']);
    }
    
    public function GetFile()
    {
        $result = Request::get($this->url . 'DBFile/file/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetFile call");
        $this->assertContains('{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}',$result['content']);
        
        $result = Request::get($this->url . 'DBFile/file/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetFile call");
    }
    
    public function SetFile()
    {

    }
    
    public function RemoveFile()
    {

    }
    
    public function EditFile()
    {

    }
}