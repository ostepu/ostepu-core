<?php 


/**
 * @file DBCourseStatus.php contains the DBCourseStatus class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBCourseStatus/CourseStatusSample.json
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
 * A class, to abstract the "CourseStatus" table from database
 */
class DBCourseStatus
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
    private static $_prefix = 'coursestatus';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBCourseStatus::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBCourseStatus::$_prefix = $value;
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
        $com = new CConfig( DBCourseStatus::getPrefix( ), dirname(__FILE__) );

        // runs the DBCourseStatus
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
                         
        // PUT EditMemberRight
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '/course/:courseid/user/:userid(/)',
                         array( 
                               $this,
                               'editMemberRight'
                               )
                         );

        // DELETE RemoveCourseMember
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '/course/:courseid/user/:userid(/)',
                            array( 
                                  $this,
                                  'removeCourseMember'
                                  )
                            );

        // POST AddCourseMember
        $this->_app->post( 
                          '/' . $this->getPrefix( ) . '(/)',

        // /course/:courseid/user/:userid
        array( 
              $this,
              'addCourseMember'
              )
                          );

        // GET GetMemberRight
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:courseid/user/:userid(/)',
                         array( 
                               $this,
                               'getMemberRight'
                               )
                         );

        // GET GetMemberRights
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/user/:userid(/)',
                         array( 
                               $this,
                               'getMemberRights'
                               )
                         );

        // GET GetCourseRights
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:courseid(/)',
                         array( 
                               $this,
                               'getCourseRights'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Edits the course status of a user in a specific course.
     *
     * Called when this component receives an HTTP PUT request to
     * /coursestatus/course/$courseid/user/$userid(/).
     * The request body should contain a JSON object representing the user's new
     * course status.
     *
     * @param int $courseid The id of the course.
     * @param int $userid The id of the user whose status is being updated.
     */
    public function editMemberRight( 
                                    $courseid,
                                    $userid
                                    )
    {
        Logger::Log( 
                    'starts PUT EditMemberRight',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $courseid ),
                           ctype_digit( $userid )
                           );

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
            $data = $in->getCourseStatusInsertData( );

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/EditMemberRight.sql',
                                                  array( 
                                                        'courseid' => $courseid,
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
                            'PUT EditMemberRight failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes the course status of a user in a specific course.
     *
     * Called when this component receives an HTTP DELETE request to
     * /coursestatus/course/$courseid/user/$userid(/).
     *
     * @param int $courseid The id of the course.
     * @param int $userid The id of the user whose status is being deleted.
     */
    public function removeCourseMember( 
                                       $courseid,
                                       $userid
                                       )
    {
        Logger::Log( 
                    'starts DELETE RemoveCourseMember',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $courseid ),
                           ctype_digit( $userid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/RemoveCourseMember.sql',
                                              array( 
                                                    'courseid' => $courseid,
                                                    'userid' => $userid
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
                        'DELETE RemoveCourseMember failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds a course status to a user in a specific course.
     *
     * Called when this component receives an HTTP POST request to
     * /coursestatus(/).
     * The request body should contain a JSON object representing the user's
     * course status.
     */
    public function addCourseMember( )
    {
        Logger::Log( 
                    'starts POST AddCourseMember',
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

        foreach ( $insert as $in ){

            // generates the insert data for the object
            $data = $in->getCourseStatusInsertData( );

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/AddCourseMember.sql',
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
                            'POST AddCourseMember failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    public function get( 
                        $functionName,
                        $params=array(),
                        $singleResult = false,
                        $checkSession = true
                        )
    {
        // checks whether incoming data has the correct data type
        $params = DBJson::mysql_real_escape_string( $params );
        foreach($params as $param)
            $functionName.='/'.$param;

        // starts a query, by using a given file
        /*$result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              $sqlFile,
                                              $params,
                                              $checkSession
                                              );*/
                                             // echo '/query/procedure/'.$functionName;return;
        $result = Request::routeRequest( 
                                        'GET',
                                        '/query/procedure/'.$functionName,
                                        array(),
                                        '',
                                        $this->query2,
                                        'query'
                                        );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );
            if (is_array($query)) $query = $query[0];

            if ( $query->getNumRows( ) > 0 ){
                $res = User::ExtractCourseStatus( 
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
        
        $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
        $this->_app->response->setBody( User::encodeUser( new User( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns the course status of a user in a specific course.
     *
     * Called when this component receives an HTTP GET request to
     * /coursestatus/course/$courseid/user/$userid(/).
     *
     * @param int $courseid The id of the course.
     * @param int $userid The id of the user whose status is being returned.
     */
    public function getMemberRight( 
                                   $courseid,
                                   $userid
                                   )
    {
        $this->get( 
                   'DBCourseStatusGetMemberRight',
                   array("courseid"=>$courseid,"userid"=>$userid),
                   true
                   );
    }

    /**
     * Returns all course status objects of a user.
     *
     * Called when this component receives an HTTP GET request to
     * /coursestatus/user/$userid(/).
     *
     * @param int $userid The id of the user.
     */
    public function getMemberRights( $userid )
    {
        $this->get( 
                   'DBCourseStatusGetMemberRights',
                   array("userid"=>$userid)
                   );
    }

    /**
     * Returns all course status objects of a course.
     *
     * Called when this component receives an HTTP GET request to
     * /coursestatus/course/$courseid(/).
     *
     * @param int $courseid The id of the course.
     */
    public function getCourseRights( $courseid )
    {
        $this->get( 
                   'DBCourseStatusGetCourseRights',
                   array("courseid"=>$courseid)
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
                   'DBCourseStatusGetExistsPlatform',
                   array(),
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