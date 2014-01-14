<?php

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

class LUser
{
    private $_conf=null;

    /**
     * Prefix of this component
     */
    private static $_prefix = "user";

    /**
     * Get the Prefix of this component.
     *
     * @return mixed
     */
    public static function getPrefix()
    {
        return LUser::$_prefix;
    }

    /**
     * Sets the Prefix of this component.
     *
     * @param mixed $_prefix the _prefix
     */
    public static function setPrefix($value)
    {
        LUser::$_prefix = $value;
    }

    //the URL of the Logic-Controller
    private $lURL = "";             //Einlesen aus config

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct($conf)
    {
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->_conf = $conf;
        $this->query = array();

        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        $this->lURL = $this->query->getAddress();

        //SetUserRights
        $this->app->put('/user/:userid/right', array($this, 'setUserRights'));          //Adressen noch anpassen(kein .php;+ Compo-Namen

        //AddUser
        $this->app->post(':date+', array($this, 'addUser'));        //data+ soll leerer Parameter sein

        //EditUser
        $this->app->put('/user/:userid', array($this, 'editUser'));

        //GetUsers
        $this->app->get('/user', array($this, 'getUsers'));

        //GetUser
        $this->app->get('/user/:userid', array($this, 'getUser'));

        $this->app->run();
    }

    /**
     * Set a user's uer-rights.
     *
     * Called when this component receives an HTTP POST request to
     * /users/$userid/rights(/).
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
    public function addUser($userid){
        //Parameter abfangen wenn $data "nicht leer"
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
     * @param int $userid The id of the user that is beeing updated.
     */
    public function editUser($userid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/user/course/'.$userid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Returns a list of all users.
     *
     * Called when this component receives an HTTP GET request to /users(/).
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
     * /users/$userid(/).
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

/**
 * get new config data from DB
 */
$com = new CConfig(LUser::getPrefix());

/**
 * create a new instance of LUser class with the config data
 */
if (!$com->used())
    new LUser($com->loadConfig());
?>