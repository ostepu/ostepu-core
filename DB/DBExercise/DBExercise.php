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

$com = new CConfig(DBExercise::getPrefix());

if (!$com->used())
    new DBExercise($com->loadConfig());  
    
/**
 * (description)
 */
class DBExercise
{
    private $_app=null;
    private $_conf=null;
    
    private $query=array();
    
    private static $_prefix = "exercise";
    
    public static function getPrefix()
    {
        return DBExercise::$_prefix;
    }
    
    public static function setPrefix($value)
    {
        DBExercise::$_prefix = $value;
    }
    
    public function __construct($conf){
        $this->_conf = $conf;
        $this->query = array(CConfig::getLink($conf->getLinks(),"out"));
        
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');

        // PUT EditExercise
        $this->_app->put('/' . $this->getPrefix() . '/exercise/:eid',
                        array($this,'editExercise'));
        
        // DELETE DeleteExercise
        $this->_app->delete('/' . $this->getPrefix() . '/exercise/:eid',
                           array($this,'deleteExercise'));
        
        // POST SetExercise
        $this->_app->post('/' . $this->getPrefix(),
                         array($this,'setExercise'));    
        
        // GET GetExercise
        $this->_app->get('/' . $this->getPrefix() . '/exercise/:eid',
                        array($this,'getExercise'));
        
        // GET GetSheetExercises
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid',
                        array($this,'getSheetExercises'));
                        
        // GET GetCourseExercises
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid',
                        array($this,'getCourseExercises'));
        
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
    // PUT EditExercise
    public function editExercise($eid)
    {

    }
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // DELETE DeleteExercise
    public function deleteExercise($eid)
    {

    }
    
    /**
     * (description)
     */
    // POST SetExercise
    public function setExercise()
    {

    }
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // GET GetExercise
    public function getExercise($eid)
    {        

    }
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // GET GetSheetExercises
    public function getSheetExercises($esid)
    {             

    }
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // GET GetCourseExercises
    public function getCourseExercises($courseid)
    {             

    }
}
?>