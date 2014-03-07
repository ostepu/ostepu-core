<?php 


/**
 * @file DBApprovalConditionTest.php contains the DBApprovalConditionTest class
 *
 * @author Till Uhlig
 */

include_once ( '/../../Assistants/Request.php' );
include_once ( '/../../Assistants/Structures.php' );

/**
 * A class, to test the DBApprovalCondition component
 */
class DBApprovalConditionTest extends PHPUnit_Framework_TestCase
{
    private $url = '';

    public function testDBApprovalCondition( )
    {

        // loads the component url from phpunit.ini file
        if ( file_exists( 'phpunit.ini' ) ){
            $this->url = parse_ini_file( 
                                        'phpunit.ini',
                                        TRUE
                                        )['PHPUNIT']['url'];
            
        } else 
            $this->url = parse_ini_file( 
                                        '../phpunit.ini',
                                        TRUE
                                        )['PHPUNIT']['url'];

        $this->AddApprovalCondition( );
        $this->EditApprovalCondition( );
        $this->DeleteApprovalCondition( );
        $this->GetCourseApprovalConditions( );
        $this->GetAllApprovalConditions( );
        $this->GetApprovalCondition( );
    }

    public function GetCourseApprovalConditions( )
    {
        $result = Request::get( 
                               $this->url . 'DBApprovalCondition/approvalcondition/course/1',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               ''
                               );
        $this->assertEquals( 
                            200,
                            $result['status'],
                            'Unexpected HTTP status code for GetCourseApprovalConditions call'
                            );
        $this->assertContains( 
                              '{"id":"1","courseId":"1","exerciseTypeId":"1","percentage":"0.5"}',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBApprovalCondition/approvalcondition/course/AAA',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for GetCourseApprovalConditions call'
                            );
    }

    public function GetAllApprovalConditions( )
    {
        $result = Request::get( 
                               $this->url . 'DBApprovalCondition/approvalcondition',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               ''
                               );
        $this->assertEquals( 
                            200,
                            $result['status'],
                            'Unexpected HTTP status code for GetAllApprovalConditions call'
                            );
        $this->assertContains( 
                              '{"id":"1","courseId":"1","exerciseTypeId":"1","percentage":"0.5"}',
                              $result['content']
                              );
    }

    public function GetApprovalCondition( )
    {
        $result = Request::get( 
                               $this->url . 'DBApprovalCondition/approvalcondition/1',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               ''
                               );
        $this->assertEquals( 
                            200,
                            $result['status'],
                            'Unexpected HTTP status code for GetApprovalCondition call'
                            );
        $this->assertContains( 
                              '{"id":"1","courseId":"1","exerciseTypeId":"1","percentage":"0.5"}',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBApprovalCondition/approvalcondition/AAA',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for GetApprovalCondition call'
                            );
    }

    public function AddApprovalCondition( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBApprovalCondition/approvalcondition/100',
                                  array( 
                                        'SESSION: abc',
                                        'USER: 3',
                                        'DATE: ' . time( )
                                        ),
                                  ''
                                  );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for AddApprovalCondition call'
                            );

        // createApprovalCondition($approvalConditionId,$courseId,$exerciseTypeId,$percentage)
        $obj = ApprovalCondition::createApprovalCondition( 
                                                          '100',
                                                          '1',
                                                          '1',
                                                          '0.5'
                                                          );

        $result = Request::post( 
                                $this->url . 'DBApprovalCondition/approvalcondition',
                                array( 
                                      'SESSION: abc',
                                      'USER: 3',
                                      'DATE: ' . time( )
                                      ),
                                ApprovalCondition::encodeApprovalCondition( $obj )
                                );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for AddApprovalCondition call'
                            );

        $result = Request::post( 
                                $this->url . 'DBApprovalCondition/approvalcondition',
                                array( ),
                                ''
                                );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for AddApprovalCondition call'
                            );
    }

    public function DeleteApprovalCondition( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBApprovalCondition/approvalcondition/100',
                                  array( 
                                        'SESSION: abc',
                                        'USER: 3',
                                        'DATE: ' . time( )
                                        ),
                                  ''
                                  );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteApprovalCondition call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBApprovalCondition/approvalcondition/AAA',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteApprovalCondition call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBApprovalCondition/approvalcondition/100',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteApprovalCondition call'
                            );
    }

    public function EditApprovalCondition( )
    {

        // createApprovalCondition($approvalConditionId,$courseId,$exerciseTypeId,$percentage)
        $obj = ApprovalCondition::createApprovalCondition( 
                                                          null,
                                                          '1',
                                                          '1',
                                                          '0.8'
                                                          );

        $result = Request::put( 
                               $this->url . 'DBApprovalCondition/approvalcondition/100',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               ApprovalCondition::encodeApprovalCondition( $obj )
                               );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for EditApprovalCondition call'
                            );

        $result = Request::put( 
                               $this->url . 'DBApprovalCondition/approvalcondition/100',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for EditApprovalCondition call'
                            );

        $result = Request::get( 
                               $this->url . 'DBApprovalCondition/approvalcondition/100',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               ''
                               );
        $this->assertEquals( 
                            200,
                            $result['status'],
                            'Unexpected HTTP status code for EditApprovalCondition call'
                            );
        $this->assertContains( 
                              '"percentage":"0.8"',
                              $result['content']
                              );
    }
}
