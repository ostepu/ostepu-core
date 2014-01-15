<?php

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * The Group class
 *
 * This class handles everything belongs to a Group
 */
class LGroup
{
    /**
     * Values that are required for communication with other components
     */
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

    /**
     * Address of the Logic-Controller
     * dynamic set by CConf below
     */
    private $lURL = "";

    public function __construct($conf){
        /**
         * Initialise the Slim-Framework
         */
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        /**
         * Get the URL of the Logic-Controller of the CConf.json file and set
         * the $lURL variable
         */
        $this->_conf = $conf;
        $this->query = array();
        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        $this->lURL = $this->query->getAddress();


        /**
         * When getting a PUT
         * and there are the parameters "/user/1" for example,
         * the inviteInGroup function is called
         */
        $this->app->put('/user/:userid',
                            array($this, 'inviteInGroup'));

        /**
         * When getting a PUT
         * and there are the parameter "/accept",
         * the joinGroup function is called
         */
        $this->app->put('/accept', array($this, 'joinGroup'));

        /**
         * When getting a PUT 
         * and there are the parameters "/user/1/leave" for example,
         * the leaveGroup function is called
         */
        $this->app->put('/user/:userid/leave',
                            array($this, 'leaveGroup'));

        /**
         * When getting a PUT
         * and there are the parameters "/user/1/deleteMember" for example,
         * the ejectFromGroup function is called
         */
        $this->app->put('/user/:userid/deleteMember',
                            array($this, 'ejectFromGroup'));

        /**
         * When getting a GET
         * and there are the parameters "/user/1/exercisesheet/2" for example,
         * the getGroup function is called
         */
        $this->app->get('/user/:userid/exercisesheet/:sheetid',
                        array($this, 'getGroup'));

        /**
         * runs the application
         */
        $this->app->run();
    }

    /**
     * Function to invite an other user to the group
     * takes one argument and returns a Status-Code
     * @param $userid an identifier of the user who invite another
     */
    public function inviteInGroup($userid){
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->headers->all();
        $URL = $this->lURL.'/DB/user/'.$userid;
        $answer = Request::custom('PUT', $URL, $header, $req);
        $this->app->response->setStatus($answer['status']);

    }

    /**
     * Function to accept an invitation and join this group
     * takes no argument and returns a Status-Code
     */
    public function joinGroup(){
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->headers->all();
        $URL = $this->lURL.'/DB/accept';
        $answer = Request::custom('PUT', $URL, $header, $req);
        $this->app->response->setStatus($answer['status']);

    }

    /**
     * Function to leave a group
     * takes one argument and returns a Status-Code
     * @param $userid an identifier of the user who want to leave the group
     */
    public function leaveGroup($userid){
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->headers->all();
        $URL = $this->lURL.'/DB/user/'.$userid.'/leave';
        $answer = Request::custom('PUT', $URL, $header, $req);
        $this->app->response->setStatus($answer['status']);

    }

    /**
     * Function to eject another user from the group
     * takes one argument and returns a Status-Code
     * @param $userid an identifier of the user who want to eject another
     */
    public function ejectFromGroup($userid){
        $req = \Slim\Slim::getInstance()->request()->getBody();
        $header = \Slim\Slim::getInstance()->request()->headers->all();
        $URL = $this->lURL.'/DB/user/'.$userid.'/deletMember';
        $answer = Request::custom('PUT', $URL, $header, $req);
        $this->app->response->setStatus($answer['status']);

    }

    /**
     * Function to get all users who are member in this group
     * takes two arguments and returns a Status-Code
     * @param $userid an identifier of the user
     * @param $sheetid an identifier of the exercisesheet
     */
    public function getGroup($userid, $sheetid){
        //$req = \Slim\Slim::getInstance()->request()->getBody();
        $req = "";
        $header = \Slim\Slim::getInstance()->request()->headers->all();
        $URL = $this->lURL.'/DB/user/'.$userid.'/exerciseSheet/'.$sheetid;
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