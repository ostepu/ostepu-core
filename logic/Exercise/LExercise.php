<?php
/**
 * @file LExercise.php Contains the LExercise class
 *
 * @author Martin Daute
 * @author Christian Elze
 * @author Peter Koenig
 */

require '../Include/Slim/Slim.php';
include '../Include/Request.php';
include_once( '../Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LExercise-Component
 */
class LExercise
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "exercise";

     /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LExercise::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LExercise::$_prefix = $value;
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

        // POST AddExercise
        $this->app->post('/'.$this->getPrefix().'(/)', array($this, 'addExercise'));

        // GET GetExercise
        $this->app->get('/'.$this->getPrefix().'/exercise/:exerciseid(/)',
                        array ($this, 'getExercise'));

        // DELETE DeleteExercise
        $this->app->delete('/'.$this->getPrefix().'/exercise/:exerciseid(/)',
                        array($this, 'deleteExercise'));

        // PUT EditExercise
        $this->app->put('/'.$this->getPrefix().'/exercise/:exerciseid(/)',
                        array($this, 'editExercise'));

        // run Slim
        $this->app->run();
    }

    /**
     * Adds an exercise.
     *
     * Called when this component receives an HTTP POST request to
     * /exercise(/).
     * The request body should contain a JSON object representing the exercise's
     * attributes.
     */
    public function addExercise(){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        // request to database
        $URL = $this->lURL.'/DB/exercise';
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Returns a single exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /exercise/exercise/$exerciseid(/).
     *
     * @param int $exerciseid The id of the exercise that should be returned.
     */
    public function getExercise($exerciseid) {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;
        //request to database
        $answer = Request::custom('GET', $URL, $header, $body);
        //set response
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Deletes an exercise.
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercise/exercise/$exerciseid(/).
     *
     * @param int $exerciseid The id of the exercise that is beeing deleted.
     */
    public function deleteExercise($exerciseid){
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/exercise/exercise/'.$exerciseid;
        // request to database
        print_r($URL);
        $answer = Request::custom('DELETE', $URL, $header, "");
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Edits an exercise.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercise/exercise/$exerciseid(/).
     * The request body should contain a JSON object representing the exercise's new
     * attributes.
     *
     * @param int $exerciseid The id of the exercise that is beeing updated.
     */
    public function editExercise($exerciseid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;
        // request to database
        $answer = Request::custom('PUT', $URL, $header, $body);
        // set response
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }
}

// get new config data from DB
$com = new CConfig(LExercise::getPrefix());

// create a new instance of LExercise class with the config data
if (!$com->used())
    new LExercise($com->loadConfig());
?>