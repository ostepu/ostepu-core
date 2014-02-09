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
     * Adds the exercise file to the filesystem first. At success
     * the informations belongs to this exercise will be stored in the database.
     */
    public function addExercise(){
        $header = $this->app->request->headers->all();

        // get the file object from the request body to send it to the filesystem
        $body = json_decode($this->app->request->getBody(), true);
        var_dump($body);
        $file = json_encode($body['file']);

        // request to the filesystem to save the file
        $URL = $this->lURL.'/FS';
        $answer = Request::custom('POST', $URL, $header, $file);

        /*
         * if the file has been stored, the information
         * belongs to this exercise will be stored in the database
         */
        if($answer['status'] >= 200 && $answer['status'] < 300){ 
            $body['file'] = json_decode($answer['content'], true);
            // send a request to database
            $URL = $this->lURL.'/DB';
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        } else {
            // if the file has not been stored response the (error-)status-code
            $this->app->response->setStatus($answer['status']);
        }
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
     * Deletes the exercise information from the database first. At success
     * the file belongs to this exercise will be deleted from the filesystem.
     *
     * @param int $exerciseid The id of the exercise that is beeing deleted.
     */
    public function deleteExercise($exerciseid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;
        // request to database
        $answer = Request::custum('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);

        /*
         * if the file information has been deleted, the file
         * will being deleted from filesystem
         */
        $fileObject = json_decode($answer['content'], true);
        // if address-field exists, read it out
        if (isset($fileObject['address']) and $answer['status'] >= 200 && $answer['status'] < 300){
            $fileAddress = $fileObject['address'];
            // request to filesystem
            $URL = $this->lURL.'/FS/'.$fileAddress;
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }
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