<?php
/**
 * @file (filename)
 * (description)
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DbJson.php' );
include_once( 'Include/DbRequest.php' );
include_once( 'Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

$com = new CConfig(DBUser::getPrefix());

if (!$com->used())
    new DBUser($com->loadConfig());
    
/**
 * (description)
 */
class DBUser
{
    private $_app=null;
    private $_conf=null;
    
    private $query=array();
    
    private static $_prefix = "user";
    
    public static function getPrefix()
    {
        return DBUser::$_prefix;
    }
    public static function setPrefix($value)
    {
        DBUser::$_prefix = $value;
    }
    
    public function __construct($conf)
    {
        $this->_conf = $conf;
        $this->query = array(CConfig::getLink($conf->getLinks(),"query"));
        
        $this->app = new \Slim\Slim();
        
        // PUT SetUserRights
        $this->app->put('/user/user/:userid/rights',
                        array($this,'SetUserRights'));
                        
        // PUT EditUser
        $this->app->put('/user/user/:userid',
                        array($this,'EditUser'));
                        
        // DELETE RemoveUser
        $this->app->delete('/user/user/:userid',
                        array($this,'RemoveUser'));
                        
        // POST AddUser
        $this->app->post('/user',
                        array($this,'AddUser'));
                        
        // GET GetUsers
        $this->app->get('/user/user',
                        array($this,'GetUsers'));
                        
        // GET GetUser
        $this->app->get('/user/user/:userid',
                        array($this,'GetUser'));

        if (strpos ($this->app->request->getResourceUri(),'/' . $this->getPrefix()) === 0){
            // run Slim
            $this->app->run();
        }
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // PUT SetUserRights
    public function SetUserRights($userid)
    {
            $this->app->response->setStatus(200);  
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // PUT EditUser
    public function EditUser($userid)
    {
            $this->app->response->setStatus(200);  
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // DELETE RemoveUser
    public function RemoveUser($userid)
    {
            $this->app->response->setStatus(252);  
    }
    
    /**
     * (description)
     */
    // POST AddUser
    public function AddUser()
    {
            $this->app->response->setStatus(201);  
    }
    
    /**
     * (description)
     */
    // GET GetUsers
    public function GetUsers()
    {
            $this->app->response->setStatus(200);  
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // GET GetUser
    public function GetUser($userid)
    {
        $this->app->response->setStatus(200);    
    }
}
?>