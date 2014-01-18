<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBGroupTest extends PHPUnit_Framework_TestCase
{    
    private $url = "";
    
    public function testDBGroup()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
            
        $this->SetGroup();
        $this->EditGroup();
        $this->DeleteGroup();
        $this->GetUserGroups();
        $this->GetAllGroups();
        $this->GetSheetUserGroups();
        $this->GetSheetGroups();
    }
    
    public function GetSheetGroups()
    {
        $result = Request::get($this->url . 'DBGroup/group/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetGroups call");
        $this->assertContains('{"sheetId":"1","members":[{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"}',$result['content']);
        
        $result = Request::get($this->url . 'DBGroup/group/exercisesheet/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetGroups call");
    }

    public function GetSheetUserGroups()
    {
        $result = Request::get($this->url . 'DBGroup/group/user/2/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetUserGroups call");
        $this->assertContains('{"sheetId":"1","members":[{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"}',$result['content']);
        
        $result = Request::get($this->url . 'DBGroup/group/user/2/exercisesheet/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetUserGroups call");
    }
    
    public function GetAllGroups()
    {
        $result = Request::get($this->url . 'DBGroup/group',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllGroups call");
        $this->assertContains('{"sheetId":"1","members":[{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"}',$result['content']);    
    }
    
    public function GetUserGroups()
    {
        $result = Request::get($this->url . 'DBGroup/group/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUserGroups call");
        $this->assertContains('{"sheetId":"1","members":[{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"}',$result['content']);
        
        $result = Request::get($this->url . 'DBGroup/group/user/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserGroups call");
    }
    
    public function SetGroup()
    {

    }
    
    public function DeleteGroup()
    {

    }
    
    public function EditGroup()
    {

    }
}