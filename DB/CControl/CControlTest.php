<?php 


/**
 * @file CControlTest.php contains the CControlTest class
 *
 * @author Till Uhlig
 */

include_once ( '/../../Assistants/Request.php' );
include_once ( '/../../Assistants/Structures.php' );

/**
 * A class, to test the CControl component
 */
class CControlTest extends PHPUnit_Framework_TestCase
{
    private $url = '';

    public function testCControl( )
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

        $this->AddLink( );
        $this->EditLink( );
        $this->DeleteLink( );
        $this->GetLink( );

        $this->AddComponent( );
        $this->EditComponent( );
        $this->DeleteComponent( );
        $this->GetComponent( );

        $this->GetComponentDefinitions( );
        $this->GetComponentDefinition( );
        $this->SendComponentDefinitions( );
    }

    public function GetLink( )
    {
        $result = Request::get( 
                               $this->url . 'CControl/link/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            200,
                            $result['status'],
                            'Unexpected HTTP status code for GetLink call'
                            );

        $this->assertContains( 
                              '"id":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'CControl/link/AAA',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for GetLink call'
                            );
    }

    public function AddLink( )
    {
        $result = Request::delete( 
                                  $this->url . 'CControl/link/100',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for AddLink call'
                            );

        // createLink($id,$owner,$target,$name,$relevanz)
        $obj = Link::createLink( 
                                '100',
                                '1',
                                '5',
                                'Link',
                                ''
                                );

        $result = Request::post( 
                                $this->url . 'CControl/link',
                                array( ),
                                Link::encodeLink( $obj )
                                );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for AddLink call'
                            );
    }

    public function EditLink( )
    {

        // createLink($id,$owner,$target,$name,$relevanz)
        $obj = Link::createLink( 
                                '100',
                                '1',
                                '10',
                                'NeuLink',
                                ''
                                );

        $result = Request::put( 
                               $this->url . 'CControl/link/100',
                               array( ),
                               Link::encodeLink( $obj )
                               );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for EditLink call'
                            );

        $result = Request::put( 
                               $this->url . 'CControl/link/AAA',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for EditLink call'
                            );

        $result = Request::get( 
                               $this->url . 'CControl/link/100',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            200,
                            $result['status'],
                            'Unexpected HTTP status code for EditLink call'
                            );
        $this->assertContains( 
                              '"name":"NeuLink"',
                              $result['content']
                              );
    }

    public function DeleteLink( )
    {
        $result = Request::delete( 
                                  $this->url . 'CControl/link/100',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteLink call'
                            );

        $result = Request::delete( 
                                  $this->url . 'CControl/link/AAA',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteLink call'
                            );
    }

    public function GetComponent( )
    {
        $result = Request::get( 
                               $this->url . 'CControl/component/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            200,
                            $result['status'],
                            'Unexpected HTTP status code for GetComponent call'
                            );
        $this->assertContains( 
                              '"id":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'CControl/component/AAA',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for GetComponent call'
                            );
    }

    public function AddComponent( )
    {
        $result = Request::delete( 
                                  $this->url . 'CControl/component/100',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for AddComponent call'
                            );

        // createComponent($id,$name,$address,$option)
        $obj = Component::createComponent( 
                                          '100',
                                          'Component',
                                          'DB/Component',
                                          ''
                                          );

        $result = Request::post( 
                                $this->url . 'CControl/component',
                                array( ),
                                Component::encodeComponent( $obj )
                                );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for AddComponent call'
                            );
    }

    public function EditComponent( )
    {

        // createComponent($id,$name,$address,$option)
        $obj = Component::createComponent( 
                                          '100',
                                          'NeuComponent',
                                          'DB/Component',
                                          ''
                                          );

        $result = Request::put( 
                               $this->url . 'CControl/component/100',
                               array( ),
                               Component::encodeComponent( $obj )
                               );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for EditComponent call'
                            );

        $result = Request::put( 
                               $this->url . 'CControl/component/AAA',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for EditComponent call'
                            );

        $result = Request::get( 
                               $this->url . 'CControl/component/100',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            200,
                            $result['status'],
                            'Unexpected HTTP status code for EditComponent call'
                            );
        $this->assertContains( 
                              '"name":"NeuComponent"',
                              $result['content']
                              );
    }

    public function DeleteComponent( )
    {
        $result = Request::delete( 
                                  $this->url . 'CControl/component/100',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            201,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteComponent call'
                            );

        $result = Request::delete( 
                                  $this->url . 'CControl/component/AAA',
                                  array( ),
                                  ''
                                  );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for DeleteComponent call'
                            );
    }

    public function GetComponentDefinitions( )
    {
        $result = Request::get( 
                               $this->url . 'CControl/definition',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            200,
                            $result['status'],
                            'Unexpected HTTP status code for GetSheetInvitations call'
                            );
        $this->assertContains( 
                              '"id":"1"',
                              $result['content']
                              );
    }

    public function GetComponentDefinition( )
    {
        $result = Request::get( 
                               $this->url . 'CControl/definition/1',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            200,
                            $result['status'],
                            'Unexpected HTTP status code for GetSheetInvitations call'
                            );
        $this->assertContains( 
                              '"id":"1"',
                              $result['content']
                              );

        $result = Request::get( 
                               $this->url . 'CControl/definition/AAA',
                               array( ),
                               ''
                               );
        $this->assertEquals( 
                            412,
                            $result['status'],
                            'Unexpected HTTP status code for GetSheetInvitations call'
                            );
    }

    public function SendComponentDefinitions( )
    {

        // no test available
    }
}
?>