<?php 


/**
 * @file DBSession.php contains the DBSession class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBSession/SessionSample.json
 * @date 2013-2014
 */

require_once ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBJson.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to abstract the "Session" table from database
 */
class DBSession
{

    /**
     * @var Slim $_app the slim object
     */
    private $_app = null;

    /**
     * @var Component $_conf the component data object
     */
    private $_conf = null;

    /**
     * @var Link[] $query a list of links to a query component
     */
    private $query = array( );
    private $query2 = array( );

    /**
     * @var string $_prefix the prefixes, the class works with (comma separated)
     */
    private static $_prefix = 'session';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBSession::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBSession::$_prefix = $value;
    }

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct( )
    {
        // runs the CConfig
        $com = new CConfig( DBSession::getPrefix( ), dirname(__FILE__) );

        // runs the DBSession
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
            
        // initialize component
        $this->_conf = $conf;
        $this->query = array( CConfig::getLink( 
                                               $conf->getLinks( ),
                                               'out'
                                               ) );
        $this->query2 = array( CConfig::getLink( 
                                               $conf->getLinks( ),
                                               'out2'
                                               ) );

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
                         
        // PUT EditSession
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '(/session)/:seid(/)',
                         array( 
                               $this,
                               'editSession'
                               )
                         );

        // DELETE RemoveSession
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '(/session)/:seid(/)',
                            array( 
                                  $this,
                                  'removeSession'
                                  )
                            );

        // PUT EditUserSession
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '/user/:userid(/)',
                         array( 
                               $this,
                               'editUserSession'
                               )
                         );

        // DELETE RemoveUserSession
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '/user/:userid(/)',
                            array( 
                                  $this,
                                  'removeUserSession'
                                  )
                            );

        // POST AddSession
        $this->_app->post( 
                          '/' . $this->getPrefix( ) . '(/)',
                          array( 
                                $this,
                                'addSession'
                                )
                          );

        // GET GetUserSession
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/user/:userid(/)',
                         array( 
                               $this,
                               'getUserSession'
                               )
                         );

        // GET GetSessionUser
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/session)/:seid(/)',
                         array( 
                               $this,
                               'getSessionUser'
                               )
                         );

        // GET GetAllSessions
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/session)(/)',
                         array( 
                               $this,
                               'getAllSessions'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Edits a session identified by a sessionId.
     *
     * Called when this component receives an HTTP PUT request to
     * /session/session/$seid(/) or /session/$seid(/).
     * The request body should contain a JSON object representing the
     * sessions's new attributes.
     *
     * @param string $seid The id of the session which is being updated.
     */
    public function editSession( $seid )
    {
        Logger::Log( 
                    'starts PUT EditSession',
                    LogLevel::DEBUG
                    );

        $seid = DBJson::mysql_real_escape_string( $seid );

        $insert = Session::decodeSession( $this->_app->request->getBody( ) );
        if ( !is_array( $insert ) )
            $insert = array( $insert );

        foreach ( $insert as $in ){

            // generates the update data for the object
            $data = $in->getInsertData( );

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/EditSession.sql',
                                                  array( 
                                                        'seid' => $seid,
                                                        'values' => $data
                                                        )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'PUT EditSession failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes a session identified by a sessionId.
     *
     * Called when this component receives an HTTP DELETE request to
     * /session/session/$seid(/) or /session/$seid(/).
     *
     * @param string $seid The id of the session which is being deleted.
     */
    public function removeSession( $seid )
    {
        Logger::Log( 
                    'starts DELETE RemoveSession',
                    LogLevel::DEBUG
                    );

        $seid = DBJson::mysql_real_escape_string( $seid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteSession.sql',
                                              array( 'seid' => $seid )
                                              );

        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $this->_app->response->setStatus( 201 );
            if ( isset( $result['headers']['Content-Type'] ) )
                $this->_app->response->headers->set( 
                                                    'Content-Type',
                                                    $result['headers']['Content-Type']
                                                    );
            
        } else {
            Logger::Log( 
                        'DELETE RemoveSession failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Edits a session identified by an userId.
     *
     * Called when this component receives an HTTP PUT request to
     * /session/user/$userid(/).
     * The request body should contain a JSON object representing the
     * sessions's new attributes.
     *
     * @param int $userid The id of the user that is being updated.
     */
    public function editUserSession( $userid )
    {
        Logger::Log( 
                    'starts PUT RemoveSession',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $userid )
                           );

        // decode the received session data, as an object
        $insert = Session::decodeSession( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        foreach ( $insert as $in ){
            $data = $in->getInsertData( );

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/EditUserSession.sql',
                                                  array( 
                                                        'userid' => $userid,
                                                        'values' => $data
                                                        )
                                                  );

            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'PUT RemoveSession failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes a session identified by an userId.
     *
     * Called when this component receives an HTTP DELETE request to
     * /session/user/$userid(/).
     *
     * @param int $userid The id of the user that is being deleted.
     */
    public function removeUserSession( $userid )
    {
        Logger::Log( 
                    'starts DELETE RemoveUserSession',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $userid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteUserSession.sql',
                                              array( 'userid' => $userid )
                                              );

        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $this->_app->response->setStatus( 201 );
            if ( isset( $result['headers']['Content-Type'] ) )
                $this->_app->response->headers->set( 
                                                    'Content-Type',
                                                    $result['headers']['Content-Type']
                                                    );
            
        } else {
            Logger::Log( 
                        'DELETE RemoveUserSession failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds a session.
     *
     * Called when this component receives an HTTP POST request to
     * /session(/).
     * The request body should contain a JSON object representing the new session.
     */
    public function addSession( )
    {
        Logger::Log( 
                    'starts POST AddSession',
                    LogLevel::DEBUG
                    );

        // decode the received session data, as an object
        $insert = Session::decodeSession( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        foreach ( $insert as $in ){

            // generates the insert data for the object
            $userid = $in->getUser( );
            $sessionid = $in->getSession( );
            DBJson::checkInput( 
                               $this->_app,
                               ctype_digit( $userid )
                               );

            $sessionid = DBJson::mysql_real_escape_string( $sessionid );

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/AddSession.sql',
                                                  array( 
                                                        'userid' => $userid,
                                                        'sessionid' => $sessionid
                                                        ),
                                                  false
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'POST AddSession failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    public function get( 
                        $functionName,
                        $sqlFile,
                        $userid,
                        $courseid,
                        $esid,
                        $eid,
                        $seid,
                        $mid,
                        $singleResult = false,
                        $checkSession = true
                        )
    {
        Logger::Log( 
                    'starts GET ' . $functionName,
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        $seid = DBJson::mysql_real_escape_string( $seid );
        DBJson::checkInput( 
                           $this->_app,
                           $userid == '' ? true : ctype_digit( $userid ),
                           $courseid == '' ? true : ctype_digit( $courseid ),
                           $esid == '' ? true : ctype_digit( $esid ),
                           $eid == '' ? true : ctype_digit( $eid ),
                           $mid == '' ? true : ctype_digit( $mid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              $sqlFile,
                                              array( 
                                                    'userid' => $userid,
                                                    'courseid' => $courseid,
                                                    'esid' => $esid,
                                                    'eid' => $eid,
                                                    'seid' => $seid,
                                                    'mid' => $mid
                                                    ),
                                              $checkSession
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );

            if ( $query->getNumRows( ) > 0 ){
                $res = Session::ExtractSession( 
                                               $query->getResponse( ),
                                               $singleResult
                                               );
                $this->_app->response->setBody( Session::encodeSession( $res ) );

                $this->_app->response->setStatus( 200 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );

                $this->_app->stop( );
                
            } else 
                $result['status'] = 404;
        }

        Logger::Log( 
                    'GET ' . $functionName . ' failed',
                    LogLevel::ERROR
                    );
        $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
        $this->_app->response->setBody( Session::encodeSession( new Session( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns a session identified by an userId.
     *
     * Called when this component receives an HTTP GET request to
     * /session/user/$userid(/).
     *
     * @param int $userid The id of the user.
     */
    public function getUserSession( $userid )
    {
        $this->get( 
                   'GetUserSession',
                   dirname(__FILE__) . '/Sql/GetUserSession.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $seid ) ? $seid : '',
                   isset( $mid ) ? $mid : '',
                   true
                   );
    }

    /**
     * Returns a session identified by a sessionId.
     *
     * Called when this component receives an HTTP GET request to
     * /session/session/$seid(/) or /session/$seid(/).
     *
     * @param string $seid The id or the session.
     */
    public function getSessionUser( $seid )
    {
        $this->get( 
                   'GetSessionUser',
                   dirname(__FILE__) . '/Sql/GetSessionUser.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $seid ) ? $seid : '',
                   isset( $mid ) ? $mid : '',
                   true
                   );
    }

    /**
     * Returns all sessions.
     *
     * Called when this component receives an HTTP GET request to
     * /session/session(/) or /session(/).
     */
    public function getAllSessions( )
    {
        $this->get( 
                   'GetAllSessions',
                   dirname(__FILE__) . '/Sql/GetAllSessions.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $seid ) ? $seid : '',
                   isset( $mid ) ? $mid : ''
                   );
    }
    
    /**
     * Returns status code 200, if this component is correctly installed for the platform
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/platform.
     */
    public function getExistsPlatform( )
    {
        $this->get( 
                   'GetExistsPlatform',
                   dirname(__FILE__) . '/Sql/GetExistsPlatform.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $seid ) ? $seid : '',
                   isset( $mid ) ? $mid : '',
                   true,
                   false
                   );
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

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query2,
                                              dirname(__FILE__) . '/Sql/DeletePlatform.sql',
                                              array( ),
                                              false
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){

            $this->_app->response->setStatus( 201 );
            $this->_app->response->setBody( '' );
            if ( isset( $result['headers']['Content-Type'] ) )
                $this->_app->response->headers->set( 
                                                    'Content-Type',
                                                    $result['headers']['Content-Type']
                                                    );
            
        } else {
            Logger::Log( 
                        'DELETE DeletePlatform failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
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
        
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query2,
                                                  dirname(__FILE__) . '/Sql/AddPlatform.sql',
                                                  array( 'object' => $in ),
                                                  false
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                $res[] = $in;
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'POST AddPlatform failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
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

 