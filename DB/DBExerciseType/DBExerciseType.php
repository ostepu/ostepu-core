<?php 


/**
 * @file DBExerciseType.php contains the DBExerciseType class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBExerciseType/ExerciseTypeSample.json
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
 * A class, to abstract the "ExerciseType" table from database
 */
class DBExerciseType
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
    private static $_prefix = 'exercisetype';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBExerciseType::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBExerciseType::$_prefix = $value;
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
        $com = new CConfig( DBExerciseType::getPrefix( ), dirname(__FILE__) );

        // runs the DBExerciseType
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
                         
        // PUT EditExerciseType
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '(/exercisetype)/:etid(/)',
                         array( 
                               $this,
                               'editExerciseType'
                               )
                         );

        // DELETE DeleteExerciseType
        $this->_app->delete( 
                            '/' . $this->getPrefix( ) . '(/exercisetype)/:etid(/)',
                            array( 
                                  $this,
                                  'deleteExerciseType'
                                  )
                            );

        // POST AddExerciseType
        $this->_app->post( 
                          '/' . $this->getPrefix( ) . '(/)',
                          array( 
                                $this,
                                'addExerciseType'
                                )
                          );

        // GET GetExerciseType
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/exercisetype)/:etid(/)',
                         array( 
                               $this,
                               'getExerciseType'
                               )
                         );

        // GET GetAllExerciseTypes
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/exercisetype)(/)',
                         array( 
                               $this,
                               'getAllExerciseTypes'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Edits an exercise type.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercisetype/exercisetype/$etid(/) or /exercisetype/$etid(/).
     * The request body should contain a JSON object representing the
     * exercise type's new attributes.
     *
     * @param int $etid The id or the exercise type.
     */
    public function editExerciseType( $etid )
    {
        Logger::Log( 
                    'starts PUT EditExerciseType',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $etid )
                           );

        // decode the received exercise type data, as an object
        $insert = ExerciseType::decodeExerciseType( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/EditExerciseType.sql',
                                                  array( 
                                                        'etid' => $etid,
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
                            'PUT EditExerciseType failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }

    /**
     * Deletes an exercise type.
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisetype/exercisetype/$etid(/) or /exercisetype/$etid(/).
     *
     * @param int $etid The id or the exercise type that is being deleted.
     */
    public function deleteExerciseType( $etid )
    {
        Logger::Log( 
                    'starts DELETE DeleteExerciseType',
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        DBJson::checkInput( 
                           $this->_app,
                           ctype_digit( $etid )
                           );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteExerciseType.sql',
                                              array( 'etid' => $etid )
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
                        'DELETE DeleteExerciseType failed',
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
     * /exercisetype(/).
     * The request body should contain a JSON object representing the
     * new exercise type's attributes.
     */
    public function addExerciseType( )
    {
        Logger::Log( 
                    'starts POST SetExerciseType',
                    LogLevel::DEBUG
                    );

        // decode the received exercise type data, as an object
        $insert = ExerciseType::decodeExerciseType( $this->_app->request->getBody( ) );

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
                                                  dirname(__FILE__) . '/Sql/AddExerciseType.sql',
                                                  array( 'values' => $data )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new ExerciseType( );
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
                            'POST SetExerciseType failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( ExerciseType::encodeExerciseType( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( ExerciseType::encodeExerciseType( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( ExerciseType::encodeExerciseType( $res ) );
    }

    public function get( 
                        $functionName,
                        $params=array(),
                        $singleResult = false,
                        $checkSession = true
                        )
    {
        // checks whether incoming data has the correct data type
        $params = DBJson::mysql_real_escape_string( $params );
        foreach($params as $param)
            $functionName.='/'.$param;

        // starts a query, by using a given file
        /*$result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              $sqlFile,
                                              $params,
                                              $checkSession
                                              );*/
        $result = Request::routeRequest( 
                                        'GET',
                                        '/query/procedure/'.$functionName,
                                        array(),
                                        '',
                                        $this->query2,
                                        'query'
                                        );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );
            if (is_array($query)) $query=$query[0];

            if ( $query->getNumRows( ) > 0 ){
                $res = ExerciseType::ExtractExerciseType( 
                                                         $query->getResponse( ),
                                                         $singleResult
                                                         );
                $this->_app->response->setBody( ExerciseType::encodeExerciseType( $res ) );

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

        $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
        $this->_app->response->setBody( ExerciseType::encodeExerciseType( new ExerciseType( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns all exercise types.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisetype(/) or /exercisetype/exercisetype(/).
     */
    public function getAllExerciseTypes( )
    {
        $this->get( 
                   'DBExerciseTypeGetAllExerciseTypes',
                   array()
                   );
    }

    /**
     * Returns an exercise type.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisetype/$etid(/) or /exercisetype/exercisetype/$etid(/).
     *
     * @param string $etid The id of the exercise type that should be returned.
     */
    public function getExerciseType( $etid )
    {
        $this->get( 
                   'DBExerciseTypeGetExerciseType',
                   array("etid"=>$etid),
                   true
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
                   'DBExerciseTypeGetExistsPlatform',
                   array(),
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

 
?>