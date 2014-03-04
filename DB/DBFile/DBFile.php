<?php
/**
 * @file DBFile.php contains the DBFile class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBFile/FileSample.json
 */ 

require_once( '../../Assistants/Slim/Slim.php' );
include_once( '../../Assistants/Structures.php' );
include_once( '../../Assistants/Request.php' );
include_once( '../../Assistants/DBJson.php' );
include_once( '../../Assistants/CConfig.php' );
include_once( '../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader();
    
/**
 * A class, to abstract the "File" table from database
 */
class DBFile
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
    private static $_prefix = "file";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBFile::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBFile::$_prefix = $value;
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
        
        // PUT EditFile
        $this->_app->put('/' . $this->getPrefix() . '(/file)/:fileid(/)',
                        array($this, 'editFile'));
                        
        // POST AddFile
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                        array($this, 'addFile'));
                        
        // DELETE RemoveFile
        $this->_app->delete('/' . $this->getPrefix() . '(/file)/:fileid(/)',
                        array($this, 'removeFile'));
                                           
        // GET GetFile
        $this->_app->get('/' . $this->getPrefix() . '(/file)/:fileid(/)',
                        array($this, 'getFile'));
                        
        // GET GetFileByHash
        $this->_app->get('/' . $this->getPrefix() . '/hash/:hash(/)',
                        array($this, 'getFileByHash'));
                        
        // GET GetAllFiles
        $this->_app->get('/' . $this->getPrefix() . '(/file)(/)',
                        array($this, 'getAllFiles'));
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefix()) === 0){
                    
            // run Slim
            $this->_app->run();
        }
    }


    /**
     * Edits a file.
     *
     * Called when this component receives an HTTP PUT request to
     * /file/$fileid(/) or /file/file/$fileid(/).
     * The request body should contain a JSON object representing the file's new
     * attributes.
     *
     * @param string $fileid The id of the file that is being updated.
     */
    public function editFile($fileid)
    {
        Logger::Log("starts PUT EditFile",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($fileid));
                            
        // decode the received file data, as an object
        $insert = File::decodeFile($this->_app->request->getBody());
        
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
                                            "Sql/EditFile.sql", 
                                            array("fileid" => $fileid, 
                                            "values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditFile failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
                $this->_app->stop();
            }
        }
    }


    /**
     * Deletes a file.
     *
     * Called when this component receives an HTTP DELETE request to
     * /file/$fileid(/) or /file/file/$fileid(/).
     *
     * @param string $fileid The id of the file that is being deleted.
     */
    public function removeFile($fileid)
    {
        Logger::Log("starts DELETE RemoveFile",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($fileid));
                            
         // starts a query, by using a given file
         $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteFile.sql", 
                                        array("fileid" => $fileid));    
        
        // checks the correctness of the query                        
        if ($result['status']>=200 && $result['status']<=299){ 
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();
            
            // generates an assoc array of an file by using a defined list of 
            // its attributes
            $file = DBJson::getResultObjectsByAttributes($data, 
                                        File::getDBPrimaryKey(), 
                                        File::getDBConvert());
            
            // only one object as result 
            if (count($file)>0)
                $file = $file[0];
                
            if ($file!==null){
                $this->_app->response->setBody(File::encodeFile($file));
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
            } else{
                Logger::Log("DELETE RemoveFile failed (no file in db)",LogLevel::ERROR);
                $this->_app->response->setBody(File::encodeFile(new File()));
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
                $this->_app->stop();
            }
                
        } else{
            Logger::Log("DELETE RemoveFile failed",LogLevel::ERROR);
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->stop();
        }
    }


    /**
     * Adds a file.
     *
     * Called when this component receives an HTTP POST request to
     * /file(/).
     * The request body should contain a JSON object representing the file's
     * attributes.
     */
    public function addFile()
    {
        Logger::Log("starts POST AddFile",LogLevel::DEBUG);
        
        // decode the received file data, as an object
        $insert = File::decodeFile($this->_app->request->getBody());
        
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
                                            "Sql/AddFile.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                //$obj = new File();
                $in->setFileId($queryResult->getInsertId());
                $in->setBody(null);
            
                array_push($res, $in);
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST AddFile failed",LogLevel::ERROR);
                $this->_app->response->setBody(File::encodeFile($res));
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
                $this->_app->stop();
            }
        }
        
        if (!$arr && count($res)==1){
            $this->_app->response->setBody(File::encodeFile($res[0])); 
        }
        else
            $this->_app->response->setBody(File::encodeFile($res)); 
    }

    
    public function get($functionName,$sqlFile,$userid,$courseid,$esid,$eid,$fileid,$hash,$singleResult=false)
    {
        Logger::Log("starts GET " . $functionName,LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        $hash = DBJson::mysql_real_escape_string($hash);
        DBJson::checkInput($this->_app, 
                            $userid == "" ? true : ctype_digit($userid), 
                            $courseid == "" ? true : ctype_digit($courseid), 
                            $esid == "" ? true : ctype_digit($esid), 
                            $eid == "" ? true : ctype_digit($eid), 
                            $fileid == "" ? true : ctype_digit($fileid));
                            
            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        $sqlFile, 
                                        array("userid" => $userid,
                                        'courseid' => $courseid,
                                        'esid' => $esid,
                                        'eid' => $eid,
                                        'fileid' => $fileid,
                                        'hash' => $hash));
 
        // checks the correctness of the query                                        
        if ($result['status']>=200 && $result['status']<=299){ 
            $query = Query::decodeQuery($result['content']);
            
            if ($query->getNumRows()>0){
                $res = File::ExtractFile($query->getResponse(),$singleResult); 
                $this->_app->response->setBody(File::encodeFile($res));
        
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
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->stop();
    }
    
    
    /**
     * Returns a file.
     *
     * Called when this component receives an HTTP GET request to
     * /file/$fileid(/) or /file/file/$fileid(/).
     *
     * @param string $file The id of the file that should be returned.
     */
    public function getFile($fileid)
    {
        $this->get("GetFile",
                "Sql/GetFile.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($fileid) ? $fileid : "",
                isset($hash) ? $hash : "",
                true);
    }


    /**
     * Returns a file identified by a given hash.
     *
     * Called when this component receives an HTTP GET request to
     * /file/hash/$hash(/).
     *
     * @param string $hash The hash of the file that should be returned.
     */
    public function getFileByHash($hash)
    {
        $this->get("GetFileByHash",
                "Sql/GetFileByHash.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($fileid) ? $fileid : "",
                isset($hash) ? $hash : "",
                true);
    }


    /**
     * Returns all files.
     *
     * Called when this component receives an HTTP GET request to
     * /file(/) or /file/file(/).
     */
    public function getAllFiles()
    {
        $this->get("GetAllFiles",
                "Sql/GetAllFiles.sql",
                isset($userid) ? $userid : "",
                isset($courseid) ? $courseid : "",
                isset($esid) ? $esid : "",
                isset($eid) ? $eid : "",
                isset($fileid) ? $fileid : "",
                isset($hash) ? $hash : "");
    }
}

// runs the CConfig
$com = new CConfig(DBFile::getPrefix());

// runs the DBFile
if (!$com->used())
    new DBFile($com->loadConfig());
?>