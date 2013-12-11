<?php
/**
 * @file (filename)
 * (description)
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/DbJson.php' );
include_once( 'Include/DbRequest.php' );
include_once( 'Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

$com = new CConfig("");

if (!$com->used())
    new DbQuery();

/**
 * (description)
 */
class DbQuery
{
    private static $_prefix = "query";
    public static function getPrefix(){
        return DbQuery::$_prefix;
    }
    public static function setPrefix($value){
        DbQuery::$_prefix = $value;
    }
    
    public function __construct(){
        $this->app = new \Slim\Slim();
        
        // GET queryResult
        $this->app->put('/query',
                        array($this,'queryResult'));

        if (strpos ($this->app->request->getResourceUri(),'/' . $this->_prefix) === 0){
            // run Slim
            $this->app->run();
        }
    }
    
    /**
     * GET queryResult
     */
    public function queryResult(){
            $this->app->response->setStatus(200);  
    }
}
?>