<?php
/**
 * @file LUser.php Contains the LUser class
 *
 * @author Peter Koenig
 * @author Martin Daute
 * @author Christian Elze
 */

require '../../Assistants/Slim/Slim.php';
include '../../Assistants/Request.php';
include_once '../../Assistants/CConfig.php';

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

        // POST addType
        $this->app->post('/'.$this->getPrefix().'(/)' ,
                            array($this, 'addType'));

        // DELETE deleteType
        $this->app->delete('/'.$this->getPrefix().'/exercisetype/:typeid(/)' ,
                            array($this, 'deleteType'));

        // PUT editType
        $this->app->put('/'.$this->getPrefix().'/exercisetype/:typeid(/)' ,
                            array($this, 'editType'));

        // GET getType
        $this->app->get('/'.$this->getPrefix().'/exercisetype/:typeid(/)' ,
                            array($this, 'getType'));

        // GET getAllTypes
        $this->app->get('/'.$this->getPrefix().'(/)' ,
                            array($this, 'getAllTypes'));

        // run Slim
        $this->app->run();
    }

    /**
     * Adds an exercise type
     *
     * Called when this component receives an HTTP POST request to
     * /exercisetype(/).
     * The request body should contain a JSON object representing the
     * new exercise type's attributes.
     */
    public function addType()
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisetype';
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }

    /**
     * Deletes an exercise type
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisetype/exercisetype/$typeid(/).
     *
     * @param int $typeid The id of the exercisetype that should being deleted.
     */
    public function deleteType($typeid)
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisetype/exercisetype/'.$typeid;
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Edits an exercise type
     *
     * Called when this component receives an HTTP POST request to
     * /exercisetype/exercisetype/$typeid(/).
     * The request body should contain a JSON object representing the
     * new exercise type's attributes.
     *
     * @param int $typeid The id of the exercisetype that should being updatedd.
     */
    public function editType($typeid)
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisetype/exercisetype/'.$typeid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Returns an exercise type
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisetype/exercisetype/$typeid(/).
     *
     * @param int $typeid The id of the exercisetype that should being returned.
     */
    public function getType($typeid)
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisetype/exercisetype/'.$typeid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }

    /**
     * Returns all exercise types
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisetype(/).
     */
    public function getAllTypes()
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisetype';
        $answer = Request::custom('GET', $URL, $header, $body);
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