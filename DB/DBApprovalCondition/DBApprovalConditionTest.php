<?php
include_once( 'Include/Request.php' );
include_once( 'Include/Structures.php' );

class DBApprovalConditionTest extends PHPUnit_Framework_TestCase
{    
   
    public function testGetCourseApprovalConditions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBApprovalCondition/approvalcondition/course/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetCourseApprovalConditions call");
        $this->assertContains('{"id":"1","courseId":"1","exerciseTypeId":"1","percentage":"0.5"}',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBApprovalCondition/approvalcondition/course/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetCourseApprovalConditions call");
   }
    
    public function testGetAllApprovalConditions()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBApprovalCondition/approvalcondition',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetAllApprovalConditions call");
        $this->assertContains('{"id":"1","courseId":"1","exerciseTypeId":"1","percentage":"0.5"}',$result['content']); 
    }
    
    public function testGetApprovalCondition()
    {
        $result = Request::get('http://localhost/uebungsplattform/DB/DBApprovalCondition/approvalcondition/1',array(),"");
        $this->assertEquals(200, $result['status'], "Unexpected HTTP status code for GetApprovalCondition call");
        $this->assertContains('{"id":"1","courseId":"1","exerciseTypeId":"1","percentage":"0.5"}',$result['content']);
        
        $result = Request::get('http://localhost/uebungsplattform/DB/DBApprovalCondition/approvalcondition/AAA',array(),"");
        $this->assertEquals(412, $result['status'], "Unexpected HTTP status code for GetApprovalCondition call"); 
    }
    
    public function testSetApprovalCondition()
    {

    }
    
    public function testDeleteApprovalCondition()
    {

    }
    
    public function testEditApprovalCondition()
    {

    }
}