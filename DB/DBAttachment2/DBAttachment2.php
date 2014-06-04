<?php 


/**
 * @file DBAttachment2.php contains the DBAttachment2 class
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
 * A class, to abstract the "Attachment" table from database
 */
class DBAttachment2
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
    private static $_prefix = 'attachment';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBAttachment2::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBAttachment2::$_prefix = $value;
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
        $this->_conf = $conf;
        
        // initialize slim
        $this->_app = new \Slim\Slim(array('debug' => true));
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
                         
        // POST DeleteCourse
        $this->_app->delete( 
                         '(/:pre)/course/:courseid(/)',
                         array( 
                               $this,
                               'deleteCourse'
                               )
                         );

        // PUT EditAttachment
        $this->_app->put( 
                         '(/:pre)/' . $this->getPrefix( ) . '(/attachment)/:aid(/)',
                         array( 
                               $this,
                               'editAttachment'
                               )
                         );

        // DELETE DeleteAttachment
        $this->_app->delete( 
                            '(/:pre)/' . $this->getPrefix( ) . '(/attachment)/:aid(/)',
                            array( 
                                  $this,
                                  'deleteAttachment'
                                  )
                            );

        // POST AddAttachment
        $this->_app->post( 
                          '(/:pre)/' . $this->getPrefix( ),
                          array( 
                                $this,
                                'addAttachment'
                                )
                          );
                          
        // GET GetExistsCourseAttachments
        $this->_app->get( 
                         '(/:pre)/link/exists/course/:courseid(/)',
                         array( 
                               $this,
                               'getExistsCourseAttachments'
                               )
                        );
                        
        // GET GetAttachment
        $this->_app->get( 
                         '(/:pre)/' . $this->getPrefix( ) . '(/attachment)/:aid(/)',
                         array( 
                               $this,
                               'getAttachment'
                               )
                         );

        // GET GetExerciseAttachments
        $this->_app->get( 
                         '(/:pre)/' . $this->getPrefix( ) . '/exercise/:eid(/)',
                         array( 
                               $this,
                               'getExerciseAttachments'
                               )
                         );

        // GET GetSheetAttachments
        $this->_app->get( 
                         '(/:pre)/' . $this->getPrefix( ) . '/exercisesheet/:esid(/)',
                         array( 
                               $this,
                               'getSheetAttachments'
                               )
                         );
                                             
        // GET GetCourseAttachments
        $this->_app->get( 
                         '(/:pre)/' . $this->getPrefix( ) . '/course/:courseid(/)',
                         array( 
                               $this,
                               'getCourseAttachments'
                               )
                         );
                         
        // run Slim
        $this->_app->run( );
    }
    
    public function loadConfig( $pre='' ){
        // initialize component
        $this->_conf = $this->_conf->loadConfig( $pre );
        $this->query = array( CConfig::getLink( 
                                               $this->_conf->getLinks( ),
                                               'out'
                                               ) );
    }

    /**
     * Edits an attachment.
     *
     * Called when this component receives an HTTP PUT request to
     * /attachment/$aid(/) or /attachment/attachment/$aid(/).
     * The request body should contain a JSON object representing the
     * attachment's new attributes.
     *
     * @param string $aid The id of the attachment that is being updated.
     */
    public function editAttachment( $pre='' ,$aid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;
        
        Logger::Log( 
                    'starts PUT EditAttachment',
                    LogLevel::DEBUG
                    );

        $aid = DBJson::mysql_real_escape_string( $aid );
        $pre = DBJson::mysql_real_escape_string( $pre );

        // decode the received attachment data, as an object
        $insert = Attachment::decodeAttachment( $this->_app->request->getBody( ) );

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
                                                  'Sql/EditAttachment.sql',
                                                  array( 
                                                        'aid' => $aid,
                                                        'object' => $in,
                                                        'pre' => $pre
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
                            'PUT EditAttachment failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes an attachment.
     *
     * Called when this component receives an HTTP DELETE request to
     * /attachment/$aid(/) or /attachment/attachment/$aid(/).
     *
     * @param string $aid The id of the attachment that is being deleted.
     */
    public function deleteAttachment( $pre='' ,$aid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;
        
        Logger::Log( 
                    'starts DELETE DeleteAttachment',
                    LogLevel::DEBUG
                    );

        $aid = DBJson::mysql_real_escape_string( $aid );
        $pre = DBJson::mysql_real_escape_string( $pre );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              'Sql/DeleteAttachment.sql',
                                              array( 'aid' => $aid,'pre' => $pre )
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
                        'DELETE DeleteAttachment failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds an attachment.
     *
     * Called when this component receives an HTTP POST request to
     * /attachment(/).
     * The request body should contain a JSON object representing the
     * attachment's attributes.
     */
    public function addAttachment( $pre='' )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;
        
        Logger::Log( 
                    'starts POST AddAttachment',
                    LogLevel::DEBUG
                    );

        // decode the received attachment data, as an object
        $insert = Attachment::decodeAttachment( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }
        
        $pre = DBJson::mysql_real_escape_string( $pre );

        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $insert as $in ){

            // generates the insert data for the object
            $data = $in->getInsertData( );

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  'Sql/AddAttachment.sql',
                                                  array( 'object' => $in,'pre' => $pre )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new Attachment( );
                $course = Course::ExtractCourse($queryResult[count($queryResult)-1]->getResponse(),true);

                $obj->setId( $course['id'] . '_' . $queryResult[count($queryResult)-2]->getInsertId( ) );
                

                $res[] = $obj;
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'POST AddAttachment failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( Attachment::encodeAttachment( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( Attachment::encodeAttachment( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( Attachment::encodeAttachment( $res ) );
    }

    public function get( 
                        $functionName,
                        $sqlFile,
                        $pre='' ,
                        $userid,
                        $courseid,
                        $esid,
                        $eid,
                        $suid,
                        $aid,
                        $singleResult = false
                        )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;
        
        Logger::Log( 
                    'starts GET ' . $functionName,
                    LogLevel::DEBUG
                    );

        $pre = DBJson::mysql_real_escape_string( $pre );
        $userid = DBJson::mysql_real_escape_string( $userid );
        $courseid = DBJson::mysql_real_escape_string( $courseid );
        $esid = DBJson::mysql_real_escape_string( $esid );
        $eid = DBJson::mysql_real_escape_string( $eid );
        $suid = DBJson::mysql_real_escape_string( $suid );
        $aid = DBJson::mysql_real_escape_string( $aid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              $sqlFile,
                                              array( 
                                                    'pre' => $pre,
                                                    'userid' => $userid,
                                                    'courseid' => $courseid,
                                                    'esid' => $esid,
                                                    'eid' => $eid,
                                                    'suid' => $suid,
                                                    'aid' => $aid
                                                    )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );
            
            if (is_array($query))
            $query = $query[count($query)-1];

            if ( $query->getNumRows( ) > 0 ){
                $res = Attachment::ExtractAttachment( 
                                                     $query->getResponse( ),
                                                     $singleResult
                                                     );

                $this->_app->response->setBody( Attachment::encodeAttachment( $res ) );

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
        $this->_app->response->setBody( Attachment::encodeAttachment( new Attachment( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns an attachment.
     *
     * Called when this component receives an HTTP GET request to
     * /attachment/$aid(/) or /attachment/attachment/$aid(/).
     *
     * @param string $aid The id of the attachment that should be returned.
     */
    public function getAttachment( $pre='' ,$aid )
    {
        $this->get( 
                   'GetAttachment',
                   'Sql/GetAttachment.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $aid ) ? $aid : '',
                   true
                   );
    }

    /**
     * Returns the attachments to a given exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /attachment/exercise/$eid(/).
     *
     * @param string $eid The id of the exercise.
     */
    public function getExerciseAttachments( $pre='' ,$eid )
    {
        $this->get( 
                   'GetExerciseAttachments',
                   'Sql/GetExerciseAttachments.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $aid ) ? $aid : ''
                   );
    }

    /**
     * Returns the attachments to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /attachment/exercisesheet/$esid(/).
     *
     * @param string $esid The id of the exercise sheet.
     */
    public function getSheetAttachments($pre='' , $esid )
    {
        $this->get( 
                   'GetSheetAttachments',
                   'Sql/GetSheetAttachments.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $aid ) ? $aid : ''
                   );
    }
    
    public function getCourseAttachments($pre='' , $courseid )
    {
        $this->get( 
                   'GetCourseAttachments',
                   'Sql/GetCourseAttachments.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $aid ) ? $aid : ''
                   );
    }
    
    public function getExistsCourseAttachments( $pre='' , $courseid )
    {
        $this->get( 
                   'GetExistsCourseAttachments',
                   'Sql/GetExistsCourseAttachments.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $aid ) ? $aid : '',
                   true
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
                                              array( 'courseid' => $courseid,'pre' => $pre )
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
    
    public function addCourse( $pre='' )
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
                                                  array( 'object' => $in,'pre' => $pre )
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

