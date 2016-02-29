<?php
/**
 * @file DBProcess.php contains the DBProcess class
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

require_once ( dirname(__FILE__) . '/../../Assistants/vendor/Slim/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBJson.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBRequest.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to abstract the "DBProcess" table from database
 */
class DBProcess
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
    private static $_prefix = 'process';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBProcess::$_prefix;
    }

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function setPrefix( $value )
    {
        DBProcess::$_prefix = $value;
    }

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct( )
    {
        // runs the CConfig
        $com = new CConfig( DBProcess::getPrefix( ) . ',course,link', dirname(__FILE__) );

        // runs the DBProcess
        if ( $com->used( ) ) return;

        // initialize component
        $this->_conf = $com;

        // initialize slim
        $this->_app = new \Slim\Slim( array('debug' => true) );
        $this->_app->response->headers->set(
                                            'Content-Type',
                                            'application/json'
                                            );

        // POST AddCourse
        $this->_app->post(
                         '(/:pre)/course(/)',
                         array(
                               $this,
                               'addCourse'
                               )
                         );

        // DELETE DeleteCourse
        $this->_app->delete(
                         '(/:pre)/course(/course)/:courseid(/)',
                         array(
                               $this,
                               'deleteCourse'
                               )
                         );

        // PUT EditProcess
        $this->_app->put(
                         '(/:pre)/' . $this->getPrefix( ) . '(/process)/:processid(/)',
                         array(
                               $this,
                               'editProcess'
                               )
                         );

        // DELETE DeleteProcess
        $this->_app->delete(
                            '(/:pre)/' . $this->getPrefix( ) . '(/process)/:processid(/)',
                            array(
                                  $this,
                                  'deleteProcess'
                                  )
                            );

        // POST AddProcess
        $this->_app->post(
                          '(/:pre)/' . $this->getPrefix( ) . '(/)',
                          array(
                                $this,
                                'addProcess'
                                )
                          );

        // GET GetProcess
        $this->_app->get(
                         '(/:pre)/' . $this->getPrefix( ) . '(/process)/:processid(/)',
                         array(
                               $this,
                               'getProcess'
                               )
                         );

        // GET GetCourseProcesses
        $this->_app->get(
                         '(/:pre)/' . $this->getPrefix( ) . '/course/:courseid(/)',
                         array(
                               $this,
                               'getCourseProcesses'
                               )
                         );

        // GET GetExistsCourseProcesses
        $this->_app->get(
                         '(/:pre)/link/exists/course/:courseid(/)',
                         array(
                               $this,
                               'getExistsCourseProcesses'
                               )
                         );

        // GET GetSheetProcesses
        $this->_app->get(
                         '(/:pre)/' . $this->getPrefix( ) . '/exercisesheet/:esid(/)',
                         array(
                               $this,
                               'getSheetProcesses'
                               )
                         );

        // GET GetExerciseProcesses
        $this->_app->get(
                         '(/:pre)/' . $this->getPrefix( ) . '/exercise/:eid(/)',
                         array(
                               $this,
                               'getExerciseProcesses'
                               )
                         );

        // GET GetCourseComponentProcesses
        $this->_app->get(
                         '(/:pre)/' . $this->getPrefix( ) . '/course/:courseid/component/:comid(/)',
                         array(
                               $this,
                               'getCourseComponentProcesses'
                               )
                         );

            // run Slim
            $this->_app->run( );
    }

    /**
     * Loads the configuration data for the component from CConfig.json file
     *
     * @param int $pre A optional prefix for the process table.
     *
     * @return an component object, which represents the configuration
     */
    public function loadConfig( $pre='' )
    {
        // initialize component
        $this->_conf = $this->_conf->loadConfig( $pre );
        $this->query = array( CConfig::getLink(
                                               $this->_conf->getLinks( ),
                                               'out'
                                               ) );
    }

    /**
     * Edits a process.
     *
     * Called when this component receives an HTTP PUT request to
     * (/$pre)/process(/process)/$processid(/)
     * The request body should contain a JSON object representing the
     * process new attributes.
     *
     * @param string $processid The id of the process that is being updated.
     * @param int $pre A optional prefix for the process table.
     */
    public function editProcess( $pre='' , $processid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts PUT EditProcess',
                    LogLevel::DEBUG
                    );

        $processid = DBJson::mysql_real_escape_string( $processid );

        // decode the received choice data, as an object
        $insert = Process::decodeProcess( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        foreach ( $insert as $in ){

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile(
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/EditProcess.sql',
                                                  array(
                                                        'processid' => $processid,
                                                        'object' => $in,'pre' => $pre
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
                            'PUT EditProcess failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes a process.
     *
     * Called when this component receives an HTTP DELETE request to
     * (/$pre)/process(/process)/$processid(/).
     *
     * @param string $processid The id of the process that is being deleted.
     * @param int $pre A optional prefix for the process table.
     */
    public function deleteProcess( $pre='' , $processid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts DELETE DeleteProcess',
                    LogLevel::DEBUG
                    );

        $processid = DBJson::mysql_real_escape_string( $processid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteProcess.sql',
                                              array( 'processid' => $processid,'pre' => $pre  )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){

            if ( isset( $result['headers']['Content-Type'] ) )
                $this->_app->response->headers->set(
                                                    'Content-Type',
                                                    $result['headers']['Content-Type']
                                                    );

            $this->_app->response->setStatus( 201 );
            $this->_app->stop( );

        } else {
            Logger::Log(
                        'DELETE DeleteProcess failed',
                        LogLevel::ERROR
                        );

            $this->_app->response->setBody( Process::encodeProcess( new Form( ) ) );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds a process.
     *
     * Called when this component receives an HTTP POST request to
     * (/$pre)/process(/).
     *
     * @param int $pre A optional prefix for the process table.
     */
    public function addProcess( $pre='' )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts POST AddProcess',
                    LogLevel::DEBUG
                    );

        // decode the received choice data, as an object
        $insert = Process::decodeProcess( $this->_app->request->getBody( ) );

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
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/AddProcess.sql',
                                                  array( 'object' => $in,'pre' => $pre )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 &&
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new Process( );
                $course = Course::ExtractCourse($queryResult[count($queryResult)-1]->getResponse(),true);

                $obj->setProcessId( $course->getId() . '_' . $queryResult[count($queryResult)-2]->getInsertId( ) );

                $res[] = $obj;
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set(
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );

            } else {
                Logger::Log(
                            'POST AddProcess failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( Process::encodeProcess( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr &&
             count( $res ) == 1 ){
            $this->_app->response->setBody( Process::encodeProcess( $res[0] ) );

        } else
            $this->_app->response->setBody( Process::encodeProcess( $res ) );
    }

    public function get(
                        $functionName,
                        $sqlFile,
                        $pre='' ,
                        $processid,
                        $courseid,
                        $esid,
                        $eid,
                        $comid,
                        $singleResult = false,
                        $checkSession = true
                        )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts GET ' . $functionName,
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        $pre = DBJson::mysql_real_escape_string( $pre );
        $courseid = DBJson::mysql_real_escape_string( $courseid );
        $esid = DBJson::mysql_real_escape_string( $esid );
        $eid = DBJson::mysql_real_escape_string( $eid );
        $processid = DBJson::mysql_real_escape_string( $processid );
        $comid = DBJson::mysql_real_escape_string( $comid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              $sqlFile,
                                              array(
                                                    'pre' => $pre,
                                                    'processid' => $processid,
                                                    'courseid' => $courseid,
                                                    'esid' => $esid,
                                                    'eid' => $eid,
                                                    'comid' => $comid
                                                    ),
                                              $checkSession
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );

            if (is_array($query))
            $query = $query[count($query)-1];

            if ( $query->getNumRows( ) > 0 ){
                $res = Process::ExtractProcess(
                                         $query->getResponse( ),
                                         $singleResult
                                         );

                $this->_app->response->setBody( Process::encodeProcess( $res ) );

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
        $this->_app->response->setBody( Process::encodeProcess( new Process( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns a process.
     *
     * Called when this component receives an HTTP GET request to
     * (/$pre)/process(/process)/$processid(/).
     *
     * @param string $processid The id of the process.
     * @param int $pre A optional prefix for the process table.
     */
    public function getProcess( $pre='' , $processid )
    {
        $this->get(
                   'GetProcess',
                   dirname(__FILE__) . '/Sql/GetProcess.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $processid ) ? $processid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $comid ) ? $comid : '',
                   true
                   );
    }

    /**
     * Returns processes to a given course.
     *
     * Called when this component receives an HTTP GET request to
     * (/$pre)/process(/process)/$courseid(/).
     *
     * @param string $courseid The id of the course.
     * @param int $pre A optional prefix for the process table.
     */
    public function getCourseProcesses( $pre='' , $courseid )
    {
        $this->get(
                   'GetCourseProcesses',
                   dirname(__FILE__) . '/Sql/GetCourseProcesses.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $processid ) ? $processid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $comid ) ? $comid : '',
                   false,
                   false
                   );
    }

    /**
     * Returns status code 200, if this component is correctly installed for the given course
     *
     * Called when this component receives an HTTP GET request to
     * (/$pre)/link/exists/course/$courseid(/).
     *
     * @param string $courseid The id of the course.
     * @param int $pre A optional prefix for the attachment table.
     */
    public function getExistsCourseProcesses( $pre='' , $courseid )
    {
        $this->get(
                   'GetExistsCourseProcesses',
                   dirname(__FILE__) . '/Sql/GetExistsCourseProcesses.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $processid ) ? $processid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $comid ) ? $comid : '',
                   true,
                   false
                   );
    }

    /**
     * Returns processes to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * (/$pre)/process/exercisesheet/$esid(/)
     *
     * @param string $esid The id of the exercise sheet.
     * @param int $pre A optional prefix for the process table.
     */
    public function getSheetProcesses( $pre='' , $esid )
    {
        $this->get(
                   'GetSheetProcesses',
                   dirname(__FILE__) . '/Sql/GetSheetProcesses.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $processid ) ? $processid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $comid ) ? $comid : '',
                   false
                   );
    }

    /**
     * Returns processes to a given exercise.
     *
     * Called when this component receives an HTTP GET request to
     * (/$pre)/process/exercise/$eid(/)
     *
     * @param string $eid The id of the exercise.
     * @param int $pre A optional prefix for the process table.
     */
    public function getExerciseProcesses( $pre='' , $eid )
    {
        $this->get(
                   'GetExerciseProcesses',
                   dirname(__FILE__) . '/Sql/GetExerciseProcesses.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $processid ) ? $processid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $comid ) ? $comid : '',
                   false
                   );
    }

    /**
     * Returns processes to a given course and component.
     *
     * Called when this component receives an HTTP GET request to
     * (/$pre)/process/course/$courseid/component/$comid(/)
     *
     * @param string $courseid The id of the course.
     * @param string $comid The id of the component.
     * @param int $pre A optional prefix for the process table.
     */
    public function getCourseComponentProcesses( $pre='' , $courseid, $comid )
    {
        $this->get(
                   'GetCourseComponentProcesses',
                   dirname(__FILE__) . '/Sql/GetCourseComponentProcesses.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $processid ) ? $processid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $comid ) ? $comid : '',
                   false
                   );
    }

    /**
     * Removes the component from a given course
     *
     * Called when this component receives an HTTP DELETE request to
     * (/$pre)/course/$courseid(/).
     *
     * @param string $courseid The id of the course.
     * @param int $pre A optional prefix for the attachment table.
     */
    public function deleteCourse( $pre='' , $courseid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts DELETE DeleteCourse',
                    LogLevel::DEBUG
                    );

        $courseid = DBJson::mysql_real_escape_string( $courseid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteCourse.sql',
                                              array( 'courseid' => $courseid ,'pre' => $pre )
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
                        'DELETE DeleteCourse failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->response->setBody( '' );
            $this->_app->stop( );
        }
    }

    /**
     * Adds the component to a course
     *
     * Called when this component receives an HTTP POST request to
     * (/$pre)/course(/).
     *
     * @param int $pre A optional prefix for the attachment table.
     */
    public function addCourse($pre='' )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts POST AddCourse',
                    LogLevel::DEBUG
                    );

        // decode the received course data, as an object
        $insert = Course::decodeCourse( $this->_app->request->getBody( ) );

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
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/AddCourse.sql',
                                                  array( 'object' => $in ,'pre' => $pre )
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
                            'POST AddCourse failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( Course::encodeCourse( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr &&
             count( $res ) == 1 ){
            $this->_app->response->setBody( Course::encodeCourse( $res[0] ) );

        } else
            $this->_app->response->setBody( Course::encodeCourse( $res ) );
    }
}

 