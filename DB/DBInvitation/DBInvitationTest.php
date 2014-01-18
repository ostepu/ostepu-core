<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBInvitationTest extends PHPUnit_Framework_TestCase
{    
    private $url = "";
    
    public function testDBInvitation()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
            
        $this->SetInvitation();
        $this->EditInvitation();
        $this->DeleteInvitation();
        $this->GetLeaderInvitations();
        $this->GetMemberInvitations();
        $this->GetAllInvitations();
        $this->GetSheetLeaderInvitations();
        $this->GetSheetMemberInvitations();
        $this->GetSheetInvitations();
    }
    
    public function GetSheetInvitations()
    {
        $result = Request::get($this->url . 'DBInvitation/invitation/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetInvitations call");
        $this->assertContains('????',$result['content']);     
    
        $result = Request::get($this->url . 'DBInvitation/invitation/exercisesheet/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetInvitations call");
    }
    
    public function GetSheetMemberInvitations()
    {
        $result = Request::get($this->url . 'DBInvitation/invitation/member/exercisesheet/1/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetMemberInvitations call");
        $this->assertContains('????',$result['content']);
        
        $result = Request::get($this->url . 'DBInvitation/invitation/member/exercisesheet/1/user/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetMemberInvitations call");
    
        $result = Request::get($this->url . 'DBInvitation/invitation/member/exercisesheet/AAA/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetMemberInvitations call");
    }
    
    public function GetSheetLeaderInvitations()
    {
        $result = Request::get($this->url . 'DBInvitation/invitation/leader/exercisesheet/1/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetSheetLeaderInvitations call");
        $this->assertContains('????',$result['content']);
        
        $result = Request::get($this->url . 'DBInvitation/invitation/leader/exercisesheet/1/user/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetLeaderInvitations call");
    
        $result = Request::get($this->url . 'DBInvitation/invitation/leader/exercisesheet/AAA/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetSheetLeaderInvitations call");
    }

    public function GetAllInvitations()
    {
        $result = Request::get($this->url . 'DBInvitation/invitation',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllInvitation call");
        $this->assertContains('????',$result['content']); 
    }
    
    public function GetMemberInvitations()
    {
        $result = Request::get($this->url . 'DBInvitation/invitation/member/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetMemberInvitations call");
        $this->assertContains('????',$result['content']);
        
        $result = Request::get($this->url . 'DBInvitation/invitation/member/user/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetMemberInvitations call");
    }
    
    public function GetLeaderInvitations()
    {
        $result = Request::get($this->url . 'DBInvitation/invitation/leader/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetLeaderInvitations call");
        $this->assertContains('????',$result['content']);
        
        $result = Request::get($this->url . 'DBInvitation/invitation/leader/user/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetLeaderInvitations call");
    }
    
    public function SetInvitation()
    {

    }
    
    public function DeleteInvitation()
    {

    }
    
    public function EditInvitation()
    {

    }
}