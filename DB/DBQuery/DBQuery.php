<?php
/**
 * @file DBQuery.php contains the DBQuery class
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/DBRequest.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/Logger.php' );

\Slim\Slim::registerAutoloader();

// runs the CConfig
$com = new CConfig(DBQuery::getPrefix());

// runs the DBQuery
if (!$com->used())
    new DBQuery($com->loadConfig());

/**
 * A class, to perform requests to the database
 *
 * @author Till Uhlig
 */
class DBQuery
{
    /**
     * @var Slim $_app the slim object
     */ 
    private $_conf=null;
    
    /**
     * @var $app the slim object
     */ 
    private $_app=null;
    
    /**
     * @var string $_prefix the prefixes, the class works with (comma separated)
     */ 
    private static $_prefix = "query";
    
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBQuery::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBQuery::$_prefix = $value;
    }
    
    /**
     * the component constructor
     *
     * @param Component $conf component data
     */ 
    public function __construct($conf)
    {
        // initialize component
        $this->_conf = $conf;
        
        // initialize slim
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');
        
        // GET QueryResult
        $this->_app->get('/' . $this->getPrefix(),
                        array($this,'queryResult'));
                        
        // PUT QueryResult
        $this->_app->put('/' . $this->getPrefix(),
                        array($this,'queryResult'));

        // POST QueryResult
        $this->_app->post('/' . $this->getPrefix(),
                        array($this,'queryResult'));       
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
                    
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * GET queryResult
     */
    public function queryResult()
    {        
        Logger::Log("starts GET queryResult",LogLevel::DEBUG);
        
        $body = $this->_app->request->getBody();
        
        // decode the received query data, as an object
        $obj = Query::decodeQuery($body);

        $query_result = DBRequest::request($obj->getRequest(), $obj->getCheckSession()); 
            
        if ($query_result['errno']!=0 || !$query_result['content']){
            if ($query_result['errno']!=0)
                Logger::Log("GET queryResult failed errno: ". $query_result['errno'] . " error: " . $query_result['error'],LogLevel::ERROR);
            
            if (!$query_result['content'])
                Logger::Log("GET queryResult failed, no content",LogLevel::ERROR);
                
            $obj = new Query();
            $this->_app->response->setBody(Query::encodeQuery($obj));
            
            if($query_result['errno']==401){
                $this->_app->response->setStatus(401);
            } else
                $this->_app->response->setStatus(409);
            
        } elseif (gettype($query_result['content'])=='boolean'){
            $obj = new Query();
            $obj->setResponse(array());
            if (isset($query_result['affectedRows']))
                $obj->setAffectedRows($query_result['affectedRows']);
            if (isset($query_result['insertId']))
                $obj->setInsertId($query_result['insertId']);
            if (isset($query_result['errno']))
                $obj->setErrno($query_result['errno']);
            if (isset($query_result['numRows']))
                $obj->setNumRows($query_result['numRows']);
                
            $this->_app->response->setBody(Query::encodeQuery($obj));
            $this->_app->response->setStatus(200);
            
        } else{
            $data = array();
            if (isset($query_result['numRows']) && $query_result['numRows'] > 0){
                $data = DBJson::getRows($query_result['content']);
            }
            
            $obj = new Query();
            $obj->setResponse($data);
            if (isset($query_result['affectedRows']))
                $obj->setAffectedRows($query_result['affectedRows']);
            if (isset($query_result['insertId']))
                $obj->setInsertId($query_result['insertId']);
            if (isset($query_result['errno']))
                $obj->setErrno($query_result['errno']);
            if (isset($query_result['numRows']))
                $obj->setNumRows($query_result['numRows']);
                
            $this->_app->response->setBody(Query::encodeQuery($obj));
            $this->_app->response->setStatus(200);
            
        }    

    }
}
?>