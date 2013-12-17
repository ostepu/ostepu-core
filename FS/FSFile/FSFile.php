<?php
/**
* @file (filename)
* %(description)
*/ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/Structures.php' );

\Slim\Slim::registerAutoloader();

$com = new CConfig(FSFile::getBaseDir());

if (!$com->used())
    new FSFile($com->loadConfig());

/**
 * (description)
 */
class FSFile
{
    private static $_baseDir = "file";
    
    public static function getBaseDir()
    {
        return FSFile::$_baseDir;
    }
    
    public static function setBaseDir($value)
    {
        FSFile::$_baseDir = $value;
    }
    
    private $_app;
    private $_conf;
    private $_fs = array();

    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function __construct($_conf)
    {
        $this->_conf = $_conf;
        $this->_fs = $this->_conf->getLinks();
        
        $this->_app = new \Slim\Slim();

        $this->_app->response->headers->set('Content-Type', 'application/json');
        
        // POST file
        $this->_app->post('/'.FSFile::$_baseDir, array($this,'postFile'));
        
        // GET filedata
        $this->_app->get('/'.FSFile::$_baseDir.'/:hash', array($this,'getFileData'));
        
        // GET file as document
        $this->_app->get('/'.FSFile::$_baseDir.'/:hash/:filename', array($this,'getFileDocument'));
        
        // DELETE file
        $this->_app->delete('/'.FSFile::$_baseDir.'/:hash', array($this,'deleteFile'));
        
        if (strpos($this->_app->request->getResourceUri(), '/'.FSFile::$_baseDir) === 0){
         // run Slim
         $this->_app->run();
        }

    } 
    
    /**
     * POST File
     */
    public function postFile()
    {       
        $body = $this->_app->request->getBody();
        $fileObject = File::decodeFile($body);
        $fileObject->setHash(sha1(base64_decode($fileObject->getBody())));
        $filePath = FSFile::generateFilePath(FSFile::getBaseDir(), $fileObject->getHash());
        $fileObject->setAddress(FSFile::getBaseDir() . '/' . $fileObject->getHash());
        
        $links = FSFile::filterRelevantLinks($this->_fs, $fileObject->getHash());
        
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
            $this->_app->response->setStatus($result['status']);
            $this->_app->response->setBody(File::encodeFile($fileObject));
        } else{
            $this->_app->response->setStatus(451);
            $fileObject->setBody(null);
            $this->_app->response->setBody(File::encodeFile($fileObject));
            $this->_app->stop();
        }
    }
    
    /**
     *  GET File
     *
     * @param $hash (description)
     * @param $filename (description)
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
     * GET Filedata
     *
     * @param $hash (description)
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
     * DELETE File
     *
     * @param $hash (description)
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
            $this->_app->response->setStatus(452);
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->stop();
        }
        $this->_app->stop();  
    }
    
    /**
     * (description)
     *
     * @param $type (description)
     * @param $file (description)
     */
    public static function generateFilePath($type,$file)
    {
       if (strlen($file)>=4){
           return $type . "/" . $file[0] . "/" . $file[1] . "/" . $file[2] . "/" . substr($file,3);
       } else
           return "";
    }
    
    /**
     * (description)
     *
     * @param $path (description)
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
     * (description)
     *
     * @param $linkedComponents (description)
     * @param $hash (description)
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
     * (description)
     *
     * @param $hash (description)
     * @param $_relevantBegin (description)
     * @param $_relevantEnd (description)
     */
    public static function isRelevant($hash,$_relevantBegin,$_relevantEnd){
        $begin = hexdec(substr($_relevantBegin,0,strlen($_relevantBegin)));
        $end = hexdec(substr($_relevantEnd,0,strlen($_relevantEnd)));
        $current = hexdec(substr($hash,0,strlen($_relevantEnd)));
        if ($current>=$begin && $current<=$end){
            return true;
        } else
            return false;  
    }
  
}

?>