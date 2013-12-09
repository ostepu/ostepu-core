<?php
/**
* @file (filename)
* %(description)
*/ 

require 'Slim/Slim.php';
include 'include/Component.php';
include 'include/structures.php';
include 'include/Request.php';

\Slim\Slim::registerAutoloader();

$com = new CConf(FSFile::getBaseDir());
new FSFile($com->loadConfig());

/**
 * (description)
 */
class FSFile
{
    private static $_baseDir = "FILE";
    public static function getBaseDir(){
        return FSFile::$_baseDir;
    }
    public static function setBaseDir($value){
        FSFile::$_baseDir = $value;
    }
    
    private $_app;
    private $_conf;
    private $_relevantBegin;
    private $_relevantEnd;

    /**
     * (description)
     *
     * @param $_conf (description)
     */
	public function __construct($_conf){
	    $this->_conf = $_conf;
	    
	    $arr = explode("-",$_conf->getOption());
	    if (count($arr)<2){
	       $this->_relevantBegin=0;
           $this->_relevantEnd=0;
	    }
	    else{
	        $this->_relevantBegin=$arr[0];
            $this->_relevantEnd=$arr[1];
	    }

	    
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
     * (description)
     */
	// POST File
	public function postFile(){       
	    $body = $this->_app->request->getBody();
	    $fileobject = json_decode($body);
	        
	    if ($fileobject->gethash() == null && $fileobject->getBody() == null){
            $this->_app->response->setStatus(455);
            $this->_app->stop();
        }
	    else
	        if ($fileobject->getBody() == null && $fileobject->gethash() != null){
	    	    if (FSFile::isRelevant($fileobject->gethash(),$this->_relevantBegin,$this->_relevantEnd)===false){
	                $this->_app->response->setStatus(455);
	                $this->_app->stop();
	    	    }
	    	    else{
	    	        $this->_app->response->setStatus(456);
	                $this->_app->stop();    	    
	    	    }
	        }
	        
	        $filename = "temp/" . basename(tempnam(getcwd() . "/temp",""));
	        
	        $file = fopen($filename,"w");
	        fwrite($file, base64_decode($fileobject->getBody()));
	        fclose($file);
	        
	        // create sha1 hash
	        $hash = sha1_file($filename);
	        
	    	if (FSFile::isRelevant($hash,$this->_relevantBegin,$this->_relevantEnd)===false){
	    	    unlink($filename);
	    	    $fileobject->setHash($hash);
	    	    $this->_app->response->setBody(json_encode($fileobject));
	            $this->_app->response->setStatus(455);
	            $this->_app->stop();
	        }
	        
	        // generates the filepath to save file in filesystem
	        $savepath = FSFile::generateFilePath(FSFile::$_baseDir,$hash);
	        
	        if (!file_exists($savepath)){
	            // creates the folder structure for file
	            FSFile::generatepath(dirname($savepath));
	        
	            $file = fopen($savepath,"w");
	            fwrite($file, base64_decode($fileobject->getBody()));
	            fclose($file);
	        
	            // delete temp file
	            unlink($filename);
	
		        $this->_app->response->setStatus(201);
	        }
	        else{
	            $this->_app->response->setStatus(409);
	            $this->_app->stop();
	        }

		    $fileobject->setBody(null);
		    $fileobject->setAddress(FSFile::$_baseDir . "/" . $hash);
            $fileobject->setFileSize(filesize($savepath));
            $fileobject->setHash($hash);
            $this->_app->response->setBody(json_encode($fileobject));
	}
	
    /**
     * (description)
     *
     * @param $hash (description)
     * @param $filename (description)
     */
	// GET File
	public function getFileDocument($hash, $filename){
	    if (FSFile::isRelevant($hash,$this->_relevantBegin,$this->_relevantEnd)===false){
	        $this->_app->response->setStatus(455);
	        $this->_app->stop();
	    }
	    
	    $file = FSFile::generateFilePath(FSFile::$_baseDir,$hash);
	    if (strlen($file)>0 && file_exists($file)){
	         $this->_app->response->headers->set('Content-Type', 'application/octet-stream');
	         $this->_app->response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");
	         $this->_app->response->setStatus(200);
	         readfile($file);
	         $this->_app->stop();
	    } else{
	        $this->_app->response->setStatus(409);
	        $this->_app->stop();
	   }
	}

	/**
     * (description)
     *
     * @param $hash (description)
     */
	// GET Filedata
    public function getFileData($hash){  
        if (FSFile::isRelevant($hash,$this->_relevantBegin,$this->_relevantEnd)===false){
	        $this->_app->response->setStatus(455);
	        $this->_app->stop();
	    }
	    	
        $file = FSFile::generateFilePath(FSFile::$_baseDir,$hash);
	    if (strlen($file)>0 && file_exists($file)){  
            $this->_app->response->setStatus(200);
            $File = new File();
            $File->setAddress(FSFile::$_baseDir . "/" . $hash);
            $File->setFileSize(filesize($file));
            $File->setHash($hash);
            $this->_app->response->setBody(json_encode($File));
            $this->_app->stop();
	    } else
	        $this->_app->response->setStatus(409);
	}
	
	/**
     * (description)
     *
     * @param $hash (description)
     */
	// DELETE File
    public function deleteFile($hash){
        if (FSFile::isRelevant($hash,$this->_relevantBegin,$this->_relevantEnd)===false){
	            $this->_app->response->setStatus(455);
	            $this->_app->response->setBody(new File());
	            $this->_app->stop();
	    }
	    	
        $file = FSFile::generateFilePath(FSFile::$_baseDir,$hash);
	    if (strlen($file)>0 && file_exists($file)){ 
	        $File = new File();
            $File->setAddress(FSFile::$_baseDir . "/" . $hash);
            $File->setFileSize(filesize($file));
            $File->setHash($hash); 
	        unlink($file);
	        if (file_exists($file)){
	            $this->_app->response->setStatus(452);
	            $this->_app->response->setBody(new File());
	            $this->_app->stop();
	            }
	            
	        $this->_app->response->setBody($File);
	        $this->_app->response->setStatus(252);
	        $this->_app->stop();
	    } else{
	        // file not exists
	        $this->_app->response->setStatus(409);
	        $this->_app->response->setBody(new File());
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
	       return $type . "/" . $file[0] . "/" . $file[1] . "/" . $file[2] . "/" . substr($file,3);
	   } else
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
	                $path = $path . '/' . $parts[$i];
	        }
	    }
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