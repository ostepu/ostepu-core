<?php
/**
 * @file LUser.php Contains the LUser class
 * 
 * @author Martin Daute
 * @author Christian Elze
 * @author Peter Koenig
 * @date 2013-2014
 */

require '../../Assistants/Slim/Slim.php';
include '../../Assistants/Request.php';
include_once '../../Assistants/CConfig.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LCondition-Component
 */
class LCondition
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "condition";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LCondition::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LCondition::$_prefix = $value;
    }

    /**
     * @var string $lURL the URL of the logic-controller
     */
    private $lURL = ""; // readed out from config below

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct()
    {
        // runs the CConfig
        $com = new CConfig( LCondition::getPrefix( ), dirname(__FILE__) );

        // runs the LCondition
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
            
        // initialize slim
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;    
        $this->query = CConfig::getLink($conf->getLinks(),"controller");

        // initialize lURL
        $this->lURL = $this->query->getAddress();

        // POST SetConditions
        $this->app->post('/'.$this->getPrefix().'(/)', array($this, 'setConditions'));

        // PUT EditConditions
        $this->app->put('/'.$this->getPrefix().'/course/:courseid(/)', array($this, 'editConditions'));

        // GET CheckConditions
        $this->app->get('/'.$this->getPrefix().'/course/:courseid/user/:userid(/)',
                        array($this, 'checkConditions'));
        // run Slim
        $this->app->run();
    }

    /**
     * Adds a condition to a course.
     *
     * Called when this component receives an HTTP POST request to
     * /condition(/).
     * The request body should contain a JSON object representing the condition.
     *
     * @param int $courseid The id of the course to which the condition will being added.
     */
    public function setConditions(){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/approvalcondition';
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Edits a condition to a course.
     *
     * Called when this component receives an HTTP PUT request to
     * /condition/course/$courseid(/).
     * The request body should contain a JSON object representing the condition.
     *
     * @param int $courseid The id of the course to which the condition
     * will being updated.
     */
    public function editConditions($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/approvalcondition/'.$courseid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * description.
     *
     * Called when this component receives an HTTP GET request to
     * URL.
     *
     * @param int $courseid The id of the course for which the condition 
     * of the user should be returned.
     * @param int $userid The id of the user which condition should being returned.     
     */
    public function checkConditions($courseid, $userid){

    }
}