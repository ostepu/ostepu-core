<?php 


/**
 * @file DBExerciseFileType.php contains the DBExerciseFileType class
 *
 * @author Till Uhlig
 * @example DB/DBExerciseFileType/ExerciseFileTypeSample.json
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
 * A class, to abstract the "ExerciseFileType" table from database
 */
class DBExerciseFileType
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
    private static $_prefix = 'exercisefiletype';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBExerciseFileType::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBExerciseFileType::$_prefix = $value;
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
        $com = new CConfig( DBExerciseFileType::getPrefix( ) );

        // runs the DBExerciseFileType
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
                         
        // PUT EditExerciseFileType
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '(/exercisefiletype)/:eftid(/)',
                         array( 
                               $this,
                               'editExerciseFileType'
                               )
                         );

        // DELETE DeleteExerciseFileType
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '(/exercisefiletype)/:eftid(/)',
                            array( 
                                  $this,
                                  'deleteExerciseFileType'
                                  )
                            );

        // POST AddExerciseFileType
        $this->_app->post( 
                          '/' . $this->getPrefix( ) . '(/)',
                          array( 
                                $this,
                                'addExerciseFileType'
                                )
                          );

        // GET GetExerciseFileType
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/exercisefiletype)/:eftid(/)',
                         array( 
                               $this,
                               'getExerciseFileType'
                               )
                         );

        // GET GetExerciseExerciseFileTypes
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '/exercise/:eid(/)',
                         array( 
                               $this,
                               'getExerciseExerciseFileTypes'
                               )
                         );

        // GET GetAllExerciseFileTypes
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/exercisefiletype)(/)',
                         array( 
                               $this,
                               'getAllExerciseFileTypes'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Edits an exercise file type.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercisefiletype/exercisefiletype/$eftid(/) or /exercisefiletype/$eftid(/).
     * The request body should contain a JSON object representing the
     * exercise type's new attributes.
     *
     * @param int $eftid The id or the exercise file type.
     */
    public function editExerciseFileType( $eftid )
    {
        Logger::Log( 
                    'starts PUT EditExerciseFileType',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $eftid )
                           );

        // decode the received exercise file type data, as an object
        $insert = ExerciseFileType::decodeExerciseFileType( $this->_app->request->getBody( ) );

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
                                                  'Sql/EditExerciseFileType.sql',
                                                  array( 
                                                        'eftid' => $eftid,
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
                            'PUT EditExerciseFileType failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes an exercise file type.
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisefiletype/exercisefiletype/$eftid(/) or /exercisefiletype/$eftid(/).
     *
     * @param int $eftid The id or the exercise file type that is being deleted.
     */
    public function deleteExerciseFileType( $eftid )
    {
        Logger::Log( 
                    'starts DELETE DeleteExerciseFileType',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $eftid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              'Sql/DeleteExerciseFileType.sql',
                                              array( 'eftid' => $eftid )
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
                        'DELETE DeleteExerciseFileType failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Adds a new exercise type.
     *
     * Called when this component receives an HTTP POST request to
     * /exercisefiletype(/).
     * The request body should contain a JSON object representing the
     * new exercise file type's attributes.
     */
    public function addExerciseFileType( )
    {
        Logger::Log( 
                    'starts POST SetExerciseFileType',
                    LogLevel::DEBUG
                    );

        // decode the received exercise file type data, as an object
        $insert = ExerciseFileType::decodeExerciseFileType( $this->_app->request->getBody( ) );

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
                                                  'Sql/AddExerciseFileType.sql',
                                                  array( 'values' => $data )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new ExerciseFileType( );
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
                            'POST SetExerciseFileType failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( ExerciseFileType::encodeExerciseFileType( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( ExerciseFileType::encodeExerciseFileType( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( ExerciseFileType::encodeExerciseFileType( $res ) );
    }

    public function get( 
                        $functionName,
                        $sqlFile,
                        $userid,
                        $courseid,
                        $esid,
                        $eid,
                        $etid,
                        $eftid,
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
                           $etid == '' ? true : ctype_digit( $etid ),
                           $eftid == '' ? true : ctype_digit( $eftid )
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
                                                    'etid' => $etid,
                                                    'eftid' => $eftid
                                                    )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );

            if ( $query->getNumRows( ) > 0 ){
                $res = ExerciseFileType::ExtractExerciseFileType( 
                                                                 $query->getResponse( ),
                                                                 $singleResult
                                                                 );
                $this->_app->response->setBody( ExerciseFileType::encodeExerciseFileType( $res ) );

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
        $this->_app->response->setBody( ExerciseFileType::encodeExerciseFileType( new ExerciseFileType( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns all exercise file types.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisefiletype(/) or /exercisefiletype/exercisefiletype(/).
     */
    public function getAllExerciseFileTypes( )
    {
        $this->get( 
                   'GetAllExerciseFileTypes',
                   'Sql/GetAllExerciseFileTypes.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $etid ) ? $etid : '',
                   isset( $eftid ) ? $eftid : ''
                   );
    }

    /**
     * Returns an exercise file type.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisefiletype/$eftid(/) or /exercisefiletype/exercisefiletype/$eftid(/).
     *
     * @param string $eftid The id of the exercise file type that should be returned.
     */
    public function getExerciseFileType( $eftid )
    {
        $this->get( 
                   'GetExerciseFileType',
                   'Sql/GetExerciseFileType.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $etid ) ? $etid : '',
                   isset( $eftid ) ? $eftid : '',
                   true
                   );
    }

    /**
     * Returns all exercise exercise file types.
     *
     * Called when this component receives an HTTP GET request to
     * exercisefiletype/exercise/$eid(/).
     *
     * @param string $eid The id of the exercise that should be returned.
     */
    public function getExerciseExerciseFileTypes( $eid )
    {
        $this->get( 
                   'GetExerciseExerciseFileTypes',
                   'Sql/GetExerciseExerciseFileTypes.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $etid ) ? $etid : '',
                   isset( $eftid ) ? $eftid : ''
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
                   'Sql/GetExistsPlatform.sql',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $etid ) ? $etid : '',
                   isset( $eftid ) ? $eftid : '',
                   true
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
                                              'Sql/DeletePlatform.sql',
                                              array( )
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
                                                  'Sql/AddPlatform.sql',
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

