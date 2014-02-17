<?php
/**
 * @file DBExternalId.php contains the DBExternalId class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBExternalId/ExternalIdSample.json
 */ 

require_once( '../../Assistants/Slim/Slim.php' );
include_once( '../../Assistants/Structures.php' );
include_once( '../../Assistants/Request.php' );
include_once( '../../Assistants/DBJson.php' );
include_once( '../../Assistants/DBRequest.php' );
include_once( '../../Assistants/CConfig.php' );
include_once( '../../Assistants/Logger.php' );

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
        $arr = true;
        if (!is_array($insert)){
            $insert = array($insert);
            $arr=false;
        }

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
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
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
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
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
        $arr = true;
        if (!is_array($insert)){
            $insert = array($insert);
            $arr=false;
        }
        
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
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
                $this->_app->stop();
            }
        }
    }

    public function get($functionName,$sqlFile,$userid,$courseid,$esid,$eid,$exid,$mid,$singleResult=false)
    {
        Logger::Log("starts GET " . $functionName,LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        $exid = DBJson::mysql_real_escape_string($exid);
        DBJson::checkInput($this->_app, 
                            $userid == "" ? true : ctype_digit($userid), 
                            $courseid == "" ? true : ctype_digit($courseid), 
                            $esid == "" ? true : ctype_digit($esid), 
                            $eid == "" ? true : ctype_digit($eid),
                            $mid == "" ? true : ctype_digit($mid));
                            
            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        $sqlFile, 
                                        array("userid" => $userid,
                                        'courseid' => $courseid,
                                        'esid' => $esid,
                                        'eid' => $eid,
                                        'exid' => $exid,
                                        'mid' => $mid));
 
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){ 
            $query = Query::decodeQuery($result['content']);
            
            if ($query->getNumRows()>0){
                $res = ExternalId::ExtractExternalId($query->getResponse(),$singleResult); 
                $this->_app->response->setBody(ExternalId::encodeExternalId($res));
        
                $this->_app->response->setStatus(200);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
                $this->_app->stop(); 
            }
            else
                $result['status'] = 409;
                
        }
        
            Logger::Log("GET " . $functionName . " failed",LogLevel::ERROR);
            $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(ExternalId::encodeExternalId(new ExternalId()));
            $this->_app->stop();
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
            $this->get("GetExternalId",
                "Sql/GetExternalId.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($exid) ? $exid : "",
                isset($mid) ? $mid : "",
                true);  
    }   


    /**
     * Returns all aliases for the courses.
     *
     * Called when this component receives an HTTP GET request to
     * /externalid(/) or /externalid/externalid(/).
     */
    public function getAllExternalIds()
    {    
            $this->get("GetAllExternalIds",
                "Sql/GetAllExternalIds.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($exid) ? $exid : "",
                isset($mid) ? $mid : "");
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
            $this->get("GetCourseExternalIds",
                "Sql/GetCourseExternalIds.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($exid) ? $exid : "",
                isset($mid) ? $mid : "");
    }
}
?>