<?php 


/**
 * @file DBQuery2.php contains the DBQuery2 class
 *
 * @author Till Uhlig
 * @date 2014
 */

require_once ( '../../Assistants/Slim/Slim.php' );
include_once ( '../../Assistants/Structures.php' );
include_once ( '../../Assistants/DBJson.php' );
include_once ( '../../Assistants/DBRequest.php' );
include_once ( '../../Assistants/CConfig.php' );
include_once ( '../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to perform requests to the database
 */
class DBQuery2
{

    /**
     * @var Slim $_app the slim object
     */
    private $_conf = null;

    /**
     * @var $app the slim object
     */
    private $_app = null;

    /**
     * @var string $_prefix the prefixes, the class works with (comma separated)
     */
    private static $_prefix = 'query';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBQuery2::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBQuery2::$_prefix = $value;
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
        $com = new CConfig( DBQuery2::getPrefix( ) );

        // runs the DBQuery2
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
            
        // initialize component
        $this->_conf = $conf;

        // initialize slim
        $this->_app = new \Slim\Slim( array( 'debug' => true ));
        $this->_app->response->setStatus( 404 );

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
                         
        // GET QueryResult
        $this->_app->get( 
                         '/' . $this->getPrefix( ) . '(/)',
                         array( 
                               $this,
                               'queryResult'
                               )
                         );

        // PUT QueryResult
        $this->_app->put( 
                         '/' . $this->getPrefix( ) . '(/)',
                         array( 
                               $this,
                               'queryResult'
                               )
                         );

        // POST QueryResult
        $this->_app->post( 
                          '/' . $this->getPrefix( ) . '(/)',
                          array( 
                                $this,
                                'queryResult'
                                )
                          );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Needed to send a SQL query to the database.
     *
     * Each component which wants to send a SQL query to the database needs to send
     * the SQL query as a query object to this component. This component then returns
     * another query object including the response and possible errors.
     *
     * Called when this component receives an HTTP GET, an HTTP PUT or an HTTP POST
     * request to /query/.
     */
    public function queryResult( )
    {
        Logger::Log( 
                    'starts GET queryResult',
                    LogLevel::DEBUG
                    );

        $body = $this->_app->request->getBody( );

        // decode the received query data, as an object
        $obj = Query::decodeQuery( $body );

        $answer = DBRequest::request2( 
                                           $obj->getRequest( ),
                                           $obj->getCheckSession( )
                                           );
                                           
        $this->_app->response->setStatus( 200 );
        $result = array();
             
        foreach ($answer as $query_result){
            $obj = new Query( );
            
        if ( $query_result['errno'] != 0 ){
            if ( isset($query_result['errno']) && $query_result['errno'] != 0 )
                Logger::Log( 
                            'GET queryResult failed errno: ' . $query_result['errno'] . ' error: ' . $query_result['error'],
                            LogLevel::ERROR
                            );

            if ( !isset($query_result['content']) || !$query_result['content'] )
                Logger::Log( 
                            'GET queryResult failed, no content',
                            LogLevel::ERROR
                            );

            if ( isset($query_result['errno']) && $query_result['errno'] == 401 ){
                $this->_app->response->setStatus( 401 );
                
            } else 
                $this->_app->response->setStatus( 409 );
            
        }elseif ( gettype( $query_result['content'] ) == 'boolean' ){
            $obj->setResponse( array( ) );
            if ( isset( $query_result['affectedRows'] ) )
                $obj->setAffectedRows( $query_result['affectedRows'] );
            if ( isset( $query_result['insertId'] ) )
                $obj->setInsertId( $query_result['insertId'] );
            if ( isset( $query_result['errno'] ) )
                $obj->setErrno( $query_result['errno'] );
            if ( isset( $query_result['numRows'] ) )
                $obj->setNumRows( $query_result['numRows'] );

          if ( isset( $query_result['errno'] ) && $query_result['errno']>0 ){
          $this->_app->response->setStatus( 409 );
          }
          else
            $this->_app->response->setStatus( 200 );
            
        } else {
            $data = array( );
            if ( isset( $query_result['numRows'] ) && 
                 $query_result['numRows'] > 0 ){
                $data = DBJson::getRows2( $query_result['content'] );
            }

            $obj->setResponse( $data );
            if ( isset( $query_result['affectedRows'] ) )
                $obj->setAffectedRows( $query_result['affectedRows'] );
            if ( isset( $query_result['insertId'] ) )
                $obj->setInsertId( $query_result['insertId'] );
            if ( isset( $query_result['errno'] ) )
                $obj->setErrno( $query_result['errno'] );
            if ( isset( $query_result['numRows'] ) )
                $obj->setNumRows( $query_result['numRows'] );

            
            $this->_app->response->setStatus( 200 );
        }
        $result[]=$obj;
        }
        if (count($result)==1) $result = $result[0];        
        $this->_app->response->setBody( Query::encodeQuery( $result ) );
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
                    
        if (!file_exists('config.ini')){
            $this->_app->response->setStatus( 409 );
            $this->_app->stop();
        }
       
        $this->_app->response->setStatus( 200 );
        $this->_app->response->setBody( '' );   
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
        if (file_exists('config.ini') && !unlink('config.ini')){
            $this->_app->response->setStatus( 409 );
            $this->_app->stop();
        }
        
        $this->_app->response->setStatus( 201 );
        $this->_app->response->setBody( '' );

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
        
            $file = 'config.ini';
            $text = "[DB]\n".
                    "db_path = {$in->getDatabaseUrl()}\n".
                    "db_user = {$in->getDatabaseOperatorUser()}\n".
                    "db_passwd = {$in->getDatabaseOperatorPassword()}\n".
                    "db_name = {$in->getDatabaseName()}";
                    
            if (!@file_put_contents($file,$text)){
                Logger::Log( 
                            'POST AddPlatform failed, config.ini no access',
                            LogLevel::ERROR
                            );

                $this->_app->response->setStatus( 409 );
                $this->_app->stop();
            }   

            $platform = new Platform();
            $platform->setStatus(201);
            $res[] = $platform;
            $this->_app->response->setStatus( 201 );            
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( Platform::encodePlatform( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( Platform::encodePlatform( $res ) );
    }
}

 
?>