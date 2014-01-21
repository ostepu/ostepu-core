<?php 

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );


\Slim\Slim::registerAutoloader();

/**
 * The Condition class
 *
 * This class handles everything belongs to Conditions of a Course
 */

class LCondition
{
    /**
     *Values needed for conversation with other components
     */
    private $_conf=null;
    
    private static $_prefix = "condition";
    
    public static function getPrefix()
    {
        return LCondition::$_prefix;
    }
    public static function setPrefix($value)
    {
        LCondition::$_prefix = $value;
    }
    /**
     *Address of the Logic-Controller
     *dynamic set by CConf below
     */
    private $lURL = ""; 
    
    public function __construct($conf)
    {    
        /**
         *Initialise the Sli-Framework
         */
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        
        /**
         *Set the Logiccontroller-URL
         */
        $this->_conf = $conf;
        $this->query = array();
        
        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        $this->lURL = $this->query->getAddress();
        
        //POST SetConditions
        $this->app->post('/'.$this->getPrefix().'/course/:courseid(/)', array($this, 'setConditions'));        //Adressen noch anpassen (Parameter mit Compo-Namen)
        
        //PUT EditConditions
        $this->app->put('/'.$this->getPrefix().'/course/:courseid(/)', array($this, 'editConditions'));
        
        //GET CheckConditions
        $this->app->get('/'.$this->getPrefix().'/course/:courseid/user/:userid(/)',
                        array($this, 'checkConditions'));
        //run Slim                
        $this->app->run();
    }
    /**
     * Funktion to set Conditions of a course and save in DB 
     * takes one argument and retunrs a Status-Code
     * @param $courseid an integer identifies the Course
     */
    public function setConditions($courseid){        
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/'.$courseid;
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Funktion to edit the conditions of a course
     * takes one arguments and retunrs a Status-Code
     * @param $courseid an integer identifies the Course
     */
    public function editConditions($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/'.$courseid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }
    /**
     * Funktion to check the conditions of a course for a user
     * takes two arguments and retunrs true/false
     * @param $courseid an integer identifies the Course
     * @param $userid an integer identifies the user
     */
    public function CheckConditions($courseid, $userid){        //Funktion unklar in Aufbau, benoetigt Punkte von Marking
    
    }
}
/**
 * get new Config-Datas from DB 
 */
$com = new CConfig(LCondition::getPrefix());

/**
 * make a new instance of Condition-Class with the Config-Datas
 */
if (!$com->used())
    new LCondition($com->loadConfig());
?>