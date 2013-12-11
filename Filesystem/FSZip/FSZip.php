<?php
/**
 * @file (filename)
 * %(description)
 */ 

require 'Include/Slim/Slim.php';
include 'Include/CConfig.php';
include 'Include/Structures.php';
include 'Include/Request.php';

\Slim\Slim::registerAutoloader();

$com = new CConfig(FsZip::getBaseDir());

if (!$com->used())
    new FsZip($com->loadConfig());

/**
 * (description)
 */
class FsZip
{
    private static $_baseDir = "zip";
    public static function getBaseDir(){
        return FsZip::$_baseDir;
    }
    public static function setBaseDir($value){
        FsZip::$_baseDir = $value;
    }
    
    private $_app=null;
    private $_conf=null;
    
    
    private $getFile=null;
    private $_fs = null;
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function __construct($conf)
    {
        $this->_conf = $conf;
        $this->_fs = CConfig::deleteFromArray($this->_conf->getLinks(), "getFile");
        $this->FSControl = array(CConfig::getLink($conf->getLinks(),"getFile"));

        $this->_app = new \Slim\Slim();

        $this->_app->response->headers->set('Content-Type', '_application/json');
        
        // POST file
        $this->_app->post('/'.FsZip::$_baseDir, array($this,'postZip'));
        
        // GET filedata
        $this->_app->get('/'.FsZip::$_baseDir.'/:hash', array($this,'getZipData'));
        
        // GET file as document
        $this->_app->get('/'.FsZip::$_baseDir.'/:hash/:filename', array($this,'getZipDocument'));
        
        // DELETE file
        $this->_app->delete('/'.FsZip::$_baseDir.'/:hash', array($this,'deleteZip'));
        
        if (strpos($this->_app->request->getResourceUri(), '/'.FsZip::$_baseDir) === 0){
        // run Slim
        $this->_app->run();
        }
    }
    
    
    /**
     * POST Zip
     */
    public function postZip()
    {
        $body = $this->_app->request->getBody();
        $fileObject = File::decodeFile($body);
        if (!is_array($fileObject))
            $fileObject = array($fileObject);
        
        // generate hash
        $hashArray = array();
        foreach ($fileObject as $part){ 
            array_push($hashArray, $part->getAddress());
        }
        $hash = sha1(implode("\n",$hashArray));
       
        
        // generate zip
        $zip = new ZipArchive();
        $savepath = "temp/".$hash;

        FsZip::generatepath(dirname($savepath));
        
        if ($zip->open($savepath, ZIPARCHIVE::CREATE)===TRUE) {
            foreach ($fileObject as $part){
                $links = FsZip::filterRelevantLinks($this->FSControl, $part->getHash());
                $result = Request::routeRequest("GET",
                                                '/'.$part->getAddress() . '/' . $part->getDisplayName(),
                                                $this->_app->request->headers->all(),
                                                "",
                                                $links,
                                                explode('/',$part->getAddress())[0],
                                                "getFile");
                                                
                if (isset($result['content']))
                    $zip->addFromString($part->getDisplayName(), $result['content']);
            }            
            $zip->close();          
        }
        
        // save zip to filesystem
        $zipFile = new File();
        $zipFile->setHash($hash);
        $zipFile->setBody(base64_encode(file_get_contents($savepath)));
        $filePath = FsZip::generateFilePath(FsZip::getBaseDir(), $zipFile->getHash());
        $zipFile->setAddress(FsZip::getBaseDir() . '/' . $zipFile->getHash());
        
        $links = FsZip::filterRelevantLinks($this->_fs, $zipFile->getHash());
        
        $result = Request::routeRequest("POST",
                                      '/'.$filePath,
                                      $this->_app->request->headers->all(),
                                      File::encodeFile($zipFile),
                                      $links,
                                      FsZip::getBaseDir());
                                      
        unlink($savepath);
        
        if ($result['status']>=200 && $result['status']<=299){
            $tempObject = File::decodeFile($result['content']);
            $zipFile->setFileSize($tempObject->getFileSize());
            $zipFile->setBody(null);
            $this->_app->response->setStatus($result['status']);
            $this->_app->response->setBody(File::encodeFile($zipFile));
        } else{
            $this->_app->response->setStatus(451);
            $zipFile->setBody(null);
            $this->_app->response->setBody(File::encodeFile($zipFile));
            $this->_app->stop();
        }
    }
    
    
    /**
     * GET Zip
     *
     * @param $hash (description)
     * @param $filename (description)
     */
    public function getZipDocument($hash, $filename)
    {
        $links = FsZip::filterRelevantLinks($this->_fs, $hash);
        $filePath = FsZip::generateFilePath(FsZip::getBaseDir(), $hash);
        $result = Request::routeRequest("GET",
                                      '/'.$filePath,
                                      $this->_app->request->headers->all(),
                                      "",
                                      $links,
                                      FsZip::getBaseDir());
        
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
     * GET Zipdata
     *
     * @param $hash (description)
     */
    public function getZipData($hash)
    {   
        $links = FsZip::filterRelevantLinks($this->_fs, $hash);
        $filePath = FsZip::generateFilePath(FsZip::getBaseDir(), $hash);
        $result = Request::routeRequest("INFO",
                                      '/'.$filePath,
                                      $this->_app->request->headers->all(),
                                      "",
                                      $links,
                                      FsZip::getBaseDir());
                                      
        if (isset($result['headers']['Content-Type']))
            $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
            
        if ($result['status']>=200 && $result['status']<=299 && isset($result['content'])){
            $tempObject = File::decodeFile($result['content']);
            $tempObject->setAddress(FsZip::getBaseDir() . '/' . $hash);
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
     * (description)
     *
     * @param $hash (description)
     */
    // DELETE Zip
    public function deleteZip($hash)
    {
        $links = FsZip::filterRelevantLinks($this->_fs, $hash);
        $filePath = FsZip::generateFilePath(FsZip::getBaseDir(), $hash);
        $result = Request::routeRequest("DELETE",
                                      '/'.$filePath,
                                      $this->_app->request->headers->all(),
                                      "",
                                      $links,
                                      FsZip::getBaseDir());
                                      
        if ($result['status']>=200 && $result['status']<=299 && isset($result['content'])){
            $tempObject = File::decodeFile($result['content']);
            $tempObject->setAddress(FsZip::getBaseDir() . '/' . $hash);
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
           return $type."/".$file[0]."/".$file[1]."/".$file[2]."/".substr($file,3);
       }
       else
           return "";
    }
    
    /**
     * (description)
     *
     * @param $path (description)
     */
    public static function generatepath($path){
        $parts = explode("/", $path);
        if (count($parts)>0){
            $path = $parts[0];
            for($i=1;$i<=count($parts);$i++){
                if (!is_dir($path))
                    mkdir($path,0755);
                if ($i<count($parts))
                    $path = $path.'/'.$parts[$i];
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
            } elseif (FsZip::isRelevant($hash, $in[0],$in[1])) {
                array_push($result,$link);
            }
        }
        return $result;
    }
    
    /**
     * (description)
     *
     * @param $hash (description)
     * @param $relevant_begin (description)
     * @param $relevant_end (description)
     */
    public static function isRelevant($hash,$relevant_begin,$relevant_end)
    {
        $begin = hexdec(substr($relevant_begin,0,strlen($relevant_begin)));
        $end = hexdec(substr($relevant_end,0,strlen($relevant_end)));
        $current = hexdec(substr($hash,0,strlen($relevant_end)));
        if ($current>=$begin && $current<=$end){
            return true;
        }
        else
            return false;
    }    
}

?>