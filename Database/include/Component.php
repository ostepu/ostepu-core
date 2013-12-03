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
    private $app;
    private $CONF_FILE = "CConfig.json";
    
    /**
    * (description)
    */
    public function __construct(){
        $this->app = new \Slim\Slim();

        $this->app->response->headers->set('Content-Type', 'application/json');
        
        // POST Config
        $this->app->post('/Component', array($this,'postConfig'));
        
        if ($this->app->request->isPost() && $this->app->request->getResourceUri()=="/Component"){
        // run Slim
        $this->app->run();
        }
    }
    
    /**
    * (description)
    */
    // POST Config
    public function postConfig(){
        $body = $this->app->request->getBody();
        saveConfig($body);
        $this->app->response->setStatus(200);
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
        return json_decode(file_get_contents($this->CONF_FILE));
    }
    
    /**
    * (description)
    *
    * @param $linkList (description)
    * @param $name (description)
    */
    public static function getLink($linkList,$name){
        for ($i=0;$i<count($linkList);$i++){
            if ($linkList[$i]->name==$name)
                return $linkList[$i];
        }
        return null;
    }

    
}

?>