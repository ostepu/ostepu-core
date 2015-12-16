<?php
/**
 * @file DBForm.php contains the DBForm class
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
 * A class, to abstract the "DBForm" table from database
 */
class DBForm
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
    private static $_prefix = 'form';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBForm::$_prefix;
    }

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function setPrefix( $value )
    {
        DBForm::$_prefix = $value;
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
        $com = new CConfig( DBForm::getPrefix( ). ',course,link', dirname(__FILE__) );

        // runs the DBForm
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

        // POST AddCourse
        $this->_app->post(
                         '/course(/)',
                         array(
                               $this,
                               'addCourse'
                               )
                         );

        // POST DeleteCourse
        $this->_app->delete(
                         '/course/:courseid(/)',
                         array(
                               $this,
                               'deleteCourse'
                               )
                         );

        // PUT EditForm
        $this->_app->put(
                         '/' . $this->getPrefix( ) . '(/form)/:formid(/)',
                         array(
                               $this,
                               'editForm'
                               )
                         );

        // DELETE DeleteForm
        $this->_app->delete(
                            '/' . $this->getPrefix( ) . '(/form)/:formid(/)',
                            array(
                                  $this,
                                  'deleteForm'
                                  )
                            );

        // POST AddForm
        $this->_app->post(
                          '/' . $this->getPrefix( ) . '(/)',
                          array(
                                $this,
                                'addForm'
                                )
                          );

        // GET GetForm
        $this->_app->get(
                         '/' . $this->getPrefix( ) . '(/form)/:formid(/)',
                         array(
                               $this,
                               'getForm'
                               )
                         );

        // GET GetCourseForms
        $this->_app->get(
                         '/' . $this->getPrefix( ) . '/course/:courseid(/)',
                         array(
                               $this,
                               'getCourseForms'
                               )
                         );

        // GET GetExistsCourseForms
        $this->_app->get(
                         '/link/exists/course/:courseid(/)',
                         array(
                               $this,
                               'getExistsCourseForms'
                               )
                         );

        // GET GetSheetForms
        $this->_app->get(
                         '/' . $this->getPrefix( ) . '/exercisesheet/:esid(/)',
                         array(
                               $this,
                               'getSheetForms'
                               )
                         );

        // GET GetExerciseForms
        $this->_app->get(
                         '/' . $this->getPrefix( ) . '/exercise/:eid(/)',
                         array(
                               $this,
                               'getExerciseForms'
                               )
                         );

            // run Slim
            $this->_app->run( );
    }

    /**
     * Edits a form.
     *
     * Called when this component receives an HTTP PUT request to
     * /form(/form)/$formid(/)
     * The request body should contain a JSON object representing the
     * form's new attributes.
     *
     * @param string $formid The id of the form that is being updated.
     */
    public function editForm( $formid )
    {
        Logger::Log(
                    'starts PUT EditForm',
                    LogLevel::DEBUG
                    );

        $formid = DBJson::mysql_real_escape_string( $formid );

        // decode the received choice data, as an object
        $insert = Form::decodeForm( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/EditForm.sql',
                                                  array(
                                                        'formid' => $formid,
                                                        'object' => $in
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
                            'PUT EditForm failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes a form.
     *
     * Called when this component receives an HTTP DELETE request to
     * (/$pre)/form(/form)/$formid(/).
     *
     * @param string $formid The id of the form that is being deleted.
     */
    public function deleteForm( $formid )
    {
        Logger::Log(
                    'starts DELETE DeleteForm',
                    LogLevel::DEBUG
                    );

        $formid = DBJson::mysql_real_escape_string( $formid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteForm.sql',
                                              array( 'formid' => $formid )
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
                        'DELETE DeleteForm failed',
                        LogLevel::ERROR
                        );

            $this->_app->response->setBody( Form::encodeForm( new Form( ) ) );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds a form.
     *
     * Called when this component receives an HTTP POST request to
     * (/$pre)/form(/).
     */
    public function addForm( )
    {
        Logger::Log(
                    'starts POST AddForm',
                    LogLevel::DEBUG
                    );

        // decode the received choice data, as an object
        $insert = Form::decodeForm( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/AddForm.sql',
                                                  array( 'object' => $in)
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 &&
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new Form( );
                $course = Course::ExtractCourse($queryResult[count($queryResult)-1]->getResponse(),true);

                $obj->setFormId( $course->getId() . '_' . $queryResult[count($queryResult)-2]->getInsertId( ) );

                $res[] = $obj;
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set(
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );

            } else {
                Logger::Log(
                            'POST AddForm failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( Form::encodeForm( $res ) );
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
                        $formid,
                        $courseid,
                        $esid,
                        $eid,
                        $singleResult = false,
                        $checkSession = true
                        )
    {
        Logger::Log(
                    'starts GET ' . $functionName,
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        $formid = DBJson::mysql_real_escape_string( $formid );

        DBJson::checkInput(
                           $this->_app,
                           $courseid == '' ? true : ctype_digit( $courseid ),
                           $esid == '' ? true : ctype_digit( $esid ),
                           $eid == '' ? true : ctype_digit( $eid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              $sqlFile,
                                              array(
                                                    'formid' => $formid,
                                                    'courseid' => $courseid,
                                                    'esid' => $esid,
                                                    'eid' => $eid
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
                $res = Form::ExtractForm(
                                         $query->getResponse( ),
                                         $singleResult
                                         );
                $this->_app->response->setBody( Form::encodeForm( $res ) );

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
        $this->_app->response->setBody( Form::encodeForm( new Form( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns a form.
     *
     * Called when this component receives an HTTP GET request to
     * /form(/form)/$formid(/).
     *
     * @param string $formid The id of the form.
     */
    public function getForm( $formid )
    {
        $this->get(
                   'GetForm',
                   dirname(__FILE__) . '/Sql/GetForm.sql',
                   isset( $formid ) ? $formid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   true
                   );
    }

    /**
     * Returns forms to a given course.
     *
     * Called when this component receives an HTTP GET request to
     * /form(/form)/$processid(/).
     *
     * @param string $courseid The id of the course.
     */
    public function getCourseForms( $courseid )
    {
        $this->get(
                   'GetCourseForms',
                   dirname(__FILE__) . '/Sql/GetCourseForms.sql',
                   isset( $formid ) ? $formid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
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
     */
    public function getExistsCourseForms( $courseid )
    {
        $this->get(
                   'GetExistsCourseForms',
                   dirname(__FILE__) . '/Sql/GetExistsCourseForms.sql',
                   isset( $formid ) ? $formid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   true,
                   false
                   );
    }

    /**
     * Returns forms to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /form/exercisesheet/$esid(/)
     *
     * @param string $esid The id of the exercise sheet.
     */
    public function getSheetForms( $esid )
    {
        $this->get(
                   'GetSheetForms',
                   dirname(__FILE__) . '/Sql/GetSheetForms.sql',
                   isset( $formid ) ? $formid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   false
                   );
    }

    /**
     * Returns forms to a given exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /form/exercise/$eid(/)
     *
     * @param string $eid The id of the exercise.
     */
    public function getExerciseForms( $eid )
    {
        $this->get(
                   'GetExerciseForms',
                   dirname(__FILE__) . '/Sql/GetExerciseForms.sql',
                   isset( $formid ) ? $formid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   false
                   );
    }

    /**
     * Removes the component from a given course
     *
     * Called when this component receives an HTTP DELETE request to
     * /course/$courseid(/).
     *
     * @param string $courseid The id of the course.
     */
    public function deleteCourse( $courseid )
    {
        Logger::Log(
                    'starts DELETE DeleteCourse',
                    LogLevel::DEBUG
                    );

        $courseid = DBJson::mysql_real_escape_string( $courseid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteCourse.sql',
                                              array( 'courseid' => $courseid )
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
     * /course(/).
     */
    public function addCourse( )
    {
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
                                                  array( 'object' => $in )
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

 