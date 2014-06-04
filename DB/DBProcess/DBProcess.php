<?php 


/**
 * @file DBProcess.php contains the DBProcess class
 *
 * @author Till Uhlig
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
     *
     * @param Component $conf component data
     */
    public function __construct( $conf )
    {

        // initialize component
        $this->_conf = $conf;

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
    
    public function loadConfig( $pre='' )
    {
        // initialize component
        $this->_conf = $this->_conf->loadConfig( $pre );
        $this->query = array( CConfig::getLink( 
                                               $this->_conf->getLinks( ),
                                               'out'
                                               ) );
    }
    
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
                                                  'Sql/EditProcess.sql',
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
                                              'Sql/DeleteProcess.sql',
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
                                                  'Sql/AddProcess.sql',
                                                  array( 'object' => $in,'pre' => $pre )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new Process( );
                $course = Course::ExtractCourse($queryResult[count($queryResult)-1]->getResponse(),true);

                $obj->setProcessId( $course['id'] . '_' . $queryResult[count($queryResult)-2]->getInsertId( ) );

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
            $this->_app->response->setBody( Form::encodeForm( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( Form::encodeForm( $res ) );
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

    public function getProcess( $pre='' , $processid )
    {
        $this->get( 
                   'GetProcess',
                   'Sql/GetProcess.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $processid ) ? $processid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $comid ) ? $comid : '',
                   true
                   );
    }
   
    public function getCourseProcesses( $pre='' , $courseid )
    {
        $this->get( 
                   'GetCourseProcesses',
                   'Sql/GetCourseProcesses.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $processid ) ? $processid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $comid ) ? $comid : '',
                   false
                   );
    }
    
    public function getExistsCourseProcesses( $pre='' , $courseid )
    {
        $this->get( 
                   'GetExistsCourseProcesses',
                   'Sql/GetExistsCourseProcesses.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $processid ) ? $processid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $comid ) ? $comid : '',
                   true
                   );
    }
    
    public function getSheetProcesses( $pre='' , $esid )
    {
        $this->get( 
                   'GetSheetProcesses',
                   'Sql/GetSheetProcesses.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $processid ) ? $processid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $comid ) ? $comid : '',
                   false
                   );
    }
    
    public function getExerciseProcesses( $pre='' , $eid )
    {
        $this->get( 
                   'GetExerciseProcesses',
                   'Sql/GetExerciseProcesses.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $processid ) ? $processid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $comid ) ? $comid : '',
                   false
                   );
    }
    
    public function getCourseComponentProcesses( $pre='' , $courseid, $comid )
    {
        $this->get( 
                   'GetCourseComponentProcesses',
                   'Sql/GetCourseComponentProcesses.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $processid ) ? $processid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $comid ) ? $comid : '',
                   false
                   );
    }
    
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
                                              'Sql/DeleteCourse.sql',
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
                                                  'Sql/AddCourse.sql',
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

 
?>

