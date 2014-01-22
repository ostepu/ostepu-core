<?php
/**
 * @file DBSession.php contains the DBSession class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt 
 * @example DB/DBSession/SessionSample.json
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/Logger.php' );

\Slim\Slim::registerAutoloader();

// runs the CConfig
$com = new CConfig(DBSession::getPrefix());

// runs the DBSession
if (!$com->used())
    new DBSession($com->loadConfig());  

/**
 * A class, to abstract the "Session" table from database
 */
class DBSession
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
    private static $_prefix = "session";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBSession::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        DBSession::$_prefix = $value;
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

        // PUT EditSession
        $this->_app->put('/' . $this->getPrefix() . '(/session)/:seid(/)',
                        array($this, 'editSession'));
                        
        // DELETE RemoveSession
        $this->_app->delete('/' . $this->getPrefix() . '(/session)/:seid(/)',
                        array($this, 'removeSession'));
                        
        // PUT EditUserSession
        $this->_app->put('/' . $this->getPrefix() . '/user/:userid(/)',
                        array($this, 'editUserSession'));
                        
        // DELETE RemoveUserSession
        $this->_app->delete('/' . $this->getPrefix() . '/user/:userid(/)',
                        array($this, 'removeUserSession'));
                        
        // POST AddSession
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                        array($this, 'addSession'));
                        
        // GET GetUserSession
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid(/)',
                        array($this, 'getUserSession'));
                        
        // GET GetSessionUser
        $this->_app->get('/' . $this->getPrefix() . '(/session)/:seid(/)',
                        array($this, 'getSessionUser'));
                        
        // GET GetAllSessions
        $this->_app->get('/' . $this->getPrefix() . '(/session)(/)',
                        array($this, 'getAllSessions'));
                        
        // starts slim only if the right prefix was received 
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
            $this->getPrefix()) === 0){
            
            // run Slim
            $this->_app->run();
        }
    }


    /**
     * Edits a session identified by a sessionId.
     *
     * Called when this component receives an HTTP PUT request to
     * /session/session/$seid(/) or /session/$seid(/).
     * The request body should contain a JSON object representing the 
     * sessions's new attributes.
     *
     * @param string $seid The id of the session which is being updated.
     */
    public function editSession($seid)
    {
        Logger::Log("starts PUT EditSession",LogLevel::DEBUG);
        
        $seid = DBJson::mysql_real_escape_string($seid);
                            
        $insert = Session::decodeSession($this->_app->request->getBody());
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/EditSession.sql", 
                                        array("seid" => $seid, 
                                        "values" => $data));                   
           
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditSession failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }


    /**
     * Deletes a session identified by a sessionId.
     *
     * Called when this component receives an HTTP DELETE request to
     * /session/session/$seid(/) or /session/$seid(/).
     *
     * @param string $seid The id of the session which is being deleted.
     */
    public function removeSession($seid)
    {
        Logger::Log("starts DELETE RemoveSession",LogLevel::DEBUG);
        
        $seid = DBJson::mysql_real_escape_string($seid);
                
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteSession.sql", 
                                        array("seid" => $seid));    
                                        
        if ($result['status']>=200 && $result['status']<=299){
            $this->_app->response->setStatus(201);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE RemoveSession failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 452);
            $this->_app->stop();
        }
    }


    /**
     * Edits a session identified by an userId.
     *
     * Called when this component receives an HTTP PUT request to
     * /session/user/$userid(/).
     * The request body should contain a JSON object representing the 
     * sessions's new attributes.
     *
     * @param int $userid The id of the user that is being updated.
     */
    public function editUserSession($userid)
    {
        Logger::Log("starts PUT RemoveSession",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid));
        
        // decode the received session data, as an object
        $insert = Session::decodeSession($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/EditUserSession.sql", 
                                            array("userid" => $userid, 
                                            "values" => $data));                   
           
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT RemoveSession failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }


    /**
     * Deletes a session identified by an userId.
     *
     * Called when this component receives an HTTP DELETE request to
     * /session/user/$userid(/).
     *
     * @param int $userid The id of the user that is being deleted.
     */
    public function removeUserSession($userid)
    {
        Logger::Log("starts DELETE RemoveUserSession",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid));
                
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteUserSession.sql", 
                                        array("userid" => $userid));    
                                        
        if ($result['status']>=200 && $result['status']<=299){
            $this->_app->response->setStatus(201);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE RemoveUserSession failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 452);
            $this->_app->stop();
        }
    }


    /**
     * Adds a session.
     *
     * Called when this component receives an HTTP POST request to
     * /session(/).
     * The request body should contain a JSON object representing the new session.
     */
    public function addSession()
    {
        Logger::Log("starts POST AddSession",LogLevel::DEBUG);

        // decode the received session data, as an object
        $insert = Session::decodeSession($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/AddSession.sql", 
                                            array("values" => $data),
                                            false);                   
            
            // checks the correctness of the query   
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST AddSession failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }


    /**
     * Returns a session identified by an userId.
     *
     * Called when this component receives an HTTP GET request to
     * /session/user/$userid(/).
     *
     * @param int $userid The id of the user.
     */
    public function getUserSession($userid)
    {
        Logger::Log("starts GET GetUserSession",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid));
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetUserSession.sql", 
                                        array("userid" => $userid));        
        
        // checks the correctness of the query   
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();
            
            // generates an assoc array of an session by using a defined list 
            // of its attributes
            $session = DBJson::getResultObjectsByAttributes($data, 
                                        Session::getDBPrimaryKey(), 
                                        Session::getDBConvert());
            
            // only one object as result
            if (count($session)>0)
                $session = $session[0];
            
            $this->_app->response->setBody(Session::encodeSession($session));
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetUserSession failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Session::encodeSession(new Session()));
            $this->_app->stop();
        }
    }


    /**
     * Returns a session identified by a sessionId.
     *
     * Called when this component receives an HTTP GET request to
     * /session/session/$seid(/) or /session/$seid(/).
     *
     * @param string $seid The id or the session.
     */
    public function getSessionUser($seid)
    {
        Logger::Log("starts GET GetSessionUser",LogLevel::DEBUG);
        
        $seid = DBJson::mysql_real_escape_string($seid);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSessionUser.sql", 
                                        array("seid" => $seid));        
        
        // checks the correctness of the query   
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();
            
            // generates an assoc array of a session by using a defined list 
            // of its attributes
            $session = DBJson::getResultObjectsByAttributes($data, 
                                            Session::getDBPrimaryKey(), 
                                            Session::getDBConvert());
            
            $this->_app->response->setBody(Session::encodeSession($session));
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSessionUser failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Session::encodeSession(new Session()));
            $this->_app->stop();
        }
    }


    /**
     * Returns all sessions.
     *
     * Called when this component receives an HTTP GET request to
     * /session/session(/) or /session(/).
     */
    public function getAllSessions()
    {
        Logger::Log("starts GET GetAllSessions",LogLevel::DEBUG);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAllSessions.sql", 
                                        array());        
  
        // checks the correctness of the query   
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();
            
            // generates an assoc array of sessions by using a defined list 
            // of its attributes
            $session = DBJson::getResultObjectsByAttributes($data, 
                                        Session::getDBPrimaryKey(), 
                                        Session::getDBConvert());
          
            $this->_app->response->setBody(Session::encodeSession($session));
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllSessions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Session::encodeSession(new Session()));
            $this->_app->stop();
        }
    }
}
?>