<?php
/**
 * @file DBSubmission.php contains the DBSubmission class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt 
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
$com = new CConfig(DBSubmission::getPrefix());

// runs the DBSubmission
if (!$com->used())
    new DBSubmission($com->loadConfig());  
    
/**
 * A class, to abstract the "Submission" table from database
 */
class DBSubmission
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
    private static $_prefix = "submission";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBSubmission::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBSubmission::$_prefix = $value;
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

        // PUT EditSubmission
        $this->_app->put('/' . $this->getPrefix() . 
                        '(/submission)/:suid(/)',
                        array($this,'editSubmission'));
        
        // DELETE DeleteSubmission
        $this->_app->delete('/' . $this->getPrefix() . 
                            '(/submission)/:suid(/)',
                            array($this,'deleteSubmission'));
        
        // POST SetSubmission
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'setSubmission'));  
        
        // GET GetExerciseSubmissions  
        $this->_app->get('/' . $this->getPrefix() . '/exercise/:eid(/)',
                        array($this,'getExerciseSubmissions'));
                        
        // GET GetUserExerciseSubmissions
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid/exercise/:eid(/)',
                        array($this,'getUserExerciseSubmissions'));
        
        // GET GetGroupSubmissions
        $this->_app->get('/' . $this->getPrefix() . '/group/user/:userid/exercisesheet/:esid(/)',
                        array($this,'getGroupSubmissions'));
                        
        // GET GetGroupSelectedSubmissions
        $this->_app->get('/' . $this->getPrefix() . '/group/user/:userid/exercisesheet/:esid/selected(/)',
                        array($this,'getGroupSelectedSubmissions')); 
                        
        // GET GetGroupExerciseSubmissions
        $this->_app->get('/' . $this->getPrefix() . 
                        '/group/user/:userid/exercise/:eid(/)',
                        array($this,'getGroupExerciseSubmissions'));  
                        
        // GET GetGroupSelectedExerciseSubmissions
        $this->_app->get('/' . $this->getPrefix() . 
                        '/group/user/:userid/exercise/:eid/selected(/)',
                        array($this,'getGroupSelectedExerciseSubmissions'));
                        
        // GET GetSelectedSheetSubmissions  
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid/selected(/)',
                        array($this,'getSelectedSheetSubmissions'));                 
                        
        // GET GetAllSubmissions  
        $this->_app->get('/' . $this->getPrefix() . '(/submission)(/)',
                        array($this,'getAllSubmissions'));
                        
        // GET GetSelectedExerciseSubmissions  
        $this->_app->get('/' . $this->getPrefix() . '/exercise/:eid/selected(/)',
                        array($this,'getSelectedExerciseSubmissions')); 
                        
        // GET GetSheetSubmissions  
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid(/)',
                        array($this,'getSheetSubmissions')); 
                        
         // GET GetSubmission 
        $this->_app->get('/' . $this->getPrefix() . '(/submission)/:suid(/)',
                        array($this,'getSubmission'));  
              
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){

            // run Slim
            $this->_app->run();
        }
    }


    /**
     * Returns all submissions (including overwritten ones) of a given exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/exercise/$eid(/).
     *
     * @param string $eid The id of the exercise.
     */
    public function getExerciseSubmissions($eid)
    { 
        Logger::Log("starts GET GetExerciseSubmissions",LogLevel::DEBUG);
    
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($eid));
                       
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetExerciseSubmissions.sql", 
                                        array("eid" => $eid));
    
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of submissions by using a defined list of 
            // its attributes
            $submissions = DBJson::getObjectsByAttributes($data, 
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert());  
                                    
            // concatenates the submissions and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            Submission::getDBConvert()['S_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Submission::encodeSubmission($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetExerciseSubmissions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        } 
    }

    /**
     * Edits a submission.
     *
     * Called when this component receives an HTTP PUT request to
     * /submission/submission/$suid(/) or /submission/$suid(/).
     *
     * @param string $suid The id of the submission which is being updated.
     */
    public function editSubmission($suid)
    {
        Logger::Log("starts PUT EditSubmission",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($suid));
                            
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
                                    "Sql/EditSubmission.sql", 
                                    array("suid" => $suid));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditSubmission failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * Deletes a submission.
     *
     * Called when this component receives an HTTP DELETE request to
     * /submission/submission/$suid(/) or /submission/$suid(/).
     *
     * @param string $suid The id of the submission which is being deleted.
     */
    public function deleteSubmission($suid)
    {
        Logger::Log("starts DELETE DeleteSubmission",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($suid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteSubmission.sql", 
                                        array("suid" => $suid));    
        
        // checks the correctness of the query                          
        if ($result['status']>=200 && $result['status']<=299){
        
            $this->_app->response->setStatus(252);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteSubmission failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 452);
            $this->_app->stop();
        }
    }
    
    /**
     * Creates a submission and then returns it.
     *
     * Called when this component receives an HTTP POST request to
     * /submission(/).
     * The request body should contain a JSON object representing the new submission.
     */
    public function setSubmission()
    {
        Logger::Log("starts POST SetSubmission",LogLevel::DEBUG);
        
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
                                            "Sql/SetSubmission.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query 
            if ($result['status']>=200 && $result['status']<=299){
                 $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new Submission();
                $obj->setId($queryResult->getInsertId());
            
                $this->_app->response->setBody(Submission::encodeSubmission($obj)); 
                
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST SetSubmission failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }

    /**
     * Returns all submissions.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/submission(/) or /submission(/).
     */
    public function getAllSubmissions()
    {    
        Logger::Log("starts GET GetAllSubmissions",LogLevel::DEBUG);

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAllSubmissions.sql", 
                                        array());
                                        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                         
            // generates an assoc array of submissions by using a defined list of 
            // its attributes
           $submissions = DBJson::getObjectsByAttributes($data, 
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert(),
                                    '2'); 
                       
            // concatenates the submissions and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            Submission::getDBConvert()['S_file'] ,
                            $files,
                            File::getDBPrimaryKey(),
                            '',
                            '2');
                            
            // to reindex
            $res = array_values($res); 
    
            $this->_app->response->setBody(Submission::encodeSubmission($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllSubmissions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    }


    /**
     * Returns all submissions (including overwritten ones) of a given group
     * of a certain exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/group/user/$userid/exercisesheet/$esid(/).
     *
     * @param string $userid The id or the username of the user.
     * @param int $esid The id of the exercise sheet.
     */
    public function getGroupSubmissions($userid, $esid)
    {         
        Logger::Log("starts GET GetGroupSubmissions",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid),
                            ctype_digit($esid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetGroupSubmissions.sql", 
                                        array("userid" => $userid,"esid" => $esid ));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of submissions by using a defined list of 
            // its attributes
            $submissions = DBJson::getObjectsByAttributes($data, 
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert());  
                                    
            // concatenates the submissions and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            Submission::getDBConvert()['S_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Submission::encodeSubmission($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetGroupSubmissions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    }


    /**
     * Returns the submissions of a given group of a certain exercise sheet
     * which should be marked.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/group/user/$userid/exercisesheet/$esid/selected(/).
     *
     * @param string $userid The id or the username of the user.
     * @param int $esid The id of the exercise sheet.
     */
    public function getGroupSelectedSubmissions($userid, $esid)
    {         
        Logger::Log("starts GET GetGroupSelectedSubmissions",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid),
                            ctype_digit($esid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetGroupSelectedSubmissions.sql", 
                                        array("userid" => $userid,"esid" => $esid ));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of submissions by using a defined list of 
            // its attributes
            $submissions = DBJson::getObjectsByAttributes($data, 
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert());  
                                    
            // concatenates the submissions and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            Submission::getDBConvert()['S_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Submission::encodeSubmission($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetGroupSelectedSubmissions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    }


    /**
     * Returns all submissions (including overwritten ones) of a given group
     * of a certain exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/group/user/$userid/exercise/$eid(/).
     *
     * @param string $userid The id or the username of the user.
     * @param int $eid The id of the exercise.
     */
    public function getGroupExerciseSubmissions($userid, $eid)
    {         
        Logger::Log("starts GET GetGroupExerciseSubmissions",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid),
                            ctype_digit($eid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetGroupExerciseSubmissions.sql", 
                                        array("userid" => $userid,"eid" => $eid ));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of submissions by using a defined list of 
            // its attributes
            $submissions = DBJson::getObjectsByAttributes($data, 
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert());  
                                    
            // concatenates the submissions and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            Submission::getDBConvert()['S_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Submission::encodeSubmission($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetGroupExerciseSubmissions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    }


    /**
     * Returns the submissions of a given group of a certain exercise
     * which should be marked.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/group/user/$userid/exercise/$eid/selected(/).
     *
     * @param string $userid The id or the username of the user.
     * @param int $eid The id of the exercise.
     */
    public function getGroupSelectedExerciseSubmissions($userid, $eid)
    {         
        Logger::Log("starts GET GetGroupSelectedExerciseSubmissions",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($userid),
                            ctype_digit($eid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetGroupSelectedExerciseSubmissions.sql", 
                                        array("userid" => $userid,"eid" => $eid ));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of submissions by using a defined list of 
            // its attributes
            $submissions = DBJson::getObjectsByAttributes($data, 
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert());  
                                    
            // concatenates the submissions and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            Submission::getDBConvert()['S_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Submission::encodeSubmission($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetGroupSelectedExerciseSubmissions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    } 


    /**
     * Returns a submission.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/submission/$suid(/) or /submission/$suid(/).
     *
     * @param int $suid The id of the submission.
     */
    public function getSubmission($suid)
    { 
        Logger::Log("starts GET GetSubmission",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($suid));
           
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSubmission.sql", 
                                        array("suid" => $suid));

        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                        
            // generates an assoc array of submissions by using a defined list of 
            // its attributes
            $submissions = DBJson::getObjectsByAttributes($data, 
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert());  
                           
            // concatenates the submissions and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            Submission::getDBConvert()['S_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Submission::encodeSubmission($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSubmission failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    } 


    /**
     * Returns all submissions (including overwritten ones) of a given
     * exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/exercisesheet/$esid(/).
     *
     * @param int $esid The id of the exercise sheet.
     */
    public function getSheetSubmissions($esid)
    { 
        Logger::Log("starts GET GetSheetSubmissions",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid));
                       
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSheetSubmissions.sql", 
                                        array("esid" => $esid));
 
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of submissions by using a defined list of 
            // its attributes
            $submissions = DBJson::getObjectsByAttributes($data, 
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert());  
                                    
            // concatenates the submissions and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            Submission::getDBConvert()['S_file'],
                            $files,
                            File::getDBPrimaryKey());
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Submission::encodeSubmission($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSheetSubmissions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    } 


    /**
     * Returns all submissions of a given exercise sheet which
     * should be marked.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/exercisesheet/$esid/selected(/).
     *
     * @param int $esid The id of the exercise sheet.
     */
    public function getSelectedSheetSubmissions($esid)
    { 
        Logger::Log("starts GET GetSelectedSheetSubmissions",LogLevel::DEBUG);

        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSelectedSheetSubmissions.sql", 
                                        array("esid" => $esid));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of submissions by using a defined list of 
            // its attributes
            $submissions = DBJson::getObjectsByAttributes($data, 
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert());  
                                    
            // concatenates the submissions and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            Submission::getDBConvert()['S_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Submission::encodeSubmission($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSelectedSheetSubmissions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    } 


    /**
     * Returns all submissions of a given exercise which
     * should be marked.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/exercise/$eid/selected(/).
     *
     * @param string $eid The id of the exercise.
     */
    public function getSelectedExerciseSubmissions($eid)
    { 
        Logger::Log("starts GET GetSelectedExerciseSubmissions",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($eid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSelectedExerciseSubmissions.sql", 
                                        array("eid" => $eid));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of submissions by using a defined list of 
            // its attributes
            $submissions = DBJson::getObjectsByAttributes($data, 
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert());  
                                    
            // concatenates the submissions and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            Submission::getDBConvert()['S_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Submission::encodeSubmission($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSelectedExerciseSubmissions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    } 

    /**
     * Returns all submissions (including overwritten ones) of a given user 
     * of a certain exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/exercise/$eid/selected(/).
     *
     * @param string $userid The id or the username of the user.
     * @param string $eid The id of the exercise.
     */
    public function getUserExerciseSubmissions($userid,$eid)
    { 
        Logger::Log("starts GET GetUserExerciseSubmissions",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($eid), 
                            ctype_digit($userid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetUserExerciseSubmissions.sql", 
                                        array("userid" => $userid,"eid" => $eid));
        
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();

            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
                                            
            // generates an assoc array of submissions by using a defined list of 
            // its attributes
            $submissions = DBJson::getObjectsByAttributes($data, 
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert());  
                                    
            // concatenates the submissions and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            Submission::getDBConvert()['S_file'] ,
                            $files,
                            File::getDBPrimaryKey());
                            
            // to reindex
            $res = array_values($res); 
            
            $this->_app->response->setBody(Submission::encodeSubmission($res));
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetUserExerciseSubmissions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    }     
}
?>