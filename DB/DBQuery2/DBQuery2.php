<?php 


/**
 * @file DBQuery.php contains the DBQuery class
 *
 * @author Till Uhlig
 */

require_once ( '../../Assistants/Slim/Slim.php' );
include_once ( '../../Assistants/Structures.php' );
include_once ( '../../Assistants/DBJson.php' );
include_once ( '../../Assistants/DBRequest.php' );
include_once ( '../../Assistants/CConfig.php' );
include_once ( '../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

// runs the CConfig
$com = new CConfig( DBQuery::getPrefix( ) );

// runs the DBQuery
if ( !$com->used( ) )
    new DBQuery( $com->loadConfig( ) );

/**
 * A class, to perform requests to the database
 */
class DBQuery
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
        return DBQuery::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBQuery::$_prefix = $value;
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

        // initialize component
        $this->_conf = $conf;

        // initialize slim
        $this->_app = new \Slim\Slim( array( 'debug' => true ));
        $this->_app->response->setStatus( 404 );

        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            'application/json'
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

        // starts slim only if the right prefix was received
        if ( strpos( 
                    $this->_app->request->getResourceUri( ),
                    '/' . $this->getPrefix( )
                    ) === 0  || strpos( 
                    $this->_app->request->getResourceUri( ),
                    '/info'
                    ) === 0){

            // run Slim
            $this->_app->run( );
        }
        else
        header("HTTP/1.0 404 Not Found");
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
        //$obj->setCheckSession(false);
    /*    $obj->setRequest("SET @statement = 
concat(concat(
\"INSERT INTO Form_\", (select E.C_id from `Exercise` E where E.E_id = '4' limit 1)), \" SET E_id='4',FO_type='0';\");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;");*/
//$obj->setRequest("select * from Component where CO_id = 1;");
//$obj->setRequest("select * from Component where CO_id = 1;insert into Form_3 set E_id=4, FO_type=0;");

        $answer = DBRequest::request2( 
                                           $obj->getRequest( ),
                                           $obj->getCheckSession( )
                                           );
                                           
        $this->_app->response->setStatus( 200 );
        $result = array();
                        //var_dump($answer);                
foreach ($answer as $query_result){
            $obj = new Query( );
            
        if ( $query_result['errno'] != 0 ){
            if ( $query_result['errno'] != 0 )
                Logger::Log( 
                            'GET queryResult failed errno: ' . $query_result['errno'] . ' error: ' . $query_result['error'],
                            LogLevel::ERROR
                            );

            if ( !$query_result['content'] )
                Logger::Log( 
                            'GET queryResult failed, no content',
                            LogLevel::ERROR
                            );

           // $this->_app->response->setBody( Query::encodeQuery( $obj ) );

            if ( $query_result['errno'] == 401 ){
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

          //  $this->_app->response->setBody( Query::encodeQuery( $obj ) );
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
}

 
?>

