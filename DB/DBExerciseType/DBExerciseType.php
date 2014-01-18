<?php
/**
 * @file DBExerciseType.php contains the DBExerciseType class
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
$com = new CConfig(DBExerciseType::getPrefix());

// runs the DBExerciseSheet
if (!$com->used())
    new DBExerciseType($com->loadConfig());  
    
/**
 * A class, to abstract the "ExerciseType" table from database
 *
 * @author Till Uhlig
 */
class DBExerciseType
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
    private static $_prefix = "exercisetype";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBExerciseType::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBExerciseType::$_prefix = $value;
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

        // PUT EditPossibleType
        $this->_app->put('/' . $this->getPrefix() . '(/exercisetype)/:etid(/)',
                        array($this,'editPossibleType'));
        
        // DELETE DeletePossibleType
        $this->_app->delete('/' . $this->getPrefix() . '(/exercisetype)/:etid(/)',
                           array($this,'deletePossibleType'));
        
        // POST SetPossibleType
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'setPossibleType'));  
        
        // GET GetPossibleType
        $this->_app->get('/' . $this->getPrefix() . '(/exercisetype)/:etid(/)',
                        array($this,'getPossibleType'));
        
        // GET GetAllPossibleTypes
        $this->_app->get('/' . $this->getPrefix() . '(/exercisetype)(/)',
                        array($this,'getAllPossibleTypes'));
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
        
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * PUT EditPossibleType
     *
     * @param int $etid a database exercise type identifier
     */
    public function editPossibleType($etid)
    {
        Logger::Log("starts PUT EditPossibleType",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($etid));
                            
        // decode the received exercise type data, as an object
        $insert = ExerciseType::decodeExerciseType($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                    "Sql/EditPossibleType.sql", 
                                    array("etid" => $etid, "values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditPossibleType failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * DELETE DeletePossibleType
     *
     * @param int $etid a database exercise type identifier
     */
    public function deletePossibleType($etid)
    {
        Logger::Log("starts DELETE DeletePossibleType",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($etid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeletePossibleType.sql", 
                                        array("etid" => $etid));    
        
        // checks the correctness of the query                          
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus(252);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeletePossibleType failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 452);
            $this->_app->stop();
        }
    }
    
    /**
     * POST SetPossibleType
     */
    public function setPossibleType()
    {
        Logger::Log("starts POST SetPossibleType",LogLevel::DEBUG);
        
        // decode the received exercise type data, as an object
        $insert = ExerciseType::decodeExerciseType($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/SetPossibleType.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query 
            if ($result['status']>=200 && $result['status']<=299){
                $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new ExerciseType();
                $obj->setId($queryResult->getInsertId());
            
                $this->_app->response->setBody(ExerciseType::encodeExerciseType($obj)); 
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST SetPossibleType failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * GET GetAllPossibleType
     */
    public function getAllPossibleTypes()
    {      
        Logger::Log("starts GET GetAllPossibleType",LogLevel::DEBUG);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAllPossibleTypes.sql", 
                                        array());
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of exercise types by using a defined 
            // list of its attributes
            $exerciseTypes = DBJson::getResultObjectsByAttributes($data, 
                                        ExerciseType::getDBPrimaryKey(), 
                                        ExerciseType::getDBConvert());
 
            $this->_app->response->setBody(ExerciseType::encodeExerciseType($exerciseTypes));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllPossibleType failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(ExerciseTypes::encodeExerciseType(new ExerciseType()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetPossibleType
     *
     * @param int $etid a database exercise type identifier
     */
    public function getPossibleType($etid)
    {        
        Logger::Log("starts GET GetPossibleType",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($etid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetPossibleType.sql", 
                                        array("etid" => $etid));
        
        // checks the correctness of the query                                       
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of an exercise type by using a defined 
            // list of its attributes
            $exerciseType = DBJson::getResultObjectsByAttributes($data, 
                                        ExerciseType::getDBPrimaryKey(), 
                                        ExerciseType::getDBConvert()); 
            
            // to reindex
            $exerciseType = array_merge($exerciseType);
            
            // only one object as result
           if (count($exerciseType)>0)
                $exerciseType = $exerciseType[0];
                
            $this->_app->response->setBody(ExerciseType::encodeExerciseType($exerciseType));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetPossibleType failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(ExerciseType::encodeExerciseType(new ExerciseType()));
            $this->_app->stop();
        }
    }

}
?>