<?php
/**
 * @file DBApprovalCondition.php contains the DBApprovalCondition class
 * @include DB/DBApprovalCondition/ApprovalConditionSample.json
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/Logger.php' );

\Slim\Slim::registerAutoloader();

// runs the CConfig
$com = new CConfig(DBApprovalCondition::getPrefix());

// runs the DBExerciseSheet
if (!$com->used())
    new DBApprovalCondition($com->loadConfig());  
    
/**
 * A class, to abstract the "ApprovalCondition" table from database
 *
 * @author Till Uhlig
 */
class DBApprovalCondition
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
    private static $_prefix = "approvalcondition";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBApprovalCondition::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBApprovalCondition::$_prefix = $value;
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

        // PUT EditApprovalCondition
        $this->_app->put('/' . $this->getPrefix() . '(/approvalcondition)/:apid(/)',
                        array($this,'editApprovalCondition'));
        
        // DELETE DeleteApprovalCondition
        $this->_app->delete('/' . $this->getPrefix() . '(/approvalcondition)/:apid(/)',
                           array($this,'deleteApprovalCondition'));
        
        // POST AddApprovalCondition
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'addApprovalCondition'));  
        
        // GET GetApprovalCondition
        $this->_app->get('/' . $this->getPrefix() . '(/approvalcondition)/:apid(/)',
                        array($this,'getApprovalCondition'));
        
        // GET GetAllApprovalConditions
        $this->_app->get('/' . $this->getPrefix() . '(/approvalcondition)(/)',
                        array($this,'getAllApprovalConditions'));
                        
        // GET GetCourseApprovalConditions
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid(/)',
                        array($this,'getCourseApprovalConditions'));
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * PUT EditApprovalCondition
     *
     * @param int $apid a database approval condition identifier
     */
    public function editApprovalCondition($apid)
    {
        Logger::Log("starts PUT EditApprovalCondition",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($apid));
                            
        // decode the received approval condition data, as an object
        $insert = ApprovalCondition::decodeApprovalCondition($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/EditApprovalCondition.sql", 
                                            array("apid" => $apid, "values" => $data));                   
           
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditApprovalCondition failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 251);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * DELETE DeleteApprovalCondition
     *
     * @param int $apid a database approval condition identifier
     */
    public function deleteApprovalCondition($apid)
    {
        Logger::Log("starts DELETE DeleteApprovalCondition",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($apid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeletePossibleType.sql", 
                                        array("apid" => $apid));    
            
        // checks the correctness of the query  
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus(201);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteApprovalCondition failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 252);
            $this->_app->stop();
        }
    }
    
    /**
     * POST SetApprovalCondition
     */
    public function addApprovalCondition()
    {
        Logger::Log("starts POST AddApprovalCondition",LogLevel::DEBUG);
        
        // decode the received approval condition data, as an object
        $insert = ApprovalCondition::decodeApprovalCondition($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/AddApprovalCondition.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new ApprovalCondition();
                $obj->setId($queryResult->getInsertId());
            
                $this->_app->response->setBody(ApprovalCondition::encodeApprovalCondition($obj)); 
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST AddApprovalCondition failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 251);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * GET GetAllApprovalConditions
     */
    public function getAllApprovalConditions()
    {   
        Logger::Log("starts GET GetAllApprovalConditions",LogLevel::DEBUG);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAllApprovalConditions.sql", 
                                        array());
        // checks the correctness of the query                                    
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of approval conditions by using a 
            // defined list of its attributes
            $approvalConditions = DBJson::getResultObjectsByAttributes($data,
                                        ApprovalCondition::getDBPrimaryKey(), 
                                        ApprovalCondition::getDBConvert());
 
            $this->_app->response->setBody(ApprovalCondition::encodeApprovalCondition($approvalConditions));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllApprovalConditions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(ApprovalCondition::encodeApprovalCondition(new ApprovalCondition()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetApprovalCondition
     *
     * @param int $apid a database approval condition identifier
     */
    public function getApprovalCondition($apid)
    {     
        Logger::Log("starts GET GetApprovalCondition",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($apid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetApprovalCondition.sql", 
                                        array("apid" => $apid));
        
        // checks the correctness of the query
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of an approval condition by using a 
            // defined list of its attributes
            $approvalCondition = DBJson::getResultObjectsByAttributes($data, 
                                        ApprovalCondition::getDBPrimaryKey(), 
                                        ApprovalCondition::getDBConvert()); 
            
            // to reindex
            $approvalCondition = array_merge($approvalCondition);
            
            // only one object as result
            if (count($approvalCondition)>0)
                $approvalCondition = $approvalCondition[0];
                
            $this->_app->response->setBody(ApprovalCondition::encodeApprovalCondition($approvalCondition));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetApprovalCondition failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(ApprovalCondition::encodeApprovalCondition(new ApprovalCondition()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetCourseApprovalConditions
     *
     * @param int $courseid a database course identifier
     */
    public function getCourseApprovalConditions($courseid)
    {      
        Logger::Log("starts GET GetCourseApprovalConditions",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($courseid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetCourseApprovalConditions.sql", 
                                        array("courseid" => $courseid));
        
        // checks the correctness of the query                                  
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of approval conditions by using a 
            // defined list of its attributes
            $approvalConditions = DBJson::getResultObjectsByAttributes($data, 
                                        ApprovalCondition::getDBPrimaryKey(), 
                                        ApprovalCondition::getDBConvert()); 
            
            // to reindex
            $approvalCondition = array_merge($approvalConditions);
                
            $this->_app->response->setBody(ApprovalCondition::encodeApprovalCondition($approvalConditions));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetCourseApprovalConditions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(ApprovalCondition::encodeApprovalCondition(new ApprovalCondition()));
            $this->_app->stop();
        }
    }
}
?>