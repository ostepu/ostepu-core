<?php
/**
* @file (filename)
* %(description)
*/ 

require 'Slim/Slim.php';
include 'include/Component.php';
include 'include/structures.php';

\Slim\Slim::registerAutoloader();

$com = new CConf("");
new FSBinder();

/**
 * (description)
 */
class FSBinder
{
    private static $_baseDir = "files";
    public static function getBaseDir(){
        return FSBinder::$_baseDir;
    }
    public static function setBaseDir($value){
        FSBinder::$_baseDir = $value;
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
	public function __construct(){
    
	    $this->_app = new \Slim\Slim();

	    $this->_app->response->headers->set('Content-Type', 'application/json');
	    
	    // am besten noch ein Befehl INFO für Dateidaten abfragen
	    
		// POST file
		$this->_app->post('/:data+', array($this,'postFile'));
		
		// GET file as document or filedata
		$this->_app->get('/:data+', array($this,'getFileDocument'));
		
		// DELETE file
		$this->_app->delete('/:data+', array($this,'deleteFile'));
		
		// run Slim
		$this->_app->run();
    }
    	
    
    /**
     * (description)
     */
	// POST File
	public function postFile($data){       
	    $body = $this->_app->request->getBody();
	    $fileobject = json_decode($body);
	        
	    $file = FSBinder::$_baseDir."/".implode("/",array_slice ($data,0));
	    FSFile::generatepath(dirname($file));
	        
	    $file = fopen($filename,"w");
	    fwrite($file, base64_decode($fileobject->getBody()));
	    fclose($file);

		$fileobject->setBody(null);
		$fileobject->setAddress(FSFile::$_baseDir."/".$file);
        $fileobject->setFileSize(filesize($savepath));
        $fileobject->setHash(sha1_file($file));
        $this->_app->response->setBody(json_encode($fileobject));
	}
	
    /**
     * (description)
     *
     * @param $hash (description)
     * @param $filename (description)
     */
	// GET File //$this->_app->request->getResourceUri()
	public function getFileDocument($data){	  
        if (count($data)==0){
            $this->_app->response->setStatus(409);
            $this->_app->stop();
            return;
        }
        
        $file = FSBinder::$_baseDir."/".implode("/",array_slice ($data,0));
        
        if ($data[0]=="data"){
            if (strlen($file)>0 && file_exists($file)){  
                $this->_app->response->setStatus(200);
                $File = new File();
                $File->setAddress($file);
                $File->setFileSize(filesize($file));
                $File->setHash(sha1_file($file));
                $this->_app->response->setBody(json_encode($File));
                $this->_app->stop();
            }
            else{
                $this->_app->response->setStatus(409);
                $this->_app->stop();
            }
        
        }
	    else{
	       if (strlen($file)>0 && file_exists($file)){
	           $this->_app->response->headers->set('Content-Type', 'application/octet-stream');
	           $this->_app->response->setStatus(200);
	           readfile($file);
	           $this->_app->stop();
	       }
	       else{
	           $this->_app->response->setStatus(409);
	           $this->_app->stop();
	       }
	   }
	}
	
	/**
     * (description)
     *
     * @param $hash (description)
     */
	// DELETE File
    public function deleteFile($data){
        if (count($data)==0){
            $this->_app->response->setStatus(409);
            $this->_app->stop();
            return;
        }
        
        $file = FSBinder::$_baseDir."/".implode("/",array_slice ($data,0));
        	    	
	    if (strlen($file)>0 && file_exists($file)){ 
	        $File = new File();
            $File->setAddress(FSBinder::$_baseDir."/".$hash);
            $File->setFileSize(filesize($file));
            $File->setHash(sha1_file($file));
	        unlink($file);
	        if (file_exists($file)){
	            $this->_app->response->setStatus(452);
	            $this->_app->response->setBody(new File());
	            $this->_app->stop();
	            }
	            
	        $this->_app->response->setBody($File);
	        $this->_app->response->setStatus(252);
	        $this->_app->stop();
	    }
	    else{
	        // file not exists
	        $this->_app->response->setStatus(409);
	        $this->_app->response->setBody(new File());
	        $this->_app->stop();
	    }      
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
}

?>