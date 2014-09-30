<?php 


/**
 * @file DBInvitation.php contains the DBInvitation class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBInvitation/InvitationSample.json
 * @date 2013-2014
 */

require_once ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBJson.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBRequest.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to abstract the "ExerciseType" table from database
 */
class DBInvitation
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
    private static $_prefix = 'invitation';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBInvitation::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBInvitation::$_prefix = $value;
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
        $com = new CConfig( DBInvitation::getPrefix( ), dirname(__FILE__) );

        // runs the DBInvitation
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
                         
        // PUT EditInvitation
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '/user/:userid/exercisesheet/:esid/user/:memberid(/)',
                         array( 
                               $this,
                               'editInvitation'
                               )
                         );

        // DELETE DeleteInvitation
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '/user/:userid/exercisesheet/:esid/user/:memberid(/)',
                            array( 
                                  $this,
                                  'deleteInvitation'
                                  )
                            );

        // POST AddInvitation
        $this->_app->post( 
                          '/' . $this->getPrefix( ) . '(/)',
                          array( 
                                $this,
                                'addInvitation'
                                )
                          );

        // GET GetLeaderInvitations
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/leader/user/:userid(/)',
                         array( 
                               $this,
                               'getLeaderInvitations'
                               )
                         );

        // GET GetMemberInvitations
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/member/user/:userid(/)',
                         array( 
                               $this,
                               'getMemberInvitations'
                               )
                         );

        // GET GetAllInvitations
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/invitation)(/)',
                         array( 
                               $this,
                               'getAllInvitations'
                               )
                         );

        // GET GetSheetLeaderInvitations
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/leader/exercisesheet/:esid/user/:userid(/)',
                         array( 
                               $this,
                               'getSheetLeaderInvitations'
                               )
                         );

        // GET GetSheetMemberInvitations
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/member/exercisesheet/:esid/user/:userid(/)',
                         array( 
                               $this,
                               'getSheetMemberInvitations'
                               )
                         );

        // GET GetSheetInvitations
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercisesheet/:esid(/)',
                         array( 
                               $this,
                               'getSheetInvitations'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Edits an invitation.
     *
     * Called when this component receives an HTTP PUT request to
     * /invitation/user/$userid/exercisesheet/$esid/user/$memberid(/)
     * The request body should contain a JSON object representing the
     * invitations's new attributes.
     *
     * @param int $userid The id of the user that invites a new user.
     * @param int $esid The id of the exercise sheet the group belongs to.
     * @param int $memberid The id of the user that is invited.
     */
    public function editInvitation( 
                                   $userid,
                                   $esid,
                                   $memberid
                                   )
    {
        Logger::Log( 
                    'starts PUT EditInvitation',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $userid ),
                           ctype_digit( $esid ),
                           ctype_digit( $memberid )
                           );

        // decode the received invitation data, as an object
        $insert = Invitation::decodeInvitation( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/EditInvitation.sql',
                                                  array( 
                                                        'userid' => $userid,
                                                        'esid' => $esid,
                                                        'memberid' => $memberid,
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
                            'PUT EditInvitation failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes an invitation.
     *
     * Called when this component receives an HTTP DELETE request to
     * /invitation/user/$userid/exercisesheet/$esid/user/$memberid(/)
     *
     * @param int $userid The id of the user that invites a new user.
     * @param int $esid The id of the exercise sheet the group belongs to.
     * @param int $memberid The id of the user that is invited.
     */
    public function deleteInvitation( 
                                     $userid,
                                     $esid,
                                     $memberid
                                     )
    {
        Logger::Log( 
                    'starts DELETE DeleteInvitation',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $userid ),
                           ctype_digit( $esid ),
                           ctype_digit( $memberid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteInvitation.sql',
                                              array( 
                                                    'userid' => $userid,
                                                    'esid' => $esid,
                                                    'memberid' => $memberid
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
                        'DELETE DeleteInvitation failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds an invitation.
     *
     * Called when this component receives an HTTP POST request to
     * /invitation(/).
     * The request body should contain a JSON object representing the
     * invitations's attributes.
     */
    public function addInvitation( )
    {
        Logger::Log( 
                    'starts POST AddInvitation',
                    LogLevel::DEBUG
                    );

        // decode the received invitation data, as an object
        $insert = Invitation::decodeInvitation( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        foreach ( $insert as $in ){

            // generates the insert data for the object
            $data = $in->getInsertData( );

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/AddInvitation.sql',
                                                  array( 'values' => $data )
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
                            'POST AddInvitation failed',
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
                        $suid,
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
        DBJson::checkInput( 
                           $this->_app,
                           $userid == '' ? true : ctype_digit( $userid ),
                           $courseid == '' ? true : ctype_digit( $courseid ),
                           $esid == '' ? true : ctype_digit( $esid ),
                           $eid == '' ? true : ctype_digit( $eid ),
                           $suid == '' ? true : ctype_digit( $suid ),
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
                                                    'suid' => $suid,
                                                    'mid' => $mid
                                                    ),
                                              $checkSession
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );

            if ( $query->getNumRows( ) > 0 ){
                $res = Invitation::ExtractInvitation( 
                                                     $query->getResponse( ),
                                                     $singleResult
                                                     );
                $this->_app->response->setBody( Invitation::encodeInvitation( $res ) );

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
        $this->_app->response->setBody( Invitation::encodeInvitation( new Invitation( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns all invitations.
     *
     * Called when this component receives an HTTP GET request to
     * /invitation(/) or /invitation/invitation(/).
     */
    public function getAllInvitations( )
    {
        $this->get( 
                   'GetAllInvitations',
                   dirname(__FILE__) . '/Sql/GetAllInvitations.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $mid ) ? $mid : ''
                   );
    }

    /**
     * Returns all invitations the user created.
     *
     * Called when this component receives an HTTP GET request to
     * /invitation/leader/user/$userid(/).
     *
     * @param int $userid The id of the user that created the returned invitations.
     */
    public function getLeaderInvitations( $userid )
    {
        $this->get( 
                   'GetLeaderInvitations',
                   dirname(__FILE__) . '/Sql/GetLeaderInvitations.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $mid ) ? $mid : ''
                   );
    }

    /**
     * Returns all invitations to which the user is invited to.
     *
     * Called when this component receives an HTTP GET request to
     * /invitation/member/user/$userid(/).
     *
     * @param int $userid The id of the user.
     */
    public function getMemberInvitations( $userid )
    {
        $this->get( 
                   'GetMemberInvitations',
                   dirname(__FILE__) . '/Sql/GetMemberInvitations.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $mid ) ? $mid : ''
                   );
    }

    /**
     * Returns all invitations the user created regarding a
     * specific exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /invitation/leader/exercisesheet/$esid/user/$userid(/).
     *
     * @param int $esid The id of the exercise sheet.
     * @param int $userid The id of the user that created the returned invitations.
     */
    public function getSheetLeaderInvitations( 
                                              $esid,
                                              $userid
                                              )
    {
        $this->get( 
                   'GetSheetLeaderInvitations',
                   dirname(__FILE__) . '/Sql/GetSheetLeaderInvitations.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $mid ) ? $mid : ''
                   );
    }

    /**
     * Returns all invitations to which the user is invited to regarding a
     * specific exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /invitation/member/exercisesheet/$esid/user/$userid(/).
     *
     * @param int $esid The id of the exercise sheet.
     * @param int $userid The id of the user.
     */
    public function getSheetMemberInvitations( 
                                              $esid,
                                              $userid
                                              )
    {
        $this->get( 
                   'GetSheetMemberInvitations',
                   dirname(__FILE__) . '/Sql/GetSheetMemberInvitations.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $mid ) ? $mid : ''
                   );
    }

    /**
     * Returns all invitations to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /invitation/member/exercisesheet/$esid/user/$userid(/).
     *
     * @param int $esid The id of the exercise sheet the invitations belong to.
     */
    public function getSheetInvitations( $esid )
    {
        $this->get( 
                   'GetSheetInvitations',
                   dirname(__FILE__) . '/Sql/GetSheetInvitations.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
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
                   isset( $suid ) ? $suid : '',
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

 
?>