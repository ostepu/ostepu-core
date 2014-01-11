<?php
/**
 * @file DBSelectedSubmission.php contains the DBSelectedSubmission class
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
$com = new CConfig(DBSelectedSubmission::getPrefix());

// runs the DBSelectedSubmission
if (!$com->used())
    new DBSelectedSubmission($com->loadConfig());  
    
/**
 * A class, to abstract the "SelectedSubmission" table from database
 *
 * @author Till Uhlig
 */
class DBSelectedSubmission
{
    /**
     * @var $_app the slim object
     */ 
    private $_app=null;
    
    /**
     * @var $_conf the component data object
     */ 
    private $_conf=null;
    
    /**
     * @var $query a list of links to a query component
     */ 
    private $query=array();
    
    /**
     * @var $_prefix the prefixes, the class works with (comma separated)
     */ 
    private static $_prefix = "selectedsubmission";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBSelectedSubmission::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBSelectedSubmission::$_prefix = $value;
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
        $this->query = array(CConfig::getLink($conf->getLinks(),"out"));
        
        // initialize slim
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');

        // PUT EditSelectedSubmission
        $this->_app->put('/' . $this->getPrefix() . 
                        '/leader/:userid/exercise/:eid',
                        array($this,'editSelectedSubmission'));
        
        // DELETE DeleteSelectedSubmission
        $this->_app->delete('/' . $this->getPrefix() . 
                            '/leader/:userid/exercise/:eid',
                            array($this,'deleteSelectedSubmission'));
        
        // POST SetSelectedSubmission
        $this->_app->post('/' . $this->getPrefix(),
                         array($this,'setSelectedSubmission'));  
        
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
        
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * PUT EditSelectedSubmission
     *
     * @param $userid a database user identifier
     * @param $eid a database exercise identifier
     */
    public function editSelectedSubmission($userid, $eid)
    {
        Logger::Log("starts PUT EditSelectedSubmission",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid),
                            ctype_digit($eid));
                            
        // decode the received submission data, as an object
        $insert = Submission::decodeSubmission($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                    "Sql/EditSelectedSubmission.sql", 
                                    array("userid" => $userid,"eid" => $eid));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditSelectedSubmission failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * DELETE DeleteSelectedSubmission
     *
     * @param $userid a database user identifier
     * @param $eid a database exercise identifier
     */
    public function deleteSelectedSubmission($userid, $eid)
    {
        Logger::Log("starts DELETE DeleteSelectedSubmission",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid),
                            ctype_digit($eid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteSelectedSubmission.sql", 
                                        array("userid" => $userid,"eid" => $eid));    
        
        // checks the correctness of the query                          
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteSelectedSubmission failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
    
    /**
     * POST SetSelectedSubmission
     */
    public function setSelectedSubmission()
    {
        Logger::Log("starts POST SetSelectedSubmission",LogLevel::DEBUG);
        
        // decode the received submission data, as an object
        $insert = Submission::decodeSubmission($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/SetSelectedSubmission.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query 
            if ($result['status']>=200 && $result['status']<=299){

                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST SetSelectedSubmission failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }

}
?>