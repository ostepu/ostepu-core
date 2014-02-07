<?php
/**
 * @file DBMarking.php contains the DBMarking class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt 
 * @example DB/DBMarking/MarkingSample.json
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
$com = new CConfig(DBMarking::getPrefix());

// runs the DBMarking
if (!$com->used())
    new DBMarking($com->loadConfig());  
    
/**
 * A class, to abstract the "Marking" table from database
 */
class DBMarking
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
    private static $_prefix = "marking";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBMarking::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBMarking::$_prefix = $value;
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

        // PUT EditMarking
        $this->_app->put('/' . $this->getPrefix() . 
                        '(/marking)/:mid(/)',
                        array($this,'editMarking'));
        
        // DELETE DeleteMarking
        $this->_app->delete('/' . $this->getPrefix() . 
                            '(/marking)/:mid(/)',
                            array($this,'deleteMarking'));
        
        // POST AddMarking
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'addMarking'));  
        
        // GET GetMarking
        $this->_app->get('/' . $this->getPrefix() . '(/marking)/:mid(/)',
                        array($this,'getMarking'));
        
        // GET GetSubmissionMarking
        $this->_app->get('/' . $this->getPrefix() . '/submission/:suid(/)',
                        array($this,'getSubmissionMarking'));
                        
        // GET GetAllMarkings
        $this->_app->get('/' . $this->getPrefix() . '(/marking)(/)',
                        array($this,'getAllMarkings')); 
                        
        // GET GetExerciseMarkings
        $this->_app->get('/' . $this->getPrefix() . 
                        '/exercise/:eid(/)',
                        array($this,'getExerciseMarkings'));  
                        
        // GET GetSheetMarkings
        $this->_app->get('/' . $this->getPrefix() . 
                        '/exercisesheet/:esid(/)',
                        array($this,'getSheetMarkings'));
                        
        // GET GetUserGroupMarkings
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid/user/:userid(/)',
                        array($this,'getUserGroupMarkings'));  
                        
        // GET GetTutorSheetMarkings 
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid/tutor/:userid(/)',
                        array($this,'getTutorSheetMarkings'));
                        
        // GET GetTutorExerciseMarkings  
        $this->_app->get('/' . $this->getPrefix() . '/exercise/:eid/tutor/:userid(/)',
                        array($this,'getTutorExerciseMarkings'));  
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
        
            // run Slim
            $this->_app->run();
        }
    }


    /**
     * Edits a marking.
     *
     * Called when this component receives an HTTP PUT request to
     * /marking/$mid(/) or /marking/marking/$mid(/).
     * The request body should contain a JSON object representing the marking's new
     * attributes.
     *
     * @param int $mid The id of the marking that is being updated.
     */
    public function editMarking($mid)
    {
        Logger::Log("starts PUT EditMarking",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($mid));
                            
        // decode the received marking data, as an object
        $insert = Marking::decodeMarking($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                    "Sql/EditMarking.sql", 
                                    array("mid" => $mid, "values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditMarking failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }


    /**
     * Deletes a marking.
     *
     * Called when this component receives an HTTP DELETE request to
     * /marking/$mid(/) or /marking/marking/$mid(/).
     *
     * @param int $mid The id of the marking that is being deleted.
     */
    public function deleteMarking($mid)
    {
        Logger::Log("starts DELETE DeleteMarking",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($mid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteMarking.sql", 
                                        array("mid" => $mid));    
        
        // checks the correctness of the query                          
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus(201);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteMarking failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 452);
            $this->_app->stop();
        }
    }


    /**
     * Adds a marking.
     *
     * Called when this component receives an HTTP POST request to
     * /marking(/).
     * The request body should contain a JSON object representing the 
     * marking's attributes.
     */
    public function addMarking()
    {
        Logger::Log("starts OST AddMarking",LogLevel::DEBUG);
        
        // decode the received marking data, as an object
        $insert = Marking::decodeMarking($this->_app->request->getBody());
        
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
                                            "Sql/AddMarking.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query 
            if ($result['status']>=200 && $result['status']<=299){
                 $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new Marking();
                $obj->setId($queryResult->getInsertId());
            
                array_push($res, $obj);
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST AddMarking failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->response->setBody(Marking::encodeMarking($res)); 
                $this->_app->stop();
            }
        }
        
        if (count($res)==1){
            $this->_app->response->setBody(Marking::encodeMarking($res[0])); 
        }
        else
            $this->_app->response->setBody(Marking::encodeMarking($res)); 
    }


    /**
     * Returns all markings.
     *
     * Called when this component receives an HTTP GET request to
     * /marking(/) or /marking/marking(/).
     */
    public function getAllMarkings()
    {      
        Logger::Log("starts GET GetAllMarkings",LogLevel::DEBUG);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAllMarkings.sql", 
                                        array());
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299 && $result['numRows']>0){ 
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of a submission by using a defined 
            // list of its attributes
            $submissions = DBJson::getObjectsByAttributes($data,
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert(), 
                                    '2');
                                    
            // sets the selectedForGroup attribute
            foreach ($submissions as &$submission){
                if (!isset($submission['selectedForGroup']) || $submission['selectedForGroup']==null){
                    if (!isset($submission['id']) || !isset($submission['selectedForGroup'])){
                        $submission['selectedForGroup'] = (string) 0;
                    } elseif ($submission['id'] == $submission['selectedForGroup']) {
                        $submission['selectedForGroup'] = (string) 1;
                    } else
                        $submission['selectedForGroup'] = (string) 0;
                }
                else
                    $submission['selectedForGroup'] = (string) 0;
            } 
                    
            // generates an assoc array of markings by using a defined list of 
            // its attributes
            $markings = DBJson::getObjectsByAttributes($data, 
                                    Marking::getDBPrimaryKey(), 
                                    Marking::getDBConvert());  
                                    
            // concatenates the markings and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $markings,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // concatenates the markings and the associated submissions
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_submission'] ,
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            '2');   
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Marking::encodeMarking($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllMarkings failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Marking::encodeMarking(new Marking()));
            $this->_app->stop();
        }
    }


    /**
     * Returns a marking.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/$mid(/) or /marking/marking/$mid(/).
     *
     * @param int $mid The id of the marking that should be returned.
     */
    public function getMarking($mid)
    {    
        Logger::Log("starts GET GetMarking",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($mid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetMarking.sql", 
                                        array("mid" => $mid));
 
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299 && $result['numRows']>0){ 
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of a file by using a defined list of 
            // its attributes
            $file = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of a submission by using a defined 
            // list of its attributes
            $submissions = DBJson::getObjectsByAttributes($data,
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert(), 
                                    '2');                           
               
            // sets the selectedForGroup attribute
            foreach ($submissions as &$submission){
                if (!isset($submission['selectedForGroup']) || $submission['selectedForGroup']==null){
                    if (!isset($submission['id']) || !isset($submission['selectedForGroup'])){
                        $submission['selectedForGroup'] = (string) 0;
                    } elseif ($submission['id'] == $submission['selectedForGroup']) {
                        $submission['selectedForGroup'] = (string) 1;
                    } else
                        $submission['selectedForGroup'] = (string) 0;
                }
                else
                    $submission['selectedForGroup'] = (string) 0;
            }

            // generates an assoc array of a marking by using a defined list of 
            // its attributes
            $marking = DBJson::getObjectsByAttributes($data, 
                                    Marking::getDBPrimaryKey(), 
                                    Marking::getDBConvert());  
                 
            // concatenates the markings and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $marking,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_file'] ,
                            $file,
                            File::getDBPrimaryKey());
            
            // concatenates the markings and the associated submissions
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_submission'] ,
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            '2');  
                   
            // to reindex
            $res = array_values($res);
            
            // only one object as result
            if (count($res)>0)
                $res = $res[0]; 
                
            $this->_app->response->setBody(Marking::encodeMarking($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetMarking failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Marking::encodeMarking(new Marking()));
            $this->_app->stop();
        }  
    }


    /**
     * Returns a marking to a given submission.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/submission/$suid(/).
     *
     * @param int $suid The id of the submission.
     */
    public function getSubmissionMarking($suid)
    {    
        Logger::Log("starts GET GetSubmissionMarking",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($suid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSubmissionMarking.sql", 
                                        array("suid" => $suid));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299 && $result['numRows']>0){ 
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of a file by using a defined list of 
            // its attributes
            $file = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of a submission by using a defined 
            // list of its attributes
            $submissions = DBJson::getObjectsByAttributes($data,
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert(), 
                                    '2');
                                    
            // sets the selectedForGroup attribute
            foreach ($submissions as &$submission){
                if (!isset($submission['selectedForGroup']) || $submission['selectedForGroup']==null){
                    if (!isset($submission['id']) || !isset($submission['selectedForGroup'])){
                        $submission['selectedForGroup'] = (string) 0;
                    } elseif ($submission['id'] == $submission['selectedForGroup']) {
                        $submission['selectedForGroup'] = (string) 1;
                    } else
                        $submission['selectedForGroup'] = (string) 0;
                }
                else
                    $submission['selectedForGroup'] = (string) 0;
            }
            
            // generates an assoc array of a marking by using a defined list of 
            // its attributes
            $marking = DBJson::getObjectsByAttributes($data, 
                                    Marking::getDBPrimaryKey(), 
                                    Marking::getDBConvert());  
                                    
            // concatenates the markings and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $marking,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_file'] ,
                            $file,
                            File::getDBPrimaryKey());
                            
            // concatenates the markings and the associated submissions
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_submission'] ,
                            $submissions,
                            Submission::getDBPrimaryKey());  
                            
            // to reindex
            $res = array_values($res);
            
            // only one object as result
            if (count($res)>0)
                $res = $res[0]; 
                
            $this->_app->response->setBody(Marking::encodeMarking($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSubmissionMarking failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Marking::encodeMarking(new Marking()));
            $this->_app->stop();
        }   
    }


    /**
     * Returns all markings which belong to a given exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/exercise/$eid(/).
     *
     * @param int $eid The id of the exercise.
     */
    public function getExerciseMarkings($eid)
    {   
        Logger::Log("starts GET GetExerciseMarkings",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($eid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetExerciseMarkings.sql", 
                                        array('eid' => $eid));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299 && $result['numRows']>0){ 
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of a submission by using a defined 
            // list of its attributes
            $submissions = DBJson::getObjectsByAttributes($data,
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert(), 
                                    '2');
                                    
            // sets the selectedForGroup attribute
            foreach ($submissions as &$submission){
                if (!isset($submission['selectedForGroup']) || $submission['selectedForGroup']==null){
                    if (!isset($submission['id']) || !isset($submission['selectedForGroup'])){
                        $submission['selectedForGroup'] = (string) 0;
                    } elseif ($submission['id'] == $submission['selectedForGroup']) {
                        $submission['selectedForGroup'] = (string) 1;
                    } else
                        $submission['selectedForGroup'] = (string) 0;
                }
                else
                    $submission['selectedForGroup'] = (string) 0;
            }
            
            // generates an assoc array of markings by using a defined list of 
            // its attributes
            $markings = DBJson::getObjectsByAttributes($data, 
                                    Marking::getDBPrimaryKey(), 
                                    Marking::getDBConvert());  
                                    
            // concatenates the markings and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $markings,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // concatenates the markings and the associated submissions
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_submission'] ,
                            $submissions,
                            Submission::getDBPrimaryKey());  
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Marking::encodeMarking($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetExerciseMarkings failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Marking::encodeMarking(new Marking()));
            $this->_app->stop();
        }
    }


    /**
     * Returns all markings which belong to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/exercisesheet/$esid(/).
     *
     * @param int $esid The id of the exercise sheet.
     */
    public function getSheetMarkings($esid)
    {     
        Logger::Log("starts GET GetSheetMarkings",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSheetMarkings.sql", 
                                        array('esid' => $esid));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299 && $result['numRows']>0){ 
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of a submission by using a defined 
            // list of its attributes
            $submissions = DBJson::getObjectsByAttributes($data,
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert(), 
                                    '2');
                                    
            // sets the selectedForGroup attribute
            foreach ($submissions as &$submission){
                if (!isset($submission['selectedForGroup']) || $submission['selectedForGroup']==null){
                    if (!isset($submission['id']) || !isset($submission['selectedForGroup'])){
                        $submission['selectedForGroup'] = (string) 0;
                    } elseif ($submission['id'] == $submission['selectedForGroup']) {
                        $submission['selectedForGroup'] = (string) 1;
                    } else
                        $submission['selectedForGroup'] = (string) 0;
                }
                else
                    $submission['selectedForGroup'] = (string) 0;
            }
            
            // generates an assoc array of markings by using a defined list of 
            // its attributes
            $markings = DBJson::getObjectsByAttributes($data, 
                                    Marking::getDBPrimaryKey(), 
                                    Marking::getDBConvert());  
                                    
            // concatenates the markings and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $markings,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // concatenates the markings and the associated submissions
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_submission'] ,
                            $submissions,
                            Submission::getDBPrimaryKey());  
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Marking::encodeMarking($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSheetMarkings failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Marking::encodeMarking(new Marking()));
            $this->_app->stop();
        }  
    }


    /**
     * Returns all markings of a group regarding a specific exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/exercisesheet/$esid/user/$userid(/).
     *
     * @param int $esid The id of the exercise sheet.
     * @param int $userid The id of the user whose group the marking belongs to.
     */
    public function getUserGroupMarkings($esid,$userid)
    {     
        Logger::Log("starts GET GetUserGroupMarkings",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid),
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetUserGroupMarkings.sql", 
                                        array('esid' => $esid,
                                            'userid' => $userid));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299 && $result['numRows']>0){ 
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
            // generates an assoc array of a submission by using a defined 
            // list of its attributes
            $submissions = DBJson::getObjectsByAttributes($data,
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert(), 
                                    '2');
                                    
            // sets the selectedForGroup attribute
            foreach ($submissions as &$submission){
                if (!isset($submission['selectedForGroup']) || $submission['selectedForGroup']==null){
                    if (!isset($submission['id']) || !isset($submission['selectedForGroup'])){
                        $submission['selectedForGroup'] = (string) 0;
                    } elseif ($submission['id'] == $submission['selectedForGroup']) {
                        $submission['selectedForGroup'] = (string) 1;
                    } else
                        $submission['selectedForGroup'] = (string) 0;
                }
                else
                    $submission['selectedForGroup'] = (string) 0;
            }
            
            // generates an assoc array of markings by using a defined list of 
            // its attributes
            $markings = DBJson::getObjectsByAttributes($data, 
                                    Marking::getDBPrimaryKey(), 
                                    Marking::getDBConvert());  
                                    
            // concatenates the markings and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $markings,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // concatenates the markings and the associated submissions
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_submission'] ,
                            $submissions,
                            Submission::getDBPrimaryKey());  
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Marking::encodeMarking($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetUserGroupMarkings failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Marking::encodeMarking(new Marking()));
            $this->_app->stop();
        }    
    }


    /**
     * Returns all markings created by a given tutor regarding 
     * a specific exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/exercisesheet/$esid/tutor/$userid(/).
     *
     * @param int $esid The id of the exercise sheet.
     * @param int $userid The userid of the tutor that created the markings 
     * which should be returned.
     */
    public function getTutorSheetMarkings($esid,$userid)
    {     
        Logger::Log("starts GET GetTutorSheetMarkings",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid),
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetTutorSheetMarkings.sql", 
                                        array('esid' => $esid,
                                            'userid' => $userid));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299 && $result['numRows']>0){ 
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
            // generates an assoc array of a submission by using a defined 
            // list of its attributes
            $submissions = DBJson::getObjectsByAttributes($data,
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert(), 
                                    '2');
                                    
            // sets the selectedForGroup attribute
            foreach ($submissions as &$submission){
                if (!isset($submission['selectedForGroup']) || $submission['selectedForGroup']==null){
                    if (!isset($submission['id']) || !isset($submission['selectedForGroup'])){
                        $submission['selectedForGroup'] = (string) 0;
                    } elseif ($submission['id'] == $submission['selectedForGroup']) {
                        $submission['selectedForGroup'] = (string) 1;
                    } else
                        $submission['selectedForGroup'] = (string) 0;
                }
                else
                    $submission['selectedForGroup'] = (string) 0;
            }
            
            // generates an assoc array of markings by using a defined list of 
            // its attributes
            $markings = DBJson::getObjectsByAttributes($data, 
                                    Marking::getDBPrimaryKey(), 
                                    Marking::getDBConvert());  
                                    
            // concatenates the markings and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $markings,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // concatenates the markings and the associated submissions
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_submission'] ,
                            $submissions,
                            Submission::getDBPrimaryKey());  
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Marking::encodeMarking($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetTutorSheetMarkings failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Marking::encodeMarking(new Marking()));
            $this->_app->stop();
        }    
    }


    /**
     * Returns all markings created by a given tutor regarding 
     * a specific exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/exercise/$eid/tutor/$userid(/).
     *
     * @param int $eid The id of the exercise.
     * @param int $userid The userid of the tutor that created the markings 
     * which should be returned.
     */
    public function getTutorExerciseMarkings($eid,$userid)
    {     
        Logger::Log("starts GET GetTutorExerciseMarkings",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($eid),
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetTutorExerciseMarkings.sql", 
                                        array('eid' => $eid,
                                            'userid' => $userid));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299 && $result['numRows']>0){ 
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
            // generates an assoc array of a submission by using a defined 
            // list of its attributes
            $submissions = DBJson::getObjectsByAttributes($data,
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert(), 
                                    '2');
                                    
            // sets the selectedForGroup attribute
            foreach ($submissions as &$submission){
                if (!isset($submission['selectedForGroup']) || $submission['selectedForGroup']==null){
                    if (!isset($submission['id']) || !isset($submission['selectedForGroup'])){
                        $submission['selectedForGroup'] = (string) 0;
                    } elseif ($submission['id'] == $submission['selectedForGroup']) {
                        $submission['selectedForGroup'] = (string) 1;
                    } else
                        $submission['selectedForGroup'] = (string) 0;
                }
                else
                    $submission['selectedForGroup'] = (string) 0;
            }
            
            // generates an assoc array of markings by using a defined list of 
            // its attributes
            $markings = DBJson::getObjectsByAttributes($data, 
                                    Marking::getDBPrimaryKey(), 
                                    Marking::getDBConvert());  
                                    
            // concatenates the markings and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $markings,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // concatenates the markings and the associated submissions
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_submission'] ,
                            $submissions,
                            Submission::getDBPrimaryKey());  
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Marking::encodeMarking($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetTutorExerciseMarkings failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Marking::encodeMarking(new Marking()));
            $this->_app->stop();
        }    
    }
}
?>