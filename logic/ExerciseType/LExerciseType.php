<?php
/**
 * @file LUser.php Contains the LUser class
 * 
 * @author Peter Koenig
 * @author Martin Daute
 * @author Christian Elze
 */

require 'Slim/Slim.php';
include '../Include/Request.php';
include_once( '../Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LExerciseType-Component
 */
class LExerciseType
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "exercisetype";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LExerciseType::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LExerciseType::$_prefix = $value;
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
    public function __construct($conf)
    {
        // initialize slim
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->query = CConfig::getLink($conf->getLinks(),"controller");

        // initialize lURL        
        $this->lURL = $this->query->getAddress();

        // POST setPossibleTypes
        $this->app->post('/'.$this->getPrefix().'/course/:courseid(/)' ,
                            array($this, 'setPossibleTypes'));

        // DELETE deletePossibleTypes
        $this->app->delete('/'.$this->getPrefix().'/course/:courseid(/)' ,
                            array($this, 'deletePossibleTypes'));

        // PUT editPossibleType
        $this->app->put('/'.$this->getPrefix().'/course/:courseid(/)' ,
                            array($this, 'editPossibleTypes'));

        // run Slim
        $this->app->run();
    }

    /**
     * Sets a possible exercise type of a course
     *
     * Called when this component receives an HTTP POST request to
     * /exercisetype/course/$courseid(/).
     * The request body should contain a JSON object representing the 
     * new exercise type's attributes.
     *
     * @param int $courseid The id of the course the type should being added.
     */
    public function setPossibleTypes($courseid)
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseType/course/'.$courseid;
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Deletes a possible exercise type of a course
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisetype/course/$courseid(/).
     *
     * @param int $courseid The id of the course the type should being deleted.
     */
    public function deletePossibleTypes($courseid)
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseType/course/'.$courseid;
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Edits possible exercise types of a course
     *
     * Called when this component receives an HTTP POST request to
     * /exercisetype/course/$courseid(/).
     * The request body should contain a JSON object representing the 
     * new exercise type's attributes.
     *
     * @param int $courseid The id of the course the type should being updated.
     */
    public function editPossibleTypes($courseid)
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseType/course/'.$courseid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }
}

// get new config data from DB
$com = new CConfig(LExerciseType::getPrefix());

// create a new instance of LUser class with the config data
if (!$com->used())
    new LExerciseType($com->loadConfig());
?>