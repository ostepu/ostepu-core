<?php

require 'Slim/Slim.php';
include 'include/Assistants/request/createRequest.php';
	
\Slim\Slim::registerAutoloader();


class Group
{	
    $this->app = new \Slim\Slim();
    private $LController = "";				//Einlesen aus config.ini


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
    
    
    
    private function invitateInGroup($userid)
    {
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->getHeader();
        $URL = $LController.'/DB/user/'.$userid;
        $status = createPut($URL, $header, $req);
        $this->app->response->setStatus($status);

    }

    private function joinGroup()
    {
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->getHeader();
        $URL = $LController.'/DB/accept';
        $status = createPut($URL, $header, $req);
        $this->app->response->setStatus($status);

    }

    private function leaveGroup($userid)
    {
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->getHeader();
        $URL = $LController.'/DB/user/'.$userid.'/exit';
        $status = createPut($URL, $header, $req);
        $this->app->response->setStatus($status);

    }

    private function ejectFromGroup($userid)
    {
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->getHeader();
        $URL = $LController.'/DB/user/'.$userid.'/deletMember';
        $status = createPut($URL, $header, $req);
        $this->app->response->setStatus($status);

    }

    private function getGroup($userid, $sheetid);
    {  
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->getHeader();
        $URL = $LController.'/DB/user/'.$userid.'/exerciseSheet/'.$sheetid;

        $dbAnswer = createGet($URL, $header, $req);
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->setStatus(200);
        $this->app->response->setBody($dbAnswer);

    }
}
?>