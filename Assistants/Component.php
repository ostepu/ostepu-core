<?php
/**
* @file (filename)
* %(description)
*/ 



/**
* (description)
*/
class CConf
{
    private $_app;
    private $CONF_FILE = "CConfig.json";
    private $_prefix = "";
    
    /**
     * (description)
     */
    public function __construct($prefix){
        $this->_prefix = $prefix;
        $this->_app = new \Slim\Slim();

        $this->_app->response->headers->set('Content-Type', '_application/json');
        
        // POST Config
        $this->_app->post('/Component', array($this,'postConfig'));
        
        // GET Config
        $this->_app->get('/Component', array($this,'getConfig'));
        
        if ($this->_app->request->getResourceUri()=="/Component"){
        // run Slim
        $this->_app->run();
        }
    }
    
    /**
     * (description)
     */
    // POST Config
    public function postConfig(){
        $body = $this->_app->request->getBody();
        $Component = json_decode($body);
        $Component->setPrefix($this->_prefix);
        saveConfig(json_encode($Component));
        $this->_app->response->setStatus(200);
    }
    
    /**
     * (description)
     */
    // GET Config
    public function getConfig(){
        $configuration = loadConfig();
        $this->_app->response->setBody($configuration);
        $this->_app->response->setStatus(200);
    }
    
    /**
     * (description)
     *
     * @param $content (description)
     */
    public function saveConfig($content){
        $file = fopen($this->CONF_FILE,"w");        
        fwrite($file, $content);
        fclose($file);
    }
    
    /**
     * (description)
     */
    public function loadConfig(){ 
        return Component::decodeComponent(file_get_contents($this->CONF_FILE));
    }
    
    /**
     * (description)
     *
     * @param $linkList (description)
     * @param $name (description)
     */
    public static function getLink($linkList,$name){
        for ($i=0;$i<count($linkList);$i++){
            if ($linkList[$i]->_name==$name)
                return $linkList[$i];
        }
        return null;
    }

    
}

?>