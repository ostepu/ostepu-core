<?php

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';
    
\Slim\Slim::registerAutoloader();


class Group
{    
    private $LController = "";                //Einlesen aus config.ini

    public function __construct()
    {    
        $this->app = new \Slim\Slim();
        //InvitateInGroup
        $this->app->put('/user/:userid', 
                            array($this, 'invitateInGroup'));
        
        //JoinGroup
        $this->app->put('/accept', array($this, 'joinGroup'));
        
        //LeaveGroup    
        $this->app->put('/user/:userid/exit', 
                            array($this, 'leaveGroup'));
        
        //EjectFromGroup    
        $this->app->put('/user/:userid/deleteMember', 
                            array($this, 'ejectFromGroup'));
        
        //GetGroup    
        $this->app->get('/user/:userid/exerciseSheet/:sheetid', 
                        array($this, 'getGroup'));
                        
        $this->app->run();
    }    
    
    
    public function invitateInGroup($userid)
    {
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->headers->all();
        $URL = $this->LController.'/DB/user/'.$userid;
        $answer = Request::custom('PUT', $URL, $header, $req);
        $this->app->response->setStatus($answer['status']);

    }

    public function joinGroup()
    {
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->headers->all();
        $URL = $this->LController.'/DB/accept';
        $answer = Request::custom('PUT', $URL, $header, $req);
        $this->app->response->setStatus($answer['status']);

    }

    public function leaveGroup($userid)
    {
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->headers->all();
        $URL = $this->LController.'/DB/user/'.$userid.'/exit';
        $answer = Request::custom('PUT', $URL, $header, $req);
        $this->app->response->setStatus($answer['status']);

    }

    public function ejectFromGroup($userid)
    {
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->headers->all();
        $URL = $this->LController.'/DB/user/'.$userid.'/deletMember';
        $answer = Request::custom('PUT', $URL, $header, $req);
        $this->app->response->setStatus($answer['status']);

    }

    public function getGroup($userid, $sheetid)
    {  
        //$req = \Slim\Slim::getInstance()->request()->getBody();
        $req = "";
        $header = \Slim\Slim::getInstance()->request()->headers->all();
        $URL = $this->LController.'/DB/user/'.$userid.'/exerciseSheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, $req);
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);

    }
}

new Group();
?>