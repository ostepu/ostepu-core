<?php
/**
 * @file DBGroupTest.php contains the DBGroupTest class
 *
 * @author Till Uhlig
 */ 
 
include_once( '/../../Assistants/Request.php' );
include_once( '/../../Assistants/Structures.php' );

/**
 * A class, to test the DBGroup component
 */
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
            
        $this->AddGroup();
        $this->EditGroup();
        $this->DeleteGroup();
        $this->GetUserGroups();
        $this->GetAllGroups();
        $this->GetUserSheetGroups();
        $this->GetSheetGroups();
    }
    
    public function GetSheetGroups()
    {
        $result = Request::get($this->url . 'DBGroup/group/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetGroups call");
        $this->assertContains('{"sheetId":"1","leader":{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"},"members":[]}',$result['content']);
        
        $result = Request::get($this->url . 'DBGroup/group/exercisesheet/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetGroups call");
    }

    public function GetUserSheetGroups()
    {
        $result = Request::get($this->url . 'DBGroup/group/user/2/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetUserGroups call");
        $this->assertContains('{"sheetId":"1","leader":{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"},"members":[]}',$result['content']);
        
        $result = Request::get($this->url . 'DBGroup/group/user/2/exercisesheet/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetUserGroups call");
    }
    
    public function GetAllGroups()
    {
        $result = Request::get($this->url . 'DBGroup/group',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllGroups call");
        $this->assertContains('{"sheetId":"1","leader":{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"},"members":[]}',$result['content']);    
    }
    
    public function GetUserGroups()
    {
        $result = Request::get($this->url . 'DBGroup/group/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUserGroups call");
        $this->assertContains('{"sheetId":"1","leader":{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"},"members":[]}',$result['content']);
        
        $result = Request::get($this->url . 'DBGroup/group/user/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserGroups call");
    }
    
    public function AddGroup()
    {
        $result = Request::delete($this->url . 'DBGroup/group/user/1/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddGroup call");
        
        //createGroup($leaderId,$memberId,$sheetId)
        $obj = Group::createGroup("1","1","1");

        $result = Request::post($this->url . 'DBGroup/group',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),Group::encodeGroup($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddGroup call");   
        
        $result = Request::post($this->url . 'DBGroup/group',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for AddGroup call"); 
    }
    
    public function DeleteGroup()
    {
        $result = Request::delete($this->url . 'DBGroup/group/user/1/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for DeleteGroup call");
    }
    
    public function EditGroup()
    {
        //createGroup($leaderId,$memberId,$sheetId)
        $obj = Group::createGroup("3","1","1");

        $result = Request::put($this->url . 'DBGroup/group/user/1/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),Group::encodeGroup($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for EditGroup call");   
        
        $result = Request::put($this->url . 'DBGroup/group/user/1/exercisesheet/1',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for EditGroup call"); 
        
        $result = Request::get($this->url . 'DBGroup/group/user/1/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for EditGroup call");
        $this->assertContains('"sheetId":"1"',$result['content']);
    }
}