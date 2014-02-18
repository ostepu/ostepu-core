<?php
/**
 * @file DBSubmission.php contains the DBSubmission class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt 
 * @example DB/DBSubmission/SubmissionSample.json
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
        
        // POST AddSubmission
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'addSubmission'));  
        
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
                        
        // GET GetGroupSelectedCourseSubmissions
        $this->_app->get('/' . $this->getPrefix() . 
                        '/group/user/:userid/course/:courseid/selected(/)',
                        array($this,'getGroupSelectedCourseSubmissions'));
                        
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
                        
         // GET GetCourseSubmissions
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid(/)',
                        array($this,'getCourseSubmissions'));  
              
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
     * @param int $eid The id of the exercise.
     */
    public function getExerciseSubmissions($eid)
    { 
        $this->get("GetExerciseSubmissions",
                "Sql/GetExerciseSubmissions.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "");  
    }


    /**
     * Edits a submission.
     *
     * Called when this component receives an HTTP PUT request to
     * /submission/submission/$suid(/) or /submission/$suid(/).
     * The request body should contain a JSON object representing 
     * submission's new attributes.
     *
     * @param int $suid The id of the submission which is being updated.
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
                                    "Sql/EditSubmission.sql", 
                                    array("suid" => $suid, "values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditSubmission failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
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
     * @param int $suid The id of the submission which is being deleted.
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
        
            $this->_app->response->setStatus(201);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteSubmission failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
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
    public function addSubmission()
    {
        Logger::Log("starts POST AddSubmission",LogLevel::DEBUG);
        
        // decode the received submission data, as an object
        $insert = Submission::decodeSubmission($this->_app->request->getBody());
        
        // always been an array
        $arr = true;
        if (!is_array($insert)){
            $insert = array($insert);
            $arr=false;
        }
        
        // this array contains the indices of the inserted objects
        $res = array();
        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/AddSubmission.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query 
            if ($result['status']>=200 && $result['status']<=299){
                 $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new Submission();
                $obj->setId($queryResult->getInsertId());
            
                array_push($res, $obj);
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST AddSubmission failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
                $this->_app->response->setBody(Submission::encodeSubmission($res)); 
                $this->_app->stop();
            }
        }
        
        if (!$arr && count($res)==1){
            $this->_app->response->setBody(Submission::encodeSubmission($res[0])); 
        }
        else
            $this->_app->response->setBody(Submission::encodeSubmission($res)); 
    }

    public function get($functionName,$sqlFile,$userid,$courseid,$esid,$eid,$suid,$mid,$singleResult=false)
    {
        Logger::Log("starts GET " . $functionName,LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            $userid == "" ? true : ctype_digit($userid), 
                            $courseid == "" ? true : ctype_digit($courseid), 
                            $esid == "" ? true : ctype_digit($esid), 
                            $eid == "" ? true : ctype_digit($eid), 
                            $suid == "" ? true : ctype_digit($suid), 
                            $mid == "" ? true : ctype_digit($mid));
                            
            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        $sqlFile, 
                                        array("userid" => $userid,
                                        'courseid' => $courseid,
                                        'esid' => $esid,
                                        'eid' => $eid,
                                        'suid' => $suid,
                                        'mid' => $mid));
 
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){ 
            $query = Query::decodeQuery($result['content']);
            
            if ($query->getNumRows()>0){
                $res = Submission::ExtractSubmission($query->getResponse(),$singleResult); 
                $this->_app->response->setBody(Submission::encodeSubmission($res));
        
                $this->_app->response->setStatus(200);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
                $this->_app->stop(); 
            }
            else
                $result['status'] = 404;
                
        }
        
            Logger::Log("GET " . $functionName . " failed",LogLevel::ERROR);
            $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Submission::encodeSubmission(new Submission()));
            $this->_app->stop();
    }
    
    /**
     * Returns all submissions.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/submission(/) or /submission(/).
     */
    public function getAllSubmissions()
    {    
        $this->get("GetAllSubmissions",
                "Sql/GetAllSubmissions.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "");
    }


    /**
     * Returns all submissions (including overwritten ones) of a given group
     * of a specific exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/group/user/$userid/exercisesheet/$esid(/).
     *
     * @param int $userid The id of the user.
     * @param int $esid The id of the exercise sheet.
     */
    public function getGroupSubmissions($userid, $esid)
    {       
        $this->get("GetGroupSubmissions",
                "Sql/GetGroupSubmissions.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "");
    }


    /**
     * Returns the submissions of a given group of a specific exercise sheet
     * which should be marked.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/group/user/$userid/exercisesheet/$esid/selected(/).
     *
     * @param int $userid The id of the user.
     * @param int $esid The id of the exercise sheet.
     */
    public function getGroupSelectedSubmissions($userid, $esid)
    {       
        $this->get("GetGroupSelectedSubmissions",
                "Sql/GetGroupSelectedSubmissions.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "");
    }

    
    /**
     * Returns the submissions of a given group of a specific course
     * which should be marked.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/group/user/$userid/course/$courseid/selected(/).
     *
     * @param int $userid The id of the user.
     * @param int $courseid The id of the course.
     */
    public function getGroupSelectedCourseSubmissions($userid, $courseid)
    {       
        $this->get("GetGroupSelectedCourseSubmissions",
                "Sql/GetGroupSelectedCourseSubmissions.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "");
    }
    
    
    /**
     * Returns all submissions (including overwritten ones) of a given group
     * of a specific exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/group/user/$userid/exercise/$eid(/).
     *
     * @param int $userid The id of the user.
     * @param int $eid The id of the exercise.
     */
    public function getGroupExerciseSubmissions($userid, $eid)
    {     
        $this->get("GetGroupExerciseSubmissions",
                "Sql/GetGroupExerciseSubmissions.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "");
    }


    /**
     * Returns the submissions of a given group of a specific exercise
     * which should be marked.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/group/user/$userid/exercise/$eid/selected(/).
     *
     * @param int $userid The id of the user.
     * @param int $eid The id of the exercise.
     */
    public function getGroupSelectedExerciseSubmissions($userid, $eid)
    {      
        $this->get("GetGroupSelectedExerciseSubmissions",
                "Sql/GetGroupSelectedExerciseSubmissions.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "");
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
        $this->get("GetSubmission",
                "Sql/GetSubmission.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "",
                true);
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
        $this->get("GetSheetSubmissions",
                "Sql/GetSheetSubmissions.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "");
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
        $this->get("GetSelectedSheetSubmissions",
                "Sql/GetSelectedSheetSubmissions.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "");
    } 


    /**
     * Returns all submissions of a given exercise which
     * should be marked.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/exercise/$eid/selected(/).
     *
     * @param int $eid The id of the exercise.
     */
    public function getSelectedExerciseSubmissions($eid)
    { 
        $this->get("GetSelectedExerciseSubmissions",
                "Sql/GetSelectedExerciseSubmissions.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "");
    } 

    /**
     * Returns all submissions (including overwritten ones) of a given user 
     * of a specific exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/exercise/$eid/selected(/).
     *
     * @param int $userid The id of the user.
     * @param int $eid The id of the exercise.
     */
    public function getUserExerciseSubmissions($userid,$eid)
    { 
        $this->get("GetUserExerciseSubmissions",
                "Sql/GetUserExerciseSubmissions.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "");
    }    

    /**
     * Returns all course submissions (including overwritten ones) of a given course 
     * of a specific exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /submission/course/$courseid(/).
     *
     * @param int $courseid The id of the course.
     */
    public function getCourseSubmissions($courseid)
    { 
        $this->get("GetCourseSubmissions",
                "Sql/GetCourseSubmissions.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($suid) ? $suid : "",
                isset($mid) ? $mid : "");
    }   
}
?>