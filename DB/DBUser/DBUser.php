<?php
/**
 * @file (filename)
 * (description)
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/DBRequest.php' );
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
        
        $this->_app = new \Slim\Slim();
                        
        // PUT EditUser
        $this->_app->put('/' . $this->getPrefix() . '/user/:userid',
                        array($this, 'editUser'));
                        
        // DELETE RemoveUser
        $this->_app->delete('/' . $this->getPrefix() . '/user/:userid',
                        array($this, 'removeUser'));
                        
        // POST AddUser
        $this->_app->post('/' . $this->getPrefix(),
                        array($this, 'addUser'));
                        
        // GET GetUsers
        $this->_app->get('/' . $this->getPrefix() . '/user',
                        array($this, 'getUsers'));
                        
        // GET GetUser
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid',
                        array($this, 'getUser'));

        if (strpos ($this->_app->request->getResourceUri(),'/' . $this->getPrefix()) === 0){
            // run Slim
            $this->_app->run();
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
        $values = DBJson::getUpdateDataFromInput($this->app->request->getBody(), User::getDBConvert(), ',');
        
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/EditUser.sql", 
                                        array("userid" => $userid,"value" => $values));   
                                        
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                header($result['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // DELETE RemoveUser
    public function removeUser($userid)
    {
         $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteUser.sql", 
                                        array("userid" => $userid));    
                                        
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                header($result['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
    
    /**
     * (description)
     */
    // POST AddUser
    public function addUser()
    {
        $insert = DBJson::getInsertDataFromInput($this->app->request->getBody(), User::getDBConvert(), ',');
        foreach ($insert as $in){
            $columns = $in[0];
            $values = $in[1];

            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/AddUser.sql", 
                                            array("columns" => $columns, "values" => $values));                   
        
            if ($result['status']>=200 && $result['status']<=299){
        
                $this->_app->response->setStatus($result['status']);
                if (isset($result['headers']['Content-Type']))
                    header($result['headers']['Content-Type']);
                
            } else{
                $this->_app->response->setStatus(409);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * (description)
     */
    // GET GetUsers
    public function getUsers()
    {
         $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetUsers.sql", 
                                        array());
                                                 
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $user = DBJson::getResultObjectsByAttributes($query->getResponse(), User::getDBPrimaryKey(), User::getDBConvert());
            $this->_app->response->setBody(User::encodeUser($user));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                header($result['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->stop();
        }
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // GET GetUser
    public function getUser($userid)
    {
         $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetUser.sql", 
                                        array("userid" => $userid));
                                                 
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $user = DBJson::getResultObjectsByAttributes($query->getResponse(), User::getDBPrimaryKey(), User::getDBConvert());
            $this->_app->response->setBody(User::encodeUser($user));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                header($result['headers']['Content-Type']);
                
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(User::encodeUser(new User()));
            $this->_app->stop();
        }
    }
}
?>