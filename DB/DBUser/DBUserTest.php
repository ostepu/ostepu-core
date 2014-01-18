<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBUserTest extends PHPUnit_Framework_TestCase
{
    private $url = "";
    
    public function testDBUser()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
        
        $this->AddUser();
        $this->EditUser();
        $this->RemoveUser();
        $this->GetUser();
        $this->GetUsers();
        $this->GetCourseMember();
        $this->GetGroupMember();
        $this->GetIncreaseUserFailedLogin();
        $this->GetUserByStatus();
        $this->GetCourseUserByStatus();
    }
    
    public function GetUser()
    {
        $result = Request::get($this->url . 'DBUser/user/4',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUser call");
        $this->assertContains('"userName":"till"',$result['content']);
        
        $result = Request::get($this->url . 'DBUser/user/till',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUser call");
        $this->assertContains('"userName":"till"',$result['content']);
    }
    
    public function GetUsers()
    {
        $result = Request::get($this->url . 'DBUser/user',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUsers call");
        $this->assertContains('"userName":"till"',$result['content']);
    }
    
    public function GetCourseMember()
    {
        $result = Request::get($this->url . 'DBUser/user/course/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseMember call");
        $this->assertContains('"userName":"till"',$result['content']);
        
        $result = Request::get($this->url . 'DBUser/user/course/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseMember call"); 
    }
    
    public function GetGroupMember()
    {
        $result = Request::get($this->url . 'DBUser/user/group/user/2/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetGroupMember call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
        
        $result = Request::get($this->url . 'DBUser/user/group/user/lisa/exercisesheet/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetGroupMember call");      
        $this->assertContains('"userName":"lisa"',$result['content']); 

        $result = Request::get($this->url . 'DBUser/user/group/user/1/exercisesheet/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetGroupMember call");       
    }
    
    public function GetIncreaseUserFailedLogin()
    {
        $result = Request::get($this->url . 'DBUser/user/2/IncFailedLogin',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetIncreaseUserFailedLogin call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
        
        $result = Request::get($this->url . 'DBUser/user/lisa/IncFailedLogin',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetIncreaseUserFailedLogin call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
    }
    
    public function GetUserByStatus()
    {
        $result = Request::get($this->url . 'DBUser/user/status/0',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUserByStatus call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
        
        $result = Request::get($this->url . 'DBUser/user/status/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserByStatus call");    
    }
    
    public function GetCourseUserByStatus()
    {
        $result = Request::get($this->url . 'DBUser/user/course/1/status/0',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseUserByStatus call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
        
        $result = Request::get($this->url . 'DBUser/user/course/AAA/status/0',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseUserByStatus call"); 
        
        $result = Request::get($this->url . 'DBUser/user/course/1/status/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseUserByStatus call"); 
    }
    
    public function AddUser()
    {

    }
    
    public function RemoveUser()
    {

    }
    
    public function EditUser()
    {

    }
}