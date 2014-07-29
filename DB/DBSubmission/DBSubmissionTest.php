<?php 


/**
 * @file DBSubmissionTest.php contains the DBSubmissionTest class
 *
 * @author Till Uhlig
 */

include_once ( '/../../Assistants/Request.php' );
include_once ( '/../../Assistants/Structures.php' );

/**
 * A class, to test the DBSubmission component
 */
class DBSubmissionTest extends PHPUnit_Framework_TestCase
{
    private $url = '';

    public function testDBSubmission( )
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

        $this->AddSubmission( );
        $this->EditSubmission( );
        $this->DeleteSubmission( );
        $this->GetSelectedSheetSubmissions( );
        $this->GetSheetSubmissions( );
        $this->GetSelectedExerciseSubmissions( );
        $this->GetAllSubmissions( );
        $this->GetExerciseSubmissions( );
        $this->GetSubmission( );
        $this->GetGroupSelectedExerciseSubmissions( );
        $this->GetGroupExerciseSubmissions( );
        $this->GetGroupSelectedSubmissions( );
        $this->GetGroupSubmissions( );
        $this->GetUserExerciseSubmissions( );
    }

    public function GetSelectedSheetSubmissions( )
    {
        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/exercisesheet/1/selected',
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
                            'Unexpected HTTP status code for GetSelectedSheetSubmissions call'
                            );
        $this->assertContains( 
                              '{"id":"2","studentId":"1","exerciseId":"1","comment":"zwei","accepted":"1","date":"1389643115","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/exercisesheet/AAA/selected',
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
                            'Unexpected HTTP status code for GetSelectedSheetSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/exercisesheet/1/selected',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetSelectedSheetSubmissions call'
                            );
    }

    public function GetSheetSubmissions( )
    {
        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/exercisesheet/1',
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
                            'Unexpected HTTP status code for GetSheetSubmissions call'
                            );
        $this->assertContains( 
                              '{"id":"2","studentId":"1","exerciseId":"1","comment":"zwei","accepted":"1","date":"1389643115","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/exercisesheet/AAA',
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
                            'Unexpected HTTP status code for GetSheetSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/exercisesheet/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetSheetSubmissions call'
                            );
    }

    public function GetSelectedExerciseSubmissions( )
    {
        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/exercise/1/selected',
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
                            'Unexpected HTTP status code for GetSelectedExerciseSubmissions call'
                            );
        $this->assertContains( 
                              '{"id":"2","studentId":"1","exerciseId":"1","comment":"zwei","accepted":"1","date":"1389643115","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/exercise/AAA/selected',
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
                            'Unexpected HTTP status code for GetSelectedExerciseSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/exercise/1/selected',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetSelectedExerciseSubmissions call'
                            );
    }

    public function GetAllSubmissions( )
    {
        $result = Request::get( 
                               $this->url . 'DBSubmission/submission',
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
                            'Unexpected HTTP status code for GetAllSubmissions call'
                            );
        $this->assertContains( 
                              '{"id":"1","studentId":"1","exerciseId":"1","comment":"eins","accepted":"1","date":"1389643115","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetAllSubmissions call'
                            );
    }

    public function GetExerciseSubmissions( )
    {
        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/exercise/1',
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
                            'Unexpected HTTP status code for GetExerciseSubmissions call'
                            );
        $this->assertContains( 
                              '{"id":"1","studentId":"1","exerciseId":"1","comment":"eins","accepted":"1","date":"1389643115","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/exercise/AAA',
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
                            'Unexpected HTTP status code for GetExerciseSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/exercise/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetExerciseSubmissions call'
                            );
    }

    public function GetSubmission( )
    {
        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/1',
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
                            'Unexpected HTTP status code for GetSubmission call'
                            );
        $this->assertContains( 
                              '{"id":"1","studentId":"1","exerciseId":"1","comment":"eins","accepted":"1","date":"1389643115","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/AAA',
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
                            'Unexpected HTTP status code for GetSubmission call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetSubmission call'
                            );
    }

    public function GetGroupSelectedExerciseSubmissions( )
    {
        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/2/exercise/1/selected',
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
                            'Unexpected HTTP status code for GetGroupSelectedExerciseSubmissions call'
                            );
        $this->assertContains( 
                              '{"id":"3","studentId":"2","exerciseId":"1","comment":"drei","accepted":"1","date":"1389643115"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/2/exercise/AAA/selected',
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
                            'Unexpected HTTP status code for GetGroupSelectedExerciseSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/AAA/exercise/1/selected',
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
                            'Unexpected HTTP status code for GetGroupSelectedExerciseSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/2/exercise/1/selected',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetGroupSelectedExerciseSubmissions call'
                            );
    }

    public function GetGroupExerciseSubmissions( )
    {
        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/2/exercise/1',
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
                            'Unexpected HTTP status code for GetGroupExerciseSubmissions call'
                            );
        $this->assertContains( 
                              '{"id":"3","studentId":"2","exerciseId":"1","comment":"drei","accepted":"1","date":"1389643115"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/2/exercise/AAA',
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
                            'Unexpected HTTP status code for GetGroupExerciseSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/AAA/exercise/1',
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
                            'Unexpected HTTP status code for GetGroupExerciseSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/2/exercise/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetGroupExerciseSubmissions call'
                            );
    }

    public function GetGroupSelectedSubmissions( )
    {
        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/2/exercisesheet/1/selected',
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
                            'Unexpected HTTP status code for GetGroupSelectedSubmissions call'
                            );
        $this->assertContains( 
                              '{"id":"3","studentId":"2","exerciseId":"1","comment":"drei","accepted":"1","date":"1389643115"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/2/exercisesheet/AAA/selected',
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
                            'Unexpected HTTP status code for GetGroupSelectedSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/AAA/exercisesheet/1/selected',
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
                            'Unexpected HTTP status code for GetGroupSelectedSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/2/exercisesheet/1/selected',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetGroupSelectedSubmissions call'
                            );
    }

    public function GetGroupSubmissions( )
    {
        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/2/exercisesheet/1',
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
                            'Unexpected HTTP status code for GetGroupSubmissions call'
                            );
        $this->assertContains( 
                              '{"id":"3","studentId":"2","exerciseId":"1","comment":"drei","accepted":"1","date":"1389643115"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/2/exercisesheet/AAA',
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
                            'Unexpected HTTP status code for GetGroupSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/AAA/exercisesheet/1',
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
                            'Unexpected HTTP status code for GetGroupSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/group/user/2/exercisesheet/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetGroupSubmissions call'
                            );
    }

    public function GetUserExerciseSubmissions( )
    {
        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/user/1/exercise/1',
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
                            'Unexpected HTTP status code for GetUserExerciseSubmissions call'
                            );
        $this->assertContains( 
                              '{"id":"1","studentId":"1","exerciseId":"1","comment":"eins","accepted":"1","date":"1389643115","flag":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/user/1/exercise/AAA',
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
                            'Unexpected HTTP status code for GetUserExerciseSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/user/AAA/exercise/1',
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
                            'Unexpected HTTP status code for GetUserExerciseSubmissions call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/user/1/exercise/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetUserExerciseSubmissions call'
                            );
    }

    public function AddSubmission( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBSubmission/submission/100',
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
                            'Unexpected HTTP status code for AddSubmission call'
                            );

        // createSubmission($submissionId,$studentId,$fileId,$exerciseId,$comment,$accepted,$date)
        $obj = Submission::createSubmission( 
                                            '100',
                                            '2',
                                            '1',
                                            '1',
                                            null,
                                            null,
                                            null,
                                            null
                                            );

        $result = Request::post( 
                                $this->url . 'DBSubmission/submission',
                                array( 
                                      'SESSION: abc',
                                      'USER: 3',
                                      'DATE: ' . time( )
                                      ),
                                Submission::encodeSubmission( $obj )
                                );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for AddSubmission call'
                            );

        $result = Request::post( 
                                $this->url . 'DBSubmission/submission',
                                array( ),
                                ''
                                );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for AddSubmission call'
                            );
    }

    public function DeleteSubmission( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBSubmission/submission/100',
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
                            'Unexpected HTTP status code for DeleteSubmission call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBSubmission/submission/AAA',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteSubmission call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBSubmission/submission/100',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteSubmission call'
                            );
    }

    public function EditSubmission( )
    {

        // createSubmission($submissionId,$studentId,$fileId,$exerciseId,$comment,$accepted,$date,$flag)
        $obj = Submission::createSubmission( 
                                            '100',
                                            '2',
                                            '1',
                                            '1',
                                            'Neu',
                                            null,
                                            null,
                                            null
                                            );

        $result = Request::put( 
                               $this->url . 'DBSubmission/submission/100',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               Submission::encodeSubmission( $obj )
                               );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for EditSubmission call'
                            );

        $result = Request::put( 
                               $this->url . 'DBSubmission/submission/AAA',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for EditSubmission call'
                            );

        $result = Request::put( 
                               $this->url . 'DBSubmission/submission/100',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for EditSubmission call'
                            );

        $result = Request::get( 
                               $this->url . 'DBSubmission/submission/100',
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
                            'Unexpected HTTP status code for EditSubmission call'
                            );
        $this->assertContains( 
                              '"comment":"Neu"',
                              $result['content']
                              );
    }
}
?>