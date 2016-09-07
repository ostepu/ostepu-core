<?php
/**
 * @file DBRedirect.php contains the DBRedirect class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.5.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
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
 * A class, to abstract the "DBRedirect" table from database
 */
class DBRedirect
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
    private static $_prefix = 'redirect';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBRedirect::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBRedirect::$_prefix = $value;
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
        $com = new CConfig( DBRedirect::getPrefix( ) . ',course,link', dirname(__FILE__) );

        // runs the DBRedirect
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

        // PUT EditRedirect
        $this->_app->put(
                         '(/:pre)/' . $this->getPrefix( ) . '/redirect/:redid',
                         array(
                               $this,
                               'editRedirect'
                               )
                         );

        // DELETE DeleteRedirect
        $this->_app->delete(
                            '(/:pre)/' . $this->getPrefix( ) . '/redirect/:redid',
                            array(
                                  $this,
                                  'deleteRedirect'
                                  )
                            );

        // POST AddRedirect
        $this->_app->post(
                          '(/:pre)/' . $this->getPrefix( ).'/course/:courseid',
                          array(
                                $this,
                                'addRedirect'
                                )
                          );

        // GET GetExistsCourseRedirects
        $this->_app->get(
                         '(/:pre)/link/exists/course/:courseid',
                         array(
                               $this,
                               'getExistsCourseRedirects'
                               )
                        );

        // GET GetCourseRedirects
        $this->_app->get(
                         '(/:pre)/' . $this->getPrefix( ) . '/course/:courseid',
                         array(
                               $this,
                               'getCourseRedirects'
                               )
                         );

        // GET GetRedirect
        $this->_app->get(
                         '(/:pre)/' . $this->getPrefix( ) . '/redirect/:redid',
                         array(
                               $this,
                               'getRedirect'
                               )
                         );

        // GET GetRedirectByLocation
        $this->_app->get(
                         '(/:pre)/' . $this->getPrefix( ) . '/course/:courseid/location/:locname',
                         array(
                               $this,
                               'getRedirectByLocation'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Loads the configuration data for the component from CConfig.json file
     *
     * @param int $pre A optional prefix for the Redirect table.
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
     * Edits a Redirect.
     */
    public function editRedirect( $pre='' ,$redid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts PUT EditRedirect',
                    LogLevel::DEBUG
                    );

        $redid = DBJson::mysql_real_escape_string( $redid );
        $pre = DBJson::mysql_real_escape_string( $pre );

        // decode the received Redirect data, as an object
        $insert = Redirect::decodeRedirect( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/EditRedirect.sql',
                                                  array(
                                                        'redid' => $redid,
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
                            'PUT EditRedirect failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes a Redirect.
     */
    public function deleteRedirect( $pre='' ,$redid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts DELETE DeleteRedirect',
                    LogLevel::DEBUG
                    );

        $redid = DBJson::mysql_real_escape_string( $redid );
        $pre = DBJson::mysql_real_escape_string( $pre );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteRedirect.sql',
                                              array( 'redid' => $redid,'pre' => $pre )
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
                        'DELETE DeleteRedirect failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds an Redirect.
     */
    public function addRedirect( $pre='', $courseid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;

        Logger::Log(
                    'starts POST AddRedirect',
                    LogLevel::DEBUG
                    );

        // decode the received Redirect data, as an object
        $insert = Redirect::decodeRedirect( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/AddRedirect.sql',
                                                  array( 'object' => $in,'pre' => $pre,'courseid' => $courseid )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 &&
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new Redirect( );
                $course = Course::ExtractCourse($queryResult[count($queryResult)-1]->getResponse(),true);
                $insertId = $queryResult[count($queryResult)-2]->getInsertId( );
                if ($insertId==0 && $in->getId()>0){
                    $insertId=Redirect::getIdFromRedirectId($in->getId());
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
                            'POST AddRedirect failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( Redirect::encodeRedirect( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr &&
             count( $res ) == 1 ){
            $this->_app->response->setBody( Redirect::encodeRedirect( $res[0] ) );

        } else
            $this->_app->response->setBody( Redirect::encodeRedirect( $res ) );
    }

    public function get(
                        $functionName,
                        $sqlFile,
                        $pre='' ,
                        $redid,
                        $locname,
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
        //$redid = DBJson::mysql_real_escape_string( $redid );
        //$courseid = DBJson::mysql_real_escape_string( $courseid );
        $locname = DBJson::mysql_real_escape_string( $locname );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              $sqlFile,
                                              array(
                                                    'pre' => $pre,
                                                    'redid' => $redid,
                                                    'courseid' => $courseid,
                                                    'locname' => $locname
                                                    )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );

            if (is_array($query))
            $query = $query[count($query)-1];

            if ( $query->getNumRows( ) > 0 ){
                $res = Redirect::ExtractRedirect(
                                                     $query->getResponse( ),
                                                     $singleResult
                                                     );

                $this->_app->response->setBody( Redirect::encodeRedirect( $res ) );

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
        $this->_app->response->setBody( Redirect::encodeRedirect( new Redirect( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns the Redirects to a given course.
     */
    public function getCourseRedirects($pre='' , $courseid )
    {
        $this->get(
                   'GetCourseRedirects',
                   dirname(__FILE__) . '/Sql/GetCourseRedirects.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $redid ) ? $redid : '',
                   isset( $locname ) ? $locname : '',
                   isset( $courseid ) ? $courseid : ''
                   );
    }

    /**
     * Returns a Redirect.
     */
    public function getRedirect($pre='' , $redid )
    {
        $this->get(
                   'GetRedirect',
                   dirname(__FILE__) . '/Sql/GetRedirect.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $redid ) ? $redid : '',
                   isset( $locname ) ? $locname : '',
                   isset( $courseid ) ? $courseid : '',
                   true
                   );
    }

    /**
     * Returns a Redirect.
     */
    public function getRedirectByLocation($pre='' , $courseid, $locname )
    {
        $this->get(
                   'GetRedirectByLocation',
                   dirname(__FILE__) . '/Sql/GetRedirectByLocation.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $redid ) ? $redid : '',
                   isset( $locname ) ? $locname : '',
                   isset( $courseid ) ? $courseid : ''
                   );
    }

    /**
     * Returns status code 200, if this component is correctly installed for the given course
     */
    public function getExistsCourseRedirects( $pre='' , $courseid )
    {
        $this->get(
                   'GetExistsCourseRedirects',
                   dirname(__FILE__) . '/Sql/GetExistsCourseRedirects.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $redid ) ? $redid : '',
                   isset( $locname ) ? $locname : '',
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

 