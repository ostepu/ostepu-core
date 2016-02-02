<?php


/**
 * @file DBNotification.php contains the DBNotification class
 *
 * @author Till Uhlig
 * @date 2014
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
 * A class, to abstract the "Notifications" table from database
 */
class DBNotification
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
    private static $_prefix = 'notification';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBNotification::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBNotification::$_prefix = $value;
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
        $com = new CConfig( DBNotification::getPrefix( ) . ',course,link', dirname(__FILE__) );

        // runs the DBNotification
        if ( $com->used( ) ) return;

        $this->_conf = $com;

        // initialize slim
        $this->_app = new \Slim\Slim(array('debug' => true));
        $this->_app->response->headers->set(
                                            'Content-Type',
                                            'application/json'
                                            );

        // POST AddCourse
        $this->_app->post(
                         '(/:pre)/course',
                         array(
                               $this,
                               'addCourse'
                               )
                         );

        // POST AddPlatform
        $this->_app->post(
                         '(/:pre)/platform',
                         array(
                               $this,
                               'addPlatform'
                               )
                         );

        // POST DeleteCourse
        $this->_app->delete(
                         '(/:pre)/course/:courseid',
                         array(
                               $this,
                               'deleteCourse'
                               )
                         );

        // PUT EditNotification
        $this->_app->put(
                         '(/:pre)/' . $this->getPrefix( ) . '/notification/:notid',
                         array(
                               $this,
                               'editNotification'
                               )
                         );

        // DELETE DeleteNotification
        $this->_app->delete(
                            '(/:pre)/' . $this->getPrefix( ) . '/notification/:notid',
                            array(
                                  $this,
                                  'deleteNotification'
                                  )
                            );

        // POST AddNotification
        $this->_app->post(
                          '(/:pre)/' . $this->getPrefix( ).'/course/:courseid',
                          array(
                                $this,
                                'addNotification'
                                )
                          );

        // GET GetExistsCourseNotifications
        $this->_app->get(
                         '(/:pre)/link/exists/course/:courseid',
                         array(
                               $this,
                               'getExistsCourseNotifications'
                               )
                        );

        // GET GetCourseNotifications
        $this->_app->get(
                         '(/:pre)/' . $this->getPrefix( ) . '/course/:courseid',
                         array(
                               $this,
                               'getCourseNotifications'
                               )
                         );

        // GET GetNotification
        $this->_app->get(
                         '(/:pre)/' . $this->getPrefix( ) . '/notification/:notid',
                         array(
                               $this,
                               'getNotification'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Loads the configuration data for the component from CConfig.json file
     *
     * @param int $pre A optional prefix for the Notification table.
     *
     * @return an component object, which represents the configuration
     */
    public function loadConfig( $pre='' ){
        // initialize component
        $this->_conf = $this->_conf->loadConfig( $pre );
        $this->query = array( CConfig::getLink(
                                               $this->_conf->getLinks( ),
                                               'out'
                                               ) );
    }

    /**
     * Edits an Notification.
     */
    public function editNotification( $pre='' ,$notid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts PUT EditNotification',
                    LogLevel::DEBUG
                    );

        $notid = DBJson::mysql_real_escape_string( $notid );
        $pre = DBJson::mysql_real_escape_string( $pre );

        // decode the received Notification data, as an object
        $insert = Notification::decodeNotification( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/EditNotification.sql',
                                                  array(
                                                        'notid' => $notid,
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
                            'PUT EditNotification failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes an Notification.
     */
    public function deleteNotification( $pre='' ,$notid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts DELETE DeleteNotification',
                    LogLevel::DEBUG
                    );

        $notid = DBJson::mysql_real_escape_string( $notid );
        $pre = DBJson::mysql_real_escape_string( $pre );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteNotification.sql',
                                              array( 'notid' => $notid,'pre' => $pre )
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
                        'DELETE DeleteNotification failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds an Notification.
     */
    public function addNotification( $pre='', $courseid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts POST AddNotification',
                    LogLevel::DEBUG
                    );

        // decode the received Notification data, as an object
        $insert = Notification::decodeNotification( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        $courseid = DBJson::mysql_real_escape_string( $courseid );
        $pre = DBJson::mysql_real_escape_string( $pre );

        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $insert as $in ){

            // generates the insert data for the object
            $data = $in->getInsertData( );

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile(
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/AddNotification.sql',
                                                  array( 'object' => $in,'pre' => $pre,'courseid' => $courseid )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 &&
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new Notification( );
                $course = Course::ExtractCourse($queryResult[count($queryResult)-1]->getResponse(),true);
                $insertId = $queryResult[count($queryResult)-2]->getInsertId( );
                if ($insertId==0 && $in->getId()>0){
                    $insertId=Notification::getIdFromNotificationId($in->getId());
                }

                if ($insertId!=0)
                    $obj->setId( $course->getId() . '_' . $insertId );

                $res[] = $obj;
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set(
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );

            } else {
                Logger::Log(
                            'POST AddNotification failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( Notification::encodeNotification( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr &&
             count( $res ) == 1 ){
            $this->_app->response->setBody( Notification::encodeNotification( $res[0] ) );

        } else
            $this->_app->response->setBody( Notification::encodeNotification( $res ) );
    }

    public function get(
                        $functionName,
                        $sqlFile,
                        $pre='' ,
                        $notid,
                        $setname,
                        $courseid,
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
        //$notid = DBJson::mysql_real_escape_string( $notid );
        //$courseid = DBJson::mysql_real_escape_string( $courseid );
        $setname = DBJson::mysql_real_escape_string( $setname );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              $sqlFile,
                                              array(
                                                    'pre' => $pre,
                                                    'notid' => $notid,
                                                    'courseid' => $courseid,
                                                    'setname' => $setname
                                                    )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );

            if (is_array($query))
            $query = $query[count($query)-1];

            if ( $query->getNumRows( ) > 0 ){
                $res = Notification::ExtractNotification(
                                                     $query->getResponse( ),
                                                     $singleResult
                                                     );

                $this->_app->response->setBody( Notification::encodeNotification( $res ) );

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
        $this->_app->response->setBody( Notification::encodeNotification( new Notification( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns the Notifications to a given course.
     */
    public function getCourseNotifications($pre='' , $courseid )
    {
        $this->get(
                   'GetCourseNotifications',
                   dirname(__FILE__) . '/Sql/GetCourseNotifications.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $notid ) ? $notid : '',
                   isset( $setname ) ? $setname : '',
                   isset( $courseid ) ? $courseid : ''
                   );
    }

    /**
     * Returns a Notification.
     */
    public function getNotification($pre='' , $notid )
    {
        $this->get(
                   'GetNotification',
                   dirname(__FILE__) . '/Sql/GetNotification.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $notid ) ? $notid : '',
                   isset( $setname ) ? $setname : '',
                   isset( $courseid ) ? $courseid : '',
                   true
                   );
    }

    /**
     * Returns status code 200, if this component is correctly installed for the given course
     */
    public function getExistsCourseNotifications( $pre='' , $courseid )
    {
        $this->get(
                   'GetExistsCourseNotifications',
                   dirname(__FILE__) . '/Sql/GetExistsCourseNotifications.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $notid ) ? $notid : '',
                   isset( $setname ) ? $setname : '',
                   isset( $courseid ) ? $courseid : ''
                   );
    }

    /**
     * Removes the component from a given course
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

    /**
     * Adds the component to a course
     */
    public function addCourse( $pre='', $type='Course' )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts POST AddCourse',
                    LogLevel::DEBUG
                    );

        // decode the received course data, as an object
        $insert = $type::{'decode'.$type}( $this->_app->request->getBody( ) );

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
                                                  array( 'object' => ($type==='Course'?$in:null),'pre' => $pre )
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
                $this->_app->response->setBody( $type::{'encode'.$type}( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr &&
             count( $res ) == 1 ){
            $this->_app->response->setBody( $type::{'encode'.$type}( $res[0] ) );

        } else
            $this->_app->response->setBody( $type::{'encode'.$type}( $res ) );
    }

    /**
     * Adds the component to the platform
     */
    public function addPlatform( $pre='' )
    {
        return $this->addCourse($pre, 'platform');
    }
}

 