<?php
/**
 * @file (filename)
 * (description)
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
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
                        
        // PUT EditUser
        $this->app->put('/' . $this->getPrefix() . '/user/:userid',
                        array($this, 'editUser'));
                        
        // DELETE RemoveUser
        $this->app->delete('/' . $this->getPrefix() . '/user/:userid',
                        array($this, 'removeUser'));
                        
        // POST AddUser
        $this->app->post('/' . $this->getPrefix(),
                        array($this, 'addUser'));
                        
        // GET GetUsers
        $this->app->get('/' . $this->getPrefix() . '/user',
                        array($this, 'getUsers'));
                        
        // GET GetUser
        $this->app->get('/' . $this->getPrefix() . '/user/:userid',
                        array($this, 'getUser'));

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
    // PUT EditUser
    public function editUser($userid)
    {
 
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // DELETE RemoveUser
    public function removeUser($userid)
    {
 
    }
    
    /**
     * (description)
     */
    // POST AddUser
    public function addUser()
    {

    }
    
    /**
     * (description)
     */
    // GET GetUsers
    public function getUsers()
    {
 
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // GET GetUser
    public function getUser($userid)
    {
   
    }
}
?>