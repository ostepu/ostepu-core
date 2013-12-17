<?php

require 'Slim/Slim.php';
include 'include/Assistants/request/createRequest.php';
	
\Slim\Slim::registerAutoloader();

class User
{
    $this->app = new \Slim\Slim();
    $this->app->response->headers->set('Content-Type', 'application/json');
    //the URL of the Logic-Controller
    private $lURL = "";				//Einlesen aus config
    
    //SetUserRights
    $this->app->put('/user/:userid/right', array($this, 'setUserRights'));

    //AddUser
    $this->app->post(':date+', array($this, 'addUser'));        //data+ soll leerer Parameter sein

    //EditUser
    $this->app->put('/user/:userid', array($this, 'editUser'));

    //GetUsers
    $this->app->put('/user', array($this, 'getUsers'));

    //GetUser
    $this->app->put('/user/:userid', array($this, 'getUser'));
    
    /**
     *set the user rights
     * 
     * @param (param)
     */
    private function setUserRights($userid){        
        $body = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->getHeader();
        $URL = $lURL.'/DB/user/user/'.$userid.'/right';
        $status = createPut($URL, $header, $body);
        $this->app->response->setStatus($status);   
    }
    
     /**
     * add a new user
     * 
     * @param (param)
     */
    private function addUser($userid){
        //Parameter abfangen wenn $data "nicht leer"
        $body = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->getHeader();
        $URL = $lURL.'/DB/user';
        $status = createPost($URL, $header, $body);
        $this->app->response->setStatus($status);       
    }
    
     /**
     * edit a user
     * 
     * @param (param)
     */
    private function editUser($userid){        
        $body = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->getHeader();
        $URL = $lURL.'/DB/user/course/'.$userid;
        $status = createPut($URL, $header, $body);
        $this->app->response->setStatus($status);      
    }
        
     /**
     * return a list of all users
     * 
     * @param (param)
     */
    private function getUsers(){        
        $body = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->getHeader();
        $URL = $lURL.'/DB/user/user';
        $dbAnswer = createGet($URL, $header, $body);                    //createGet(...).getBody?
        $this->app->response->setStatus(200);                           //status aus createGet auslesen!
        $this->app->response->setBody($dbAnswer);        
    }
        
     /**
     * get a user
     * 
     * @param (param)
     */
    private function getUser(){        
        $body = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->getHeader();
        $URL = $lURL.'/DB/user/user'.$userid;
        $dbAnswer = createGet($URL, $header, $body);                    //createGet(...).getBody?
        $this->app->response->setStatus(200);                           //status aus createGet auslesen!
        $this->app->response->setBody($dbAnswer);    
    }; 
}
?>