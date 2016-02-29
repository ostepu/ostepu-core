<?php
/**
 * @file DBAttachment.php contains the DBAttachment class
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 * @example DB/DBAttachment/AttachmentSample.json
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
 * A class, to abstract the "Attachment" table from database
 */
class DBAttachment
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
    private static $_prefix = 'attachment';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBAttachment::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBAttachment::$_prefix = $value;
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
        $com = new CConfig( DBAttachment::getPrefix( ), dirname(__FILE__) );

        // runs the DBAttachment
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
        $this->_app->response->setStatus( 409 );
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

        // PUT EditAttachment
        $this->_app->put(
                         '/' . $this->getPrefix( ) . '(/attachment)/:aid(/)',
                         array(
                               $this,
                               'editAttachment'
                               )
                         );

        // DELETE DeleteAttachment
        $this->_app->delete(
                            '/' . $this->getPrefix( ) . '(/attachment)/:aid(/)',
                            array(
                                  $this,
                                  'deleteAttachment'
                                  )
                            );

        // DELETE DeleteExerciseAttachments
        $this->_app->delete(
                            '/' . $this->getPrefix( ) . '/exercise/:eid(/)',
                            array(
                                  $this,
                                  'deleteExerciseAttachment'
                                  )
                            );

        // DELETE DeleteExerciseFileAttachment
        $this->_app->delete(
                            '/' . $this->getPrefix( ) . '/exercise/:eid/file/:fileid(/)',
                            array(
                                  $this,
                                  'deleteExerciseFileAttachment'
                                  )
                            );

        // POST AddAttachment
        $this->_app->post(
                          '/' . $this->getPrefix( ) . '(/)',
                          array(
                                $this,
                                'addAttachment'
                                )
                          );

        // GET GetAttachment
        $this->_app->get(
                         '/' . $this->getPrefix( ) . '(/attachment)/:aid(/)',
                         array(
                               $this,
                               'getAttachment'
                               )
                         );

        // GET GetAllAttachments
        $this->_app->get(
                         '/' . $this->getPrefix( ) . '(/attachment)(/)',
                         array(
                               $this,
                               'getAllAttachments'
                               )
                         );

        // GET GetExerciseAttachments
        $this->_app->get(
                         '/' . $this->getPrefix( ) . '/exercise/:eid(/)',
                         array(
                               $this,
                               'getExerciseAttachments'
                               )
                         );

        // GET GetSheetAttachments
        $this->_app->get(
                         '/' . $this->getPrefix( ) . '/exercisesheet/:esid(/)',
                         array(
                               $this,
                               'getSheetAttachments'
                               )
                         );

        // run Slim
        $this->_app->run( );
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
    public function editAttachment( $aid )
    {
        Logger::Log(
                    'starts PUT EditAttachment',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput(
                           $this->_app,
                           ctype_digit( $aid )
                           );

        // decode the received attachment data, as an object
        $insert = Attachment::decodeAttachment( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/EditAttachment.sql',
                                                  array(
                                                        'aid' => $aid,
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
    public function deleteAttachment( $aid )
    {
        Logger::Log(
                    'starts DELETE DeleteAttachment',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput(
                           $this->_app,
                           ctype_digit( $aid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteAttachment.sql',
                                              array( 'aid' => $aid )
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

    public function deleteExerciseAttachment( $eid )
    {
        // checks whether incoming data has the correct data type
        DBJson::checkInput(
                           $this->_app,
                           ctype_digit( $eid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteExerciseAttachment.sql',
                                              array( 'eid' => $eid )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){
            $this->_app->response->setStatus( 201 );

        } else {
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    public function deleteExerciseFileAttachment( $eid, $fileid )
    {
        // checks whether incoming data has the correct data type
        DBJson::checkInput(
                           $this->_app,
                           ctype_digit( $eid ),
                           ctype_digit( $fileid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteExerciseFileAttachment.sql',
                                              array( 'eid' => $eid, 'fileid' => $fileid )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){
            $this->_app->response->setStatus( 201 );

        } else {
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
    public function addAttachment( )
    {
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

        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $insert as $in ){

            // generates the insert data for the object
            $data = $in->getInsertData( );

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile(
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/AddAttachment.sql',
                                                  array( 'values' => $data )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 &&
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new Attachment( );
                $obj->setId( $queryResult->getInsertId( ) );
                $obj->setStatus(201);
                $res[] = $obj;
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set(
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );

            } else {
                $obj = new Attachment( );
                $obj->setStatus(409);
                $res[] = $obj;
              /*  Logger::Log(
                            'POST AddAttachment failed',
                            LogLevel::ERROR
                            );*/
               /// $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
               // $this->_app->response->setBody( Attachment::encodeAttachment( $res ) );
               // $this->_app->stop( );
            }
        }

        if ( !$arr &&
             count( $res ) == 1 ){
            $this->_app->response->setBody( Attachment::encodeAttachment( $res[0] ) );

        } else
            $this->_app->response->setBody( Attachment::encodeAttachment( $res ) );

        $this->_app->response->setStatus( 201 );
    }

    public function get(
                        $functionName,
                        $sqlFile,
                        $userid,
                        $courseid,
                        $esid,
                        $eid,
                        $suid,
                        $aid,
                        $singleResult = false,
                        $checkSession = true
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
                           $aid == '' ? true : ctype_digit( $aid )
                           );

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
                                                    'aid' => $aid
                                                    ),
                                              $checkSession
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );

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
    public function getAttachment( $aid )
    {
        $this->get(
                   'GetAttachment',
                   dirname(__FILE__) . '/Sql/GetAttachment.sql',
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
     * Returns all attachments.
     *
     * Called when this component receives an HTTP GET request to
     * /attachment(/) or /attachment/attachment(/).
     */
    public function getAllAttachments( )
    {
        $this->get(
                   'GetAllAttachments',
                   dirname(__FILE__) . '/Sql/GetAllAttachments.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $aid ) ? $aid : ''
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
    public function getExerciseAttachments( $eid )
    {
        $this->get(
                   'GetExerciseAttachments',
                   dirname(__FILE__) . '/Sql/GetExerciseAttachments.sql',
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
    public function getSheetAttachments( $esid )
    {
        $this->get(
                   'GetSheetAttachments',
                   dirname(__FILE__) . '/Sql/GetSheetAttachments.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $aid ) ? $aid : ''
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
                   'GetExistsPlatform',
                   dirname(__FILE__) . '/Sql/GetExistsPlatform.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $suid ) ? $suid : '',
                   isset( $aid ) ? $aid : '',
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

 