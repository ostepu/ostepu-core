<?php 


/**
 * @file DBMarking.php contains the DBMarking class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBMarking/MarkingSample.json
 * @date 2013-2014
 */

require_once ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures/Marking.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures/Query.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBJson.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBRequest.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to abstract the "Marking" table from database
 */
class DBMarking
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
    private static $_prefix = 'marking';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBMarking::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBMarking::$_prefix = $value;
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
        $com = new CConfig( DBMarking::getPrefix( ), dirname(__FILE__) );

        // runs the DBMarking
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
                         
        // PUT EditMarking
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '(/marking)/:mid(/)',
                         array( 
                               $this,
                               'editMarking'
                               )
                         );

        // DELETE DeleteMarking
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '(/marking)/:mid(/)',
                            array( 
                                  $this,
                                  'deleteMarking'
                                  )
                            );
                            
        // DELETE DeleteSheetMarkings
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '(/marking)/exercisesheet/:esid(/)',
                            array( 
                                  $this,
                                  'deleteSheetMarkings'
                                  )
                            );

        // POST AddMarking
        $this->_app->post( 
                          '/' . $this->getPrefix( ) . '(/)',
                          array( 
                                $this,
                                'addMarking'
                                )
                          );

        // GET GetSubmissionMarking
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/submission/:suid(/)',
                         array( 
                               $this,
                               'getSubmissionMarking'
                               )
                         );

        // GET GetSubmissionMarkingNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/submission/:suid/nosubmission(/)',
                         array( 
                               $this,
                               'getSubmissionMarkingNoSubmission'
                               )
                         );

        // GET GetExerciseMarkings
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercise/:eid(/)',
                         array( 
                               $this,
                               'getExerciseMarkings'
                               )
                         );

        // GET GetExerciseMarkingsNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercise/:eid/nosubmission(/)',
                         array( 
                               $this,
                               'getExerciseMarkingsNoSubmission'
                               )
                         );

        // GET GetSheetMarkings
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercisesheet/:esid(/)',
                         array( 
                               $this,
                               'getSheetMarkings'
                               )
                         );

        // GET GetSheetMarkingsNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercisesheet/:esid/nosubmission(/)',
                         array( 
                               $this,
                               'getSheetMarkingsNoSubmission'
                               )
                         );

        // GET GetCourseMarkings
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:cid(/)',
                         array( 
                               $this,
                               'getCourseMarkings'
                               )
                         );

        // GET GetCourseMarkingsNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:cid/nosubmission(/)',
                         array( 
                               $this,
                               'getCourseMarkingsNoSubmission'
                               )
                         );

        // GET GetUserGroupMarkings
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercisesheet/:esid/user/:userid(/)',
                         array( 
                               $this,
                               'getUserGroupMarkings'
                               )
                         );

        // GET GetUserGroupMarkingsNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercisesheet/:esid/user/:userid/nosubmission(/)',
                         array( 
                               $this,
                               'getUserGroupMarkingsNoSubmission'
                               )
                         );

        // GET GetTutorSheetMarkings
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercisesheet/:esid/tutor/:userid(/)',
                         array( 
                               $this,
                               'getTutorSheetMarkings'
                               )
                         );

        // GET GetTutorSheetMarkingsNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercisesheet/:esid/tutor/:userid/nosubmission(/)',
                         array( 
                               $this,
                               'getTutorSheetMarkingsNoSubmission'
                               )
                         );
                         
        // GET GetTutorCourseMarkings
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:cid/tutor/:userid(/)',
                         array( 
                               $this,
                               'getTutorCourseMarkings'
                               )
                         );

        // GET GetTutorCourseMarkingsNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:cid/tutor/:userid/nosubmission(/)',
                         array( 
                               $this,
                               'getTutorCourseMarkingsNoSubmission'
                               )
                         );
                         
        // GET GetTutorExerciseMarkings
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercise/:eid/tutor/:userid(/)',
                         array( 
                               $this,
                               'getTutorExerciseMarkings'
                               )
                         );

        // GET GetTutorExerciseMarkingsNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercise/:eid/tutor/:userid/nosubmission(/)',
                         array( 
                               $this,
                               'getTutorExerciseMarkingsNoSubmission'
                               )
                         );

        // GET GetMarking
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/marking)/:mid(/)',
                         array( 
                               $this,
                               'getMarking'
                               )
                         );

        // GET GetMarkingNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/marking)/:mid/nosubmission(/)',
                         array( 
                               $this,
                               'getMarkingNoSubmission'
                               )
                         );

        // GET GetAllMarkings
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/marking)(/)',
                         array( 
                               $this,
                               'getAllMarkings'
                               )
                         );

        // GET GetAllMarkingsNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/marking)/nosubmission(/)',
                         array( 
                               $this,
                               'getAllMarkingsNoSubmission'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Edits a marking.
     *
     * Called when this component receives an HTTP PUT request to
     * /marking/$mid(/) or /marking/marking/$mid(/).
     * The request body should contain a JSON object representing the marking's new
     * attributes.
     *
     * @param int $mid The id of the marking that is being updated.
     */
    public function editMarking( $mid )
    {
        Logger::Log( 
                    'starts PUT EditMarking',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $mid )
                           );

        // decode the received marking data, as an object
        $insert = Marking::decodeMarking( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/EditMarking.sql',
                                                  array( 
                                                        'mid' => $mid,
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
                            'PUT EditMarking failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes a marking.
     *
     * Called when this component receives an HTTP DELETE request to
     * /marking/$mid(/) or /marking/marking/$mid(/).
     *
     * @param int $mid The id of the marking that is being deleted.
     */
    public function deleteMarking( $mid )
    {
        Logger::Log( 
                    'starts DELETE DeleteMarking',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $mid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteMarking.sql',
                                              array( 'mid' => $mid )
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
                        'DELETE DeleteMarking failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }
    
    /**
     * Deletes markings.
     *
     * Called when this component receives an HTTP DELETE request to
     * /marking/exercisesheet/$esid(/) or /marking/marking/exercisesheet/$esid(/).
     *
     * @param int $esid The id of the exercise sheet of the markings that are being deleted.
     */
    public function deleteSheetMarkings( $esid )
    {
        Logger::Log( 
                    'starts DELETE DeleteSheetMarkings',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $esid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteSheetMarkings.sql',
                                              array( 'esid' => $esid )
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
                        'DELETE DeleteSheetMarkings failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds a marking.
     *
     * Called when this component receives an HTTP POST request to
     * /marking(/).
     * The request body should contain a JSON object representing the
     * marking's attributes.
     */
    public function addMarking( )
    {
        Logger::Log( 
                    'starts POST AddMarking',
                    LogLevel::DEBUG
                    );

        // decode the received marking data, as an object
        $insert = Marking::decodeMarking( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/AddMarking.sql',
                                                  array( 'values' => $data )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new Marking( );
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
                            'POST AddMarking failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( Marking::encodeMarking( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( Marking::encodeMarking( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( Marking::encodeMarking( $res ) );
    }

    public function get(
                        $functionName,
                        $params,
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
            unset($result['content']);

            if ( $query->getNumRows( ) > 0 ){
                $res = Marking::ExtractMarking( 
                                               $query->getResponse( ),
                                               $singleResult
                                               );
                unset($query);                 
                $this->_app->response->setBody( Marking::encodeMarking( $res ) );
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
        $this->_app->response->setBody( Marking::encodeMarking( new Marking( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns all markings.
     *
     * Called when this component receives an HTTP GET request to
     * /marking(/) or /marking/marking(/).
     */
    public function getAllMarkings( )
    {
        $this->get( 
                   'DBMarkingGetAllMarkings',
                   array()
                   );
    }

    public function getAllMarkingsNoSubmission( )
    {
        $this->get( 
                   'DBMarkingGetAllMarkingsNoSubmission',
                   array()
                   );
    }

    /**
     * Returns a marking.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/$mid(/) or /marking/marking/$mid(/).
     *
     * @param int $mid The id of the marking that should be returned.
     */
    public function getMarking( 
                               $mid
                               )
    {
        $this->get( 
                   'DBMarkingGetMarking',
                   array("mid"=>$mid),
                   true
                   );
    }

    public function getMarkingNoSubmission( 
                                           $mid
                                           )
    {
        $this->get( 
                   'DBMarkingGetMarkingNoSubmission',
                   array("mid"=>$mid),
                   true
                   );
    }

    /**
     * Returns a marking to a given submission.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/submission/$suid(/).
     *
     * @param int $suid The id of the submission.
     */
    public function getSubmissionMarking( 
                                         $suid
                                         )
    {
        $this->get( 
                   'DBMarkingGetSubmissionMarking',
                   array("suid"=>$suid),
                   true
                   );
    }

    public function getSubmissionMarkingNoSubmission( 
                                                     $suid
                                                     )
    {
        $this->get( 
                   'DBMarkingGetSubmissionMarkingNoSubmission',
                   array("suid"=>$suid),
                   true
                   );
    }

    /**
     * Returns all markings which belong to a given exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/exercise/$eid(/).
     *
     * @param int $eid The id of the exercise.
     */
    public function getExerciseMarkings( 
                                        $eid
                                        )
    {
        $this->get( 
                   'DBMarkingGetExerciseMarkings',
                   array("eid"=>$eid)
                   );
    }

    public function getExerciseMarkingsNoSubmission( 
                                                    $eid
                                                    )
    {
        $this->get( 
                   'DBMarkingGetExerciseMarkingsNoSubmission',
                   array("eid"=>$eid)
                   );
    }

    /**
     * Returns all markings which belong to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/exercisesheet/$esid(/).
     *
     * @param int $esid The id of the exercise sheet.
     */
    public function getSheetMarkings( 
                                     $esid
                                     )
    {
        $this->get( 
                   'DBMarkingGetSheetMarkings',
                   array("esid"=>$esid)
                   );
    }

    public function getSheetMarkingsNoSubmission( 
                                                 $esid
                                                 )
    {
        $this->get( 
                   'DBMarkingGetSheetMarkingsNoSubmission',
                   array("esid"=>$esid)
                   );
    }

    /**
     * Returns all markings which belong to a given course.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/course/$cid(/).
     *
     * @param int $cid The id of the course.
     */
    public function getCourseMarkings( 
                                      $cid
                                      )
    {
        $this->get( 
                   'DBMarkingGetCourseMarkings',
                   array("cid"=>$cid)
                   );
    }

    public function getCourseMarkingsNoSubmission( 
                                                  $cid
                                                  )
    {
        $this->get( 
                   'DBMarkingGetCourseMarkingsNoSubmission',
                   array("cid"=>$cid)
                   );
    }

    /**
     * Returns all markings of a group regarding a specific exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/exercisesheet/$esid/user/$userid(/).
     *
     * @param int $esid The id of the exercise sheet.
     * @param int $userid The id of the user whose group the marking belongs to.
     */
    public function getUserGroupMarkings( 
                                         $esid,
                                         $userid
                                         )
    {
        $this->get( 
                   'DBMarkingGetUserGroupMarkings',
                   array("esid"=>$esid,"userid"=>$userid)
                   );
    }

    public function getUserGroupMarkingsNoSubmission( 
                                                     $esid,
                                                     $userid,
                                                     $sub = 0
                                                     )
    {
        $this->get( 
                   'DBMarkingGetUserGroupMarkingsNoSubmission',
                   array("esid"=>$esid,"userid"=>$userid)
                   );
    }

    /**
     * Returns all markings created by a given tutor regarding
     * a specific exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/exercisesheet/$esid/tutor/$userid(/).
     *
     * @param int $esid The id of the exercise sheet.
     * @param int $userid The userid of the tutor that created the markings
     * which should be returned.
     */
    public function getTutorSheetMarkings( 
                                          $esid,
                                          $userid
                                          )
    {
        $this->get( 
                   'DBMarkingGetTutorSheetMarkings',
                   array("esid"=>$esid,"userid"=>$userid)
                   );
    }

    public function getTutorSheetMarkingsNoSubmission( 
                                                      $esid,
                                                      $userid
                                                      )
    {
        $this->get( 
                   'DBMarkingGetTutorSheetMarkingsNoSubmission',
                   array("esid"=>$esid,"userid"=>$userid)
                   );
    }

    /**
     * Returns all markings created by a given tutor regarding
     * a specific course.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/course/$cid/tutor/$userid(/).
     *
     * @param int $cid The id of the course.
     * @param int $userid The userid of the tutor that created the markings
     * which should be returned.
     */
    public function getTutorCourseMarkings( 
                                          $cid,
                                          $userid
                                          )
    {
        $this->get( 
                   'DBMarkingGetTutorCourseMarkings',
                   array("cid"=>$cid,"userid"=>$userid)
                   );
    }

    public function getTutorCourseMarkingsNoSubmission( 
                                                      $cid,
                                                      $userid
                                                      )
    {
        $this->get( 
                   'DBMarkingGetTutorCourseMarkingsNoSubmission',
                   array("cid"=>$cid,"userid"=>$userid)
                   );
    }
    
    /**
     * Returns all markings created by a given tutor regarding
     * a specific exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/exercise/$eid/tutor/$userid(/).
     *
     * @param int $eid The id of the exercise.
     * @param int $userid The userid of the tutor that created the markings
     * which should be returned.
     */
    public function getTutorExerciseMarkings( 
                                             $eid,
                                             $userid
                                             )
    {
        $this->get( 
                   'DBMarkingGetTutorExerciseMarkings',
                   array("eid"=>$eid,"userid"=>$userid)
                   );
    }

    public function getTutorExerciseMarkingsNoSubmission( 
                                                         $eid,
                                                         $userid
                                                         )
    {
        $this->get( 
                   'DBMarkingGetTutorExerciseMarkingsNoSubmission',
                   array("eid"=>$eid,"userid"=>$userid)
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
                   'DBMarkingGetExistsPlatform',
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