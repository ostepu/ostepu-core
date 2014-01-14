<?php

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();


class LGroup
{
    private $_conf=null;

    private static $_prefix = "group";

    public static function getPrefix()
    {
        return LGroup::$_prefix;
    }
    public static function setPrefix($value)
    {
        LGroup::$_prefix = $value;
    }

    private $LController = "";              //Einlesen aus config.ini

    public function __construct($conf)
    {
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->_conf = $conf;
        $this->query = array();

        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        $this->lURL = $this->query->getAddress();


        //InviteInGroup
        $this->app->put('/user/:userid',
                            array($this, 'inviteInGroup'));

        //JoinGroup
        $this->app->put('/accept', array($this, 'joinGroup'));

        //LeaveGroup
        $this->app->put('/user/:userid/leave',
                            array($this, 'leaveGroup'));

        //EjectFromGroup
        $this->app->put('/user/:userid/deleteMember',
                            array($this, 'ejectFromGroup'));

        //GetGroup
        $this->app->get('/user/:userid/exerciseSheet/:sheetid',
                        array($this, 'getGroup'));

        $this->app->run();
    }


    public function inviteInGroup($userid)
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
        $URL = $this->LController.'/DB/user/'.$userid.'/leave';
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

/**
 * get new Config-Datas from DB
 */
$com = new CConfig(LGroup::getPrefix());

/**
 * make a new instance of Group-Class with the Config-Datas
 */
if (!$com->used())
    new LGroup($com->loadConfig());
?>