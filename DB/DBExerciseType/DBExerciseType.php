<?php
/**
 * @file DBExerciseType.php contains the DBExerciseType class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBExerciseType/ExerciseTypeSample.json
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
$com = new CConfig(DBExerciseType::getPrefix());

// runs the DBExerciseSheet
if (!$com->used())
    new DBExerciseType($com->loadConfig());  
    
/**
 * A class, to abstract the "ExerciseType" table from database
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

        // PUT EditExerciseType
        $this->_app->put('/' . $this->getPrefix() . '(/exercisetype)/:etid(/)',
                        array($this,'editExerciseType'));
        
        // DELETE DeleteExerciseType
        $this->_app->delete('/' . $this->getPrefix() . '(/exercisetype)/:etid(/)',
                           array($this,'deleteExerciseType'));
        
        // POST SetExerciseType
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'addExerciseType'));  
        
        // GET GetExerciseType
        $this->_app->get('/' . $this->getPrefix() . '(/exercisetype)/:etid(/)',
                        array($this,'getExerciseType'));
        
        // GET GetAllExerciseTypes
        $this->_app->get('/' . $this->getPrefix() . '(/exercisetype)(/)',
                        array($this,'getAllExerciseTypes'));
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
        
            // run Slim
            $this->_app->run();
        }
    }


    /**
     * Edits an exercise type.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercisetype/exercisetype/$etid(/) or /exercisetype/$etid(/).
     * The request body should contain a JSON object representing the 
     * exercise type's new attributes.
     *
     * @param int $etid The id or the exercise type.
     */
    public function editExerciseType($etid)
    {
        Logger::Log("starts PUT EditExerciseType",LogLevel::DEBUG);
        
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
                                    "Sql/EditExerciseType.sql", 
                                    array("etid" => $etid, "values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditExerciseType failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }


    /**
     * Deletes an exercise type.
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisetype/exercisetype/$etid(/) or /exercisetype/$etid(/).
     *
     * @param int $etid The id or the exercise type that is being deleted.
     */
    public function deleteExerciseType($etid)
    {
        Logger::Log("starts DELETE DeleteExerciseType",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($etid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteExerciseType.sql", 
                                        array("etid" => $etid));    
        
        // checks the correctness of the query                          
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus(201);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteExerciseType failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 452);
            $this->_app->stop();
        }
    }


    /**
     * Adds a new exercise type.
     *
     * Called when this component receives an HTTP POST request to
     * /exercisetype(/).
     * The request body should contain a JSON object representing the 
     * new exercise type's attributes.
     */
    public function addExerciseType()
    {
        Logger::Log("starts POST SetExerciseType",LogLevel::DEBUG);
        
        // decode the received exercise type data, as an object
        $insert = ExerciseType::decodeExerciseType($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);
        
        // this array contains the indices of the inserted objects
        $res = array();
        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/AddExerciseType.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query 
            if ($result['status']>=200 && $result['status']<=299){
                $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new ExerciseType();
                $obj->setId($queryResult->getInsertId());
            
                array_push($res, $obj);
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST SetExerciseType failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->response->setBody(ExerciseType::encodeExerciseType($res)); 
                $this->_app->stop();
            }
        }
        
        if (count($res)==1){
            $this->_app->response->setBody(ExerciseType::encodeExerciseType($res[0])); 
        }
        else
            $this->_app->response->setBody(ExerciseType::encodeExerciseType($res)); 
    }


    /**
     * Returns all exercise types.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisetype(/) or /exercisetype/exercisetype(/).
     */
    public function getAllExerciseTypes()
    {      
        Logger::Log("starts GET GetAllExerciseType",LogLevel::DEBUG);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAllExerciseTypes.sql", 
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
            Logger::Log("GET GetAllExerciseType failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(ExerciseTypes::encodeExerciseType(new ExerciseType()));
            $this->_app->stop();
        }
    }


    /**
     * Returns an exercise type.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisetype/$etid(/) or /exercisetype/exercisetype/$etid(/).
     *
     * @param string $etid The id of the exercise type that should be returned.
     */
    public function getExerciseType($etid)
    {        
        Logger::Log("starts GET GetExerciseType",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($etid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetExerciseType.sql", 
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
            Logger::Log("GET GetExerciseType failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(ExerciseType::encodeExerciseType(new ExerciseType()));
            $this->_app->stop();
        }
    }

}
?>