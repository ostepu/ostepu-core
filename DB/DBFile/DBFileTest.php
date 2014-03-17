<?php 


/**
 * @file DBFileTest.php contains the DBFileTest class
 *
 * @author Till Uhlig
 */

include_once ( '/../../Assistants/Request.php' );
include_once ( '/../../Assistants/Structures.php' );

/**
 * A class, to test the DBFile component
 */
class DBFileTest extends PHPUnit_Framework_TestCase
{
    private $url = '';

    public function testDBFile( )
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

        $this->AddFile( );
        $this->EditFile( );
        $this->RemoveFile( );
        $this->GetFile( );
        $this->GetFileByHash( );
        $this->GetAllFiles( );
    }

    public function GetAllFiles( )
    {
        $result = Request::get( 
                               $this->url . 'DBFile/file',
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
                            'Unexpected HTTP status code for GetAllFiles call'
                            );
        $this->assertContains( 
                              '{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBFile/file',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetAllFiles call'
                            );
    }

    public function GetFileByHash( )
    {
        $result = Request::get( 
                               $this->url . 'DBFile/file/hash/abcdef',
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
                            'Unexpected HTTP status code for GetFileByHash call'
                            );
        $this->assertContains( 
                              '{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBFile/file/hash/abcdef',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetFileByHash call'
                            );
    }

    public function GetFile( )
    {
        $result = Request::get( 
                               $this->url . 'DBFile/file/1',
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
                            'Unexpected HTTP status code for GetFile call'
                            );
        $this->assertContains( 
                              '{"fileId":"1","displayName":"a.pdf","address":"file\/abcdef","timeStamp":"1389643115","fileSize":"100","hash":"abcdef"}',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'DBFile/file/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for GetFile call'
                            );

        $result = Request::get( 
                               $this->url . 'DBFile/file/AAA',
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
                            'Unexpected HTTP status code for GetFile call'
                            );
    }

    public function AddFile( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBFile/file/100',
                                  array( 
                                        'SESSION: abc',
                                        'USER: 3',
                                        'DATE: ' . time( )
                                        ),
                                  ''
                                  );

        // createFile($fileId,$displayName,$address,$timeStamp,$fileSize,$hash)
        $obj = File::createFile( 
                                '100',
                                'datei.pdf',
                                'file/abcdefghij',
                                null,
                                '123',
                                'abcdefghij'
                                );

        $result = Request::post( 
                                $this->url . 'DBFile/file',
                                array( 
                                      'SESSION: abc',
                                      'USER: 3',
                                      'DATE: ' . time( )
                                      ),
                                File::encodeFile( $obj )
                                );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for AddFile call'
                            );
        $this->assertContains( 
                              '{"fileId":100',
                              $result['content']
                              );

        $result = Request::post( 
                                $this->url . 'DBFile/file',
                                array( ),
                                File::encodeFile( $obj )
                                );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for AddFile call'
                            );
    }

    public function RemoveFile( )
    {
        $result = Request::delete( 
                                  $this->url . 'DBFile/file/100',
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
                            'Unexpected HTTP status code for RemoveFile call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBFile/file/AAA',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for RemoveFile call'
                            );

        $result = Request::delete( 
                                  $this->url . 'DBFile/file/100',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for RemoveFile call'
                            );
    }

    public function EditFile( )
    {

        // createFile($fileId,$displayName,$address,$timeStamp,$fileSize,$hash)
        $obj = File::createFile( 
                                '100',
                                'datei2.pdf',
                                'file/abcdefghij',
                                null,
                                '123',
                                'abcdefghij'
                                );

        $result = Request::put( 
                               $this->url . 'DBFile/file/100',
                               array( 
                                     'SESSION: abc',
                                     'USER: 3',
                                     'DATE: ' . time( )
                                     ),
                               Course::encodeCourse( $obj )
                               );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for EditFile call'
                            );

        $result = Request::put( 
                               $this->url . 'DBFile/file/AAA',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for EditFile call'
                            );

        $result = Request::put( 
                               $this->url . 'DBFile/file/100',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            401,
                            $result['status'],
                            'Unexpected HTTP status code for EditFile call'
                            );

        $result = Request::get( 
                               $this->url . 'DBFile/file/100',
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
                            'Unexpected HTTP status code for EditFile call'
                            );
        $this->assertContains( 
                              '"displayName":"datei2.pdf"',
                              $result['content']
                              );
    }
}
