<?php
/**
 * @file Controller.php contains the Controller class
 *
 * @author Till Uhlig
 */ 

require_once('/Slim/Slim.php');
include_once('/Structures.php');
include_once('/Request.php');

\Slim\Slim::registerAutoloader();
   
/**
 * the Controller class represents a component, which routes incoming rest
 * requests to relevant components
 */
class Controller
{
    /**
     * @var $_prefix the prefix, the class works with
     */ 
    protected static $_prefix = "";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return Controller::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        Controller::$_prefix = $value;
    }
    
    /**
     * @var $_app the slim object
     */ 
    protected $_app;
    
    /**
     * @var $_conf the component data object
     */ 
    protected $_conf = null;
        
    /**
     * the component constructor
     *
     * @param $conf component data
     */ 
    public function __construct($_conf)
    {
        // initialize component
        $this->_conf = $_conf;
        
        // initialize slim
        $this->_app = new \Slim\Slim();
        $this->_app->map('/:data+', array($this,'getl'))->via('GET', 'POST', 'DELETE', 'PUT', 'INFO');
        
        // starts slim only if the right prefix was received
        if (strpos($this->_app->request->getResourceUri(), '/component') !== 0){
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * the getl function uses a list of links to find a 
     * relevant component for the $data request
     * 
     * @param $data a slim generated array of URI segments (String[])
     */
    public function getl($data)
    {
        Logger::Log("starts Controller routing",LogLevel::DEBUG);
            
        // if no URI is received, abort process    
        if (count($data)==0){
           Logger::Log("Controller nothing to route",LogLevel::DEBUG);
           $this->_app->response->setStatus(409);
           $this->_app->stop();
           return;
        }

        // get possible links
        $else = array();
        $list = $this->_conf->getLinks();
        
        foreach ($list as $links){
        
            // determines all supported prefixes  
            $possible = explode(',',$links->getPrefix());
            
            if (in_array($data[0],$possible)){
            
                // create a custom request
                $ch = Request::custom($this->_app->request->getMethod(),
                                      $links->getAddress().$this->_app->request->getResourceUri(),
                                      $this->_app->request->headers->all(),
                                      $this->_app->request->getBody());
                                      
                // checks the answered status code  
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
                                            
                    Logger::Log("Controller prefix search failed",LogLevel::DEBUG);
                    $this->_app->stop();
                    return;
                }
                elseif ($ch['status'] == 401){
                    $this->_app->response->setStatus($ch['status']);
                    $this->_app->response->setBody("");
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
            
                // if the prefix is not used, check if the link also 
                // permits any questions and remember him in the $else list
                array_push($else, $links);
            } 
        }
        
        // if no possible link was found or every possible component 
        // answered with a non "finished" status code, we will ask components
        // who are able to work with every prefix
        foreach ($else as $links){
        
            // create a custom request
            $ch = Request::custom($this->_app->request->getMethod(),
                                  $links->getAddress().$this->_app->request->getResourceUri(),
                                  $this->_app->request->headers->all(),
                                  $this->_app->request->getBody());
                                  
            // checks the answered status code                     
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
                                        
                Logger::Log("Controller blank search failed",LogLevel::DEBUG);
                $this->_app->stop();
                return;
            }
            elseif ($ch['status'] == 401){
                $this->_app->response->setStatus($ch['status']);
                $this->_app->response->setBody("");
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

        // no positive response or no operative link
        $this->_app->response->setStatus(409);
    }
    
   
}

?>