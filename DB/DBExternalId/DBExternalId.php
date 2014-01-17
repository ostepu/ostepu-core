<?php
/**
 * @file DBExternalId.php contains the DBExternalId class
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
$com = new CConfig(DBExternalId::getPrefix());

// runs the DBExternalId
if (!$com->used())
    new DBExternalId($com->loadConfig());  
    
/**
 * A class, to abstract the "ExternalId" table from database
 *
 * @author Till Uhlig
 */
class DBExternalId
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
    private static $_prefix = "externalid";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBExternalId::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBExternalId::$_prefix = $value;
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

        // PUT EditExternalId
        $this->_app->put('/' . $this->getPrefix() . '(/externalid)/:exid(/)',
                        array($this,'editExternalId'));
        
        // DELETE DeleteExternalId
        $this->_app->delete('/' . $this->getPrefix() . '(/externalid)/:exid(/)',
                           array($this,'deleteExternalId'));
        
        // POST SetExternalId
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'setExternalId'));
                         
        // GET GetExternalId
        $this->_app->get('/' . $this->getPrefix() . '(/externalid)/:exid(/)',
                        array($this,'getExternalId'));
                        
        // GET GetAllExternalIds
        $this->_app->get('/' . $this->getPrefix() . '(/externalid)(/)',
                        array($this,'getAllExternalIds'));
                        
        // GET GetCourseExternalIds
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid(/)',
                        array($this,'getCourseExternalIds'));
                    
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
                    
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * PUT EditExternalId
     *
     * @param string $exid a database external id identifier
     */
    public function editExternalId($exid)
    {
        Logger::Log("starts PUT EditExternalId",LogLevel::DEBUG);
                            
        // decode the received external id data, as an object
        $insert = ExternalId::decodeExternalId($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/EditExternalId.sql", 
                                            array("exid" => $exid, 
                                            "values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditExternalId failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * DELETE DeleteExternalId
     *
     * @param string $exid a database external id identifier
     */
    public function deleteExternalId($exid)
    {
        Logger::Log("starts DELETE DeleteExternalId",LogLevel::DEBUG);
        
        $exid = DBJson::mysql_real_escape_string($exid);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteExternalId.sql", 
                                        array("exid" => $exid));    
        
        // checks the correctness of the query                             
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteExternalId failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
    
    /**
     * POST SetExternalId
     */ 
    public function setExternalId()
    {
        Logger::Log("starts POST SetExternalId",LogLevel::DEBUG);
        
        // decode the received external id data, as an object
        $insert = ExternalId::decodeExternalId($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/SetExternalId.sql", 
                                            array("values" => $data));                   
           
           // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new ExternalId();
                $obj->setId($queryResult->getInsertId());
            
                $this->_app->response->setBody(ExternalId::encodeExternalId($obj)); 
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST SetExternalId failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * GET GetExternalId
     *
     * @param string $exid a database external id identifier
     */
    public function getExternalId($exid)
    {    
        Logger::Log("starts GET GetExternalId",LogLevel::DEBUG);
        
        $exid = DBJson::mysql_real_escape_string($exid);
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetExternalId.sql", 
                                        array("exid" => $exid));
       
        // checks the correctness of the query                                     
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            $data = $query->getResponse();
            
            // generates an assoc array of courses by using a defined list of 
            // its attributes
            $course = DBJson::getObjectsByAttributes($data, 
                                    Course::getDBPrimaryKey(), 
                                    Course::getDBConvert());
            
            // generates an assoc array of external IDs by using a defined list of 
            // its attributes
            $externalIds = DBJson::getObjectsByAttributes($data, 
                                    ExternalId::getDBPrimaryKey(), 
                                    ExternalId::getDBConvert());
            
            // concatenates the external IDs and the associated courses
            $res = DBJson::concatObjectListsSingleResult($data, 
                        $externalIds,ExternalId::getDBPrimaryKey(), 
                        ExternalId::getDBConvert()['EX_course'], 
                        $course,Course::getDBPrimaryKey());              
      
            // to reindex
            $res = array_merge($res);
               
            // only one object as result
            // @todo only one object as result
            /*if (count($res)>1)
                $res = $res[0];*/
             
            $this->_app->response->setBody(ExternalId::encodeExternalId($res));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                 
        } else{
            Logger::Log("GET GetExternalId failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(ExternalId::encodeExternalId(new ExternalId()));
            $this->_app->stop();
        }
    }   
    
    /**
     * GET GetAllExternalIds
     */
    public function getAllExternalIds()
    {    
        Logger::Log("starts GET GetAllExternalIds",LogLevel::DEBUG);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAllExternalIds.sql", 
                                        array());
        
        // checks the correctness of the query                                    
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of courses by using a defined list of 
            // its attributes
            $course = DBJson::getObjectsByAttributes($data, 
                                    Course::getDBPrimaryKey(), 
                                    Course::getDBConvert());
            
            // generates an assoc array of external IDs by using a defined list of 
            // its attributes
            $externalIds = DBJson::getObjectsByAttributes($data, 
                                    ExternalId::getDBPrimaryKey(), 
                                    ExternalId::getDBConvert());
            
            // concatenates the external IDs and the associated courses
            $res = DBJson::concatObjectListsSingleResult($data, 
                        $externalIds,ExternalId::getDBPrimaryKey(), 
                        ExternalId::getDBConvert()['EX_course'], 
                        $course,Course::getDBPrimaryKey());              
            
            // to reindex
            $res = array_merge($res);
            
            $this->_app->response->setBody(ExternalId::encodeExternalId($res));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllExternalIds failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(ExternalId::encodeExternalId(new ExternalId()));
            $this->_app->stop();
        }
    }    
    
    /**
     * GET getCourseExternalIds
     *
     * @param int $courseid a database course identifier
     */
    public function getCourseExternalIds($courseid)
    {    
        Logger::Log("starts GET getCourseExternalIds",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($courseid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetCourseExternalIds.sql", 
                                        array("courseid" => $courseid));

        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of a course by using a defined list of 
            // its attributes
            $course = DBJson::getObjectsByAttributes($data, 
                                    Course::getDBPrimaryKey(), 
                                    Course::getDBConvert());
           
            // generates an assoc array of external IDs by using a defined list of 
            // its attributes
            $externalIds = DBJson::getObjectsByAttributes($data, 
                                    ExternalId::getDBPrimaryKey(), 
                                    ExternalId::getDBConvert());
            
            // concatenates the external IDs and the associated course
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $externalIds,ExternalId::getDBPrimaryKey(), 
                            ExternalId::getDBConvert()['EX_course'], 
                            $course,Course::getDBPrimaryKey());              
            
            // to reindex
            $res = array_merge($res);
            
            $this->_app->response->setBody(ExternalId::encodeExternalId($res));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetExerciseSheet failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(ExternalId::encodeExternalId(new ExternalId()));
            $this->_app->stop();
        }
    }
}
?>