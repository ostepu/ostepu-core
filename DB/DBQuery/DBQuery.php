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
 */
class DBQuery
{
    /**
     * @var $_app the slim object
     */ 
    private $_conf=null;
    
    /**
     * @var $app the slim object
     */ 
    private $app=null;
    
    /**
     * @var $_prefix the prefix, the class works with
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
     * @param $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBQuery::$_prefix = $value;
    }
    
    /**
     * the component constructor
     *
     * @param $conf component data
     */ 
    public function __construct($conf)
    {
        // initialize component
        $this->_conf = $conf;
        
        // initialize slim
        $this->app = new \Slim\Slim();

        // GET QueryResult
        $this->app->get('/' . $this->getPrefix(),
                        array($this,'queryResult'));
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
                    
            // run Slim
            $this->app->run();
        }
    }
    
    /**
     * GET queryResult
     */
    public function queryResult()
    {
        $body = $this->app->request->getBody();
        
        // decode the received query data, as an object
        $obj = Query::decodeQuery($body);

        $query_result = DBRequest::request($obj->getRequest()); 
        if ($query_result['errno']!=0 || !$query_result['content']){
            Logger::Log("GET queryResult failed",LogLevel::ERROR);
            $obj = new Query();
            $this->app->response->setBody(Query::encodeQuery($obj));
            $this->app->response->setStatus(409);
        } elseif (gettype($query_result['content'])=='boolean'){
            $obj = new Query();
            $obj->setResponse("");
            if (isset($query_result['affectedRows']))
                $obj->setAffectedRows($query_result['affectedRows']);
            if (isset($query_result['insertId']))
                $obj->setInsertId($query_result['insertId']);
            if (isset($query_result['errno']))
                $obj->setErrno($query_result['errno']);
            if (isset($query_result['numRows']))
                $obj->setNumRows($query_result['numRows']);
            $this->app->response->setBody(Query::encodeQuery($obj));
            $this->app->response->setStatus(200);
        } else{
            $data = DBJson::getRows($query_result['content']);
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
            $this->app->response->setBody(Query::encodeQuery($obj));
            $this->app->response->setStatus(200);
        }    

    }
}
?>