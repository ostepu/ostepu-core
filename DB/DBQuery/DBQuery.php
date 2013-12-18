<?php
/**
 * @file (filename)
 * (description)
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/DBRequest.php' );
include_once( 'Include/CConfig.php' );

\Slim\Slim::registerAutoloader();
$com = new CConfig(DBQuery::getPrefix());
if (!$com->used())
    new DBQuery($com->loadConfig());

/**
 * (description)
 */
class DBQuery
{
    private $_conf=null;
    
    private static $_prefix = "query";
    
    public static function getPrefix()
    {
        return DBQuery::$_prefix;
    }
    public static function setPrefix($value)
    {
        DBQuery::$_prefix = $value;
    }
    
    public function __construct($conf)
    {
        $this->_conf = $conf;
        
        $this->app = new \Slim\Slim();

        // GET QueryResult
        $this->app->get('/' . $this->getPrefix(),
                        array($this,'queryResult'));

        if (strpos ($this->app->request->getResourceUri(),'/' . $this->getPrefix()) === 0){
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
        $obj = Query::decodeQuery($body);

        $query_result = DBRequest::request($obj->getRequest()); 
        $data = DBJson::getRows($query_result);

        $obj = new Query();
        $obj->setResponse($data);
        $this->app->response->setBody(Query::encodeQuery($obj));
        
        $this->app->response->setStatus(200);
    }
}
?>