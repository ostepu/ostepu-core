<?php 


/**
 * @file DBExercise.php contains the DBExercise class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBExercise/ExerciseSample.json
 * @date 2013-2014
 */

require_once ( '../../Assistants/Slim/Slim.php' );
include_once ( '../../Assistants/Structures.php' );
include_once ( '../../Assistants/Request.php' );
include_once ( '../../Assistants/DBJson.php' );
include_once ( '../../Assistants/DBRequest.php' );
include_once ( '../../Assistants/CConfig.php' );
include_once ( '../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to abstract the "Exercise" table from database
 */
class DBExercise
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
    private static $_prefix = 'exercise';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBExercise::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBExercise::$_prefix = $value;
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
        $com = new CConfig( DBExercise::getPrefix( ) );

        // runs the DBExercise
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
            
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

        // PUT EditExercise
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '(/exercise)/:eid(/)',
                         array( 
                               $this,
                               'editExercise'
                               )
                         );

        // DELETE DeleteExercise
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '(/exercise)/:eid(/)',
                            array( 
                                  $this,
                                  'deleteExercise'
                                  )
                            );

        // POST AddExercise
        $this->_app->post( 
                          '/' . $this->getPrefix( ) . '(/)',
                          array( 
                                $this,
                                'addExercise'
                                )
                          );

        // GET GetAllExercises
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/exercise)(/)',
                         array( 
                               $this,
                               'getAllExercises'
                               )
                         );

        // GET GetAllExercisesNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/exercise)/nosubmission(/)',
                         array( 
                               $this,
                               'getAllExercisesNoSubmission'
                               )
                         );

        // GET GetExercise
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/exercise)/:eid(/)',
                         array( 
                               $this,
                               'getExercise'
                               )
                         );

        // GET GetExerciseNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/exercise)/:eid/nosubmission(/)',
                         array( 
                               $this,
                               'getExerciseNoSubmission'
                               )
                         );

        // GET GetSheetExercises
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercisesheet/:esid(/)',
                         array( 
                               $this,
                               'getSheetExercises'
                               )
                         );

        // GET GetSheetExercisesNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercisesheet/:esid/nosubmission(/)',
                         array( 
                               $this,
                               'getSheetExercisesNoSubmission'
                               )
                         );

        // GET GetCourseExercises
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:courseid(/)',
                         array( 
                               $this,
                               'getCourseExercises'
                               )
                         );

        // GET GetCourseExercisesNoSubmission
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:courseid/nosubmission(/)',
                         array( 
                               $this,
                               'getCourseExercisesNoSubmission'
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
     * Edits an exercise.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercise/$eid(/) or /exercise/exercise/$eid(/).
     * The request body should contain a JSON object representing the exercise's new
     * attributes.
     *
     * @param int $eid The id of the exercise that is beeing updated.
     */
    public function editExercise( $eid )
    {
        Logger::Log( 
                    'starts PUT EditExercise',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $eid )
                           );

        // decode the received exercise data, as an object
        $insert = Exercise::decodeExercise( $this->_app->request->getBody( ) );

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
                                                  'Sql/EditExercise.sql',
                                                  array( 
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
                            'PUT EditExercise failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes an exercise.
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercise/$eid(/) or /exercise/exercise/$eid(/).
     *
     * @param int $eid The id of the exercise that is beeing deleted.
     */
    public function deleteExercise( $eid )
    {
        Logger::Log( 
                    'starts DELETE DeleteExercise',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $eid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              'Sql/DeleteExercise.sql',
                                              array( 'eid' => $eid )
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
                        'DELETE DeleteExercise failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds an exercise.
     *
     * Called when this component receives an HTTP POST request to
     * /exercise(/).
     * The request body should contain a JSON object representing the exercise's
     * attributes.
     */
    public function addExercise( )
    {
        Logger::Log( 
                    'starts POST SetExercise',
                    LogLevel::DEBUG
                    );

        // decode the received exercise data, as an object
        $insert = Exercise::decodeExercise( $this->_app->request->getBody( ) );

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
                                                  'Sql/AddExercise.sql',
                                                  array( 'values' => $data )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new Exercise( );
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
                            'POST SetExercise failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( Exercise::encodeExercise( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( Exercise::encodeExercise( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( Exercise::encodeExercise( $res ) );
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
                           $courseid == '' ? true : ctype_digit( $courseid ),
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
                                                    'courseid' => $courseid,
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
                $res = Exercise::ExtractExercise( 
                                                 $query->getResponse( ),
                                                 $singleResult
                                                 );
                $this->_app->response->setBody( Exercise::encodeExercise( $res ) );

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
        $this->_app->response->setBody( Exercise::encodeExercise( new Exercise( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns a single exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /exercise/$eid(/) or /exercise/exercise/$eid(/).
     *
     * @param int $eid The id of the exercise that should be returned.
     */
    public function getExercise( 
                                $eid,
                                $sub = 1
                                )
    {
        $this->get( 
                   'GetExercise',
                   'Sql/GetExercise.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $mid ) ? $mid : '',
                   $sub,
                   true
                   );
    }
    public function getExerciseNoSubmission( 
                                            $eid,
                                            $sub = 0
                                            )
    {
        $this->getExercise( 
                           $eid,
                           $sub
                           );
    }

    /**
     * Returns all exercises.
     *
     * Called when this component receives an HTTP GET request to
     * /exercise(/) or /exercise/exercise(/).
     */
    public function getAllExercises( $sub = 1 )
    {
        $this->get( 
                   'GetAllExercises',
                   'Sql/GetAllExercises.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $mid ) ? $mid : '',
                   $sub
                   );
    }
    public function getAllExercisesNoSubmission( $sub = 0 )
    {
        $this->getAllExercises( $sub );
    }

    /**
     * Returns all exercises which are part of a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /exercise/exercisesheet/$esid(/).
     *
     * @param int $esid The id of the exercise sheet.
     */
    public function getSheetExercises( 
                                      $esid,
                                      $sub = 1
                                      )
    {
        $this->get( 
                   'GetSheetExercises',
                   'Sql/GetSheetExercises.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $mid ) ? $mid : '',
                   $sub
                   );
    }
    public function getSheetExercisesNoSubmission( 
                                                  $esid,
                                                  $sub = 0
                                                  )
    {
        $this->getSheetExercises( 
                                 $esid,
                                 $sub
                                 );
    }

    /**
     * Returns all exercises which belong to a given course.
     *
     * Called when this component receives an HTTP GET request to
     * /exercise/course/$courseid(/).
     *
     * @param int $courseid The id of the course.
     */
    public function getCourseExercises( 
                                       $courseid,
                                       $sub = 1
                                       )
    {
        $this->get( 
                   'GetCourseExercises',
                   'Sql/GetCourseExercises.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $mid ) ? $mid : '',
                   $sub
                   );
    }
    public function getCourseExercisesNoSubmission( 
                                                   $courseid,
                                                   $sub = 0
                                                   )
    {
        $this->getCourseExercises( 
                                  $courseid,
                                  $sub
                                  );
    }
}

 
?>

