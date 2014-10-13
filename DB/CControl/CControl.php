<?php 


/**
 * @file CControl.php contains the CControl class
 *
 * @author Till Uhlig
 * @example DB/CControl/LinkSample.json
 * @example DB/CControl/ComponentSample.json
 * @date 2013-2014
 */

require_once ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBRequest.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBJson.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to abstract the "Component" and "ComponentLinkage" table from database
 *
 
 */
class CControl
{

    /**
     * @var Slim $_app the slim object
     */
    private $_app = null;
    
    
    /**
     * @var string $_prefix the prefixes, the class works with (comma separated)
     */
    private static $_prefix = 'component';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return CControl::$_prefix;
    }

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function setPrefix( $value )
    {
        CControl::$_prefix = $value;
    }

    /**
     * the component constructor
     */
    public function __construct( )
    {
        // runs the CConfig
        $com = new CConfig( CControl::getPrefix( ) . ',link,definition', dirname(__FILE__)  );

        // runs the DBSubmission
        if ( $com->used( ) ) return;
            
        // initialize slim
        $this->_app = new \Slim\Slim( );
        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            'application/json'
                                            );
                                            
        // POST AddPlatform
        $this->_app->post( 
                         '/platform',
                         array( 
                               $this,
                               'addPlatform'
                               )
                         );
                         
        // DELETE DeletePlatform
        $this->_app->delete( 
                         '/platform',
                         array( 
                               $this,
                               'deletePlatform'
                               )
                         );
                         
        // GET GetExistsPlatform
        $this->_app->get( 
                         '/link/exists/platform',
                         array( 
                               $this,
                               'getExistsPlatform'
                               )
                         );

        // PUT EditLink
        $this->_app->put( 
                         '/link/:linkid(/)',
                         array( 
                               $this,
                               'editLink'
                               )
                         );

        // DELETE DeleteLink
        $this->_app->delete( 
                            '/link/:linkid(/)',
                            array( 
                                  $this,
                                  'deleteLink'
                                  )
                            );

        // POST SetLink
        $this->_app->post( 
                          '/link(/)',
                          array( 
                                $this,
                                'setLink'
                                )
                          );

        // GET GetLink
        $this->_app->get( 
                         '/link/:linkid(/)',
                         array( 
                               $this,
                               'getLink'
                               )
                         );

        // PUT EditComponent
        $this->_app->put( 
                         '/component/:componentid(/)',
                         array( 
                               $this,
                               'editComponent'
                               )
                         );

        // DELETE DeleteComponent
        $this->_app->delete( 
                            '/component/:componentid(/)',
                            array( 
                                  $this,
                                  'deleteComponent'
                                  )
                            );

        // POST SetComponent
        $this->_app->post( 
                          '/component(/)',
                          array( 
                                $this,
                                'setComponent'
                                )
                          );

        // GET GetComponent
        $this->_app->get( 
                         '/component/:componentid(/)',
                         array( 
                               $this,
                               'getComponent'
                               )
                         );

        // GET GetComponentDefinitions
        $this->_app->get( 
                         '/definition(/)',
                         array( 
                               $this,
                               'getComponentDefinitions'
                               )
                         );

        // GET SendComponentDefinitions
        $this->_app->get( 
                         '(/definition)/send(/)',
                         array( 
                               $this,
                               'sendComponentDefinitions'
                               )
                         );

        // GET GetComponentDefinition
        $this->_app->get( 
                         '/definition/:componentid(/)',
                         array( 
                               $this,
                               'getComponentDefinition'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Edits a specific link.
     *
     * @param $linkid a database linkage identifier
     */
    public function editLink( $linkid )
    {

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $linkid )
                           );

        // decode the received link data, as an object
        $insert = Link::decodeLink( $this->_app->request->getBody( ) );

        // always been an array
        if ( !is_array( $insert ) )
            $insert = array( $insert );

        foreach ( $insert as $in ){

            // generates the update data for the object
            $values = $in->getInsertData( );

            // starts a query
            ob_start();
            eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/PutLink.sql' ));
            $sql = ob_get_contents();
            ob_end_clean();
            $result = DBRequest::request( 
                                         $sql,
                                         false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                         );

            // checks the correctness of the query
            if ( (!isset($result['errno']) || !$result['errno']) && 
                 $result['content'] ){
                $this->_app->response->setStatus( 201 );
                
            } else {
                Logger::Log( 
                            'PUT EditLink failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( 409 );
            }
        }
    }

    /**
     * Deletes a specific link.
     *
     * @param $linkid a database linkage identifier
     */
    public function deleteLink( $linkid )
    {

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $linkid )
                           );

        // starts a query
        ob_start();
        eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/DeleteLink.sql' ));
        $sql = ob_get_contents();
        ob_end_clean();
        $result = DBRequest::request( 
                                     $sql,
                                     false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                     );

        // checks the correctness of the query
        if ( (!isset($result['errno']) || !$result['errno']) && 
             $result['content'] ){
            $this->_app->response->setStatus( 201 );
            
        } else {
            Logger::Log( 
                        'DELETE DeleteLink failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( 409 );
        }
    }

    /**
     * Adds a link.
     */
    public function setLink( )
    {

        // decode the received link data, as an object
        $insert = Link::decodeLink( $this->_app->request->getBody( ) );

        // always been an array
        if ( !is_array( $insert ) )
            $insert = array( $insert );

        foreach ( $insert as $in ){
            $values = $in->getInsertData( );

            // starts a query
            ob_start();
            eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/PostLink.sql' ));
            $sql = ob_get_contents();
            ob_end_clean();
            $result = DBRequest::request( 
                                         $sql,
                                         false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                         );

            // checks the correctness of the query
            if ( (!isset($result['errno']) || !$result['errno']) && 
                 $result['content'] ){

                // sets the new auto-increment id
                $obj = new Link( );
                $obj->setId( $result['insertId'] );

                $this->_app->response->setBody( Link::encodeLink( $obj ) );
                $this->_app->response->setStatus( 201 );
                
            } else {
                Logger::Log( 
                            'POST SetLink failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( 409 );
            }
        }
    }

    /**
     * Returns a specific link.
     *
     * @param $linkid a database linkage identifier
     */
    public function getLink( $linkid )
    {

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $linkid )
                           );

        // starts a query
        ob_start();
        eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/GetLink.sql' ));
        $sql = ob_get_contents();
        ob_end_clean();
        $result = DBRequest::request( 
                                     $sql,
                                     false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                     );

        // checks the correctness of the query
        if ( (!isset($result['errno']) || !$result['errno']) && 
             $result['content'] ){
            $data = DBJson::getRows( $result['content'] );
            $links = DBJson::getResultObjectsByAttributes( 
                                                          $data,
                                                          Link::getDBPrimaryKey( ),
                                                          Link::getDBConvert( )
                                                          );
            $this->_app->response->setBody( Link::encodeLink( $links ) );
            $this->_app->response->setStatus( 200 );
            
        } else {
            Logger::Log( 
                        'GET GetLink failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( 409 );
        }
    }

    /**
     * Edits a specific component.
     *
     * @param $componentid a database component identifier
     */
    public function editComponent( $componentid )
    {

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $componentid )
                           );

        $insert = Component::decodeComponent( $this->_app->request->getBody( ) );

        if ( !is_array( $insert ) )
            $insert = array( $insert );

        foreach ( $insert as $in ){
            $values = $in->getInsertData( );

            // starts a query
            ob_start();
            eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/PutComponent.sql' ));
            $sql = ob_get_contents();
            ob_end_clean();
            $result = DBRequest::request( 
                                         $sql,
                                         false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                         );

            // checks the correctness of the query
            if ( (!isset($result['errno']) || !$result['errno']) && 
                 $result['content'] ){
                $this->_app->response->setStatus( 201 );
                
            } else {
                Logger::Log( 
                            'PUT EditComponent failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( 409 );
            }
        }
    }

    /**
     * Removes a component.
     *
     * @param $componentid a database component identifier
     */
    public function deleteComponent( $componentid )
    {

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $componentid )
                           );

        // starts a query
        ob_start();
        eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/DeleteComponent.sql' ));
        $sql = ob_get_contents();
        ob_end_clean();
        $result = DBRequest::request( 
                                     $sql,
                                     false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                     );

        // checks the correctness of the query
        if ( (!isset($result['errno']) || !$result['errno']) && 
             $result['content'] ){
            $this->_app->response->setStatus( 201 );
            
        } else {
            Logger::Log( 
                        'DELETE DeleteComponent failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( 409 );
        }
    }

    /**
     * Adds a component
     */
    public function setComponent( )
    {
        $insert = Component::decodeComponent( $this->_app->request->getBody( ) );
        if ( !is_array( $insert ) )
            $insert = array( $insert );

        foreach ( $insert as $in ){
            $values = $in->getInsertData( );

            // starts a query
            ob_start();
            eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/PostComponent.sql' ));
            $sql = ob_get_contents();
            ob_end_clean();
            $result = DBRequest::request( 
                                         $sql,
                                         false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                         );

            if ( (!isset($result['errno']) || !$result['errno']) && 
                 $result['content'] ){

                $obj = new Component( );
                $obj->setId( $result['insertId'] );

                $this->_app->response->setBody( Component::encodeComponent( $obj ) );
                $this->_app->response->setStatus( 201 );
                
            } else {
                Logger::Log( 
                            'POST SetComponent failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( 409 );
            }
        }
    }

    /**
     * Returns a specific component.
     *
     * @param $componentid a database component identifier
     */
    public function getComponent( $componentid )
    {

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $componentid )
                           );

        // starts a query
        ob_start();
        eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/GetComponent.sql' ));
        $sql = ob_get_contents();
        ob_end_clean();
        $result = DBRequest::request( 
                                     $sql,
                                     false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                     );

        // checks the correctness of the query
        if ( (!isset($result['errno']) || !$result['errno']) && 
             $result['content'] ){
            $data = DBJson::getRows( $result['content'] );
            $components = DBJson::getResultObjectsByAttributes( 
                                                               $data,
                                                               Component::getDBPrimaryKey( ),
                                                               Component::getDBConvert( )
                                                               );
            $this->_app->response->setBody( Component::encodeComponent( $components ) );
            $this->_app->response->setStatus( 200 );
            
        } else {
            Logger::Log( 
                        'GET GetComponent failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( 409 );
        }
    }

    /**
     * Returns all component definitions.
     */
    public function getComponentDefinitions( )
    {

        // starts a query
        ob_start();
        eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/GetComponentDefinitions.sql' ));
        $sql = ob_get_contents();
        ob_end_clean();
        $result = DBRequest::request( 
                                     $sql,
                                     false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                     );

        // checks the correctness of the query
        if ( (!isset($result['errno']) || !$result['errno']) && 
             $result['content'] ){
            $data = DBJson::getRows( $result['content'] );

            $components = DBJson::getObjectsByAttributes( 
                                                         $data,
                                                         Component::getDBPrimaryKey( ),
                                                         Component::getDBConvert( )
                                                         );
            $links = DBJson::getObjectsByAttributes( 
                                                    $data,
                                                    Link::getDBPrimaryKey( ),
                                                    Link::getDBConvert( )
                                                    );
            $result = DBJson::concatResultObjectLists( 
                                                      $data,
                                                      $components,
                                                      Component::getDBPrimaryKey( ),
                                                      Component::getDBConvert( )['CO_links'],
                                                      $links,
                                                      Link::getDBPrimaryKey( )
                                                      );
            $this->_app->response->setBody( Component::encodeComponent( $result ) );
            $this->_app->response->setStatus( 200 );
            
        } else {
            Logger::Log( 
                        'GET GetComponentDefinitions failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( 409 );
        }
    }

    /**
     * Returns a specific component definition.
     *
     * @param $componentid a database component identifier
     */
    public function getComponentDefinition( $componentid )
    {
        $componentid = DBJson::mysql_real_escape_string( $componentid );
        
        // starts a query
        ob_start();
        eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/GetComponentDefinition.sql' ));
        $sql = ob_get_contents();
        ob_end_clean();
        $result = DBRequest::request( 
                                     $sql,
                                     false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                     );

        // checks the correctness of the query
        if ( (!isset($result['errno']) || !$result['errno']) && 
             $result['content'] ){
            $data = DBJson::getRows( $result['content'] );

            $Components = DBJson::getObjectsByAttributes( 
                                                         $data,
                                                         Component::getDBPrimaryKey( ),
                                                         Component::getDBConvert( )
                                                         );
            $Links = DBJson::getObjectsByAttributes( 
                                                    $data,
                                                    Link::getDBPrimaryKey( ),
                                                    Link::getDBConvert( )
                                                    );
            $result = DBJson::concatResultObjectLists( 
                                                      $data,
                                                      $Components,
                                                      Component::getDBPrimaryKey( ),
                                                      Component::getDBConvert( )['CO_links'],
                                                      $Links,
                                                      Link::getDBPrimaryKey( )
                                                      );
            if ( count( $result ) > 0 )
                $this->_app->response->setBody( Component::encodeComponent( $result[0] ) );
            $this->_app->response->setStatus( 200 );
            
        } else {
            Logger::Log( 
                        'GET GetComponentDefinition failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( 409 );
        }
    }

    /**
     * Initializes all components, with the data, which can be found in database.
     */
    public function sendComponentDefinitions( )
    {
        $this->_app->response->setStatus( 200 );
        // starts a query
        ob_start();
        eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/GetComponentDefinitions.sql' ));
        $sql = ob_get_contents();
        ob_end_clean();
        $result = DBRequest::request( 
                                     $sql,
                                     false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                     );

        // checks the correctness of the query
        if ( (!isset($result['errno']) || !$result['errno']) && 
             $result['content'] ){
            $data = DBJson::getRows( $result['content'] );

            $Components = DBJson::getObjectsByAttributes( 
                                                         $data,
                                                         Component::getDBPrimaryKey( ),
                                                         Component::getDBConvert( )
                                                         );
            $Links = DBJson::getObjectsByAttributes( 
                                                    $data,
                                                    Link::getDBPrimaryKey( ),
                                                    Link::getDBConvert( )
                                                    );
            $objects = DBJson::concatResultObjectLists( 
                                                      $data,
                                                      $Components,
                                                      Component::getDBPrimaryKey( ),
                                                      Component::getDBConvert( )['CO_links'],
                                                      $Links,
                                                      Link::getDBPrimaryKey( )
                                                      );
            
            $request = new Request_MultiRequest();
            $data =  parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     );
                                     
            $tempObjects = array();
            foreach ( $objects as $object ){
                $object = Component::decodeComponent( Component::encodeComponent( $object ) );
                
                // prüfen, welche Komponente auf diesem Server ist
                if (strpos($object->getAddress().'/', $data['PL']['urlExtern'].'/')===false) continue;

                $object->setAddress($data['PL']['url'].substr($object->getAddress(),strlen($data['PL']['urlExtern'])));
                $links = $object->getLinks();
                foreach($links as &$link){
                    if (strpos($link->getAddress().'/', $data['PL']['urlExtern'].'/')===false) continue;
                    $link->setAddress($data['PL']['url'].substr($link->getAddress(),strlen($data['PL']['urlExtern'])));
                }
                $object->setLinks($links);
                
                $result = Request_CreateRequest::createPost( 
                                                            $object->getAddress( ) . '/control',
                                                            array( ),
                                                            Component::encodeComponent( $object )
                                                            );
                $tempObjects[] = $object;
                $request->addRequest($result);
            }
            $results = $request->run();
            $objects = $tempObjects;

            $i=0;
            $res = array();
            foreach ( $objects as $object){
                $object = Component::decodeComponent( Component::encodeComponent( $object ) );
                $result = $results[$i++];
                                   
                $newObject = new Component();
                $newObject->setId($object->getId());
                $newObject->setName($object->getName());
                $newObject->setAddress($object->getAddress());
                $newObject->setStatus($result['status']);
                $res[] = $newObject;

                if ( $result['status'] != 201 ){
                    $add = '';
                    $this->_app->response->setStatus( 409 );
                    if ( isset( $result['content'] ) )
                        $add = $result['content'];

                    Logger::Log( 
                                $result['status'] . '--' . $object->getName( ) . '--' . $object->getAddress( ) . "\n" . $add . "\n",
                                LogLevel::ERROR
                                );
                }
            }
            
            $this->_app->response->setBody( json_encode($res) );
            
        } else {
            Logger::Log( 
                        'GET SendComponentDefinitions failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
        }
    }
    
    /**
     * Returns status code 200, if this component is correctly installed for the platform
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/platform.
     */
    public function getExistsPlatform( )
    {
        Logger::Log( 
                    'starts GET GetExistsPlatform',
                    LogLevel::DEBUG
                    );
                    
        if (!file_exists(dirname(__FILE__).'/config.ini')){
            $this->_app->response->setStatus( 409 );
            $this->_app->stop();
        }

        // starts a query
        ob_start();
        eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/GetExistsPlatform.sql' ));
        $sql = ob_get_contents();
        ob_end_clean();
        $result = DBRequest::request( 
                                     $sql,
                                     false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                     );

        // checks the correctness of the query
        if ( (!isset($result['errno']) || !$result['errno']) && 
             $result['content'] ){

            $this->_app->response->setStatus( 200 );
            $this->_app->response->setBody( '' );
            
        } else {
            Logger::Log( 
                        'GET GetExistsPlatform failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( 409 );
            $this->_app->response->setBody( '' );
            $this->_app->stop( );
        }
    }
    
    /**
     * Removes the component from the platform
     *
     * Called when this component receives an HTTP DELETE request to
     * /platform.
     */
    public function deletePlatform( )
    {
        Logger::Log( 
                    'starts DELETE DeletePlatform',
                    LogLevel::DEBUG
                    );

        // starts a query
        ob_start();
        eval("?>" .  file_get_contents( dirname(__FILE__) . '/Sql/DeletePlatform.sql' ));
        $sql = ob_get_contents();
        ob_end_clean();
        $result = DBRequest::request2( 
                                     $sql,
                                     false,
                                           parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                     );

        // checks the correctness of the query
        if ( (!isset($result['errno']) || !$result['errno'])){

            $this->_app->response->setStatus( 201 );
            $this->_app->response->setBody( '' );
            
        } else {
            Logger::Log( 
                        'DELETE DeletePlatform failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( 409 );
            $this->_app->response->setBody( '' );
            $this->_app->stop( );
        }
    }
    
    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( )
    {
        Logger::Log( 
                    'starts POST AddPlatform',
                    LogLevel::DEBUG
                    );

        // decode the received course data, as an object
        $insert = Platform::decodePlatform( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $insert as $in ){
        
            $file = dirname(__FILE__).'/config.ini';
            $text = "[DB]\n".
                    "db_path = \"".str_replace(array("\\","\""),array("\\\\","\\\""),$in->getDatabaseUrl())."\"\n".
                    "db_user = \"".str_replace(array("\\","\""),array("\\\\","\\\""),$in->getDatabaseOperatorUser())."\"\n".
                    "db_passwd = \"".str_replace(array("\\","\""),array("\\\\","\\\""),$in->getDatabaseOperatorPassword())."\"\n".
                    "db_name = \"".str_replace(array("\\","\""),array("\\\\","\\\""),$in->getDatabaseName())."\"\n".
                    "[PL]\n".
                    "urlExtern = \"".str_replace(array("\\","\""),array("\\\\","\\\""),$in->getExternalUrl())."\"\n".
                    "url = \"".str_replace(array("\\","\""),array("\\\\","\\\""),$in->getBaseUrl())."\"";
            
            if (!@file_put_contents($file,$text)){
                Logger::Log( 
                            'POST AddPlatform failed, config.ini no access',
                            LogLevel::ERROR
                            );

                $this->_app->response->setStatus( 409 );
                $this->_app->stop();
            }       

            // starts a query
            ob_start();
            eval("?>" .  file_get_contents( dirname(__FILE__).'/Sql/AddPlatform.sql' ));
            $sql = ob_get_contents();
            ob_end_clean();
            
            $result = DBRequest::request2( 
                                         $sql,
                                         false,
                                         parse_ini_file( 
                                     dirname(__FILE__).'/config.ini',
                                     TRUE
                                     )
                                         );

            // checks the correctness of the query
            if ( (!isset($result['errno']) || !$result['errno'])){

                $platform = new Platform();
                $platform->setStatus(201);
                $res[] = $platform;
                $this->_app->response->setStatus( 201 );
                
            } else {
                Logger::Log( 
                            'POST AddPlatform failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( 409 );
                $this->_app->response->setBody( Platform::encodePlatform( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( Platform::encodePlatform( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( Platform::encodePlatform( $res ) );
    }
}

 
?>