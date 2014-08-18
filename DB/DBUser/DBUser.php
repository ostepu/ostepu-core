<?php 


/**
 * @file DBUser.php Contains the DBUser class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBUser/UserSample.json
 * @date 2013-2014
 */

require_once ( '../../Assistants/Slim/Slim.php' );
include_once ( '../../Assistants/Structures.php' );
include_once ( '../../Assistants/Request.php' );
include_once ( '../../Assistants/DBRequest.php' );
include_once ( '../../Assistants/DBJson.php' );
include_once ( '../../Assistants/CConfig.php' );
include_once ( '../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to abstract the "User" table from database
 */
class DBUser
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
    private static $_prefix = 'user';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBUser::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBUser::$_prefix = $value;
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
        $com = new CConfig( DBUser::getPrefix( ) );

        // runs the DBUser
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
        $this->_app = new \Slim\Slim( array( 'debug' => false ) );
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
                         
        // PUT EditUser
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '(/user)/:userid(/)',
                         array( 
                               $this,
                               'editUser'
                               )
                         );

        // DELETE RemoveUser
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '(/user)/:userid(/)',
                            array( 
                                  $this,
                                  'removeUser'
                                  )
                            );

        // DELETE RemoveUserPermanent
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '(/user)/:userid/permanent(/)',
                            array( 
                                  $this,
                                  'removeUserPermanent'
                                  )
                            );

        // POST AddUser
        $this->_app->post( 
                          '/' . $this->getPrefix( ) . '(/)',
                          array( 
                                $this,
                                'addUser'
                                )
                          );

        // GET GetUsers
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/user)(/)',
                         array( 
                               $this,
                               'getUsers'
                               )
                         );

        // GET GetIncreaseUserFailedLogin
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/user)/:userid/IncFailedLogin(/)',
                         array( 
                               $this,
                               'getIncreaseUserFailedLogin'
                               )
                         );

        // GET GetUser
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/user)/:userid(/)',
                         array( 
                               $this,
                               'getUser'
                               )
                         );

        // GET GetCourseUserByStatus
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:courseid/status/:statusid(/)',
                         array( 
                               $this,
                               'getCourseUserByStatus'
                               )
                         );

        // GET GetCourseMember
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:courseid(/)',
                         array( 
                               $this,
                               'getCourseMember'
                               )
                         );

        // GET GetGroupMember
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/group/user/:userid/exercisesheet/:esid(/)',
                         array( 
                               $this,
                               'getGroupMember'
                               )
                         );

        // GET GetUserByStatus
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/status/:statusid(/)',
                         array( 
                               $this,
                               'getUserByStatus'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Edits a user.
     *
     * Called when this component receives an HTTP PUT request to
     * /user/$userid(/) or /user/user/$userid(/).
     * The request body should contain a JSON object representing the user's new
     * attributes.
     *
     * @param string $userid The id or the username of the user that is being updated.
     */
    public function editUser( $userid )
    {
        Logger::Log( 
                    'starts PUT EditUser',
                    LogLevel::DEBUG
                    );

        $userid = DBJson::mysql_real_escape_string( $userid );

        // decode the received user data, as an object
        $insert = User::decodeUser( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        foreach ( $insert as $in ){

            // generates the update data for the object
            $data = $in->getInsertData( );

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  'Sql/EditUser.sql',
                                                  array( 
                                                        'userid' => $userid,
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
                            'PUT EditUser failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes a user (updates the user flag = 0).
     *
     * Called when this component receives an HTTP DELETE request to
     * /user/$userid(/) or /user/user/$userid(/).
     *
     * @param string $userid The id or the username of the user that is being deleted.
     */
    public function removeUser( $userid )
    {
        Logger::Log( 
                    'starts DELETE RemoveUser',
                    LogLevel::DEBUG
                    );

        $userid = DBJson::mysql_real_escape_string( $userid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              'Sql/DeleteUser.sql',
                                              array( 'userid' => $userid )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){

            if ( isset( $result['headers']['Content-Type'] ) )
                $this->_app->response->headers->set( 
                                                    'Content-Type',
                                                    $result['headers']['Content-Type']
                                                    );

            Logger::Log( 
                        'DELETE RemoveUser ok',
                        LogLevel::DEBUG
                        );
            $this->_app->response->setStatus( 201 );
            $this->_app->stop( );
            
        } else {
            Logger::Log( 
                        'DELETE RemoveUser failed',
                        LogLevel::ERROR
                        );

            $this->_app->response->setBody( User::encodeUser( new User( ) ) );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Deletes a user permanent.
     *
     * Called when this component receives an HTTP DELETE request to
     * /user/$userid/permanent(/) or /user/user/$userid/permanent(/).
     *
     * @param string $userid The id or the username of the user that is being deleted.
     */
    public function removeUserPermanent( $userid )
    {
        Logger::Log( 
                    'starts DELETE RemoveUserPermanent',
                    LogLevel::DEBUG
                    );

        $userid = DBJson::mysql_real_escape_string( $userid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              'Sql/DeleteUserPermanent.sql',
                                              array( 'userid' => $userid )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){

            if ( isset( $result['headers']['Content-Type'] ) )
                $this->_app->response->headers->set( 
                                                    'Content-Type',
                                                    $result['headers']['Content-Type']
                                                    );

            Logger::Log( 
                        'DELETE RemoveUserPermanent ok',
                        LogLevel::DEBUG
                        );
            $this->_app->response->setStatus( 201 );
            $this->_app->stop( );
            
        } else {
            Logger::Log( 
                        'DELETE RemoveUserPermanent failed',
                        LogLevel::ERROR
                        );

            $this->_app->response->setBody( User::encodeUser( new User( ) ) );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds a user and then returns the created user.
     *
     * Called when this component receives an HTTP POST request to
     * /user(/).
     * The request body should contain a JSON object representing the new user.
     */
    public function addUser( )
    {
        Logger::Log( 
                    'starts POST AddUser',
                    LogLevel::DEBUG
                    );

        // decode the received user data, as an object
        $insert = User::decodeUser( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $insert as $in ){

            // generates the insert data for the object
            $data = $in->getInsertData( );

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  'Sql/AddUser.sql',
                                                  array( 'values' => $data ),
                                                  false
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new User( );
                $obj->setId( $queryResult->getInsertId( ) );

                $res[] = $obj;
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'POST AddUser failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( User::encodeUser( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( User::encodeUser( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( User::encodeUser( $res ) );
    }

    public function get( 
                        $functionName,
                        $sqlFile,
                        $userid,
                        $courseid,
                        $esid,
                        $eid,
                        $suid,
                        $statusid,
                        $singleResult = false,
                        $checkSession = true
                        )
    {
        Logger::Log( 
                    'starts GET ' . $functionName,
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        $userid = DBJson::mysql_real_escape_string( $userid );

        DBJson::checkInput( 
                           $this->_app,
                           $courseid == '' ? true : ctype_digit( $courseid ),
                           $esid == '' ? true : ctype_digit( $esid ),
                           $eid == '' ? true : ctype_digit( $eid ),
                           $suid == '' ? true : ctype_digit( $suid ),
                           $statusid == '' ? true : ctype_digit( $statusid )
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
                                                    'suid' => $suid,
                                                    'statusid' => $statusid
                                                    ),
                                              $checkSession
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );

            if ( $query->getNumRows( ) > 0 ){
                $res = User::ExtractUser( 
                                         $query->getResponse( ),
                                         $singleResult
                                         );
                $this->_app->response->setBody( User::encodeUser( $res ) );

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
        $this->_app->response->setBody( User::encodeUser( new User( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns all users.
     *
     * Called when this component receives an HTTP GET request to
     * /user(/) or /user/user(/).
     */
    public function getUsers( )
    {
        $this->get( 
                   'GetUsers',
                   'Sql/GetUsers.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $statusid ) ? $statusid : ''
                   );
    }

    /**
     * Returns a user.
     *
     * Called when this component receives an HTTP GET request to
     * /user/$userid(/) or user/user/$userid(/).
     *
     * @param string $userid The id or the username of the user that should be returned.
     */
    public function getUser( $userid )
    {
        $this->get( 
                   'GetUser',
                   'Sql/GetUser.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $statusid ) ? $statusid : '',
                   true,
                   false
                   );
    }

    /**
     * Increases the number of failed login attempts of a user and then returns the user.
     *
     * Called when this component receives an HTTP GET request to
     * /user/$userid/IncFailedLogin(/) or /user/user/$userid/IncFailedLogin(/).
     *
     * @param string $userid The id or the username of the user.
     */
    public function getIncreaseUserFailedLogin( $userid )
    {
        $this->get( 
                   'GetIncreaseUserFailedLogin',
                   'Sql/GetIncreaseUserFailedLogin.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $statusid ) ? $statusid : '',
                   true,
                   false
                   );
    }

    /**
     * Returns all users of a course.
     *
     * Called when this component receives an HTTP GET request to
     * /user/course/$courseid(/).
     *
     * @param int $courseid The id or the course.
     */
    public function getCourseMember( $courseid )
    {
        $this->get( 
                   'GetCourseMember',
                   'Sql/GetCourseMember.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $statusid ) ? $statusid : ''
                   );
    }

    /**
     * Returns all members of the group the user is part of
     * regarding a specific exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /user/group/user/$userid/exercisesheet/$esid(/).
     *
     * @param string $userid The id or the username of the user.
     * @param int $esid The id of the exercise sheet.
     */
    public function getGroupMember( 
                                   $userid,
                                   $esid
                                   )
    {
        $this->get( 
                   'GetGroupMember',
                   'Sql/GetGroupMember.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $statusid ) ? $statusid : ''
                   );
    }

    /**
     * Returns all users with a given status.
     *
     * Called when this component receives an HTTP GET request to
     * /user/status/$statusid(/).
     *
     * @param string $statusid The status the users should have.
     */
    public function getUserByStatus( $statusid )
    {
        $this->get( 
                   'GetUserByStatus',
                   'Sql/GetUserByStatus.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $statusid ) ? $statusid : ''
                   );
    }

    /**
     * Returns all users with a given status which are members of a
     * specific course.
     *
     * Called when this component receives an HTTP GET request to
     * /course/$courseid/status/$statusid(/).
     *
     * @param string $courseid The courseid of the course.
     * @param string $statusid The status the users should have.
     */
    public function getCourseUserByStatus( 
                                          $courseid,
                                          $statusid
                                          )
    {
        $this->get( 
                   'GetCourseUserByStatus',
                   'Sql/GetCourseUserByStatus.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $statusid ) ? $statusid : ''
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
                   'Sql/GetExistsPlatform.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $statusid ) ? $statusid : '',
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
                                              'Sql/DeletePlatform.sql',
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
                                                  'Sql/AddPlatform.sql',
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

 
?>