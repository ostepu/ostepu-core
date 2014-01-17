<?php
/**
 * @file DBGroup.php contains the DBGroup class
 * (description)
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

// runs the CConfig
$com = new CConfig(DBGroup::getPrefix());

// runs the DBUser
if (!$com->used())
    new DBGroup($com->loadConfig());  
    
/**
 * A class, to abstract the "Group" table from database
 *
 * @author Till Uhlig
 */
class DBGroup
{
    /**
     * @var Slim $_app the slim object
     */ 
    private $_app=null;
    
    /**
     * @var Component $_conf the component data object
     */ 
    private $_conf=null;
    
    /**
     * @var Link[] $query a list of links to a query component
     */ 
    private $query=array();
    
    /**
     * @var string $_prefix the prefixes, the class works with (comma separated)
     */ 
    private static $_prefix = "group";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBGroup::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBGroup::$_prefix = $value;
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
        $this->query = array(CConfig::getLink($conf->getLinks(),"out"));
        
        // initialize slim
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');

        // PUT EditGroup
        $this->_app->put('/' . $this->getPrefix() . 
                        '/user/:userid/exercisesheet/:esid(/)',
                        array($this,'editGroup'));
        
        // DELETE DeleteGroup
        $this->_app->delete('/' . $this->getPrefix() . 
                            '/user/:userid/exercisesheet/:esid(/)',
                           array($this,'deleteGroup'));
                                                      
