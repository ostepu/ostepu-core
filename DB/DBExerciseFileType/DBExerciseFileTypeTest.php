<?php 


/**
 * @file DBExerciseFileTypeTest.php contains the DBExerciseFileTypeTest class
 *
 * @author Till Uhlig
 */

include_once ( '/../../Assistants/Request.php' );
include_once ( '/../../Assistants/Structures.php' );

/**
 * A class, to test the DBExerciseFileType component
 */
class DBExerciseFileTypeTest extends PHPUnit_Framework_TestCase
{
    private $url = '';

    public function testDBExerciseFileType( )
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

        $this->AddExerciseFileType( );
        $this->EditExerciseFileType( );
        $this->DeleteExerciseFileType( );
        $this->GetExerciseFileType( );
        $this->GetExerciseExerciseFileType( );
        $this->GetAllExerciseFileTypes( );
    }

    public function GetAllExerciseFileTypes( )
    {
        $result = Request::get( 
                               $this->url . 'DBExerciseFileType/exercisefiletype',
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
                            'Unexpected HTTP status code for GetAllExerciseFileTypes call'
                            );
        $this->assertContains( 
                              '{"id":"1","text":"application\/pdf","exerciseId":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBExerciseFileType/exercisefiletype',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetAllExerciseFileTypes call'
                            );
    }

    public function GetExerciseExerciseFileType( )
    {
        $result = Request::get( 
                               $this->url . 'DBExerciseFileType/exercisefiletype/exercise/1',
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
                            'Unexpected HTTP status code for GetExerciseExerciseFileType call'
                            );
        $this->assertContains( 
                              '{"id":"1","text":"application\/pdf","exerciseId":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBExerciseFileType/exercisefiletype/exercise/AAA',
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
                            'Unexpected HTTP status code for GetExerciseExerciseFileType call'
                            );

        $result = Request::get( 
                               $this->url . 'DBExerciseFileType/exercisefiletype/exercise/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetExerciseExerciseFileType call'
                            );
    }

    public function GetExerciseFileType( )
    {
        $result = Request::get( 
                               $this->url . 'DBExerciseFileType/exercisefiletype/1',
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
                            'Unexpected HTTP status code for GetExerciseFileType call'
                            );
        $this->assertContains( 
                              '{"id":"1","text":"application\/pdf","exerciseId":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBExerciseFileType/exercisefiletype/AAA',
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
                            'Unexpected HTTP status code for GetExerciseFileType call'
                            );

        $result = Request::get( 
                               $this->url . 'DBExerciseFileType/exercisefiletype/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetExerciseFileType call'
                            );
    }

    public function AddExerciseFileType( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBExerciseFileType/exercisefiletype/100',
                                  array( 
                                        'SESSION: abc',
                                        'USER: 3',
                                        'DATE: ' . time( )
                                        ),
                                  ''
                                  );

        // createExerciseType($typeid,$name)
        $obj = ExerciseFileType::createExerciseFileType( 
                                                        '100',
                                                        'Sonderpunkte',
                                                        1
                                                        );

        $result = Request::post( 
                                $this->url . 'DBExerciseFileType/exercisefiletype',
                                array( 
                                      'SESSION: abc',
                                      'USER: 3',
                                      'DATE: ' . time( )
                                      ),
                                ExerciseFileType::encodeExerciseFileType( $obj )
                                );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for AddExerciseFileType call'
                            );
        $this->assertContains( 
                              '{"id":100}',
                              $result['content']
                              );

        $result = Request::post( 
                                $this->url . 'DBExerciseFileType/exercisefiletype',
                                array( ),
                                ExerciseFileType::encodeExerciseFileType( $obj )
                                );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for AddExerciseFileType call'
                            );
    }

    public function DeleteExerciseFileType( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBExerciseFileType/exercisefiletype/100',
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
                            'Unexpected HTTP status code for DeleteExerciseFileType call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBExerciseFileType/exercisefiletype/AAA',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteExerciseFileType call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBExerciseFileType/exercisefiletype/100',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteExerciseFileType call'
                            );
    }

    public function EditExerciseFileType( )
    {

        // createExerciseFileType($typeid,$name,$exerciseId)
        $obj = ExerciseFileType::createExerciseFileType( 
                                                        '100',
                                                        'NeuSonderpunkte',
                                                        1
                                                        );

        $result = Request::put( 
                               $this->url . 'DBExerciseFileType/exercisefiletype/100',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               ExerciseFileType::encodeExerciseFileType( $obj )
                               );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for EditExerciseFileType call'
                            );

        $result = Request::put( 
                               $this->url . 'DBExerciseFileType/exercisefiletype/AAA',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for EditExerciseFileType call'
                            );

        $result = Request::put( 
                               $this->url . 'DBExerciseFileType/exercisefiletype/100',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for EditExerciseFileType call'
                            );

        $result = Request::get( 
                               $this->url . 'DBExerciseFileType/exercisefiletype/100',
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
                            'Unexpected HTTP status code for EditExerciseFileType call'
                            );
        $this->assertContains( 
                              '{"id":"100","text":"NeuSonderpunkte","exerciseId":"1"',
                              $result['content']
                              );
    }
}
