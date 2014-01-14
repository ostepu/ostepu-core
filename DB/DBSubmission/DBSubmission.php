<?php
/**
 * @file DBSubmission.php contains the DBSubmission class
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
 *
 * @author Till Uhlig
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

        // PUT EditSubmission
        $this->_app->put('/' . $this->getPrefix() . 
                        '/submission/:suid',
                        array($this,'editSubmission'));
        
        // DELETE DeleteSubmission
        $this->_app->delete('/' . $this->getPrefix() . 
                            '/submission/:suid',
                            array($this,'deleteSubmission'));
        
        // POST SetSubmission
        $this->_app->post('/' . $this->getPrefix(),
                         array($this,'setSubmission'));  
        
        // GET GetUserExerciseSubmissions
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid/exercise/:eid',
                        array($this,'getUserExerciseSubmissions'));
        
        // GET GetGroupSubmissions
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid/exercisesheet/:esid',
                        array($this,'getGroupSubmissions'));
                        
        // GET GetGroupSelectedSubmissions
        $this->_app->get('/' . $this->getPrefix() . '/user/:userid/exercisesheet/:esid',
                        array($this,'getGroupSelectedSubmissions')); 
                        
        // GET GetGroupExerciseSubmissions
        $this->_app->get('/' . $this->getPrefix() . 
                        '/user/:userid/exercise/:eid',
                        array($this,'getGroupExerciseSubmissions'));  
                        
        // GET GetGroupSelectedExerciseSubmissions
        $this->_app->get('/' . $this->getPrefix() . 
                        '/user/:userid/exercise/:eid',
                        array($this,'getGroupSelectedExerciseSubmissions'));
                        
        // GET GetSubmission 
        $this->_app->get('/' . $this->getPrefix() . '/submission/:suid',
                        array($this,'getSubmission '));  
                        
        // GET GetExerciseSubmissions  
        $this->_app->get('/' . $this->getPrefix() . 'exercise/:eid',
                        array($this,'getExerciseSubmissions  ')); 
                        
        // GET GetAllSubmissions  
        $this->_app->get('/' . $this->getPrefix() . '/submission',
                        array($this,'getAllSubmissions  '));
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
        
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * PUT EditSubmission
     *
     * @param int $suid a database Submission identifier
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
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * DELETE DeleteSubmission
     *
     * @param int $suid a database submission identifier
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
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteSubmission failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
    
    /**
     * POST SetSubmission
     */
    public function SetSubmission()
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
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * GET GetAllSubmissions
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
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllSubmissions failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetGroupSubmissions
     *
     * @param int $userid a database user identifier
     * @param int $esid a database exericse sheet identifier
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
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetGroupSubmissions failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetGroupSelectedSubmissions
     *
     * @param int $esid a database exericse sheet identifier
     * @param int $userid a database user identifier
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
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetGroupSelectedSubmissions failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    }
    
   /**
     * GET GetGroupExerciseSubmissions
     *
     * @param int $userid a database user identifier
     * @param int $eid a database exercise identifier
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
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetGroupExerciseSubmissions failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetGroupSelectedExerciseSubmissions
     *
     * @param int $userid a database user identifier
     * @param int $eid a database exercise identifier
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
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetGroupSelectedExerciseSubmissions failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetExerciseSubmissions
     *
     * @param int $eid a database exercise identifier
     */
    public function getExerciseSubmissions ($eid)
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
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetExerciseSubmissions failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    }    
    
    /**
     * GET GetSubmission
     *
     * @param int $suid a database submission identifier
     */
    public function getSubmission ($suid)
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
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSubmission failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
        }
    } 

}
?>