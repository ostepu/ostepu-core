<?php
/**
 * @file DBInvitation.php contains the DBInvitation class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/DBRequest.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/Logger.php' );

\Slim\Slim::registerAutoloader();

// runs the CConfig
$com = new CConfig(DBInvitation::getPrefix());

// runs the DBInvitation
if (!$com->used())
    new DBInvitation($com->loadConfig());  
    
/**
 * A class, to abstract the "ExerciseType" table from database
 */
class DBInvitation
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
    private static $_prefix = "invitation";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBInvitation::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBInvitation::$_prefix = $value;
    }


    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
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

        // PUT EditInvitation
        $this->_app->put('/' . $this->getPrefix() . 
                        '/user/:userid/exercisesheet/:esid/user/:memberid(/)',
                        array($this,'editInvitation'));
        
        // DELETE DeleteInvitation
        $this->_app->delete('/' . $this->getPrefix() . 
                            '/user/:userid/exercisesheet/:esid/user/:memberid(/)',
                            array($this,'deleteInvitation'));
        
        // POST AddInvitation
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'addInvitation'));  
        
        // GET GetLeaderInvitations
        $this->_app->get('/' . $this->getPrefix() . '/leader/user/:userid(/)',
                        array($this,'getLeaderInvitations'));
        
        // GET GetMemberInvitations
        $this->_app->get('/' . $this->getPrefix() . '/member/user/:userid(/)',
                        array($this,'getMemberInvitations'));
                        
        // GET GetAllInvitations
        $this->_app->get('/' . $this->getPrefix() . '(/invitation)(/)',
                        array($this,'getAllInvitations')); 
                        
        // GET GetSheetLeaderInvitations 
        $this->_app->get('/' . $this->getPrefix() . 
                        '/leader/exercisesheet/:esid/user/:userid(/)',
                        array($this,'getSheetLeaderInvitations'));  
                        
        // GET GetSheetMemberInvitations 
        $this->_app->get('/' . $this->getPrefix() . 
                        '/member/exercisesheet/:esid/user/:userid(/)',
                        array($this,'getSheetMemberInvitations'));
                        
        // GET GetSheetInvitations 
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid(/)',
                        array($this,'getSheetInvitations'));  
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
        
            // run Slim
            $this->_app->run();
        }
    }


    /**
     * Edits an invitation.
     *
     * Called when this component receives an HTTP PUT request to
     * /invitation/user/$userid/exercisesheet/$esid/user/$memberid(/)
     * The request body should contain a JSON object representing the 
     * invitations's new attributes.
     *
     * @param int $userid The id of the user that invites a new user.
     * @param int $esid The id of the exercise sheet the group belongs to.
     * @param int $memberid The id of the user that is invited.
     */
    public function editInvitation($userid,$esid,$memberid)
    {
        Logger::Log("starts PUT EditInvitation",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid),
                            ctype_digit($esid),
                            ctype_digit($memberid));
                            
        // decode the received invitation data, as an object
        $insert = Invitation::decodeInvitation($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                    "Sql/EditInvitation.sql", 
                                    array("userid" => $userid,
                                        "esid" => $esid,
                                        "memberid" => $memberid, 
                                        "values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditInvitation failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }


    /**
     * Deletes an invitation.
     *
     * Called when this component receives an HTTP DELETE request to
     * /invitation/user/$userid/exercisesheet/$esid/user/$memberid(/)
     *
     * @param int $userid The id of the user that invites a new user.
     * @param int $esid The id of the exercise sheet the group belongs to.
     * @param int $memberid The id of the user that is invited.
     */
    public function deleteInvitation($userid,$esid,$memberid)
    {
        Logger::Log("starts DELETE DeleteInvitation",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid),
                            ctype_digit($esid),
                            ctype_digit($memberid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteInvitation.sql", 
                                        array("userid" => $userid,
                                            "esid" => $esid,
                                            "memberid" => $memberid));    
        
        // checks the correctness of the query                          
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus(201);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteInvitation failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 452);
            $this->_app->stop();
        }
    }


    /**
     * Adds an invitation.
     *
     * Called when this component receives an HTTP POST request to
     * /invitation(/).
     * The request body should contain a JSON object representing the 
     * invitations's attributes.
     */
    public function addInvitation()
    {
        Logger::Log("starts POST AddInvitation",LogLevel::DEBUG);
        
        // decode the received invitation data, as an object
        $insert = Invitation::decodeInvitation($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/AddInvitation.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query 
            if ($result['status']>=200 && $result['status']<=299){
 
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST AddInvitation failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }


    /**
     * Returns all invitations.
     *
     * Called when this component receives an HTTP GET request to
     * /invitation(/) or /invitation/invitation(/).
     */
    public function getAllInvitations()
    {    
        Logger::Log("starts GET GetAllInvitations",LogLevel::DEBUG);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAllInvitations.sql", 
                                        array());
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of users by using a defined list of 
            // its attributes
            $leader = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert());
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $member = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert(), 
                                            '2');
                                            
            // generates an assoc array of invitations by using a defined list of 
            // its attributes
            $invitations = DBJson::getObjectsByAttributes($data, 
                                    Invitation::getDBPrimaryKey(), 
                                    Invitation::getDBConvert());  
                                    
            // concatenates the invitations and the associated invitation leader
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $invitations,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_leader'] ,
                            $leader,
                            User::getDBPrimaryKey());
       
            // concatenates the invitations and the associated invitation member
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_member'] ,
                            $member,
                            User::getDBPrimaryKey(),
                            '2');
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Invitation::encodeInvitation($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllInvitations failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Invitation::encodeInvitation(new Invitation()));
            $this->_app->stop();
        }
    }


    /**
     * Returns all invitations the user created.
     *
     * Called when this component receives an HTTP GET request to
     * /invitation/leader/user/$userid(/).
     *
     * @param int $userid The id of the user that created the returned invitations.
     */
    public function getLeaderInvitations($userid)
    {    
        Logger::Log("starts GET GetLeaderInvitations",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetLeaderInvitations.sql", 
                                        array("userid" => $userid));
        
        // checks the correctness of the query                                       
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            $data = $query->getResponse();
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $leader = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert());
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $member = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert(), 
                                            '2');
                                            
            // generates an assoc array of invitations by using a defined list 
            // of its attributes
            $invitations = DBJson::getObjectsByAttributes($data, 
                                    Invitation::getDBPrimaryKey(), 
                                    Invitation::getDBConvert());  
                                    
            // concatenates the invitations and the associated invitation leader
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $invitations,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_leader'] ,
                            $leader,
                            User::getDBPrimaryKey());
       
            // concatenates the invitations and the associated invitation member
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_member'] ,
                            $member,
                            User::getDBPrimaryKey(),
                            '2');
                            
            // to reindex
            $res = array_values($res); 
                
            $this->_app->response->setBody(Invitation::encodeInvitation($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetLeaderInvitations failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Invitation::encodeInvitation(new Invitation()));
            $this->_app->stop();
        }
    }


    /**
     * Returns all invitations to which the user is invited to.
     *
     * Called when this component receives an HTTP GET request to
     * /invitation/member/user/$userid(/).
     *
     * @param int $userid The id of the user.
     */
    public function getMemberInvitations($userid)
    {    
        Logger::Log("starts GET GetMemberInvitations",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetMemberInvitations.sql", 
                                        array("userid" => $userid));
        
        // checks the correctness of the query                                       
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $leader = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert());
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $member = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert(), 
                                            '2');
                                            
            // generates an assoc array of invitations by using a defined list of 
            // its attributes
            $invitations = DBJson::getObjectsByAttributes($data, 
                                    Invitation::getDBPrimaryKey(), 
                                    Invitation::getDBConvert());  
                                    
            // concatenates the invitations and the associated invitation leader
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $invitations,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_leader'] ,
                            $leader,
                            User::getDBPrimaryKey());
       
            // concatenates the invitations and the associated invitation member
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_member'] ,
                            $member,
                            User::getDBPrimaryKey(),
                            '2');
                            
            // to reindex
            $res = array_values($res); 
                
            $this->_app->response->setBody(Invitation::encodeInvitation($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetMemberInvitations failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Invitation::encodeInvitation(new Invitation()));
            $this->_app->stop();
        }
    }


    /**
     * Returns all invitations the user created regarding a 
     * specific exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /invitation/leader/exercisesheet/$esid/user/$userid(/).
     *
     * @param int $esid The id of the exercise sheet.
     * @param int $userid The id of the user that created the returned invitations.
     */
    public function getSheetLeaderInvitations($esid,$userid)
    {     
        Logger::Log("starts GET GetSheetLeaderInvitations",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid), 
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSheetLeaderInvitations.sql", 
                                        array("esid" => $esid,"userid" => $userid));
        
        // checks the correctness of the query                                       
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $leader = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert());
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $member = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert(), 
                                            '2');
                                            
            // generates an assoc array of invitations by using a defined list of 
            // its attributes
            $invitations = DBJson::getObjectsByAttributes($data, 
                                    Invitation::getDBPrimaryKey(), 
                                    Invitation::getDBConvert());  
                                    
            // concatenates the invitations and the associated invitation leader
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $invitations,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_leader'] ,
                            $leader,
                            User::getDBPrimaryKey());
       
            // concatenates the invitations and the associated invitation member
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_member'] ,
                            $member,
                            User::getDBPrimaryKey(),
                            '2');
                            
            // to reindex
            $res = array_values($res); 
                
            $this->_app->response->setBody(Invitation::encodeInvitation($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSheetLeaderInvitations failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Invitation::encodeInvitation(new Invitation()));
            $this->_app->stop();
        }
    }


    /**
     * Returns all invitations to which the user is invited to regarding a 
     * specific exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /invitation/member/exercisesheet/$esid/user/$userid(/).
     *
     * @param int $esid The id of the exercise sheet.
     * @param int $userid The id of the user.
     */
    public function getSheetMemberInvitations($esid,$userid)
    {      
        Logger::Log("starts GET GetSheetMemberInvitations",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid), 
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSheetMemberInvitations.sql", 
                                        array("esid" => $esid,"userid" => $userid));
        
        // checks the correctness of the query                                       
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $leader = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert());
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $member = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert(), 
                                            '2');
                                            
            // generates an assoc array of invitations by using a defined list of 
            // its attributes
            $invitations = DBJson::getObjectsByAttributes($data, 
                                    Invitation::getDBPrimaryKey(), 
                                    Invitation::getDBConvert());  
                                    
            // concatenates the invitations and the associated invitation leader
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $invitations,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_leader'] ,
                            $leader,
                            User::getDBPrimaryKey());
       
            // concatenates the invitations and the associated invitation member
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_member'] ,
                            $member,
                            User::getDBPrimaryKey(),
                            '2');
                            
            // to reindex
            $res = array_values($res); 
                
            $this->_app->response->setBody(Invitation::encodeInvitation($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSheetMemberInvitations failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Invitation::encodeInvitation(new Invitation()));
            $this->_app->stop();
        }
    }


    /**
     * Returns all invitations to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /invitation/member/exercisesheet/$esid/user/$userid(/).
     *
     * @param int $esid The id of the exercise sheet the invitations belong to.
     */
    public function getSheetInvitations($esid)
    {     
        Logger::Log("starts GET GetSheetInvitations",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSheetInvitations.sql", 
                                        array("esid" => $esid));
        
        // checks the correctness of the query                                       
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $leader = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert());
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $member = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert(), 
                                            '2');
                                            
            // generates an assoc array of invitations by using a defined list of 
            // its attributes
            $invitations = DBJson::getObjectsByAttributes($data, 
                                    Invitation::getDBPrimaryKey(), 
                                    Invitation::getDBConvert());  
                                    
            // concatenates the invitations and the associated invitation leader
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $invitations,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_leader'] ,
                            $leader,
                            User::getDBPrimaryKey());
       
            // concatenates the invitations and the associated invitation member
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_member'] ,
                            $member,
                            User::getDBPrimaryKey(),
                            '2');
                            
            // to reindex
            $res = array_values($res); 
                
            $this->_app->response->setBody(Invitation::encodeInvitation($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSheetInvitations failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Invitation::encodeInvitation(new Invitation()));
            $this->_app->stop();
        }
    }
}
?>