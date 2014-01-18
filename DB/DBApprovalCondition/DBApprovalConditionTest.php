<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBApprovalConditionTest extends PHPUnit_Framework_TestCase
{   
    private $url = "";
     
    public function testDBApprovalCondition()
    {
        // loads the component url from phpunit.ini file
        if (file_exists("phpunit.ini")){
            $this->url = parse_ini_file("phpunit.ini", TRUE)['PHPUNIT']['url'];
        }
        else
            $this->url = parse_ini_file("../phpunit.ini", TRUE)['PHPUNIT']['url'];
            
        $this->SetApprovalCondition();
        $this->EditApprovalCondition();
        $this->DeleteApprovalCondition();
        $this->GetCourseApprovalConditions();
        $this->GetAllApprovalConditions();
        $this->GetApprovalCondition();
    }
   
    public function GetCourseApprovalConditions()
    {
        $result = Request::get($this->url . 'DBApprovalCondition/approvalcondition/course/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseApprovalConditions call");
        $this->assertContains('{"id":"1","courseId":"1","exerciseTypeId":"1","percentage":"0.5"}',$result['content']);
        
        $result = Request::get($this->url . 'DBApprovalCondition/approvalcondition/course/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseApprovalConditions call");
   }
    
    public function GetAllApprovalConditions()
    {
        $result = Request::get($this->url . 'DBApprovalCondition/approvalcondition',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllApprovalConditions call");
        $this->assertContains('{"id":"1","courseId":"1","exerciseTypeId":"1","percentage":"0.5"}',$result['content']); 
    }
    
    public function GetApprovalCondition()
    {
        $result = Request::get($this->url . 'DBApprovalCondition/approvalcondition/1',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetApprovalCondition call");
        $this->assertContains('{"id":"1","courseId":"1","exerciseTypeId":"1","percentage":"0.5"}',$result['content']);
        
        $result = Request::get($this->url . 'DBApprovalCondition/approvalcondition/AAA',array('SESSION: abc', 'USER: 3', 'DATE: ' . time()),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetApprovalCondition call"); 
    }
    
    public function SetApprovalCondition()
    {

    }
    
    public function DeleteApprovalCondition()
    {

    }
    
    public function EditApprovalCondition()
    {

    }
}