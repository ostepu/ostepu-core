<?php 


/**
 * @file DBExerciseSheet.php contains the DBExerciseSheet class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBExerciseSheet/ExerciseSheetSample.json
 * @date 2013-2014 
 */

require_once ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBJson.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBRequest.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/LArraySorter.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to abstract the "ExerciseSheet" table from database
 */
class DBExerciseSheet
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
    private static $_prefix = 'exercisesheet';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBExerciseSheet::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBExerciseSheet::$_prefix = $value;
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
        $com = new CConfig( DBExerciseSheet::getPrefix( ) );

        // runs the DBExerciseSheet
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig2( dirname(__FILE__) );//array(), dirname(__FILE__) );

        // initialize component
        $this->_conf = $conf;
        $this->query = array( CConfig::getLink( 
                                               $this->_conf->getLinks( ),
                                               'out'
                                               ) );

        $this->query2 = array( CConfig::getLink( 
                                               $this->_conf->getLinks( ),
                                               'out2'
                                               ) );

        // initialize slim
        $this->_app = new \Slim\Slim( array( 'debug' => true ) );
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
                         
        // PUT EditExerciseSheet
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '(/exercisesheet)/:esid(/)',
                         array( 
                               $this,
                               'editExerciseSheet'
                               )
                         );

        // DELETE DeleteExerciseSheet
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '(/exercisesheet)/:esid(/)',
                            array( 
                                  $this,
                                  'deleteExerciseSheet'
                                  )
                            );

        // POST SetExerciseSheet
        $this->_app->post( 
                          '/' . $this->getPrefix( ) . '(/)',
                          array( 
                                $this,
                                'addExerciseSheet'
                                )
                          );

        // GET GetExerciseSheetURL
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/exercisesheet)/:esid/url(/)',
                         array( 
                               $this,
                               'getExerciseSheetURL'
                               )
                         );

        // GET GetCourseSheetURLs
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:courseid/url(/)',
                         array( 
                               $this,
                               'getCourseSheetURLs'
                               )
                         );

        // GET GetCourseSheets
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/course/:courseid+(/)',
                         array( 
                               $this,
                               'getCourseSheets'
                               )
                         );

        // GET GetExerciseSheet
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/exercisesheet)/:esid+(/)',
                         array( 
                               $this,
                               'getExerciseSheet'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Edits an exercise sheet.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercisesheet/$esid(/) or /exercisesheet/exercisesheet/$esid(/).
     * The request body should contain a JSON object representing the exercise
     * sheet's new attributes.
     *
     * @param int $esid The id of the exercise sheet that is being updated.
     */
    public function editExerciseSheet( $esid )
    {
        Logger::Log( 
                    'starts PUT EditExerciseSheet',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $esid )
                           );

        // decode the received exercise sheet data, as an object
        $insert = ExerciseSheet::decodeExerciseSheet( $this->_app->request->getBody( ) );

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
                                                  'Sql/EditExerciseSheet.sql',
                                                  array( 
                                                        'esid' => $esid,
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
                            'PUT EditExerciseSheet failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes an exercise sheet.
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisesheet/$esid(/) or /exercisesheet/exercisesheet/$esid(/).
     *
     * @param int $esid The id of the exercise sheet that is being deleted.
     */
    public function deleteExerciseSheet( $esid )
    {
        Logger::Log( 
                    'starts DELETE DeleteExerciseSheet',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $esid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              'Sql/DeleteExerciseSheet.sql',
                                              array( 'esid' => $esid )
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
                        'DELETE DeleteExerciseSheet failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds an exercise sheet.
     *
     * Called when this component receives an HTTP POST request to
     * /exercisesheet(/).
     * The request body should contain a JSON object representing the exercise
     * sheet's attributes.
     */
    public function addExerciseSheet( )
    {
        Logger::Log( 
                    'starts POST AddExerciseSheet',
                    LogLevel::DEBUG
                    );

        // decode the received exercise sheet data, as an object
        $insert = ExerciseSheet::decodeExerciseSheet( $this->_app->request->getBody( ) );

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
                                                  'Sql/AddExerciseSheet.sql',
                                                  array( 'values' => $data )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new ExerciseSheet( );
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
                            'POST AddExerciseSheet failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( ExerciseSheet::encodeExerciseSheet( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( ExerciseSheet::encodeExerciseSheet( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( ExerciseSheet::encodeExerciseSheet( $res ) );
    }

    public function getUrl( 
                           $functionName,
                           $sqlFile,
                           $userid,
                           $courseid,
                           $esid,
                           $eid,
                           $fileid,
                           $hash,
                           $singleResult = false
                           )
    {
        Logger::Log( 
                    'starts GET ' . $functionName,
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        $hash = DBJson::mysql_real_escape_string( $hash );
        DBJson::checkInput( 
                           $this->_app,
                           $userid == '' ? true : ctype_digit( $userid ),
                           $courseid == '' ? true : ctype_digit( $courseid ),
                           $esid == '' ? true : ctype_digit( $esid ),
                           $eid == '' ? true : ctype_digit( $eid ),
                           $fileid == '' ? true : ctype_digit( $fileid )
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
                                                    'fileid' => $fileid,
                                                    'hash' => $hash
                                                    )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );

            if ( $query->getNumRows( ) > 0 ){
                $res = File::ExtractFile( 
                                         $query->getResponse( ),
                                         $singleResult
                                         );
                $this->_app->response->setBody( File::encodeFile( $res ) );

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
        $this->_app->response->setBody( File::encodeFile( new File( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns the URL to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisesheet/$esid/url(/) or /exercisesheet/exercisesheet/$esid/url(/).
     *
     * @param int $esid The id of the exercise sheet the returned URL belongs to.
     */
    public function getExerciseSheetURL( $esid )
    {
        $this->getUrl( 
                      'GetExerciseSheetURL',
                      dirname(__FILE__).'/Sql/GetExerciseSheetURL.sql',
                      isset( $userid ) ? $userid : '',
                      isset( $courseid ) ? $courseid : '',
                      isset( $esid ) ? $esid : '',
                      isset( $eid ) ? $eid : '',
                      isset( $fileid ) ? $fileid : '',
                      isset( $hash ) ? $hash : '',
                      true
                      );
    }

    /**
     * Returns the URLs to all exercise sheets of a given course.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisesheet/course/$courseid/url(/).
     *
     * @param int $courseid The id of the course.
     */
    public function getCourseSheetURLs( $courseid )
    {
        $this->getUrl( 
                      'GetCourseSheetURLs',
                      dirname(__FILE__).'/Sql/GetCourseSheetURLs.sql',
                      isset( $userid ) ? $userid : '',
                      isset( $courseid ) ? $courseid : '',
                      isset( $esid ) ? $esid : '',
                      isset( $eid ) ? $eid : '',
                      isset( $fileid ) ? $fileid : '',
                      isset( $hash ) ? $hash : ''
                      );
    }

    /**
     * Returns an exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisesheet/$esid(/) or /exercisesheet/exercisesheet/$esid(/).
     *
     * @param int $esid The id of the exercise sheet that should be returned.
     */
    public function getExerciseSheet( $esid )
    {
        Logger::Log( 
                    'starts GET GetExerciseSheet',
                    LogLevel::DEBUG
                    );

        if ( count( $esid ) < 1 ){
            Logger::Log( 
                        'GET EditExerciseSheet wrong use',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->response->setBody( ExerciseSheet::encodeExerciseSheet( new ExerciseSheet( ) ) );
            $this->_app->stop( );
            return;
        }

        $options = array_splice( 
                                $esid,
                                1
                                );
        $esid = $esid[0];

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $esid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__).'/Sql/GetExerciseSheet.sql',
                                              array( 'esid' => $esid )
                                              );

        // checks the exercise option
        if ( in_array( 
                      'exercise',
                      $options
                      ) ){

            // starts a query, by using a given file
            $result2 = DBRequest::getRoutedSqlFile( 
                                                   $this->query,
                                                   dirname(__FILE__).'/Sql/GetSheetExercises.sql',
                                                   array( 'esid' => $esid )
                                                   );
        }

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 && 
             ( !isset( $result2 ) || 
               ( $result2['status'] >= 200 && 
                 $result2['status'] <= 299 ) ) ){
            $query = Query::decodeQuery( $result['content'] );

            if ( $query->getNumRows( ) > 0 ){

                $data = $query->getResponse( );

                // generates an assoc array of an exercise sheet by using a defined list of its attributes
                $exerciseSheet = DBJson::getObjectsByAttributes( 
                                                                $data,
                                                                ExerciseSheet::getDBPrimaryKey( ),
                                                                ExerciseSheet::getDBConvert( )
                                                                );

                // generates an assoc array of an file by using a defined list of its attributes
                $exerciseSheetFile = DBJson::getObjectsByAttributes( 
                                                                    $data,
                                                                    File::getDBPrimaryKey( ),
                                                                    File::getDBConvert( )
                                                                    );

                // generates an assoc array of an file by using a defined list of its attributes
                $sampleSolutions = DBJson::getObjectsByAttributes( 
                                                                  $data,
                                                                  File::getDBPrimaryKey( ),
                                                                  File::getDBConvert( ),
                                                                  '2'
                                                                  );

                // concatenates the exercise sheet and the associated sample solution
                $res = DBJson::concatObjectListsSingleResult( 
                                                             $data,
                                                             $exerciseSheet,
                                                             ExerciseSheet::getDBPrimaryKey( ),
                                                             ExerciseSheet::getDBConvert( )['F_id_file'],
                                                             $exerciseSheetFile,
                                                             File::getDBPrimaryKey( )
                                                             );

                // concatenates the exercise sheet and the associated exercise sheet file
                $res = DBJson::concatObjectListsSingleResult( 
                                                             $data,
                                                             $res,
                                                             ExerciseSheet::getDBPrimaryKey( ),
                                                             ExerciseSheet::getDBConvert( )['F_id_sampleSolution'],
                                                             $sampleSolutions,
                                                             File::getDBPrimaryKey( ),
                                                             '2'
                                                             );

                // checks the exercise option
                if ( in_array( 
                              'exercise',
                              $options
                              ) ){
                    $query = Query::decodeQuery( $result2['content'] );
                    $data = $query->getResponse( );

                    // generates an assoc array of exercises by using a defined list of its attributes
                    $exercises = DBJson::getObjectsByAttributes( 
                                                                $data,
                                                                Exercise::getDBPrimaryKey( ),
                                                                Exercise::getDBConvert( )
                                                                );

                    // generates an assoc array of files by using a defined
                    // list of its attributes
                    $attachments = DBJson::getObjectsByAttributes( 
                                                                  $data,
                                                                  File::getDBPrimaryKey( ),
                                                                  File::getDBConvert( )
                                                                  );

                    // concatenates the exercise and the associated attachments
                    $exercises = DBJson::concatObjectListResult( 
                                                                $data,
                                                                $exercises,
                                                                Exercise::getDBPrimaryKey( ),
                                                                Exercise::getDBConvert( )['E_attachments'],
                                                                $attachments,
                                                                File::getDBPrimaryKey( )
                                                                );

                    // generates an assoc array of exercise file types by using a defined
                    // list of its attributes
                    $fileTypes = DBJson::getObjectsByAttributes( 
                                                                $data,
                                                                ExerciseFileType::getDBPrimaryKey( ),
                                                                ExerciseFileType::getDBConvert( )
                                                                );

                    // concatenates the exercise and the associated filetypes
                    $exercises = DBJson::concatObjectListResult( 
                                                                $data,
                                                                $exercises,
                                                                Exercise::getDBPrimaryKey( ),
                                                                Exercise::getDBConvert( )['E_fileTypes'],
                                                                $fileTypes,
                                                                ExerciseFileType::getDBPrimaryKey( )
                                                                );

                    // concatenates the exercise sheet and the associated exercises
                    $res = DBJson::concatResultObjectLists( 
                                                           $data,
                                                           $res,
                                                           ExerciseSheet::getDBPrimaryKey( ),
                                                           ExerciseSheet::getDBConvert( )['ES_exercises'],
                                                           $exercises,
                                                           Exercise::getDBPrimaryKey( )
                                                           );
                }

                // to reindex
                $res = array_merge( $res );

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];

                $this->_app->response->setBody( ExerciseSheet::encodeExerciseSheet( $res ) );
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
                    'GET GetExerciseSheet failed',
                    LogLevel::ERROR
                    );
        $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
        $this->_app->response->setBody( ExerciseSheet::encodeExerciseSheet( new ExerciseSheet( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns all exercise sheets of a given course.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisesheet/course/$courseid(/).
     *
     * @param int $courseid The id of the course the exercise sheets belong to.
     */
    public function getCourseSheets( $courseid )
    {
        Logger::Log( 
                    'starts GET GetCourseSheets',
                    LogLevel::DEBUG
                    );

        if ( count( $courseid ) < 1 ){
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->response->setBody( ExerciseSheet::encodeExerciseSheet( new ExerciseSheet( ) ) );
            $this->_app->stop( );
            return;
        }

        $options = array_splice( 
                                $courseid,
                                1
                                );
        $courseid = $courseid[0];

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $courseid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__).'/Sql/GetCourseSheets.sql',
                                              array( 'courseid' => $courseid )
                                              );

        // checks the exercise option
        if ( in_array( 
                      'exercise',
                      $options
                      ) ){
            $result2 = DBRequest::getRoutedSqlFile( 
                                                   $this->query,
                                                   dirname(__FILE__).'/Sql/GetCourseExercises.sql',
                                                   array( 'courseid' => $courseid )
                                                   );
        }

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 && 
             ( !isset( $result2 ) || 
               ( $result2['status'] >= 200 && 
                 $result2['status'] <= 299 ) ) ){
            $query = Query::decodeQuery( $result['content'] );

            if ( $query->getNumRows( ) > 0 ){
                $data = $query->getResponse( );

                // generates an assoc array of an exercise sheet by using a defined list of its attributes
                $exerciseSheet = DBJson::getObjectsByAttributes( 
                                                                $data,
                                                                ExerciseSheet::getDBPrimaryKey( ),
                                                                ExerciseSheet::getDBConvert( )
                                                                );           

                // generates an assoc array of an file by using a defined list of its attributes
                $exerciseSheetFile = DBJson::getObjectsByAttributes( 
                                                                    $data,
                                                                    File::getDBPrimaryKey( ),
                                                                    File::getDBConvert( )
                                                                    );

                // generates an assoc array of an file by using a defined list of its attributes
                $sampleSolutions = DBJson::getObjectsByAttributes( 
                                                                  $data,
                                                                  File::getDBPrimaryKey( ),
                                                                  File::getDBConvert( ),
                                                                  '2'
                                                                  );

                // concatenates the exercise sheet and the associated sample solution
                $res = DBJson::concatObjectListsSingleResult( 
                                                             $data,
                                                             $exerciseSheet,
                                                             ExerciseSheet::getDBPrimaryKey( ),
                                                             ExerciseSheet::getDBConvert( )['F_id_file'],
                                                             $exerciseSheetFile,
                                                             File::getDBPrimaryKey( )
                                                             );

                // concatenates the exercise sheet and the associated exercise sheet file
                $res = DBJson::concatObjectListsSingleResult( 
                                                             $data,
                                                             $res,
                                                             ExerciseSheet::getDBPrimaryKey( ),
                                                             ExerciseSheet::getDBConvert( )['F_id_sampleSolution'],
                                                             $sampleSolutions,
                                                             File::getDBPrimaryKey( ),
                                                             '2'
                                                             );

                // checks the exercise option
                if ( in_array( 
                              'exercise',
                              $options
                              ) ){
                    $query = Query::decodeQuery( $result2['content'] );
                    $data = $query->getResponse( );

                    $exercises = DBJson::getObjectsByAttributes( 
                                                                $data,
                                                                Exercise::getDBPrimaryKey( ),
                                                                Exercise::getDBConvert( )
                                                                );

                    // generates an assoc array of files by using a defined
                    // list of its attributes
                    $attachments = DBJson::getObjectsByAttributes( 
                                                                  $data,
                                                                  File::getDBPrimaryKey( ),
                                                                  File::getDBConvert( )
                                                                  );

                    // concatenates the exercise and the associated attachments
                    $exercises = DBJson::concatObjectListResult( 
                                                                $data,
                                                                $exercises,
                                                                Exercise::getDBPrimaryKey( ),
                                                                Exercise::getDBConvert( )['E_attachments'],
                                                                $attachments,
                                                                File::getDBPrimaryKey( )
                                                                );

                    // generates an assoc array of exercise file types by using a defined
                    // list of its attributes
                    $fileTypes = DBJson::getObjectsByAttributes( 
                                                                $data,
                                                                ExerciseFileType::getDBPrimaryKey( ),
                                                                ExerciseFileType::getDBConvert( )
                                                                );

                    // concatenates the exercise and the associated filetypes
                    $exercises = DBJson::concatObjectListResult( 
                                                                $data,
                                                                $exercises,
                                                                Exercise::getDBPrimaryKey( ),
                                                                Exercise::getDBConvert( )['E_fileTypes'],
                                                                $fileTypes,
                                                                ExerciseFileType::getDBPrimaryKey( )
                                                                );

                    // concatenates the exercise sheet and the associated exercises
                    $res = DBJson::concatResultObjectLists( 
                                                           $data,
                                                           $res,
                                                           ExerciseSheet::getDBPrimaryKey( ),
                                                           ExerciseSheet::getDBConvert( )['ES_exercises'],
                                                           $exercises,
                                                           Exercise::getDBPrimaryKey( )
                                                           );
                }
                
                // to reindex
                $res = array_merge( $res );
                
                $res = LArraySorter::orderBy($res, 'startDate', SORT_ASC, 'id',  SORT_ASC);
                                
                // sets the sheet names
                $id = 1;
                foreach ( $res as & $sheet ){
                    if ( !isset( $sheet['sheetName'] ) || 
                         $sheet['sheetName'] == null ){
                        $sheet['sheetName'] = 'Serie ' . ( string )$id;
                        $id++;
                    }
                } 

                $this->_app->response->setBody( ExerciseSheet::encodeExerciseSheet( $res ) );
                $this->_app->response->setStatus( 200 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );

                $this->_app->stop( );
                
            } else {
                $result['status'] = 404;
                }
        }

        Logger::Log( 
                    'GET GetCourseSheets failed',
                    LogLevel::ERROR
                    );
        $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
        $this->_app->response->setBody( ExerciseSheet::encodeExerciseSheet( new ExerciseSheet( ) ) );
        $this->_app->stop( );
    }
    
    /**
     * Returns status code 200, if this component is correctly installed for the platform
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/platform.
     */
    public function getExistsPlatform( )
    {
        Logger::Log( 
                'starts GET GetExistsPlatform',
                LogLevel::DEBUG
                );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__).'/Sql/GetExistsPlatform.sql',
                                              array( ),
                                              false
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){

            $this->_app->response->setStatus( 200 );
            $this->_app->response->setBody( '' );
            if ( isset( $result['headers']['Content-Type'] ) )
                $this->_app->response->headers->set( 
                                                    'Content-Type',
                                                    $result['headers']['Content-Type']
                                                    );
            
        } else {
            Logger::Log( 
                        'GET GetExistsPlatform failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->response->setBody( '' );
            $this->_app->stop( );
        }
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
                                              dirname(__FILE__).'/Sql/DeletePlatform.sql',
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
                                                  dirname(__FILE__).'/Sql/AddPlatform.sql',
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

 
?>