<?php
/**
 * @file DBExternalId.php contains the DBExternalId class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBExternalId/ExternalIdSample.json
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

        // PUT EditExternalId
        $this->_app->put('/' . $this->getPrefix() . '(/externalid)/:exid(/)',
                        array($this,'editExternalId'));
        
        // DELETE DeleteExternalId
        $this->_app->delete('/' . $this->getPrefix() . '(/externalid)/:exid(/)',
                           array($this,'deleteExternalId'));
        
        // POST AddExternalId
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'addExternalId'));
                         
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
     * Edits an alias for an already existing course.
     *
     * Called when this component receives an HTTP PUT request to
     * /externalid/$exid(/) or /externalid/externalid/$exid(/).
     * The request body should contain a JSON object representing the 
     * externalId's new attributes.
     *
     * @param string $exid The alias of the course that is being updated.
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
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }


    /**
     * Deletes an alias for an already existing course.
     *
     * Called when this component receives an HTTP DELETE request to
     * /externalid/$exid(/) or /externalid/externalid/$exid(/).
     *
     * @param string $exid The alias of the course that is being deleted.
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
        
            $this->_app->response->setStatus(201);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteExternalId failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 452);
            $this->_app->stop();
        }
    }


    /**
     * Adds an alias for an already existing course.
     *
     * Called when this component receives an HTTP POST request to
     * /externalid/$exid(/) or /externalid/externalid/$exid(/).
     * The request body should contain a JSON object representing the 
     * externalId's attributes.
     *
     * @param string $exid The alias of the course that is being created.
     */
    public function addExternalId()
    {
        Logger::Log("starts POST AddExternalId",LogLevel::DEBUG);
        
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
                                            "Sql/AddExternalId.sql", 
                                            array("values" => $data));                   
           
           // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $queryResult = Query::decodeQuery($result['content']);
                
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST AddExternalId failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }


    /**
     * Returns the alias for an already existing course.
     *
     * Called when this component receives an HTTP GET request to
     * /externalid/$exid(/) or /externalid/externalid/$exid(/).
     *
     * @param string $exid The alias of the course that should be returned.
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
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                 
        } else{
            Logger::Log("GET GetExternalId failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(ExternalId::encodeExternalId(new ExternalId()));
            $this->_app->stop();
        }
    }   


    /**
     * Returns all aliases for the courses.
     *
     * Called when this component receives an HTTP GET request to
     * /externalid(/) or /externalid/externalid(/).
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
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllExternalIds failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(ExternalId::encodeExternalId(new ExternalId()));
            $this->_app->stop();
        }
    }    


    /**
     * Returns the aliases for an already existing course.
     *
     * Called when this component receives an HTTP GET request to
     * /externalid/$exid(/) or /externalid/externalid/$exid(/).
     *
     * @param int $courseid The id of the course whose aliases should be returned.
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
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetExerciseSheet failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(ExternalId::encodeExternalId(new ExternalId()));
            $this->_app->stop();
        }
    }
}
?>