        // POST SetGroup
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'setGroup'));
               
        // GET GetUserGroups
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid(/)',
                        array($this,'getUserGroups'));
                        
        // GET GetAllGroups
        $this->_app->get('/' . $this->getPrefix() . '(/group)(/)',
                        array($this,'getAllGroups'));
                        
        // GET GetSheetUserGroups
        $this->_app->get('/' . $this->getPrefix() . 
                        '/user/:userid/exercisesheet/:esid(/)',
                        array($this,'getSheetUserGroups'));
        
        // GET GetSheetGroups
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid(/)',
                        array($this,'getSheetGroups'));
        
        
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
                    
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * PUT EditGroup
     *
     * @param int $userid a database user identifier
     * @param int $esid a database exercise sheet identifier
     */
    public function editGroup($userid, $esid)
    {
        Logger::Log("starts PUT EditGroup",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid),
                            ctype_digit($esid));
                            
        // decode the received group data, as an object
        $insert = Group::decodeGroup($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/EditGroup.sql", 
                                            array("esid" => $esid,
                                                "userid" => $userid, 
                                                "values" => $data)
                                            );                   
           
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditGroup failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * DELETE DeleteGroup
     *
     * @param int $userid a database user identifier
     * @param int $esid a database exercise sheet identifier
     */
    public function deleteGroup($userid, $esid)
    {
        Logger::Log("starts DELETE DeleteGroup",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid),
                            ctype_digit($esid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteGroup.sql", 
                                        array("esid" => $esid,
                                            "userid" => $userid)
                                        );    
                                        
        // checks the correctness of the query                          
        if ($result['status']>=200 && $result['status']<=299){
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteGroup failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
    
    /**
     * POST SetGroup
     */
    public function setGroup()
    {
        Logger::Log("starts POST SetGroup",LogLevel::DEBUG);
        
        // decode the received group data, as an object
        $insert = Group::decodeGroup($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/SetGroup.sql", 
                                            array("values" => $data));                   
           
            // checks the correctness of the query    
            if ($result['status']>=200 && $result['status']<=299){

                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST SetGroup failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * GET GetUserGroups
     *
     * @param int $userid a database user identifier
     */
    public function getUserGroups($userid)
    {     
        Logger::Log("starts GET GetUserGroups",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetUserGroups.sql", 
                                        array("userid" => $userid));  
                                        
        // checks the correctness of the query    
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();

            // generates an assoc array of an user by using a defined list of 
            // its attributes
            $leader = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert()
                                            );
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $member = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert(),
                                            '2'
                                            );
                                            
            // generates an assoc array of groups by using a defined list of 
            // its attributes
            $groups = DBJson::getObjectsByAttributes($data, 
                                    Group::getDBPrimaryKey(), 
                                    Group::getDBConvert());
                                  
                                    
            // concatenates the groups and the associated group leader
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $groups,
                            Group::getDBPrimaryKey(),
                            Group::getDBConvert()['U_leader'] ,
                            $leader,
                            User::getDBPrimaryKey()
                            );
       
            // concatenates the groups and the associated group member
            $res = DBJson::concatResultObjectLists($data, 
                            $res,
                            Group::getDBPrimaryKey(),
                            Group::getDBConvert()['U_member'] ,
                            $member,
                            User::getDBPrimaryKey(),
                            '2'
                            );
                            
                          
            // to reindex
            $res = array_merge($res);
        
            $this->_app->response->setBody(Group::encodeGroup($res));
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetUserGroups failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Group::encodeGroup(new Group()));
            $this->_app->stop();
        }
    }   
    
    /**
     * GET GetAllGroups
     */
    public function getAllGroups()
    {     
        Logger::Log("starts GET GetAllGroups",LogLevel::DEBUG);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAllGroups.sql", 
                                        array());  
                                        
        // checks the correctness of the query    
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();

            // generates an assoc array of an user by using a defined list of 
            // its attributes
            $leader = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert()
                                            );
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $member = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert(),
                                            '2'
                                            );
                                            
            // generates an assoc array of groups by using a defined list of 
            // its attributes
            $groups = DBJson::getObjectsByAttributes($data, 
                                    Group::getDBPrimaryKey(), 
                                    Group::getDBConvert());
                                  
                                    
            // concatenates the groups and the associated group leader
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $groups,
                            Group::getDBPrimaryKey(),
                            Group::getDBConvert()['U_leader'] ,
                            $leader,
                            User::getDBPrimaryKey()
                            );
       
            // concatenates the groups and the associated group member
            $res = DBJson::concatResultObjectLists($data, 
                            $res,
                            Group::getDBPrimaryKey(),
                            Group::getDBConvert()['U_member'] ,
                            $member,
                            User::getDBPrimaryKey(),
                            '2'
                            );
                                                  
            // to reindex
            $res = array_merge($res);
        
            $this->_app->response->setBody(Group::encodeGroup($res));
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllGroups failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Group::encodeGroup(new Group()));
            $this->_app->stop();
        }
    } 
    
    /**
     * GET GetSheetUserGroups
     *
     * @param int $userid a database user identifier
     * @param int $esid a database exercise sheet identifier
     */
    public function getSheetUserGroups($userid, $esid)
    {    
        Logger::Log("starts GET GetSheetUserGroups",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid),
                            ctype_digit($esid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSheetUserGroups.sql", 
                                        array("userid" => $userid,
                                            "esid" => $esid));  
                                        
        // checks the correctness of the query    
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();

            // generates an assoc array of an user by using a defined list of 
            // its attributes
            $leader = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert(),
                                            '2');
            
            // generates an assoc array of usersby using a defined list of 
            // its attributes
            $member = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert(),
                                            '2'
                                            );
                                            
            // generates an assoc array of groups by using a defined list of 
            // its attributes
            $groups = DBJson::getObjectsByAttributes($data, 
                                    Group::getDBPrimaryKey(), 
                                    Group::getDBConvert());
                                  
                                    
            // concatenates the groups and the associated group leader
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $groups,
                            Group::getDBPrimaryKey(),
                            Group::getDBConvert()['U_leader'] ,
                            $leader,
                            User::getDBPrimaryKey()
                            );
       
            // concatenates the groups and the associated group member
            $res = DBJson::concatResultObjectLists($data, 
                            $res,
                            Group::getDBPrimaryKey(),
                            Group::getDBConvert()['U_member'] ,
                            $member,
                            User::getDBPrimaryKey(),
                            '2'
                            );
                            
                          
            // to reindex
            $res = array_merge($res);
        
            $this->_app->response->setBody(Group::encodeGroup($res));
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSheetUserGroups failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Group::encodeGroup(new Group()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetSheetGroups
     *
     * @param int $esid a database exercise sheet identifier
     */
    public function getSheetGroups($esid)
    {     
        Logger::Log("starts GET GetSheetGroups",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSheetGroups.sql", 
                                        array("esid" => $esid));  
                                        
        // checks the correctness of the query    
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();

            // generates an assoc array of an user by using a defined list of 
            // its attributes
            $leader = DBJson::getResultObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert());
            
            // generates an assoc array of usersby using a defined list of 
            // its attributes
            $member = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert(),
                                            '2'
                                            );
                                            
            // generates an assoc array of groups by using a defined list of 
            // its attributes
            $groups = DBJson::getObjectsByAttributes($data, 
                                    Group::getDBPrimaryKey(), 
                                    Group::getDBConvert());
                                  
                                    
            // concatenates the groups and the associated group leader
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $groups,
                            Group::getDBPrimaryKey(),
                            Group::getDBConvert()['U_leader'] ,
                            $leader,
                            User::getDBPrimaryKey(),
                            '2'
                            );
       
            // concatenates the groups and the associated group member
            $res = DBJson::concatResultObjectLists($data, 
                            $res,
                            Group::getDBPrimaryKey(),
                            Group::getDBConvert()['U_member'] ,
                            $member,
                            User::getDBPrimaryKey(),
                            '2'
                            );
                            
                          
            // to reindex
            $res = array_merge($res);
        
            $this->_app->response->setBody(Group::encodeGroup($res));
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSheetGroups failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Group::encodeGroup(new Group()));
            $this->_app->stop();
        }
    }

}
?>