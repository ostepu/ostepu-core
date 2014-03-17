<?php 


/**
 * @file DBExternalIdTest.php contains the DBExternalIdTest class
 *
 * @author Till Uhlig
 */

include_once ( '/../../Assistants/Request.php' );
include_once ( '/../../Assistants/Structures.php' );

/**
 * A class, to test the DBExternalId component
 */
class DBExternalIdTest extends PHPUnit_Framework_TestCase
{
    private $url = '';

    public function testDBExternalId( )
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

        $this->AddExternalId( );
        $this->EditExternalId( );
        $this->DeleteExternalId( );
        $this->GetExternalId( );
        $this->GetCourseExternalIds( );
        $this->GetAllExternalIds( );
    }

    public function GetAllExternalIds( )
    {
        $result = Request::get( 
                               $this->url . 'DBExternalId/externalid',
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
                            'Unexpected HTTP status code for GetAllExternalIds call'
                            );
        $this->assertContains( 
                              '"name":"Fachschaftsseminar fuer Mathematik"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBExternalId/externalid',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetAllExternalIds call'
                            );
    }

    public function GetCourseExternalIds( )
    {
        $result = Request::get( 
                               $this->url . 'DBExternalId/externalid/course/2',
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
                            'Unexpected HTTP status code for GetCourseExternalIds call'
                            );
        $this->assertContains( 
                              '"name":"Fachschaftsseminar fuer Mathematik"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBExternalId/externalid/course/2',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetCourseExternalIds call'
                            );

        $result = Request::get( 
                               $this->url . 'DBExternalId/externalid/course/AAA',
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
                            'Unexpected HTTP status code for GetCourseExternalIds call'
                            );
    }

    public function GetExternalId( )
    {
        $result = Request::get( 
                               $this->url . 'DBExternalId/externalid/Ver2',
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
                            'Unexpected HTTP status code for GetExternalId call'
                            );
        $this->assertContains( 
                              '"name":"Fachschaftsseminar fuer Mathematik"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBExternalId/externalid/Ver2',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetExternalId call'
                            );
    }

    public function AddExternalId( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBExternalId/externalid/hlskdaghlkj',
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
                            'Unexpected HTTP status code for AddExternalId call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBExternalId/externalid/Neuhlskdaghlkj',
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
                            'Unexpected HTTP status code for AddExternalId call'
                            );

        // createExternalId($externalId,$courseId)
        $obj = ExternalId::createExternalId( 
                                            'hlskdaghlkj',
                                            '1'
                                            );

        $result = Request::post( 
                                $this->url . 'DBExternalId/externalid',
                                array( 
                                      'SESSION: abc',
                                      'USER: 3',
                                      'DATE: ' . time( )
                                      ),
                                ExternalId::encodeExternalId( $obj )
                                );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for SetExternalId call'
                            );

        $result = Request::post( 
                                $this->url . 'DBExternalId/externalid',
                                array( ),
                                ExternalId::encodeExternalId( $obj )
                                );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for SetExternalId call'
                            );
    }

    public function DeleteExternalId( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBExternalId/externalid/hlskdaghlkj',
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
                            'Unexpected HTTP status code for DeleteExternalId call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBExternalId/externalid/hlskdaghlkj',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteExternalId call'
                            );
    }

    public function EditExternalId( )
    {

        // createExternalId($externalId,$courseId)
        $obj = ExternalId::createExternalId( 
                                            'Neuhlskdaghlkj',
                                            '1'
                                            );

        $result = Request::put( 
                               $this->url . 'DBExternalId/externalid/hlskdaghlkj',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               ExternalId::encodeExternalId( $obj )
                               );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for EditExternalId call'
                            );

        $result = Request::put( 
                               $this->url . 'DBExternalId/externalid/hlskdaghlkj',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for EditExternalId call'
                            );

        $result = Request::get( 
                               $this->url . 'DBExternalId/externalid/course/1',
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
                            'Unexpected HTTP status code for EditExternalId call'
                            );
        $this->assertContains( 
                              '"id":"Neuhlskdaghlkj"',
                              $result['content']
                              );
    }
}
