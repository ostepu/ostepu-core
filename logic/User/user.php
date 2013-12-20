<?php

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';    
	
\Slim\Slim::registerAutoloader();

class User
{
    //the URL of the Logic-Controller
    private $lURL = "";				//Einlesen aus config
    
    public function __construct()
    {    
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
    
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
     *set the user rights
     * 
     * @param (param)
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
     * @param (param)
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
     * @param (param)
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
     * @param (param)
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
     * @param (param)
     */
    public function getUser(){        
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/user/user'.$userid;
        $answer = Request::custom('GET', $URL, $header, $body);            
        $this->app->response->setStatus($answer['status']);                
        $this->app->response->setBody($answer['content']);    
    }
}

new User();
?>