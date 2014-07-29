<?php 


/**
 * @file DBMarkingTest.php contains the DBMarkingTest class
 *
 * @author Till Uhlig
 */

include_once ( '/../../Assistants/Request.php' );
include_once ( '/../../Assistants/Structures.php' );

/**
 * A class, to test the DBMarking component
 */
class DBMarkingTest extends PHPUnit_Framework_TestCase
{
    private $url = '';

    public function testDBMarking( )
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

        $this->AddMarking( );
        $this->EditMarking( );
        $this->DeleteMarking( );
        $this->GetMarking( );
        $this->GetSubmissionMarking( );
        $this->GetAllMarkings( );
        $this->GetExerciseMarkings( );
        $this->GetSheetMarkings( );
        $this->GetUserGroupMarkings( );
        $this->GetTutorSheetMarkings( );
        $this->GetTutorExerciseMarkings( );
    }

    public function GetTutorExerciseMarkings( )
    {
        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercise/1/tutor/2',
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
                            'Unexpected HTTP status code for GetTutorExerciseMarkings call'
                            );
        $this->assertContains( 
                              '{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercise/1/tutor/2',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetTutorExerciseMarkings call'
                            );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercise/AAA/tutor/2',
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
                            'Unexpected HTTP status code for GetTutorExerciseMarkings call'
                            );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercise/2/tutor/AAA',
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
                            'Unexpected HTTP status code for GetTutorExerciseMarkings call'
                            );
    }

    public function GetTutorSheetMarkings( )
    {
        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercisesheet/1/tutor/2',
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
                            'Unexpected HTTP status code for GetTutorSheetMarkings call'
                            );
        $this->assertContains( 
                              '{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercisesheet/1/tutor/2',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetTutorSheetMarkings call'
                            );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercisesheet/AAA/tutor/2',
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
                            'Unexpected HTTP status code for GetTutorSheetMarkings call'
                            );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercisesheet/2/tutor/AAA',
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
                            'Unexpected HTTP status code for GetTutorSheetMarkings call'
                            );
    }

    public function GetUserGroupMarkings( )
    {
        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercisesheet/1/user/2',
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
                            'Unexpected HTTP status code for GetUserGroupMarkings call'
                            );
        $this->assertContains( 
                              '{"id":"3","tutorId":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercisesheet/1/user/2',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetUserGroupMarkings call'
                            );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercisesheet/AAA/user/2',
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
                            'Unexpected HTTP status code for GetUserGroupMarkings call'
                            );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercisesheet/2/user/AAA',
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
                            'Unexpected HTTP status code for GetUserGroupMarkings call'
                            );
    }

    public function GetSheetMarkings( )
    {
        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercisesheet/1',
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
                            'Unexpected HTTP status code for GetSheetMarkings call'
                            );
        $this->assertContains( 
                              '{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercisesheet/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetSheetMarkings call'
                            );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercisesheet/AAA',
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
                            'Unexpected HTTP status code for GetSheetMarkings call'
                            );
    }

    public function GetExerciseMarkings( )
    {
        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercise/1',
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
                            'Unexpected HTTP status code for GetExerciseMarkings call'
                            );
        $this->assertContains( 
                              '{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercise/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetExerciseMarkings call'
                            );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/exercise/AAA',
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
                            'Unexpected HTTP status code for GetExerciseMarkings call'
                            );
    }

    public function GetAllMarkings( )
    {
        $result = Request::get( 
                               $this->url . 'DBMarking/marking',
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
                            'Unexpected HTTP status code for GetAllMarkings call'
                            );
        $this->assertContains( 
                              '{"id":"2","tutorId":"2","tutorComment":"nichts","outstanding":"1","status":"0","points":"12"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetAllMarkings call'
                            );
    }

    public function GetSubmissionMarking( )
    {
        $result = Request::get( 
                               $this->url . 'DBMarking/marking/submission/1',
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
                            'Unexpected HTTP status code for GetSubmissionMarking call'
                            );
        $this->assertContains( 
                              '{"id":"1","tutorId":"2","tutorComment":"nichts","outstanding":"0","status":"0","points":"10"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/submission/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetSubmissionMarking call'
                            );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/submission/AAA',
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
                            'Unexpected HTTP status code for GetSubmissionMarking call'
                            );
    }

    public function GetMarking( )
    {
        $result = Request::get( 
                               $this->url . 'DBMarking/marking/1',
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
                            'Unexpected HTTP status code for GetMarking call'
                            );
        $this->assertContains( 
                              '{"id":"1","tutorId":"2","tutorComment":"nichts","outstanding":"0","status":"0","points":"10"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetMarking call'
                            );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/AAA',
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
                            'Unexpected HTTP status code for GetMarking call'
                            );
    }

    public function AddMarking( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBMarking/marking/100',
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
                            'Unexpected HTTP status code for AddMarking call'
                            );

        // createMarking($markingId,$tutorId,$fileId,$submissionId,$tutorComment,$outstanding,$status,$points,$date)
        $obj = Marking::createMarking( 
                                      '100',
                                      '1',
                                      '1',
                                      '1',
                                      'test',
                                      '1',
                                      '1',
                                      '15',
                                      '123123'
                                      );

        $result = Request::post( 
                                $this->url . 'DBMarking/marking',
                                array( 
                                      'SESSION: abc',
                                      'USER: 3',
                                      'DATE: ' . time( )
                                      ),
                                Marking::encodeMarking( $obj )
                                );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for AddMarking call'
                            );

        $result = Request::post( 
                                $this->url . 'DBMarking/marking',
                                array( ),
                                ''
                                );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for AddMarking call'
                            );
    }

    public function DeleteMarking( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBMarking/marking/100',
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
                            'Unexpected HTTP status code for DeleteMarking call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBMarking/marking/AAA',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteMarking call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBMarking/marking/100',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteMarking call'
                            );
    }

    public function EditMarking( )
    {

        // createMarking($markingId,$tutorId,$fileId,$submissionId,$tutorComment,$outstanding,$status,$points,$date)
        $obj = Marking::createMarking( 
                                      '100',
                                      '1',
                                      '1',
                                      '1',
                                      'Neutest',
                                      '1',
                                      '1',
                                      '15',
                                      '123123'
                                      );

        $result = Request::put( 
                               $this->url . 'DBMarking/marking/100',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               Marking::encodeMarking( $obj )
                               );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for EditMarking call'
                            );

        $result = Request::put( 
                               $this->url . 'DBMarking/marking/AAA',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for EditMarking call'
                            );

        $result = Request::put( 
                               $this->url . 'DBMarking/marking/100',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for EditMarking call'
                            );

        $result = Request::get( 
                               $this->url . 'DBMarking/marking/100',
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
                            'Unexpected HTTP status code for EditMarking call'
                            );
        $this->assertContains( 
                              '"tutorComment":"Neutest"',
                              $result['content']
                              );
    }
}
?>