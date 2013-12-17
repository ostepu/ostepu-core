<?php
/**
 * @file (filename)
 * (description)
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

$com = new CConfig(DBAttachment::getPrefix());

if (!$com->used())
    new DBAttachment($com->loadConfig());  
    
/**
 * (description)
 */
class DBAttachment
{
    private $_app=null;
    private $_conf=null;
    
    private $query=array();
    
    private static $_prefix = "attachment";
    
    public static function getPrefix()
    {
        return DBAttachment::$_prefix;
    }
    
    public static function setPrefix($value)
    {
        DBAttachment::$_prefix = $value;
    }
    
    public function __construct($conf){
        $this->_conf = $conf;
        $this->query = array(CConfig::getLink($conf->getLinks(),"out"));
        
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');

        // PUT EditAttachment
        $this->_app->put('/' . $this->getPrefix() . '/attachment/:aid',
                        array($this,'editAttachment'));
        
        // DELETE DeleteAttachment
        $this->_app->delete('/' . $this->getPrefix() . '/attachment/:aid',
                           array($this,'deleteAttachment'));
        
        // POST SetAttachment
        $this->_app->post('/' . $this->getPrefix(),
                         array($this,'setAttachment'));    
        
        // GET GetAttachment
        $this->_app->get('/' . $this->getPrefix() . '/attachment/:aid',
                        array($this,'getAttachment'));
        
        // GET GetAttachments
        $this->_app->get('/' . $this->getPrefix() . '/attachment',
                        array($this,'getAttachments'));
                        
        // GET GetExerciseAttachments
        $this->_app->get('/' . $this->getPrefix() . '/exercise/:eid',
                        array($this,'getExerciseAttachments'));
        
        if (strpos ($this->_app->request->getResourceUri(),'/' . $this->getPrefix()) === 0){
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // PUT EditAttachment
    public function editAttachment($aid)
    {

    }
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // DELETE DeleteAttachment
    public function deleteAttachment($aid)
    {

    }
    
    /**
     * (description)
     */
    // POST SetAttachment
    public function setAttachment()
    {

    }
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // GET GetAttachment
    public function getAttachment($aid)
    {        

    }
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // GET GetAttachments
    public function getAttachments($aid)
    {             

    }
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // GET GetExerciseAttachments
    public function getExerciseAttachments($eid)
    {             

    }
}
?>