<?php 


/**
 * @file DBSelectedSubmission.php contains the DBSelectedSubmission class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBSelectedSubmission/SelectedSubmissionSample.json
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
 * A class, to abstract the "SelectedSubmission" table from database
 */
class DBSelectedSubmission
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
    private static $_prefix = 'selectedsubmission';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBSelectedSubmission::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBSelectedSubmission::$_prefix = $value;
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
        $com = new CConfig( DBSelectedSubmission::getPrefix( ), dirname(__FILE__) );

        // runs the DBSelectedSubmission
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
                         
        // PUT EditSelectedSubmission
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '/leader/:userid/exercise/:eid(/)',
                         array( 
                               $this,
                               'editSelectedSubmission'
                               )
                         );

        // DELETE DeleteSelectedSubmission
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '/leader/:userid/exercise/:eid(/)',
                            array( 
                                  $this,
                                  'deleteSelectedSubmission'
                                  )
                            );

        // PUT EditSubmissionSelectedSubmission
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '/submission/:suid(/)',
                         array( 
                               $this,
                               'editSubmissionSelectedSubmission'
                               )
                         );

        // DELETE DeleteSubmissionSelectedSubmission
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '/submission/:suid(/)',
                            array( 
                                  $this,
                                  'deleteSubmissionSelectedSubmission'
                                  )
                            );

        // DELETE DeleteUserSheetSelectedSubmission
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '/user/:userid/exercisesheet/:esid(/)',
                            array( 
                                  $this,
                                  'deleteUserSheetSelectedSubmission'
                                  )
                            );

        // POST AddSelectedSubmission
        $this->_app->post( 
                          '/' . $this->getPrefix( ) . '(/)',
                          array( 
                                $this,
                                'addSelectedSubmission'
                                )
                          );

        // GET GetExerciseSelected
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercise/:eid(/)',
                         array( 
                               $this,
                               'getExerciseSelected'
                               )
                         );

        // GET GetSheetSelected
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercisesheet/:esid(/)',
                         array( 
                               $this,
                               'getSheetSelected'
                               )
                         );
                         
                // GET GetCourseSelected
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:courseid(/)',
                         array( 
                               $this,
                               'getCourseSelected'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Sets the submission that should be marked.
     *
     * Called when this component receives an HTTP PUT request to
     * /selectedsubmission/leader/$userid/exercise/$eid(/).
     * The request body should contain a JSON object representing the new selectedSubmission.
     *
     * @param string $userid The id or the user which leads the group.
     * @param int $eid The id of the exercise.
     */
    public function editSelectedSubmission( 
                                           $userid,
                                           $eid
                                           )
    {
        Logger::Log( 
                    'starts PUT EditSelectedSubmission',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $userid ),
                           ctype_digit( $eid )
                           );

        // decode the received selected submission data, as an object
        $insert = SelectedSubmission::decodeSelectedSubmission( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/EditSelectedSubmission.sql',
                                                  array( 
                                                        'userid' => $userid,
                                                        'eid' => $eid,
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
                            'PUT EditSelectedSubmission failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Sets the submission that should be marked.
     *
     * Called when this component receives an HTTP PUT request to
     * /selectedsubmission/submission/$suid(/).
     * The request body should contain a JSON object representing the new selectedSubmission.
     *
     * @param string $suid The id or the submission.
     */
    public function editSubmissionSelectedSubmission( $suid )
    {
        Logger::Log( 
                    'starts PUT EditSubmissionSelectedSubmission',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $suid )
                           );

        // decode the received selected submission data, as an object
        $insert = SelectedSubmission::decodeSelectedSubmission( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/EditSubmissionSelectedSubmission.sql',
                                                  array( 
                                                        'suid' => $suid,
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
                            'PUT EditSubmissionSelectedSubmission failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Unsets the submission that should be marked.
     *
     * Called when this component receives an HTTP DELETE request to
     * /selectedsubmission/leader/$userid/exercise/$eid(/).
     *
     * @param string $userid The id or the user which leads the group.
     * @param int $eid The id of the exercise.
     */
    public function deleteSelectedSubmission( 
                                             $userid,
                                             $eid
                                             )
    {
        Logger::Log( 
                    'starts DELETE DeleteSelectedSubmission',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $userid ),
                           ctype_digit( $eid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteSelectedSubmission.sql',
                                              array( 
                                                    'userid' => $userid,
                                                    'eid' => $eid
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
                        'DELETE DeleteSelectedSubmission failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Unsets the submission that should be marked.
     *
     * Called when this component receives an HTTP DELETE request to
     * /selectedsubmission/user/$userid/exercisesheet/$esid(/).
     *
     * @param string $userid The id or the user which leads the group.
     * @param int $esid The id of the exercise sheet.
     */
    public function deleteUserSheetSelectedSubmission( 
                                                      $userid,
                                                      $esid
                                                      )
    {
        Logger::Log( 
                    'starts DELETE DeleteUserSheetSelectedSubmission',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $userid ),
                           ctype_digit( $esid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteUserSheetSelectedSubmission.sql',
                                              array( 
                                                    'userid' => $userid,
                                                    'esid' => $esid
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
                        'DELETE DeleteUserSheetSelectedSubmission failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Unsets the submission that should be marked.
     *
     * Called when this component receives an HTTP DELETE request to
     * /selectedsubmission/submission/$suid(/).
     *
     * @param string $suid The id or the submission.
     */
    public function deleteSubmissionSelectedSubmission( $suid )
    {
        Logger::Log( 
                    'starts DELETE DeleteSubmissionSelectedSubmission',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $suid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteSubmissionSelectedSubmission.sql',
                                              array( 'suid' => $suid )
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
                        'DELETE DeleteSubmissionSelectedSubmission failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Sets the submission that should be marked.
     *
     * Called when this component receives an HTTP POST request to
     * /selectedsubmission/leader/$userid/exercise/$eid(/).
     * The request body should contain a JSON object representing the new selectedSubmission.
     */
    public function addSelectedSubmission( )
    {
        Logger::Log( 
                    'starts POST AddSelectedSubmission',
                    LogLevel::DEBUG
                    );

        // decode the received selected submission data, as an object
        $insert = SelectedSubmission::decodeSelectedSubmission( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/AddSelectedSubmission.sql',
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
                            'POST AddSelectedSubmission failed',
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
        $userid = DBJson::mysql_real_escape_string( $userid );
        $courseid = DBJson::mysql_real_escape_string( $courseid );
        $esid = DBJson::mysql_real_escape_string( $esid );
        $eid = DBJson::mysql_real_escape_string( $eid );
        $suid = DBJson::mysql_real_escape_string( $suid );
        $mid = DBJson::mysql_real_escape_string( $mid );

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
            if (is_array($query)) $query = $query[0];
            if ( $query->getNumRows( ) > 0 ){
                $res = SelectedSubmission::ExtractSelectedSubmission( 
                                                                     $query->getResponse( ),
                                                                     $singleResult
                                                                     );
                $this->_app->response->setBody( SelectedSubmission::encodeSelectedSubmission( $res ) );

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
        $this->_app->response->setBody( SelectedSubmission::encodeSelectedSubmission( new SelectedSubmission( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns the submission that should be marked.
     *
     * Called when this component receives an HTTP GET request to
     * /selectedsubmission/exercise/$eid(/).
     *
     * @param int $eid The id of the exercise.
     */
    public function getExerciseSelected( $eid )
    {
        $this->get( 
                   'GetExerciseSelected',
                   dirname(__FILE__) . '/Sql/GetExerciseSelected.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $mid ) ? $mid : ''
                   );
    }

    /**
     * Returns all exercises that should be marked to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /selectedsubmission/exercisesheet/$esid(/).
     *
     * @param int $eid The id of the exercise sheet.
     */
    public function getSheetSelected( $esid )
    {
        $this->get( 
                   'GetSheetSelected',
                   dirname(__FILE__) . '/Sql/GetSheetSelected.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $mid ) ? $mid : ''
                   );
    }
    
    /**
     * Returns all exercises that should be marked to a given course.
     *
     * Called when this component receives an HTTP GET request to
     * /selectedsubmission/course/$courseid(/).
     *
     * @param int $courseid The id of the course.
     */
    public function getCourseSelected( $courseid )
    {
        $this->get( 
                   'GetCourseSelected',
                   dirname(__FILE__) . '/Sql/GetCourseSelected.sql',
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