<?php
/**
* @file (filename)
* %(description)
*/ 

require 'Include/Slim/Slim.php';
include 'Include/Com.php';
include 'Include/Structures.php';

\Slim\Slim::registerAutoloader();

$com = new CConf("");

if (!$com->used())
    new FsBinder();

/**
 * (description)
 */
class FsBinder
{
    private static $_baseDir = "files";
    public static function getBaseDir(){
        return FsBinder::$_baseDir;
    }
    public static function setBaseDir($value){
        FsBinder::$_baseDir = $value;
    }
    
    private $_app;
    private $_conf;

    /**
     * (description)
     */
	public function __construct(){
    
	    $this->_app = new \Slim\Slim();

	    $this->_app->response->headers->set('Content-Type', 'application/json');
	    
		// POST file
		$this->_app->post('/:data+', array($this,'postFile'));
		
		// GET file as document or filedata
		$this->_app->get('/:data+', array($this,'getFile'));
		
		// DELETE file
		$this->_app->delete('/:data+', array($this,'deleteFile'));
		
		// INFO file
		$this->_app->map('/:data+', array($this,'infoFile'))->via('INFO');
		
		// run Slim
		$this->_app->run();
    }
    	
    
    /**
     * POST File
     * 
     * @param $data (description)
     */
	public function postFile($data){       
	    $body = $this->_app->request->getBody();
	    $fileobject = json_decode($body);
	        
	    $file = FsBinder::$_baseDir."/".implode("/",array_slice ($data,0));
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
     * GET File
     *
     * @param $data (description)
     */
	public function getFile($data){	  
        if (count($data)==0){
            $this->_app->response->setStatus(409);
            $this->_app->stop();
            return;
        }
        
        $file = FsBinder::$_baseDir . $this->_app->request->getResourceUri();
        
	    if (strlen($file)>0 && file_exists($file)){
	       $this->_app->response->headers->set('Content-Type', 'application/octet-stream');
	       $this->_app->response->setStatus(200);
	       readfile($file);
	       $this->_app->stop();
	    } else{
	       $this->_app->response->setStatus(409);
	       $this->_app->stop();
	    }
	}
	
    /**
     * INFO File
     *
     * @param $data (description)
     */
    public function infoFile($data){   
        if (count($data)==0){
            $this->_app->response->setStatus(409);
            $this->_app->stop();
            return;
        }
        
        $file = FsBinder::$_baseDir . $this->_app->request->getResourceUri();
        
        if (strlen($file)>0 && file_exists($file)){  
            $this->_app->response->setStatus(200);
            $File = new File();
            $File->setAddress($file);
            $File->setFileSize(filesize($file));
            $File->setHash(sha1_file($file));
            $this->_app->response->setBody(json_encode($File));
            $this->_app->stop();
        } else{
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
	
	/**
     * DELETE File
     *
     * @param $hash (description)
     */
    public function deleteFile($data){
        if (count($data)==0){
            $this->_app->response->setStatus(409);
            $this->_app->stop();
            return;
        }
        
        $file = FsBinder::$_baseDir."/".implode("/",array_slice ($data,0));
        	    	
	    if (strlen($file)>0 && file_exists($file)){ 
	        $File = new File();
            $File->setAddress(FsBinder::$_baseDir."/".$hash);
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