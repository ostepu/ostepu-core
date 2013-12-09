<?php
/**
 * @file (filename)
 * (description)
 */ 

require 'Slim/Slim.php';
include 'include/Component.php';
include 'include/structures.php';
include 'include/Request.php';

\Slim\Slim::registerAutoloader();

$com = new CConf(FSControl::getPrefix());
new FSControl($com->loadConfig());

/**
 * (description)
 */
class FSControl
{
    private static $_prefix = "";
    public static function getPrefix(){
        return FSControl::$_prefix;
    }
    public static function setPrefix($value){
        FSControl::$_prefix = $value;
    }
    
    private $_app;
    private $_fs = null;
    private $_conf = null;
        
    /**
     * (description)
     *
     * @param $_conf (description)
     */
	public function __construct($_conf){
	    $this->_conf = $_conf;
	    $this->_fs = $_conf->getLinks();
	
	    $this->_app = new \Slim\Slim();
        $this->_app->map('/:data+', array($this,'getl'))->via('GET', 'POST', 'DELETE', 'PUT', 'INFO');
		
        if (strpos($this->_app->request->getResourceUri(), '/component') !== 0){
        $this->_app->run();
        }
	}
	
    /**
     * (description)
     */
	public function getl($data){
	    $fileobject = json_decode($this->_app->request->getBody());
	    if ($this->_app->request->isPost())
	        $fileobject->setBody(null);
	    
	    $fileobjectcomplete = json_decode($this->_app->request->getBody());
	    $found = false;
	    
	    for ($i=0;$i<count($this->_fs);$i++){
            $ch = Request::custom($this->_app->request->getMethod(),
                                  $this->_fs[$i]->address.$this->_app->request->getResourceUri(),
                                  $this->_app->request->headers->all(),
                                  json_encode($fileobject)); 

		    if ($ch['status']>=200 && $ch['status']<=299){
		            // finished
		            $this->_app->response->setStatus($ch['status']);
		            $this->_app->response->setBody($ch['content']);

		            header($ch['headers']['Content-Type']);
		            header($ch['headers']['Content-Disposition']);

	                $this->_app->stop();
		            break;
		    }
		    else
		        if($ch['status']==455){
		             if ($found==false && $this->_app->request->isPost()){
		                 $ch = Request::custom($this->_app->request->getMethod(),
		                                       $this->_fs[$i]->address.$this->_app->request->getResourceUri(),
		                                       $this->_app->request->headers->all(),
                                               json_encode($fileobjectcomplete));
                                            
		                 $fileobject = json_decode($ch['content']);
		                 $found = true;
		             } 
		        }
	            else
		            if($ch['status']==456){
		                $ch = Request::custom($this->_app->request->getMethod(),
                                              $this->_fs[$i]->address.$this->_app->request->getResourceUri(),
                                              $this->_app->request->headers->all(),
                                              json_encode($fileobjectcomplete));
                                            
		                $this->_app->response->setStatus($ch['status']);
		                $this->_app->response->setBody($ch['content']);
		                //$this->_app->response->setHeaders($ch['headers']);
	                    $this->_app->stop();
		                break;
		            }
	    }
	    
	    $this->_app->response->setStatus(404);
	}
	
	
}

?>