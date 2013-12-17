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

$com = new CConfig(DBExerciseType::getPrefix());

if (!$com->used())
    new DBExerciseType($com->loadConfig());  
    
/**
 * (description)
 */
class DBExerciseType
{
    private $_app=null;
    private $_conf=null;
    
    private $query=array();
    
    private static $_prefix = "exercisetype";
    
    public static function getPrefix()
    {
        return DBExerciseType::$_prefix;
    }
    
    public static function setPrefix($value)
    {
        DBExerciseType::$_prefix = $value;
    }
    
    public function __construct($conf){
        $this->_conf = $conf;
        $this->query = array(CConfig::getLink($conf->getLinks(),"out"));
        
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');

        // PUT EditPossibleType
        $this->_app->put('/' . $this->getPrefix() . '/exercisetype/:etid',
                        array($this,'editPossibleType'));
        
        // DELETE DeletePossibleType
        $this->_app->delete('/' . $this->getPrefix() . '/exercisetype/:etid',
                           array($this,'deletePossibleType'));
        
        // POST SetPossibleType
        $this->_app->post('/' . $this->getPrefix(),
                         array($this,'setPossibleType'));  
        
        // GET GetPossibleType
        $this->_app->get('/' . $this->getPrefix() . '/exercisetype/:etid',
                        array($this,'getPossibleType'));
        
        // GET GetPossibleTypes
        $this->_app->get('/' . $this->getPrefix() . '/exercisetype',
                        array($this,'getPossibleTypes'));
        
        if (strpos ($this->_app->request->getResourceUri(),'/' . $this->getPrefix()) === 0){
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // PUT EditPossibleType
    public function editPossibleType($etid)
    {

    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // DELETE DeletePossibleType
    public function deletePossibleType($etid)
    {

    }
    
    /**
     * (description)
     */
    // POST SetPossibleType
    public function SetPossibleType()
    {

    }
    
    /**
     * (description)
     */
    // GET GetPossibleType
    public function getPossibleTypes()
    {        

    }
    
    /**
     * (description)
     *
     * @param $id (description)
     */
    // GET GetPossibleType
    public function getPossibleType($etid)
    {             


    }

}
?>