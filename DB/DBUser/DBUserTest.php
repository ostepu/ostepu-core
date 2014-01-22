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
        $this->RemoveUserPermanent();
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
        
        $result = Request::get($this->url . 'DBUser/user',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for GetUsers call");
        $this->assertContains('[]',$result['content']);
    }
    
    public function GetCourseMember()
    {
        $result = Request::get($this->url . 'DBUser/user/course/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseMember call");
        $this->assertContains('"userName":"till"',$result['content']);
        
        $result = Request::get($this->url . 'DBUser/user/course/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseMember call"); 
        
        $result = Request::get($this->url . 'DBUser/user/course/1',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for GetUsers call");
        $this->assertContains('[]',$result['content']);
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
    
        $result = Request::get($this->url . 'DBUser/user/group/user/2/exercisesheet/1',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for GetUsers call");
        $this->assertContains('[]',$result['content']);
    }
    
    public function GetIncreaseUserFailedLogin()
    {
        // createUser($userId,$userName,$email,$firstName,$lastName,$title,$flag,$password,$salt,$failedLogins)
        $obj = User::createUser('2',null,null,null,null,null,null,null,null,'0');
        
        $result = Request::put($this->url . 'DBUser/user/2',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),User::encodeUser($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for EditUser call");  
        
        $result = Request::get($this->url . 'DBUser/user/2/IncFailedLogin',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetIncreaseUserFailedLogin call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
        
        $result = Request::get($this->url . 'DBUser/user/lisa/IncFailedLogin',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetIncreaseUserFailedLogin call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
        
        $result = Request::get($this->url . 'DBUser/user/2/IncFailedLogin',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUsers call");
        $this->assertContains('"userName":"lisa"',$result['content']);
    }
    
    public function GetUserByStatus()
    {
        $result = Request::get($this->url . 'DBUser/user/status/0',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetUserByStatus call");      
        $this->assertContains('"userName":"lisa"',$result['content']);
        
        $result = Request::get($this->url . 'DBUser/user/status/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetUserByStatus call");    
   
        $result = Request::get($this->url . 'DBUser/user/status/0',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for GetUsers call");
        $this->assertContains('[]',$result['content']);
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
   
        $result = Request::get($this->url . 'DBUser/user/course/1/status/0',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for GetUsers call");
        $this->assertContains('[]',$result['content']);
    }
    
    public function AddUser()
    {      
        $result = Request::delete($this->url . 'DBUser/user/NeuTill/permanent',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddUser call"); 
        
        $result = Request::delete($this->url . 'DBUser/user/tilltill/permanent',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddUser call"); 
        
        // createUser($userId,$userName,$email,$firstName,$lastName,$title,$flag,$password,$salt,$failedLogins)
        $obj = User::createUser(null,"tilltill","till2@email.de","Till","Uhlig","-","1","test","abc",null);
        
        $result = Request::post($this->url . 'DBUser/user',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),User::encodeUser($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for AddUser call");      
        $this->assertContains('{"id":',$result['content']);
    }
    
    public function RemoveUser()
    {
        $result = Request::delete($this->url . 'DBUser/user/NeuTill',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for RemoveUser call"); 
        
        $result = Request::delete($this->url . 'DBUser/user/NeuTill',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for RemoveUser call");
    }
    
    public function RemoveUserPermanent()
    {
        $result = Request::delete($this->url . 'DBUser/user/NeuTill/permanent',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for RemoveUserPermanent call"); 
        
        $result = Request::delete($this->url . 'DBUser/user/NeuTill/permanent',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for RemoveUserPermanent call");
    }
    
    public function EditUser()
    {
        // createUser($userId,$userName,$email,$firstName,$lastName,$title,$flag,$password,$salt,$failedLogins)
        $obj = User::createUser(null,"NeuTill",null,null,null,null,null,null,null,null);
        
        $result = Request::put($this->url . 'DBUser/user/tilltill',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),User::encodeUser($obj));
        $this->assertEquals(201, $result['status'], "Unexpected HTTP status code for EditUser call");  

        $result = Request::put($this->url . 'DBUser/user/tilltill',array(),"");
        $this->assertEquals(401, $result['status'], "Unexpected HTTP status code for EditUser call");
        
        $result = Request::get($this->url . 'DBUser/user/NeuTill',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for EditUser call");
        $this->assertContains('"userName":"NeuTill"',$result['content']);
    }
}