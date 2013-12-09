<?php
/**
 * @file (filename)
 * %(description)
 */ 

require 'Include/Slim/Slim.php';
include 'Include/Com.php';
include 'Include/Structures.php';
include 'Include/Request.php';

\Slim\Slim::registerAutoloader();

$com = new CConf(FsZIP::getBaseDir());

if (!$com->used())
    new FsZIP($com->loadConfig());

/**
 * (description)
 */
class FsZIP
{
    private static $_baseDir = "zip";
    public static function getBaseDir(){
        return FsZIP::$_baseDir;
    }
    public static function setBaseDir($value){
        FsZIP::$_baseDir = $value;
    }
    
    private $_app=null;
    private $conf=null;
    private $FSControl=null;
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
	public function __construct($conf){
	    $this->conf = $conf;
	    $this->FSControl = CConf::getLink($conf->getLinks(),"FSControl");
	    $this->_app = new \Slim\Slim();

        $this->_app->response->headers->set('Content-Type', '_application/json');
		
		// POST file
		$this->_app->post('/'.FsZIP::$_baseDir, array($this,'postZip'));
		
		// GET filedata
		$this->_app->get('/'.FsZIP::$_baseDir.'/:hash', array($this,'getZipData'));
		
		// GET file as document
		$this->_app->get('/'.FsZIP::$_baseDir.'/:hash/:filename', array($this,'getZipDocument'));
		
		// DELETE file
		$this->_app->delete('/'.FsZIP::$_baseDir.'/:hash', array($this,'deleteZip'));
		
		if (strpos($this->_app->request->getResourceUri(), '/'.FsZIP::$_baseDir) === 0){
		// run Slim
		$this->_app->run();
		}
    }
	
    
    /**
     * (description)
     */
	// POST Zip
	public function postZip(){
	    $body = $this->_app->request->getBody();
	    $fileobject = json_decode($body);
	        
	    if ($fileobject->hash == null && $fileobject->body == null){
                $this->_app->response->setStatus(405);
                $this->_app->stop();
        }
	    else
	    	if ($fileobject->body == null && $fileobject->hash != null){
	    	    if (FsZIP::isRelevant($fileobject->hash,$this->relevant_begin,$this->relevant_end)===false){
	                $this->_app->response->setStatus(405);
	                $this->_app->stop();
	    	    }
	    	    else{
	    	        $this->_app->response->setStatus(406);
	                $this->_app->stop();    	    
	    	    }
	        }
	        
	        $files = explode('\n',$fileobject->body);
	        
	        // create sha1 request hash
	        $hash = sha1($fileobject->body);
	        
	        if (FsZIP::isRelevant($hash,$this->relevant_begin,$this->relevant_end)===false){
	        	$fileobject->hash = $hash;
	    	    $this->_app->response->setBody(json_encode($fileobject));
	            $this->_app->response->setStatus(405);
	            $this->_app->stop();
	        }
	        
	        // generates the filepath to save file in filesystem
	        $savepath = FsZIP::generateFilePath(FsZIP::$_baseDir,$hash);
	        
	        if (!file_exists($savepath)){

	            // creates the folder structure for file
	            FsZIP::generatepath(dirname($savepath));
	        
	            $zip = new ZipArchive();
	            
	            if ($zip->open($savepath, ZIPARCHIVE::CREATE)===TRUE) {
                    for ($i=0;$i<count($files);$i++){
                        $ch = curl_init($this->FSControl->address.'/'.dirname($files[$i]));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $content = curl_exec($ch);
                        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        if ($http_status == 200) {
                        $ch = curl_init($this->FSControl->address.'/'.$files[$i]);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $content = curl_exec($ch);
                        curl_close($ch);

                        $zip->addFromString(basename($files[$i]),$content);
                        }            
                        $zip->close();          
                    }
		            $this->_app->response->setStatus(201);
	            }
	            else{
	                $this->_app->response->setStatus(400);
	                $this->_app->stop();
	            }
	        }
	        else{
	            $this->_app->response->setStatus(409);
	            $this->_app->stop();
	        }

		    $fileobject->body=null;
		    $fileobject->address = FsZIP::$_baseDir."/".$hash;
            $fileobject->fileSize = filesize($savepath);
            $fileobject->hash = sha1_file($savepath);
            $this->_app->response->setBody(json_encode($fileobject));
	}
	
	
	/**
     * (description)
     *
     * @param $hash (description)
     * @param $filename (description)
     */
	// GET Zip
	public function getZipDocument($hash, $filename){
	    if (FsZIP::isRelevant($hash,$this->relevant_begin,$this->relevant_end)===false){
	        $this->_app->response->setStatus(405);
	        $this->_app->stop();
	    }
	    	
	    $file = FsZIP::generateFilePath(FsZIP::$_baseDir,$hash);
	    if (strlen($file)>0 && file_exists($file)){
	         $this->_app->response->headers->set('Content-Type', '_application/zip');
	         $this->_app->response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");
	         $this->_app->response->setStatus(200);
	         readfile($file);
	         $this->_app->stop();
	    }
	    else	    
	        {
	            $this->_app->response->headers->set('Content-Type', '_application/html');
	            $this->_app->response->setStatus(404);
	            $this->_app->stop();
	        }
	}

    /**
     * (description)
     *
     * @param $hash (description)
     */
	// GET Zipdata
    public function getZipData($hash){   
        if (FsZIP::isRelevant($hash,$this->relevant_begin,$this->relevant_end)===false){
	        $this->_app->response->setStatus(405);
	        $this->_app->stop();
	    } 
	    	
        $file = FsZIP::generateFilePath(FsZIP::$_baseDir,$hash);
	    if (strlen($file)>0 && file_exists($file)){  
            $this->_app->response->setStatus(200);
            $File = new File();
            $File->address = FsZIP::$_baseDir."/".$hash;
            $File->fileSize = filesize($file);
            $File->hash = $hash;
            $this->_app->response->setBody(json_encode($File));
            $this->_app->stop();
	    }
	}
	
	/**
     * (description)
     *
     * @param $hash (description)
     */
	// DELETE Zip
    public function deleteZip($hash){
    	if (FsZIP::isRelevant($hash,$this->relevant_begin,$this->relevant_end)===false){
	        $this->_app->response->setStatus(405);
	        $this->_app->stop();
	    }
	    	
        $file = FsZIP::generateFilePath(FsZIP::$_baseDir,$hash);
	    if (strlen($file)>0 && file_exists($file)){ 
	        $File = new File();
            $File->address = FsZIP::$_baseDir."/".$hash;
            $File->fileSize = filesize($file);
            $File->hash = $hash; 
	        unlink($file);
	        $this->_app->response->setBody(json_encode($File));
	        if (file_exists($file)){
	            $this->_app->response->setStatus(402);
	            $this->_app->stop();
	        } else {
	            $this->_app->response->setStatus(202);
	            $this->_app->stop();
	        }
	    } else{
	        // Zip not exists
	        $this->_app->response->setStatus(409);
	        $this->_app->stop();
	    }
	}
	
	/**
     * (description)
     *
     * @param $type (description)
     * @param $file (description)
     */
	public static function generateFilePath($type,$file){
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
     * @param $hash (description)
     * @param $relevant_begin (description)
     * @param $relevant_end (description)
     */
    public static function isRelevant($hash,$relevant_begin,$relevant_end){
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