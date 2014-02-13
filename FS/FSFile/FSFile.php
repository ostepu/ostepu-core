<?php
/**
 * @file FSFile.php contains the FSFile class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt
 */ 

require_once( '../../Assistants/Slim/Slim.php' );
include_once( '../../Assistants/CConfig.php' );
include_once( '../../Assistants/Request.php' );
include_once( '../../Assistants/Structures.php' );

\Slim\Slim::registerAutoloader();

// runs the CConfig
$com = new CConfig(FSFile::getBaseDir());

// runs the FSFile
if (!$com->used())
    new FSFile($com->loadConfig());

/**
 * The class for storing and hashing files.
 */
class FSFile
{
    /**
     * @var string $_baseDir the root directory of this component.
     */
    private static $_baseDir = "file";
    

    /**
     * the $_baseDir getter
     *
     * @return the value of $_baseDir
     */ 
    public static function getBaseDir()
    {
        return FSFile::$_baseDir;
    }

    /**
     * the $_baseDir setter
     *
     * @param string $value the new value for $_baseDir
     */ 
    public static function setBaseDir($value)
    {
        FSFile::$_baseDir = $value;
    }
    
    /**
     * @var Slim $_app the slim object
     */
    private $_app;
    
    /**
     * @var Component $_conf the component data object
     */ 
    private $_conf;
    
    /**
     * @var Link[] $_fs links to components which work with files, e.g. FSBinder
     */ 
    private $_fs = array();


    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct($_conf)
    {
        $this->_conf = $_conf;
        $this->_fs = $this->_conf->getLinks();
        
        $this->_app = new \Slim\Slim(array('debug' => false));

        $this->_app->response->headers->set('Content-Type', 'application/json');
        
        // POST File
        $this->_app->post('/'.FSFile::$_baseDir . '(/)', array($this,'postFile'));
        
        // GET Filedata
        $this->_app->get('/'.FSFile::$_baseDir.'/:hash(/)', array($this,'getFileData'));
        
        // GET GetFileDocument
        $this->_app->get('/'.FSFile::$_baseDir.'/:hash/:filename(/)', array($this,'getFileDocument'));
        
        // DELETE File
        $this->_app->delete('/'.FSFile::$_baseDir.'/:hash(/)', array($this,'deleteFile'));
        
        if (strpos($this->_app->request->getResourceUri(), '/'.FSFile::$_baseDir) === 0){
         // run Slim
         $this->_app->run();
        }
    } 


    /**
     * Prepares the saving process by generating the hash and the place where the file is stored.
     *
     * Called when this component receives an HTTP POST request to
     * /file.
     * The request body should contain a JSON object representing the file's 
     * attributes.
     */
    public function postFile()
    {       
        $body = $this->_app->request->getBody();
        $fileObjects = File::decodeFile($body);
        
        $result = array();
        
        foreach ($fileObjects as $fileObject){ 
        
            $fileObject->setHash(sha1(base64_decode($fileObject->getBody())));
            $filePath = FSFile::generateFilePath(FSFile::getBaseDir(), $fileObject->getHash());
            $fileObject->setAddress(FSFile::getBaseDir() . '/' . $fileObject->getHash());
        
            $links = FSFile::filterRelevantLinks($this->_fs, $fileObject->getHash());
        
            $result = Request::routeRequest("INFO",
                                        '/'.$filePath,
                                        $this->_app->request->headers->all(),
                                        "",
                                        $links,
                                        FSFile::getBaseDir());
                                      
            if ($result['status']>=200 && $result['status']<=299){
                $tempObject = File::decodeFile($result['content']);
                $fileObject->setFileSize($tempObject->getFileSize());
                $fileObject->setBody(null);
                $result[] = $fileObject;
                //$this->_app->response->setStatus(201);
               // $this->_app->response->setBody(File::encodeFile($fileObject));
                //$this->_app->stop();
                continue;
            }
        
            $result = Request::routeRequest("POST",
                                        '/'.$filePath,
                                        $this->_app->request->headers->all(),
                                        File::encodeFile($fileObject),
                                        $links,
                                        FSFile::getBaseDir());
        
            if ($result['status']>=200 && $result['status']<=299){
                $tempObject = File::decodeFile($result['content']);
                $fileObject->setFileSize($tempObject->getFileSize());
                $fileObject->setBody(null);
                $result[] = $fileObject;
                //$this->_app->response->setStatus($result['status']);
                //$this->_app->response->setBody(File::encodeFile($fileObject));
            } else{
                $this->_app->response->setStatus(409);
               // $fileObject->setBody(null);
                $this->_app->response->setBody(File::encodeFile($result));
                $this->_app->stop();
            }
        }
        
        if (count($result)==1)
            $result = $result[0];
        
        $this->_app->response->setStatus(201);
        $this->_app->response->setBody(File::encodeFile($result));
    }


