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

$com = new CConfig(DBExerciseSheet::getPrefix());

if (!$com->used())
    new DBExerciseSheet($com->loadConfig());  
    
/**
 * (description)
 */
class DBExerciseSheet
{
    private $_app=null;
    private $_conf=null;
    
    private $query=array();
    
    private static $_prefix = "exercisesheet";
    
    public static function getPrefix()
    {
        return DBExerciseSheet::$_prefix;
    }
    
    public static function setPrefix($value)
    {
        DBExerciseSheet::$_prefix = $value;
    }
    
    public function __construct($conf){
        $this->_conf = $conf;
        $this->query = array(CConfig::getLink($conf->getLinks(),"out"));
        
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');

        // PUT EditExerciseSheet
        $this->_app->put('/' . $this->getPrefix() . '/exercisesheet/:esid',
                        array($this,'editExerciseSheet'));
        
        // DELETE DeleteExerciseSheet
        $this->_app->delete('/' . $this->getPrefix() . '/exercisesheet/:esid',
                           array($this,'deleteExerciseSheet'));
        
        // POST SetExerciseSheet
        $this->_app->post('/' . $this->getPrefix(),
                         array($this,'setExerciseSheet'));
               
        // GET GetExerciseSheetURL
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid/url',
                        array($this,'getExerciseSheetURL'));
                        
        // GET GetExerciseSheetURLs
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid/url',
                        array($this,'GetExerciseSheetURLs'));
                        
        // GET GetExerciseSheet
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid',
                        array($this,'getExerciseSheet'));
        
        // GET GetExerciseSheets
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid',
                        array($this,'getExerciseSheets'));
        
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
    // PUT EditExerciseSheet
    public function editExerciseSheet($esid)
    {

    }
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // DELETE DeleteExerciseSheet
    public function deleteExerciseSheet($esid)
    {

    }
    
    /**
     * (description)
     */
    // POST SetExerciseSheet
    public function setExerciseSheet()
    {

    }
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // GET GetExerciseSheetURL
    public function getExerciseSheetURL($esid)
    {        

    }   
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // GET GetExerciseSheetURLs
    public function getExerciseSheetURLs($courseid)
    {        

    } 
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // GET GetExerciseSheet
    public function getExerciseSheet($esid)
    {        

    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // GET GetExerciseSheets
    public function getExerciseSheets($courseid)
    {             


    }

}
?>