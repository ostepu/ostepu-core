<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBUserTest extends PHPUnit_Framework_TestCase
{

    public function testGetUser()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/4',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUser call");
        $this->assertContains('"userName":"till"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/till',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUser call");
        $this->assertContains('"userName":"till"',$result['content']);
    }
    
    public function testGetUsers()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUsers call");
        $this->assertContains('"userName":"till"',$result['content']);
    }
    
    public function testGetCourseMember()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/course/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseMember call");
        $this->assertContains('"userName":"till"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/course/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseMember call"); 
    }
    
    public function testGetGroupMember()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/group/user/2/exercisesheet/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetGroupMember call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/group/user/lisa/exercisesheet/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetGroupMember call");      
        $this->assertContains('"userName":"lisa"',$result['content']); 

        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/group/user/1/exercisesheet/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetGroupMember call");       
    }
    
    public function testGetIncreaseUserFailedLogin()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/2/IncFailedLogin',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetIncreaseUserFailedLogin call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/lisa/IncFailedLogin',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetIncreaseUserFailedLogin call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
    }
    
    public function testGetUserByStatus()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/status/0',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUserByStatus call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/status/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserByStatus call");    
    }
    
    public function testGetCourseUserByStatus()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/course/1/status/0',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseUserByStatus call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/course/AAA/status/0',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseUserByStatus call"); 
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBUser/user/course/1/status/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseUserByStatus call"); 
    }
    
    public function testAddUser()
    {

    }
    
    public function testRemoveUser()
    {

    }
    
    public function testEditUser()
    {

    }
}