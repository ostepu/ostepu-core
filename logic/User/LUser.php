<?php
/**
 * @file LUser.php Contains the LUser class
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
 * A class, to handle requests to the LUser-Component
 */
class LUser
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "user";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LUser::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        LUser::$_prefix = $value;
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

        // PUT SetUserRights
        $this->app->put('/'.$this->getPrefix().'/user/:userid/right', array($this, 'setUserRights'));

        // POST AddUser
        $this->app->post('/'.$this->getPrefix().'(/)', array($this, 'addUser'));

        // PUT EditUser
        $this->app->put('/'.$this->getPrefix().'/user/:userid(/)', array($this, 'editUser'));

        // GET GetUsers
        $this->app->get('/'.$this->getPrefix().'/user(/)', array($this, 'getUsers'));

        // GET GetUser
        $this->app->get('/'.$this->getPrefix().'/user/:userid(/)', array($this, 'getUser'));

        // run Slim
        $this->app->run();
    }

    /**
     * Set a user's user-rights.
     *
     * Called when this component receives an HTTP PUT request to
     * /user/$userid/right(/).
     * The request body should contain a JSON object representing the user's new
     * rights.
     *
     * @param int $userid The id of the user whose rights should be updated.
     */
    public function setUserRights($userid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/user/user/'.$userid.'/right';
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Adds a new user.
     *
     * Called when this component reveives an HTTP POST request to
     * /users(/).
     * The request body should contain a JSON object representing the new user.
     */
    public function addUser(){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/user';
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Edits a user.
     *
     * Called when this component receives an HTTP PUT request to
     * /users/$userid(/).
     * The request body should contain a JSON object representing the user's new
     * attributes
     *
     * @param int $userid The id or the username of the user that is being updated.
     */
    public function editUser($userid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/user/'.$userid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setBody(" ");
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Returns a list of all users.
     *
     * Called when this component receives an HTTP GET request to /user(/).
     */
    public function getUsers(){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/user/user';
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }

    /**
     * Returns a user.
     *
     * Called when this component receives an HTTP GET request to
     * /user/$userid(/).
     *
     * @param int $userid The id of the user that should be returned.
     */
    public function getUser($userid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/user/user/'.$userid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }
}


// get new config data from DB
$com = new CConfig(LUser::getPrefix());

// create a new instance of LUser class with the config data
if (!$com->used())
    new LUser($com->loadConfig());
?>