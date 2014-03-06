<?php 


/**
 * @file DBMarking.php contains the DBMarking class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBMarking/MarkingSample.json
 */

require_once ( '../../Assistants/Slim/Slim.php' );
include_once ( '../../Assistants/Structures.php' );
include_once ( '../../Assistants/Request.php' );
include_once ( '../../Assistants/DBJson.php' );
include_once ( '../../Assistants/DBRequest.php' );
include_once ( '../../Assistants/CConfig.php' );
include_once ( '../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

// runs the CConfig
$com = new CConfig( DBMarking::getPrefix( ) );

// runs the DBMarking
if ( !$com->used( ) )
    new DBMarking( $com->loadConfig( ) );

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
    public function __construct( $conf )
    {

        // initialize component
        $this->_conf = $conf;
        $this->query = array( CConfig::getLink( 
                                               $conf->getLinks( ),
                                               'out'
                                               ) );

        // initialize slim
        $this->_app = new \Slim\Slim( );
        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            'application/json'
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

        // starts slim only if the right prefix was received
        if ( strpos( 
                    $this->_app->request->getResourceUri( ),
                    '/' . $this->getPrefix( )
                    ) === 0 ){

            // run Slim
            $this->_app->run( );
        }
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
                                                  'Sql/EditMarking.sql',
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
                                              'Sql/DeleteMarking.sql',
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
                    'starts OST AddMarking',
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
                                                  'Sql/AddMarking.sql',
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
                        $sqlFile,
                        $userid,
                        $cid,
                        $esid,
                        $eid,
                        $suid,
                        $mid,
                        $sub,
                        $singleResult = false
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
                           $cid == '' ? true : ctype_digit( $cid ),
                           $esid == '' ? true : ctype_digit( $esid ),
                           $eid == '' ? true : ctype_digit( $eid ),
                           $suid == '' ? true : ctype_digit( $suid ),
                           $mid == '' ? true : ctype_digit( $mid )
                           );

        if ( $sub != 1 )
            $sub = 0;

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              $sqlFile,
                                              array( 
                                                    'userid' => $userid,
                                                    'cid' => $cid,
                                                    'esid' => $esid,
                                                    'eid' => $eid,
                                                    'suid' => $suid,
                                                    'mid' => $mid,
                                                    'sub' => $sub
                                                    )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );

            if ( $query->getNumRows( ) > 0 ){
                $res = Marking::ExtractMarking( 
                                               $query->getResponse( ),
                                               $singleResult
                                               );
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
    public function getAllMarkings( $sub = 1 )
    {
        $this->get( 
                   'GetAllMarkings',
                   'Sql/GetAllMarkings.sql',
                   '',
                   '',
                   '',
                   '',
                   '',
                   '',
                   $sub
                   );
    }

    public function getAllMarkingsNoSubmission( $sub = 0 )
    {
        $this->getAllMarkings( $sub );
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
                               $mid,
                               $sub = 1
                               )
    {
        $this->get( 
                   'GetMarking',
                   'Sql/GetMarking.sql',
                   '',
                   '',
                   '',
                   '',
                   '',
                   $mid,
                   $sub,
                   true
                   );
    }

    public function getMarkingNoSubmission( 
                                           $mid,
                                           $sub = 0
                                           )
    {
        $this->getMarking( 
                          $mid,
                          $sub,
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
                                         $suid,
                                         $sub = 1
                                         )
    {
        $this->get( 
                   'GetSubmissionMarking',
                   'Sql/GetSubmissionMarking.sql',
                   '',
                   '',
                   '',
                   '',
                   $suid,
                   '',
                   $sub,
                   true
                   );
    }

    public function getSubmissionMarkingNoSubmission( 
                                                     $suid,
                                                     $sub = 0
                                                     )
    {
        $this->getSubmissionMarking( 
                                    $suid,
                                    $sub,
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
                                        $eid,
                                        $sub = 1
                                        )
    {
        $this->get( 
                   'GetExerciseMarkings',
                   'Sql/GetExerciseMarkings.sql',
                   '',
                   '',
                   '',
                   $eid,
                   '',
                   '',
                   $sub
                   );
    }

    public function getExerciseMarkingsNoSubmission( 
                                                    $eid,
                                                    $sub = 0
                                                    )
    {
        $this->getExerciseMarkings( 
                                   $eid,
                                   $sub
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
                                     $esid,
                                     $sub = 1
                                     )
    {
        $this->get( 
                   'GetSheetMarkings',
                   'Sql/GetSheetMarkings.sql',
                   '',
                   '',
                   $esid,
                   '',
                   '',
                   '',
                   $sub
                   );
    }

    public function getSheetMarkingsNoSubmission( 
                                                 $esid,
                                                 $sub = 0
                                                 )
    {
        $this->getSheetMarkings( 
                                $esid,
                                $sub
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
                                      $cid,
                                      $sub = 1
                                      )
    {
        $this->get( 
                   'GetCourseMarkings',
                   'Sql/GetCourseMarkings.sql',
                   '',
                   $cid,
                   '',
                   '',
                   '',
                   '',
                   $sub
                   );
    }

    public function getCourseMarkingsNoSubmission( 
                                                  $cid,
                                                  $sub = 0
                                                  )
    {
        $this->getCourseMarkings( 
                                 $cid,
                                 $sub
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
                                         $userid,
                                         $sub = 1
                                         )
    {
        $this->get( 
                   'GetUserGroupMarkings',
                   'Sql/GetUserGroupMarkings.sql',
                   $userid,
                   '',
                   $esid,
                   '',
                   '',
                   '',
                   $sub
                   );
    }

    public function getUserGroupMarkingsNoSubmission( 
                                                     $esid,
                                                     $userid,
                                                     $sub = 0
                                                     )
    {
        $this->getUserGroupMarkings( 
                                    $esid,
                                    $userid,
                                    $sub
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
                                          $userid,
                                          $sub = 1
                                          )
    {
        $this->get( 
                   'GetTutorSheetMarkings',
                   'Sql/GetTutorSheetMarkings.sql',
                   $userid,
                   '',
                   $esid,
                   '',
                   '',
                   '',
                   $sub
                   );
    }

    public function getTutorSheetMarkingsNoSubmission( 
                                                      $esid,
                                                      $userid,
                                                      $sub = 0
                                                      )
    {
        $this->getTutorSheetMarkings( 
                                     $esid,
                                     $userid,
                                     $sub
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
                                             $userid,
                                             $sub = 1
                                             )
    {
        $this->get( 
                   'GetTutorExerciseMarkings',
                   'Sql/GetTutorExerciseMarkings.sql',
                   $userid,
                   '',
                   '',
                   $eid,
                   '',
                   '',
                   $sub
                   );
    }

    public function getTutorExerciseMarkingsNoSubmission( 
                                                         $eid,
                                                         $userid,
                                                         $sub = 0
                                                         )
    {
        $this->getTutorExerciseMarkings( 
                                        $eid,
                                        $userid,
                                        $sub
                                        );
    }
}

 
?>

