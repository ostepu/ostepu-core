<?php 


/**
 * @file DBInvitationTest.php contains the DBInvitationTest class
 *
 * @author Till Uhlig
 */

include_once ( dirname( __FILE__ ) . '/../../Assistants/Request.php' );
include_once ( dirname( __FILE__ ) . '/../../Assistants/Structures.php' );

/**
 * A class, to test the DBInvitation component
 */
class DBInvitationTest extends PHPUnit_Framework_TestCase
{
    private $url = '';

    public function testDBInvitation( )
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

        $this->AddInvitation( );
        $this->EditInvitation( );
        $this->DeleteInvitation( );
        $this->GetLeaderInvitations( );
        $this->GetMemberInvitations( );
        $this->GetAllInvitations( );
        $this->GetSheetLeaderInvitations( );
        $this->GetSheetMemberInvitations( );
        $this->GetSheetInvitations( );
    }

    public function GetSheetInvitations( )
    {
        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/exercisesheet/1',
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
                            'Unexpected HTTP status code for GetSheetInvitations call'
                            );
        $this->assertContains( 
                              '{"sheet":"1","leader":{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/exercisesheet/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetSheetInvitations call'
                            );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/exercisesheet/AAA',
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
                            'Unexpected HTTP status code for GetSheetInvitations call'
                            );
    }

    public function GetSheetMemberInvitations( )
    {
        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/member/exercisesheet/1/user/1',
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
                            'Unexpected HTTP status code for GetSheetMemberInvitations call'
                            );
        $this->assertContains( 
                              '{"sheet":"1","leader":{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/member/exercisesheet/1/user/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetSheetMemberInvitations call'
                            );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/member/exercisesheet/1/user/AAA',
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
                            'Unexpected HTTP status code for GetSheetMemberInvitations call'
                            );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/member/exercisesheet/AAA/user/2',
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
                            'Unexpected HTTP status code for GetSheetMemberInvitations call'
                            );
    }

    public function GetSheetLeaderInvitations( )
    {
        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/leader/exercisesheet/1/user/2',
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
                            'Unexpected HTTP status code for GetSheetLeaderInvitations call'
                            );
        $this->assertContains( 
                              '{"sheet":"1","leader":{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/leader/exercisesheet/1/user/2',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetSheetLeaderInvitations call'
                            );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/leader/exercisesheet/1/user/AAA',
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
                            'Unexpected HTTP status code for GetSheetLeaderInvitations call'
                            );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/leader/exercisesheet/AAA/user/2',
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
                            'Unexpected HTTP status code for GetSheetLeaderInvitations call'
                            );
    }

    public function GetAllInvitations( )
    {
        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation',
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
                            'Unexpected HTTP status code for GetAllInvitation call'
                            );
        $this->assertContains( 
                              '{"sheet":"1","leader":{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetAllInvitation call'
                            );
    }

    public function GetMemberInvitations( )
    {
        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/member/user/1',
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
                            'Unexpected HTTP status code for GetMemberInvitations call'
                            );
        $this->assertContains( 
                              '{"sheet":"1","leader":{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/member/user/AAA',
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
                            'Unexpected HTTP status code for GetMemberInvitations call'
                            );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/member/user/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetMemberInvitations call'
                            );
    }

    public function GetLeaderInvitations( )
    {
        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/leader/user/2',
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
                            'Unexpected HTTP status code for GetLeaderInvitations call'
                            );
        $this->assertContains( 
                              '{"sheet":"1","leader":{"id":"2","userName":"lisa","email":"lisa@email.de","firstName":"Lisa","lastName":"Dietrich","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/leader/user/AAA',
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
                            'Unexpected HTTP status code for GetLeaderInvitations call'
                            );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/leader/user/2',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetLeaderInvitations call'
                            );
    }

    public function AddInvitation( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBInvitation/invitation/user/1/exercisesheet/1/user/1',
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
                            'Unexpected HTTP status code for AddInvitation call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBInvitation/invitation/user/1/exercisesheet/3/user/1',
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
                            'Unexpected HTTP status code for AddInvitation call'
                            );

        // createInvitation($leaderId,$memberId,$sheetId)
        $obj = Invitation::createInvitation( 
                                            '1',
                                            '1',
                                            '1'
                                            );

        $result = Request::post( 
                                $this->url . 'DBInvitation/invitation',
                                array( 
                                      'SESSION: abc',
                                      'USER: 3',
                                      'DATE: ' . time( )
                                      ),
                                Invitation::encodeInvitation( $obj )
                                );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for AddInvitation call'
                            );

        $result = Request::post( 
                                $this->url . 'DBInvitation/invitation',
                                array( ),
                                ''
                                );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for AddInvitation call'
                            );
    }

    public function DeleteInvitation( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBInvitation/invitation/user/1/exercisesheet/1/user/1',
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
                            'Unexpected HTTP status code for DeleteInvitation call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBInvitation/invitation/user/AAA/exercisesheet/1/user/1',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteInvitation call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBInvitation/invitation/user/1/exercisesheet/AAA/user/1',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteInvitation call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBInvitation/invitation/user/1/exercisesheet/1/user/AAA',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteInvitation call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBInvitation/invitation/user/1/exercisesheet/1/user/1',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteInvitation call'
                            );
    }

    public function EditInvitation( )
    {

        // createInvitation($leaderId,$memberId,$sheetId)
        $obj = Invitation::createInvitation( 
                                            '1',
                                            '1',
                                            '3'
                                            );

        $result = Request::put( 
                               $this->url . 'DBInvitation/invitation/user/1/exercisesheet/1/user/1',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               Invitation::encodeInvitation( $obj )
                               );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for EditInvitation call'
                            );

        $result = Request::put( 
                               $this->url . 'DBInvitation/invitation/user/1/exercisesheet/1/user/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for EditInvitation call'
                            );

        $result = Request::put( 
                               $this->url . 'DBInvitation/invitation/user/AAA/exercisesheet/1/user/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for EditInvitation call'
                            );

        $result = Request::put( 
                               $this->url . 'DBInvitation/invitation/user/1/exercisesheet/AAA/user/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for EditInvitation call'
                            );

        $result = Request::put( 
                               $this->url . 'DBInvitation/invitation/user/1/exercisesheet/1/user/AAA',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for EditInvitation call'
                            );

        $result = Request::get( 
                               $this->url . 'DBInvitation/invitation/leader/exercisesheet/3/user/1',
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
                            'Unexpected HTTP status code for EditInvitation call'
                            );
        $this->assertContains( 
                              '{"sheet":"3","leader":{"id":"1","userName":"super-admin","flag":"1"',
                              $result['content']
                              );
    }
}