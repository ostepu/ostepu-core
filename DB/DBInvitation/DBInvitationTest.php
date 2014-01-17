<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBInvitationTest extends PHPUnit_Framework_TestCase
{    
    public function testGetSheetInvitations()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation/exercisesheet/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetInvitations call");
        $this->assertContains('????',$result['content']);     
    
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetInvitations call");
    }
    
    public function testGetSheetMemberInvitations()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation/member/exercisesheet/1/user/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetMemberInvitations call");
        $this->assertContains('????',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation/member/exercisesheet/1/user/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetMemberInvitations call");
    
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation/member/exercisesheet/AAA/user/2',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetMemberInvitations call");
    }
    
    public function testGetSheetLeaderInvitations()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation/leader/exercisesheet/1/user/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetLeaderInvitations call");
        $this->assertContains('????',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation/leader/exercisesheet/1/user/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetLeaderInvitations call");
    
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation/leader/exercisesheet/AAA/user/2',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetLeaderInvitations call");
    }

    public function testGetAllInvitations()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllInvitation call");
        $this->assertContains('????',$result['content']); 
    }
    
    public function testGetMemberInvitations()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation/member/user/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetMemberInvitations call");
        $this->assertContains('????',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation/member/user/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetMemberInvitations call");
    }
    
    public function testGetLeaderInvitations()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation/leader/user/2',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetLeaderInvitations call");
        $this->assertContains('????',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBInvitation/invitation/leader/user/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetLeaderInvitations call");
    }
    
    public function testSetInvitation()
    {

    }
    
    public function testDeleteInvitation()
    {

    }
    
    public function testEditInvitation()
    {

    }
}