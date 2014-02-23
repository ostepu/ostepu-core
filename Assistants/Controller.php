<?php
/**
 * @file (filename)
 * (description)
 */ 
 
require_once('Include/Slim/Slim.php');
include_once('Include/Structures.php');
include_once('Include/Request.php');

\Slim\Slim::registerAutoloader();
   
/**
 * (description)
 */
class Controller
{
    protected static $_prefix = "";
    
    /**
     * (description)
     */
    public static function getPrefix()
    {
        return Controller::$_prefix;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function setPrefix($value)
    {
        Controller::$_prefix = $value;
    }
    
    protected $_app;
    protected $_fs = null;
    protected $_conf = null;
        
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function __construct($_conf)
    {
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
     * 
     * @param $param (description)
     */
    public function getl($data)
    {
        if (count($data)==0){
           $this->_app->response->setStatus(404);
           $this->_app->stop();
           return;
        }

        // suche passenden
        $else = array();
        $list = $this->_conf->getLinks();
        foreach ($list as $links){
            $possible = explode(',',$links->getPrefix());
            if (in_array($data[0],$possible)){
                $ch = Request::custom($this->_app->request->getMethod(),
                                      $links->getAddress().$this->_app->request->getResourceUri(),
                                      $this->_app->request->headers->all(),
                                      $this->_app->request->getBody());
    
                if ($ch['status']>=200 && $ch['status']<=299){
                    // finished
                    $this->_app->response->setStatus($ch['status']);
                    $this->_app->response->setBody($ch['content']);

                    if (isset($ch['headers']['Content-Type']))
                        $this->_app->response->headers->set('Content-Type', 
                                            $ch['headers']['Content-Type']);
                                            
                    if (isset($ch['headers']['Content-Disposition']))
                        $this->_app->response->headers->set('Content-Disposition', 
                                            $ch['headers']['Content-Disposition']);

                    $this->_app->stop();
                    return;
                }
                                     
            } elseif(in_array("",$possible)){
                array_push($else, $links);
            } 
        }
        
        foreach ($else as $links){
            $ch = Request::custom($this->_app->request->getMethod(),
                                  $links->getAddress().$this->_app->request->getResourceUri(),
                                  $this->_app->request->headers->all(),
                                  $this->_app->request->getBody());
            if ($ch['status']>=200 && $ch['status']<=299){
                // finished
                $this->_app->response->setStatus($ch['status']);
                $this->_app->response->setBody($ch['content']);

                if (isset($ch['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', 
                                        $ch['headers']['Content-Type']);
                                        
                if (isset($ch['headers']['Content-Disposition']))
                    $this->_app->response->headers->set('Content-Disposition', 
                                        $ch['headers']['Content-Disposition']);

                $this->_app->stop();
                return;
            }
        }

        
        $this->_app->response->setStatus(404);
    }
    
   
}

?>