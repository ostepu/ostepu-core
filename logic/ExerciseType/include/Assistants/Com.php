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
    private $_used = false;
    
    /**
     * (description)
     */
    public function __construct($prefix)
    {
        $this->_prefix = $prefix;
        $this->_app = new \Slim\Slim();

        $this->_app->response->headers->set('Content-Type' ,
                                            '_application/json');
        
        // POST Config
        $this->_app->post('/component' , array($this , 'postConfig'));
        
        // GET Config
        $this->_app->get('/component' , array($this , 'getConfig'));
        
        if ($this->_app->request->getResourceUri() == "/component"){
        
            // run Slim
            $this->_used = true;
            $this->_app->run();
        } else{

            $conf = $this->loadConfig();
            $links = $conf->getLinks();
            $changed = false;
            foreach ($links as &$link){
                if (!$link->getComplete()){
                    $result = Request::get($link->getAddress() . '/component',array(),"");
                    
                    if ($result['status'] == 200){
                        $changed = true;
                        $obj = Component::decodeComponent($result['content']);
                        $link->setComplete(true);
                        $link->setPrefix($obj->getPrefix());
                    }        
                }
            }
            
            if ($changed){
                $conf->setLinks($links);
                $this->saveConfig(Component::encodeComponent($conf));
            }
        }
    }
    
    /**
     * (description)
     */
    public function used()
    {
        return $this->_used;   
    }
    
    /**
     * (description)
     */
    // POST Config
    public function postConfig()
    {
        $body = $this->_app->request->getBody();
        $Component = Component::decodeComponent($body);
        $Component->setPrefix($this->_prefix);
        $this->saveConfig(json_encode($Component));
        $this->_app->response->setStatus(200);
    }
    
    /**
     * (description)
     */
    // GET Config
    public function getConfig()
    {
        if (file_exists($this->CONF_FILE)){
            $this->_app->response->setBody(file_get_contents($this->CONF_FILE));
            $this->_app->response->setStatus(200);
        }
        else{
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Component::encodeComponent(new Component()));   
        }
    }
    
    /**
     * (description)
     *
     * @param $content (description)
     */
    public function saveConfig($content)
    {
        $file = fopen($this->CONF_FILE,"w");        
        fwrite($file, $content);
        fclose($file);
    }
    
    /**
     * (description)
     */
    public function loadConfig()
    { 
        if (file_exists($this->CONF_FILE)){
            return Component::decodeComponent(file_get_contents($this->CONF_FILE));
        }
        else{
            return new Component();        
        }
    }
    
    /**
     * (description)
     *
     * @param $linkList (description)
     * @param $name (description)
     */
    public static function getLink($linkList,$name)
    {
        for ($i=0;$i<count($linkList);$i++){
            if ($linkList[$i]->_name==$name)
                return $linkList[$i];
        }
        return null;
    }

    
}

?>