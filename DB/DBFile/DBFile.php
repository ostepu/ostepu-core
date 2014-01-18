<?php
/**
 * @file DBFile.php contains the DBFile class
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/Logger.php' );

\Slim\Slim::registerAutoloader();
    
/**
 * A class, to abstract the "File" table from database
 *
 * @author Till Uhlig
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
        
        // PUT EditFile
        $this->_app->put('/' . $this->getPrefix() . '(/file)/:fileid(/)',
                        array($this, 'editFile'));
                        
        // POST SetFile
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                        array($this, 'editFile'));
                        
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
     * PUT EditFile
     *
     * @param int $fileid a database file identifier
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
        if (!is_array($insert))
            $insert = array($insert);

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
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * DELETE RemoveFile
     *
     * @param int $fileid a database file identifier
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
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 452);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
            } else{
                Logger::Log("DELETE RemoveFile failed (no file in db)",LogLevel::ERROR);
                $this->_app->response->setBody(File::encodeFile(new File()));
                $this->_app->response->setStatus(252);
                $this->_app->stop();
            }
                
        } else{
            Logger::Log("DELETE RemoveFile failed",LogLevel::ERROR);
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 452);
            $this->_app->stop();
        }
    }
    
    /**
     * POST AddFile
     */
    public function addFile()
    {
        Logger::Log("starts POST AddFile",LogLevel::DEBUG);
        
        // decode the received file data, as an object
        $insert = File::decodeFile($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/SetFile.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new File();
                $obj->setFileId($queryResult->getInsertId());
            
                $this->_app->response->setBody(File::encodeFile($obj)); 
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST AddFile failed",LogLevel::ERROR);
                $this->_app->response->setBody(File::encodeFile(new File()));
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * GET GetFile
     *
     * @param int $fileid a database file identifier
     */
    public function getFile($fileid)
    {
        Logger::Log("starts GET GetFile",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($fileid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetFile.sql", 
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
                
            $this->_app->response->setBody(File::encodeFile($file));
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetFile failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetFileByHash
     *
     * @param int $fileid a database file identifier
     */
    public function getFileByHash($hash)
    {
        Logger::Log("starts GET GetFileByHash",LogLevel::DEBUG);
        
        $hash = DBJson::mysql_real_escape_string($hash);
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetFileByHash.sql", 
                                        array("hash" => $hash));        
        
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
                
            $this->_app->response->setBody(File::encodeFile($file));
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetFileByHash failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetAllFiles
     */
    public function getAllFiles()
    {
        Logger::Log("starts GET GetAllFiles",LogLevel::DEBUG);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAllFiles.sql", 
                                        array());        
        
        // checks the correctness of the query
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);

            $data = $query->getResponse();
            
            // generates an assoc array of files by using a defined list of 
            // its attributes
            $file = DBJson::getResultObjectsByAttributes($data, 
                                        File::getDBPrimaryKey(), 
                                        File::getDBConvert());
                
            $this->_app->response->setBody(File::encodeFile($file));
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllFiles failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->stop();
        }
    }
}

// runs the CConfig
$com = new CConfig(DBFile::getPrefix());

// runs the DBFile
if (!$com->used())
    new DBFile($com->loadConfig());
?>