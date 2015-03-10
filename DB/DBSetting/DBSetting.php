<?php 


/**
 * @file DBSetting.php contains the DBSetting class
 *
 * @author Till Uhlig
 * @date 2014
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
 * A class, to abstract the "Settings" table from database
 */
class DBSetting
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
    private static $_prefix = 'setting';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBSetting::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBSetting::$_prefix = $value;
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
        $com = new CConfig( DBSetting::getPrefix( ) . ',course,link', dirname(__FILE__) );

        // runs the DBSetting
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
                         
        // POST DeleteCourse
        $this->_app->delete( 
                         '(/:pre)/course/:courseid',
                         array( 
                               $this,
                               'deleteCourse'
                               )
                         );

        // PUT EditSetting
        $this->_app->put( 
                         '(/:pre)/' . $this->getPrefix( ) . '/setting/:setid',
                         array( 
                               $this,
                               'editSetting'
                               )
                         );

        // DELETE DeleteSetting
        $this->_app->delete( 
                            '(/:pre)/' . $this->getPrefix( ) . '/setting/:setid',
                            array( 
                                  $this,
                                  'deleteSetting'
                                  )
                            );

        // POST AddSetting
        $this->_app->post( 
                          '(/:pre)/' . $this->getPrefix( ).'/course/:courseid',
                          array( 
                                $this,
                                'addSetting'
                                )
                          );
                          
        // GET GetExistsCourseSettings
        $this->_app->get( 
                         '(/:pre)/link/exists/course/:courseid',
                         array( 
                               $this,
                               'getExistsCourseSettings'
                               )
                        );
                                             
        // GET GetCourseSettings
        $this->_app->get( 
                         '(/:pre)/' . $this->getPrefix( ) . '/course/:courseid',
                         array( 
                               $this,
                               'getCourseSettings'
                               )
                         );
                         
        // GET GetSetting
        $this->_app->get( 
                         '(/:pre)/' . $this->getPrefix( ) . '/setting/:setid',
                         array( 
                               $this,
                               'getSetting'
                               )
                         ); 
                         
        // GET GetSettingByName
        $this->_app->get( 
                         '(/:pre)/' . $this->getPrefix( ) . '/course/:courseid/name/:setname',
                         array( 
                               $this,
                               'getSettingByName'
                               )
                         );  
                         
        // run Slim
        $this->_app->run( );
    }
    
    /**
     * Loads the configuration data for the component from CConfig.json file
     *
     * @param int $pre A optional prefix for the Setting table.
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
     * Edits an Setting.
     */
    public function editSetting( $pre='' ,$setid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;
        
        Logger::Log( 
                    'starts PUT EditSetting',
                    LogLevel::DEBUG
                    );

        $setid = DBJson::mysql_real_escape_string( $setid );
        $pre = DBJson::mysql_real_escape_string( $pre );

        // decode the received Setting data, as an object
        $insert = Setting::decodeSetting( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/EditSetting.sql',
                                                  array( 
                                                        'setid' => $setid,
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
                            'PUT EditSetting failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes an Setting.
     */
    public function deleteSetting( $pre='' ,$setid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;
        
        Logger::Log( 
                    'starts DELETE DeleteSetting',
                    LogLevel::DEBUG
                    );

        $setid = DBJson::mysql_real_escape_string( $setid );
        $pre = DBJson::mysql_real_escape_string( $pre );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteSetting.sql',
                                              array( 'setid' => $setid,'pre' => $pre )
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
                        'DELETE DeleteSetting failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds an Setting.
     */
    public function addSetting( $pre='', $courseid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;
        
        Logger::Log( 
                    'starts POST AddSetting',
                    LogLevel::DEBUG
                    );

        // decode the received Setting data, as an object
        $insert = Setting::decodeSetting( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/AddSetting.sql',
                                                  array( 'object' => $in,'pre' => $pre,'courseid' => $courseid )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new Setting( );
                $course = Course::ExtractCourse($queryResult[count($queryResult)-1]->getResponse(),true);
                $insertId = $queryResult[count($queryResult)-2]->getInsertId( );
                if ($insertId==0 && $in->getId()>0){
                    $insertId=Setting::getIdFromSettingId($in->getId());
                }
                
                if ($insertId!=0)
                    $obj->setId( $course['id'] . '_' . $insertId );

                $res[] = $obj;
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'POST AddSetting failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( Setting::encodeSetting( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( Setting::encodeSetting( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( Setting::encodeSetting( $res ) );
    }

    public function get( 
                        $functionName,
                        $sqlFile,
                        $pre='' ,
                        $setid,
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
        $setid = DBJson::mysql_real_escape_string( $setid );
        $courseid = DBJson::mysql_real_escape_string( $courseid );
        $setname = DBJson::mysql_real_escape_string( $setname );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              $sqlFile,
                                              array( 
                                                    'pre' => $pre,
                                                    'setid' => $setid,
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
                $res = Setting::ExtractSetting( 
                                                     $query->getResponse( ),
                                                     $singleResult
                                                     );

                $this->_app->response->setBody( Setting::encodeSetting( $res ) );

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
        $this->_app->response->setBody( Setting::encodeSetting( new Setting( ) ) );
        $this->_app->stop( );
    }
    
    /**
     * Returns the Settings to a given course.
     */
    public function getCourseSettings($pre='' , $courseid )
    {
        $this->get( 
                   'GetCourseSettings',
                   dirname(__FILE__) . '/Sql/GetCourseSettings.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $setid ) ? $setid : '',
                   isset( $setname ) ? $setname : '',
                   isset( $courseid ) ? $courseid : ''
                   );
    }
    
    /**
     * Returns a Setting.
     */
    public function getSetting($pre='' , $setid )
    {
        $this->get( 
                   'GetSetting',
                   dirname(__FILE__) . '/Sql/GetSetting.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $setid ) ? $setid : '',
                   isset( $setname ) ? $setname : '',
                   isset( $courseid ) ? $courseid : '',
                   true
                   );
    }
    
    /**
     * Returns a Setting.
     */
    public function getSettingByName($pre='' , $courseid, $setname )
    {
        $this->get( 
                   'GetSettingByName',
                   dirname(__FILE__) . '/Sql/GetSettingByName.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $setid ) ? $setid : '',
                   isset( $setname ) ? $setname : '',
                   isset( $courseid ) ? $courseid : '',
                   true
                   );
    }
    
    /**
     * Returns status code 200, if this component is correctly installed for the given course
     */
    public function getExistsCourseSettings( $pre='' , $courseid )
    {
        $this->get( 
                   'GetExistsCourseSettings',
                   dirname(__FILE__) . '/Sql/GetExistsCourseSettings.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $setid ) ? $setid : '',
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
                                                  dirname(__FILE__) . '/Sql/AddCourse.sql',
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