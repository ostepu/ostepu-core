<?php 


/**
 * @file DBSessionTest.php contains the DBSessionTest class
 *
 * @author Till Uhlig
 */

include_once ( '/../../Assistants/Request.php' );
include_once ( '/../../Assistants/Structures.php' );

/**
 * A class, to test the DBSession component
 */
class DBSessionTest extends PHPUnit_Framework_TestCase
{
    private $url = '';

    public function testDBSession( )
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

        $this->AddSession( );
        $this->EditSession( );
        $this->EditUserSession( );
        $this->RemoveSession( );
        $this->RemoveUserSession( );
        $this->GetUserSession( );
        $this->GetSessionUser( );
        $this->GetAllSessions( );
    }

    public function GetAllSessions( )
    {
        $result = Request::get( 
                               $this->url . 'DBSession/session',
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
                            'Unexpected HTTP status code for GetAllSessions call'
                            );
        $this->assertContains( 
                              '{"user":"1","session":"abcd"}',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSession/session',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetAllSessions call'
                            );
    }

    public function GetSessionUser( )
    {
        $result = Request::get( 
                               $this->url . 'DBSession/session/abcd',
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
                            'Unexpected HTTP status code for GetSessionUser call'
                            );
        $this->assertContains( 
                              '{"user":"1","session":"abcd"}',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSession/session/abcd',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetSessionUser call'
                            );
    }

    public function GetUserSession( )
    {
        $result = Request::get( 
                               $this->url . 'DBSession/session/user/1',
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
                            'Unexpected HTTP status code for GetUserSession call'
                            );
        $this->assertContains( 
                              '{"user":"1","session":"abcd"}',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSession/session/user/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetUserSession call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSession/session/user/AAA',
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
                            'Unexpected HTTP status code for GetUserSession call'
                            );
    }

    public function AddSession( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBSession/session/user/2',
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
                            'Unexpected HTTP status code for AddSession call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBSession/session/Neuhlskdaghlkj',
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
                            'Unexpected HTTP status code for RemoveSession call'
                            );

        // createSession($userId,$sessionId)
        $obj = Session::createSession( 
                                      '2',
                                      'hlskdaghlkj'
                                      );

        $result = Request::post( 
                                $this->url . 'DBSession/session',
                                array( ),
                                Session::encodeSession( $obj )
                                );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for AddSession call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBSession/session/user/2',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for AddSession call'
                            );
    }

    public function RemoveUserSession( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBSession/session/user/2',
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
                            'Unexpected HTTP status code for RemoveUserSession call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBSession/session/user/2',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for RemoveUserSession call'
                            );
    }

    public function EditUserSession( )
    {

        // createSession($userId,$sessionId)
        $obj = Session::createSession( 
                                      '2',
                                      'Neuhlskdaghlkj'
                                      );

        $result = Request::put( 
                               $this->url . 'DBSession/session/user/2',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               Session::encodeSession( $obj )
                               );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for EditUserSession call'
                            );

        $result = Request::put( 
                               $this->url . 'DBSession/session/user/2',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for EditUserSession call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSession/session/user/2',
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
                            'Unexpected HTTP status code for EditUserSession call'
                            );
        $this->assertContains( 
                              '"session":"Neuhlskdaghlkj"',
                              $result['content']
                              );
    }

    public function RemoveSession( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBSession/session/Neuhlskdaghlkj',
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
                            'Unexpected HTTP status code for RemoveSession call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBSession/session/Neuhlskdaghlkj',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for RemoveSession call'
                            );
    }

    public function EditSession( )
    {

        // createSession($userId,$sessionId)
        $obj = Session::createSession( 
                                      '2',
                                      'Neuhlskdaghlkj'
                                      );

        $result = Request::put( 
                               $this->url . 'DBSession/session/hlskdaghlkj',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               Session::encodeSession( $obj )
                               );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for EditUserSession call'
                            );

        $result = Request::put( 
                               $this->url . 'DBSession/session/hlskdaghlkj',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for EditUserSession call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSession/session/Neuhlskdaghlkj',
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
                            'Unexpected HTTP status code for EditUserSession call'
                            );
        $this->assertContains( 
                              '"session":"Neuhlskdaghlkj"',
                              $result['content']
                              );
    }
}
