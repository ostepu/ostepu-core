<?php

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );
    
\Slim\Slim::registerAutoloader();

class LUser
{
    private $_conf=null;
    
    private static $_prefix = "user";
    
    public static function getPrefix()
    {
        return LUser::$_prefix;
    }
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
     * set the user rights
     * 
     * This function changes the right of the user.
     *
     * @return integer $status the status code
     */
    public function setUserRights($userid){        
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/user/user/'.$userid.'/right';
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);       
    }
    
    /**
     * add a new user
     *
     * This function adds a user.
     * The new user will be written in the database.
     *
     * @return integer $status the status code
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
     * edit a user
     *
     * This function edit a user.
     * The user will be overwritten in the database.
     */
    public function editUser($userid){        
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/user/course/'.$userid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);        
    }
        
     /**
     * return a list of all users
     * 
     * This function returns a list of all users from the database.
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
     * get a user
     * 
     * This function returns a user from the database.
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
 * get new Config-Datas from DB 
 */
$com = new CConfig(LUser::getPrefix());

/**
 * make a new instance of User-Class with the Config-Datas
 */
if (!$com->used())
    new LUser($com->loadConfig());
?>