<?php
/**
 * @file DBSelectedSubmission.php contains the DBSelectedSubmission class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBSelectedSubmission/SelectedSubmissionSample.json
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
 */
class DBSelectedSubmission
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
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBSelectedSubmission::$_prefix = $value;
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

        // PUT EditSelectedSubmission
        $this->_app->put('/' . $this->getPrefix() . 
                        '/leader/:userid/exercise/:eid(/)',
                        array($this,'editSelectedSubmission'));
        
        // DELETE DeleteSelectedSubmission
        $this->_app->delete('/' . $this->getPrefix() . 
                            '/leader/:userid/exercise/:eid(/)',
                            array($this,'deleteSelectedSubmission'));
        
        // POST AddSelectedSubmission
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'addSelectedSubmission'));  
                         
        // GET GetExerciseSelected
        $this->_app->get('/' . $this->getPrefix() . '/exercise/:eid(/)',
                        array($this,'getExerciseSelected'));
                        
        // GET GetSheetSelected
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid(/)',
                        array($this,'getSheetSelected'));
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
        
            // run Slim
            $this->_app->run();
        }
    }


    /**
     * Sets the submission that should be marked.
     *
     * Called when this component receives an HTTP PUT request to
     * /selectedsubmission/leader/$userid/exercise/$eid(/).
     * The request body should contain a JSON object representing the new selectedSubmission.
     *
     * @param string $userid The id or the user which leads the group.
     * @param int $eid The id of the exercise.
     */
    public function editSelectedSubmission($userid, $eid)
    {
        Logger::Log("starts PUT EditSelectedSubmission",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid),
                            ctype_digit($eid));

        // decode the received selected submission data, as an object
        $insert = SelectedSubmission::decodeSelectedSubmission($this->_app->request->getBody());

        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
 
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                    "Sql/EditSelectedSubmission.sql", 
                                    array("userid" => $userid,"eid" => $eid, "values" => $data));                   
 
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditSelectedSubmission failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }


    /**
     * Unsets the submission that should be marked.
     *
     * Called when this component receives an HTTP DELETE request to
     * /selectedsubmission/leader/$userid/exercise/$eid(/).
     *
     * @param string $userid The id or the user which leads the group.
     * @param int $eid The id of the exercise.
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
        
            $this->_app->response->setStatus(201);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteSelectedSubmission failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 452);
            $this->_app->stop();
        }
    }


    /**
     * Sets the submission that should be marked.
     *
     * Called when this component receives an HTTP POST request to
     * /selectedsubmission/leader/$userid/exercise/$eid(/).
     * The request body should contain a JSON object representing the new selectedSubmission.
     */
    public function addSelectedSubmission()
    {
        Logger::Log("starts POST AddSelectedSubmission",LogLevel::DEBUG);
        
        // decode the received selected submission data, as an object
        $insert = SelectedSubmission::decodeSelectedSubmission($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/AddSelectedSubmission.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query 
            if ($result['status']>=200 && $result['status']<=299){

                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST AddSelectedSubmission failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }


    /**
     * Returns the submission that should be marked.
     *
     * Called when this component receives an HTTP GET request to
     * /selectedsubmission/exercise/$eid(/).
     *
     * @param int $eid The id of the exercise.
     */
    public function getExerciseSelected($eid)
    {    
        Logger::Log("starts GET GetExerciseSelected",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($eid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetExerciseSelected.sql", 
                                        array("eid" => $eid));
        
        // checks the correctness of the query                                     
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of selected entry's by using a defined list of 
            // its attributes
            $selected = DBJson::getResultObjectsByAttributes($data, 
                                    SelectedSubmission::getDBPrimaryKey(), 
                                    SelectedSubmission::getDBConvert());          
                
            $this->_app->response->setBody(SelectedSubmission::encodeSelectedSubmission($selected));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetExerciseSelected failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(SelectedSubmission::encodeSelectedSubmission(new SelectedSubmission()));
            $this->_app->stop();
        }
    } 


    /**
     * Returns all exercises that should be marked to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /selectedsubmission/exercisesheet/$esid(/).
     *
     * @param int $eid The id of the exercise sheet.
     */
    public function getSheetSelected($esid)
    {  
        Logger::Log("starts GET GetSheetSelected",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSheetSelected.sql", 
                                        array("esid" => $esid));
        
        // checks the correctness of the query                                     
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of selected entry's by using a defined list of 
            // its attributes
            $selected = DBJson::getResultObjectsByAttributes($data, 
                                    SelectedSubmission::getDBPrimaryKey(), 
                                    SelectedSubmission::getDBConvert());          
                
            $this->_app->response->setBody(SelectedSubmission::encodeSelectedSubmission($selected));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSheetSelected failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(SelectedSubmission::encodeSelectedSubmission(new SelectedSubmission()));
            $this->_app->stop();
        }
    }
}
?>