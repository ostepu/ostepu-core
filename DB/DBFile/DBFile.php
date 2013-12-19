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

$com = new CConfig(DBFile::getPrefix());

if (!$com->used())
    new DBFile($com->loadConfig());
    
/**
 * (description)
 */
class DBFile
{
    private $_app=null;
    private $_conf=null;
    
    private $query=array();
    
    private static $_prefix = "file";
    
    public static function getPrefix()
    {
        return DBFile::$_prefix;
    }
    public static function setPrefix($value)
    {
        DBFile::$_prefix = $value;
    }
    
    public function __construct($conf)
    {
        $this->_conf = $conf;
        $this->query = array(CConfig::getLink($conf->getLinks(),"query"));
        
        $this->app = new \Slim\Slim();
        
        // PUT EditFile
        $this->app->put('/' . $this->getPrefix() . '/file/:fileid',
                        array($this, 'editFile'));
                        
        // POST SetFile
        $this->app->post('/' . $this->getPrefix(),
                        array($this, 'editFile'));
                        
        // DELETE RemoveFile
        $this->app->delete('/' . $this->getPrefix() . '/file/:fileid',
                        array($this, 'removeFile'));
                                           
        // GET GetFile
        $this->app->get('/' . $this->getPrefix() . '/file/:fileid',
                        array($this, 'getFile'));
                        
        // GET GetFiles
        $this->app->get('/' . $this->getPrefix() . '/file',
                        array($this, 'getFiles'));

        if (strpos ($this->app->request->getResourceUri(),'/' . $this->getPrefix()) === 0){
            // run Slim
            $this->app->run();
        }
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // PUT EditFile
    public function editFile($fileid)
    {

    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // DELETE RemoveFile
    public function removeFile($fileid)
    {

    }
    
    /**
     * (description)
     */
    // POST AddFile
    public function addFile()
    {

    }
    
    /**
     * (description)
     */
    // GET GetFile
    public function getFile($fileid)
    {

    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // GET GetFiles
    public function getFiles()
    {
 
    }
}
?>