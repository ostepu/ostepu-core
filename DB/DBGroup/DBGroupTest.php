<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBGroupTest extends PHPUnit_Framework_TestCase
{    
    public function testGetSheetGroups()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBGroup/group/exercisesheet/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetGroups call");
        $this->assertContains('{"sheetId":"1","members":[{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"2"}',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBGroup/group/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetGroups call");
    }

    public function testGetSheetUserGroups()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBGroup/group/user/2/exercisesheet/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetUserGroups call");
        $this->assertContains('{"sheetId":"1","members":[{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"2"}',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBGroup/group/user/2/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetUserGroups call");
    }
    
    public function testGetAllGroups()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBGroup/group',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllGroups call");
        $this->assertContains('{"sheetId":"1","members":[{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"2"}',$result['content']);    
    }
    
    public function testGetUserGroups()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBGroup/group/user/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUserGroups call");
        $this->assertContains('{"sheetId":"1","members":[{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"2"}',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBGroup/group/user/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserGroups call");
    }
    
    public function testSetGroup()
    {

    }
    
    public function testDeleteGroup()
    {

    }
    
    public function testEditGroup()
    {

    }
}