    /**
     * Returns a file.
     *
     * Called when this component receives an HTTP GET request to
     * /file/$hash/$filename.
     *
     * @param string $hash The hash of the file which should be returned.
     * @param string $filename A freely chosen filename of the returned file.
     */
    public function getFileDocument($hash, $filename)
    {      
        $links = FSFile::filterRelevantLinks($this->_fs, $hash);
        $filePath = FSFile::generateFilePath(FSFile::getBaseDir(), $hash);
        $result = Request::routeRequest("GET",
                                      '/'.$filePath,
                                      $this->_app->request->headers->all(),
                                      "",
                                      $links,
                                      FSFile::getBaseDir());
        
        if (isset($result['status']))
            $this->_app->response->setStatus($result['status']);
        
        if (isset($result['content']))
            $this->_app->response->setBody($result['content']);

        if (isset($result['headers']['Content-Type']))
            $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
        $this->_app->response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");
        $this->_app->stop();
    }


    /**
     * Returns the file infos as a JSON file object.
     *
     * Called when this component receives an HTTP GET request to
     * /file/$hash.
     *
     * @param string $hash The hash of the requested file.
     */
    public function getFileData($hash)
    {  
        $links = FSFile::filterRelevantLinks($this->_fs, $hash);
        $filePath = FSFile::generateFilePath(FSFile::getBaseDir(), $hash);
        $result = Request::routeRequest("INFO",
                                      '/'.$filePath,
                                      $this->_app->request->headers->all(),
                                      "",
                                      $links,
                                      FSFile::getBaseDir());
                                      
        if (isset($result['headers']['Content-Type']))
            $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
            
        if ($result['status']>=200 && $result['status']<=299 && isset($result['content'])){
            $tempObject = File::decodeFile($result['content']);
            $tempObject->setAddress(FSFile::getBaseDir() . '/' . $hash);
            $this->_app->response->setStatus($result['status']);
            $this->_app->response->setBody(File::encodeFile($tempObject));
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->stop();
        }
        $this->_app->stop();
    }


    /**
     * Deletes a file.
     *
     * Called when this component receives an HTTP DELETE request to
     * /file/$hash.
     *
     * @param string $hash The hash of the file which should be deleted.
     */
    public function deleteFile($hash)
    {
        $links = FSFile::filterRelevantLinks($this->_fs, $hash);
        $filePath = FSFile::generateFilePath(FSFile::getBaseDir(), $hash);
        $result = Request::routeRequest("DELETE",
                                      '/'.$filePath,
                                      $this->_app->request->headers->all(),
                                      "",
                                      $links,
                                      FSFile::getBaseDir());
                                      
        if ($result['status']>=200 && $result['status']<=299 && isset($result['content'])){
            $tempObject = File::decodeFile($result['content']);
            $tempObject->setAddress(FSFile::getBaseDir() . '/' . $hash);
            $this->_app->response->setStatus($result['status']);
            $this->_app->response->setBody(File::encodeFile($tempObject));
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->stop();
        }
        $this->_app->stop();  
    }


    /**
     * Creates a file path by splitting the hash.
     *
     * @param string $type The prefix of the file path.
     * @param string $hash The hash of the file.
     */
    public static function generateFilePath($type,$hash)
    {
       if (strlen($hash)>=4){
           return $type . "/" . $hash[0] . "/" . $hash[1] . "/" . $hash[2] . "/" . substr($hash,3);
       } else
           return "";
    }


    /**
     * Creates the path in the filesystem, if necessary.
     *
     * @param string $path The path which should be created.
     */
    public static function generatepath($path)
    {
        $parts = explode("/", $path);
        if (count($parts)>0){
            $path = $parts[0];
            for($i=1;$i<=count($parts);$i++){
                if (!is_dir($path))
                    mkdir($path,0755);
                if ($i<count($parts))
                    $path = $path . '/' . $parts[$i];
            }
        }
    }


    /**
     * Selects the components which are responsible for handling the file with
     * the given hash.
     *
     * @param link[] $linkedComponents An array of links to components which could 
     * possibly handle the file.
     * @param string $hash The hash of the file.
     */
    public static function filterRelevantLinks($linkedComponents, $hash)
    {
        $result = array();
        foreach ($linkedComponents as $link){
            $in = explode('-', $link->getRelevanz());
            if (count($in)<2){
                array_push($result,$link);
            } elseif (FSFile::isRelevant($hash, $in[0],$in[1])) {
                array_push($result,$link);
            }
        }
        return $result;
    }


    /**
     * Decides if the given component is responsible for the specific hash.
     *
     * @param string $hash The hash of the file.
     * @param string $_relevantBegin The minimum hash the component is responsible for.
     * @param string $_relevantEnd The maximum hash the component is responsible for.
     */
    public static function isRelevant($hash,$relevant_begin,$relevant_end)
    {
        // to compare the begin and the end, we need an other form
        $begin = hexdec(substr($relevant_begin,0,strlen($relevant_begin)));
        $end = hexdec(substr($relevant_end,0,strlen($relevant_end)));
        
        // the numeric form of the test hash
        $current = hexdec(substr($hash,0,strlen($relevant_end)));
        
        if ($current>=$begin && $current<=$end){
            return true;
        }
        else
            return false;
    }  
  
}

?>