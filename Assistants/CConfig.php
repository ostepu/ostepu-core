<?php
 /**
 * @file CConfig.php contains the CConfig class
 *
 * @author Till Uhlig
 */ 
include_once( dirname(__FILE__) . '/Structures.php' );


/**
 * this class is used to link components, to save new linkage data and to
 * retrieve linkage data
 */
class CConfig
{
    /**
     * @var $_app the slim object
     */ 
    private $_app;
    
    /**
     * @var $CONF_FILE the file where the component configuration would be stored 
     */ 
    private $CONF_FILE = "CConfig.json";
    
    /**
     * @var $_prefix the prefix, the class works with
     */ 
    private $_prefix = "";
    
    /**
     * @var $_used to check whether the component configuration 
     * has been addressed
     */ 
    private $_used = false;
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public function getPrefix()
    {
        return $this->_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param $value the new value for $_prefix
     */ 
    public function setPrefix($value)
    {
        $this->_prefix = $value;
    }
    
    /**
     * the CConfig constructor
     *
     * @param $prefix the prefix, the component works with
     */ 
    public function __construct($prefix)
    {
        // initialize slim
        $this->setPrefix($prefix);
        $this->_app = new \Slim\Slim();

        $this->_app->response->headers->set('Content-Type' ,
                                            'application/json');
        
        // POST Config
        $this->_app->post('/component' , array($this , 'postConfig'));
        
        // GET Config
        $this->_app->get('/component' , array($this , 'getConfig'));
        
        // starts slim only if the right prefix was received
        if ($this->_app->request->getResourceUri() == "/component"){
        
            // run Slim
            $this->_used = true;
            $this->_app->run();
            
        } else {
            // if the "component" was not started, check if the links 
            // existing know their prefixes
            $conf = $this->loadConfig();
            $links = $conf->getLinks();
            
            // always been an array
            if (!is_array($links))
                $links = array($links);
                
            $changed = false;
            foreach ($links as &$link){
                // if a link has no prefix, we have to ask the link target
                // for the prefix list
                if ($link->getPrefix()===null){
                    $result = Request::get($link->getAddress() . '/component',array(),"");
                    
                    if ($result['status'] == 200){
                        // the link target has send its component definition,
                        // so that we can remember this
                        $changed = true;
                        $obj = Component::decodeComponent($result['content']);
                        $link->setPrefix($obj->getPrefix());
                    }        
                }
            }

            // if any new prefix was found, we have to store the link definitions
            if ($changed){
                $conf->setLinks($links);
                $this->saveConfig(Component::encodeComponent($conf));
            }
        }
    }
    
    /**
     * returns the value of $_used
     *
     * @return the value of $_used
     */
    public function used()
    {
        return $this->_used;   
    }
    
    /**
     * POST Config
     * - to store new component data
     */
    public function postConfig()
    {
        $this->_app->response->setStatus(451);
        $body = $this->_app->request->getBody();
        $Component = Component::decodeComponent($body);
        $Component->setPrefix($this->getPrefix());
        $this->saveConfig(json_encode($Component));
        $this->_app->response->setStatus(201);
    }
    
    /**
     * GET Config
     * - to ask this component for his component data
     */
    public function getConfig()
    {
        if (file_exists($this->CONF_FILE)){
            $com = Component::decodeComponent(file_get_contents($this->CONF_FILE));
            $com->setPrefix($this->getPrefix());
            $this->_app->response->setBody(Component::encodeComponent($com));
            $this->_app->response->setStatus(200);
        } else{
            $this->_app->response->setStatus(409);
            $com = new Component();
            $com->setPrefix($this->getPrefix());
            $this->_app->response->setBody(Component::encodeComponent($com));   
        }
    }
    
    /**
     * stores a Component object (json encoded) to the $CONF_FILE file
     *
     * @param $content the json encode Component object
     */
    public function saveConfig($content)
    {
        $file = fopen($this->CONF_FILE,"w");        
        fwrite($file, $content);
        fclose($file);
    }
    
    /**
     * the function loads the $CONF_FILE file
     *
     * @return a Component object
     */
    public function loadConfig()
    { 
        if (file_exists($this->CONF_FILE)){
            // file was found, create a Component object from file content
            $com = Component::decodeComponent(file_get_contents($this->CONF_FILE));
            $com->setPrefix($this->getPrefix());
            return $com;
        } else{
            // can't find the file, create an empty object
            $com = new Component();
            $com->setPrefix($this->getPrefix());
            return $com;        
        }
    }
    
    /**
     * to get a link from a link list with a specified name
     *
     * @param $linkList an array of links
     * @param $name the name of the searched link
     *
     * @return a link object, with the name $name
     */
    public static function getLink($linkList,$name)
    {
        foreach ($linkList as $link){
            // search a link with the name $name
            if ($link->getName() == $name)
                return $link;
        }

        return null;
    }
    
    /**
     * to get links from a link list with a specified name
     *
     * @param $linkList an array of links
     * @param $name the name of the searched links
     *
     * @return a array of link objects, which are named $name
     */
    public static function getLinks($linkList,$name)
    {
        $result = array();
        foreach ($linkList as $link){
            // return only links, which name is $name
            if ($link->getName() == $name)
                array_push($result, $link);
        }

        return $result;
    }
    
    /**
     * deletes a link ($linkName) from a link list ($links)
     *
     * @param $links an array of Links
     * @param $linkName a link name (String)
     *
     * @return the link list without these links 
     */
    public static function deleteFromArray($links, $linkName)
    {
        $result = array();
        foreach($links as $link)
        {
            // add only links to the new list, which does not named $linkName
            if($link->getName() != $linkName)
            {
                array_push($result,$link); 
            }
        }
        return $result;
    }
    
}

?>