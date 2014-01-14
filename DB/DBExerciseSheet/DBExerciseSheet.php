<?php
/**
 * @file DBExerciseSheet.php contains the DBExerciseSheet class
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
$com = new CConfig(DBExerciseSheet::getPrefix());

// runs the DBExerciseSheet
if (!$com->used())
    new DBExerciseSheet($com->loadConfig());  
    
/**
 * A class, to abstract the "ExerciseSheet" table from database
 *
 * @author Till Uhlig
 */
class DBExerciseSheet
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
    private static $_prefix = "exercisesheet";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBExerciseSheet::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBExerciseSheet::$_prefix = $value;
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

        // PUT EditExerciseSheet
        $this->_app->put('/' . $this->getPrefix() . '/exercisesheet/:esid',
                        array($this,'editExerciseSheet'));
        
        // DELETE DeleteExerciseSheet
        $this->_app->delete('/' . $this->getPrefix() . '/exercisesheet/:esid',
                           array($this,'deleteExerciseSheet'));
        
        // POST SetExerciseSheet
        $this->_app->post('/' . $this->getPrefix(),
                         array($this,'setExerciseSheet'));
               
        // GET GetExerciseSheetURL
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid/url',
                        array($this,'getExerciseSheetURL'));
                        
        // GET GetCourseSheetURLs
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid/url',
                        array($this,'getCourseSheetURLs'));
                        
        // GET GetExerciseSheet
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid+',
                        array($this,'getExerciseSheet'));
        
        // GET GetCourseSheets
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid+',
                        array($this,'getCourseSheets'));
        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
                    
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * PUT EditExerciseSheet
     *
     * @param int $esid a database exercise sheet identifier
     */ 
    public function editExerciseSheet($esid)
    {
        Logger::Log("starts PUT EditExerciseSheet",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid));
                            
        // decode the received exercise sheet data, as an object
        $insert = ExerciseSheet::decodeExerciseSheet($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/EditExerciseSheet.sql", 
                                            array("esid" => $esid, "values" => $data));                   
           
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditExerciseSheet failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * DELETE DeleteExerciseSheet
     *
     * @param int $esid a database exercise sheet identifier
     */
    public function deleteExerciseSheet($esid)
    {
        Logger::Log("starts DELETE DeleteExerciseSheet",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteExerciseSheet.sql", 
                                        array("esid" => $esid));    
                                        
        // checks the correctness of the query                          
        if ($result['status']>=200 && $result['status']<=299){
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteExerciseSheet failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
    
    /**
     * POST SetExerciseSheet
     */
    public function setExerciseSheet()
    {
        Logger::Log("starts POST SetExerciseSheet",LogLevel::DEBUG);
        
        // decode the received exercise sheet data, as an object
        $insert = ExerciseSheet::decodeExerciseSheet($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/SetExerciseSheet.sql", 
                                            array("values" => $data));                   
           
            // checks the correctness of the query    
            if ($result['status']>=200 && $result['status']<=299){
                $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new ExerciseSheet();
                $obj->setId($queryResult->getInsertId());
            
                $this->_app->response->setBody(ExerciseSheet::encodeExerciseSheet($obj)); 
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST SetExerciseSheet failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * GET GetExerciseSheetURL
     *
     * @param int $esid a database exercise sheet identifier
     */
    public function getExerciseSheetURL($esid)
    {     
        Logger::Log("starts GET GetExerciseSheetURL",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetExerciseSheetURL.sql", 
                                        array("esid" => $esid));        

        // checks the correctness of the query                                
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();
            
            // generates an assoc array of an file by using a defined 
            // list of its attributes
            $exerciseSheetFile = DBJson::getResultObjectsByAttributes($data, 
                                                        File::getDBPrimaryKey(), 
                                                        File::getDBConvert());
            
            // only one object as result
            if (count($exerciseSheetFile)>0)
                $exerciseSheetFile = $exerciseSheetFile[0];
            
            $this->_app->response->setBody(File::encodeFile($exerciseSheetFile));
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetExerciseSheetURL failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(File::encodeExerciseSheet(new File()));
            $this->_app->stop();
        }
    }   
    
    /**
     * GET GetCourseSheetURLs
     *
     * @param int $courseid a database course identifier
     */
    public function getCourseSheetURLs($courseid)
    {     
        Logger::Log("starts GET GetCourseSheetURLs",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($courseid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetCourseSheetURLs.sql", 
                                        array("courseid" => $courseid));        

        // checks the correctness of the query                             
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();
            
            // generates an assoc array of files by using a defined list of its attributes
            $exerciseSheetFiles = DBJson::getResultObjectsByAttributes($data, File::getDBPrimaryKey(), File::getDBConvert());
            
            $this->_app->response->setBody(File::encodeFile($exerciseSheetFiles));
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetCourseSheetURLs failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->stop();
        }
    } 
    
    /**
     * GET GetExerciseSheet
     *
     * @param int $esid the identifier of a exercise sheet
     */
    public function getExerciseSheet($esid)
    {     
        Logger::Log("starts GET GetExerciseSheet",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid));
                            
        if (count($esid)<1){
            Logger::Log("PUT EditExerciseSheet wrong use",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(ExerciseSheet::encodeExerciseSheet(new ExerciseSheet()));
            $this->_app->stop();
            return;
        }

        $options = array_splice($esid,1);
        $esid = $esid[0];
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetExerciseSheet.sql", 
                                        array("esid" => $esid));  
                                        
        // checks the exercise option          
        if (in_array('exercise',$options)){
            // starts a query, by using a given file
            $result2 = DBRequest::getRoutedSqlFile($this->query, 
                                    "Sql/GetSheetExercises.sql", 
                                    array("esid" => $esid)); 
        }

        // checks the correctness of the query    
        if ($result['status']>=200 && $result['status']<=299 && (!isset($result2) || ($result2['status']>=200 && $result2['status']<=299))){
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();
            
            // generates an assoc array of an exercise sheet by using a defined list of its attributes
            $exerciseSheet = DBJson::getObjectsByAttributes($data, 
                                    ExerciseSheet::getDBPrimaryKey(), 
                                    ExerciseSheet::getDBConvert());
            
            // generates an assoc array of an file by using a defined list of its attributes
            $sampleSolutions = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
            
            // generates an assoc array of an file by using a defined list of its attributes
            $exerciseSheetFile = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert(), 
                                            '2');
          
            // concatenates the exercise sheet and the associated sample solution
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $exerciseSheet,
                            ExerciseSheet::getDBPrimaryKey(),
                            ExerciseSheet::getDBConvert()['F_id_sampleSolution'],
                            $sampleSolutions,
                            File::getDBPrimaryKey());  
            
            // concatenates the exercise sheet and the associated exercise sheet file
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            ExerciseSheet::getDBPrimaryKey(),
                            ExerciseSheet::getDBConvert()['F_id_file'] ,
                            $exerciseSheetFile,
                            File::getDBPrimaryKey(), 
                            '2');
            
            // checks the exercise option
            if (in_array('exercise',$options)){
                $query = Query::decodeQuery($result2['content']);
                $data = $query->getResponse();
            
                // generates an assoc array of exercises by using a defined list of its attributes
                $exercises = DBJson::getObjectsByAttributes($data, 
                                        Exercise::getDBPrimaryKey(), 
                                        Exercise::getDBConvert());
            
                // concatenates the exercise sheet and the associated exercises
                $res = DBJson::concatResultObjectLists($data, 
                                    $res,
                                    ExerciseSheet::getDBPrimaryKey(),
                                    ExerciseSheet::getDBConvert()['ES_exercises'],
                                    $exercises,Exercise::getDBPrimaryKey());
            }
           
            // to reindex
            $res = array_merge($res);
            
            // only one object as result
            if (count($res)>0)
                $res = $res[0];
                
            $this->_app->response->setBody(ExerciseSheet::encodeExerciseSheet($res));
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetExerciseSheet failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(ExerciseSheet::encodeExerciseSheet(new ExerciseSheet()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetCourseSheets
     *
     * @param int $courseid a database course identifier
     */
    public function getCourseSheets($courseid)
    {     
        Logger::Log("starts GET GetCourseSheets",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($courseid));
                            
        if (count($courseid)<1){
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(ExerciseSheet::encodeExerciseSheet(new ExerciseSheet()));
            $this->_app->stop();
            return;
        }
        
        $options = array_splice($courseid,1);
        $courseid = $courseid[0];
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetCourseSheets.sql", 
                                        array("courseid" => $courseid));  
                                        
        // checks the exercise option                           
        if (in_array('exercise',$options)){
            $result2 = DBRequest::getRoutedSqlFile($this->query, 
                                    "Sql/GetCourseExercises.sql", 
                                    array("courseid" => $courseid)); 
        }
        
        // checks the correctness of the query    
        if ($result['status']>=200 && $result['status']<=299 && (!isset($result2) || ($result2['status']>=200 && $result2['status']<=299))){
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();
            
            // generates an assoc array of an exercise sheet by using a defined list of its attributes
            $exerciseSheet = DBJson::getObjectsByAttributes($data, 
                                ExerciseSheet::getDBPrimaryKey(), 
                                ExerciseSheet::getDBConvert());

            // sets the sheet names
            $id = 1;
            foreach ($exerciseSheet as &$sheet){
                if (!isset($sheet['sheetName']) || $sheet['sheetName']==null){
                    $sheet['sheetName'] = 'Serie '. (string) $id;
                    $id++;
                }
            }
            
            // generates an assoc array of an file by using a defined list of its attributes
            $sampleSolutions = DBJson::getObjectsByAttributes($data, 
                                                File::getDBPrimaryKey(), 
                                                File::getDBConvert());
            
            // generates an assoc array of an file by using a defined list of its attributes
            $exerciseSheetFile = DBJson::getObjectsByAttributes($data, 
                                                File::getDBPrimaryKey(), 
                                                File::getDBConvert(), '2');
          
            // concatenates the exercise sheet and the associated sample solution
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $exerciseSheet,ExerciseSheet::getDBPrimaryKey(),
                            ExerciseSheet::getDBConvert()['F_id_sampleSolution'],
                            $sampleSolutions,
                            File::getDBPrimaryKey());  
            
            // concatenates the exercise sheet and the associated exercise sheet file
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            ExerciseSheet::getDBPrimaryKey(),
                            ExerciseSheet::getDBConvert()['F_id_file'],
                            $exerciseSheetFile,File::getDBPrimaryKey(), '2');
           
            // checks the exercise option
            if (in_array('exercise',$options)){
                $query = Query::decodeQuery($result2['content']);
                            $data = $query->getResponse();
                $exercises = DBJson::getObjectsByAttributes($data, 
                            Exercise::getDBPrimaryKey(), 
                            Exercise::getDBConvert());
            
                // concatenates the exercise sheet and the associated exercises
                $res = DBJson::concatResultObjectLists($data, 
                            $res,
                            ExerciseSheet::getDBPrimaryKey(),
                            ExerciseSheet::getDBConvert()['ES_exercises'], 
                            $exercises,
                            Exercise::getDBPrimaryKey());
            }
           
            // to reindex
            $res = array_merge($res);        
            
            $this->_app->response->setBody(ExerciseSheet::encodeExerciseSheet($res));
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetCourseSheets failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(ExerciseSheet::encodeExerciseSheet(new ExerciseSheet()));
            $this->_app->stop();
        }    

    }

}
?>