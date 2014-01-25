<?php

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

class LController
{
    /**
     *Values needed for conversation with other components
     */
    private $_conf=null;

    private static $_prefix = "";

    public static function getPrefix()
    {
        return LController::$_prefix;
    }
    public static function setPrefix($value)
    {
        LController::$_prefix = $value;
    }

    public function __construct($conf)
    {
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        $this->_conf = $conf;
        $this->query = array();

        $this->query = $conf->getLinks();

        $this->app->map('/:string+', array($this, 'chooseDestination'))
                    ->via('POST', 'GET', 'PUT', 'DELETE');

        $this->app->run();
    }

    /**
     * pick out the config.ini file and store all addresses in $links
     *
     * @param (param)
     */

    public function chooseDestination($string){
        $method = $this->app->request->getMethod();
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();

        if ($string[0] == "DB") {
            unset($string[0]);
            $URI = CConfig::getLink($this->query, "database")->getAddress();//DB-URL;                                                            //URI ergänzen
            foreach ($string as $str) {
                $URI = $URI.'/'.$str;
            }
            $answer = Request::custom($method, $URI, $header, $body);
            $this->app->response->setBody($answer['content']);
        } elseif ($string[0] == "FS") {
            unset($string[0]);
            $URI = CConfig::getLink($this->query, "filesystem")->getAddress();//FS-URL;                                                            //URI ergänzen
            foreach ($string as $str) {
                $URI = $URI.'/'.$str;
            }
            $answer = Request::custom($method, $URI, $header, $body);
            $this->app->response->setBody($answer['content']);
        } else {
            $URI = $this->getLink($this->query,$string[0]);//L-URL
            foreach ($string as $str) {
                $URI = $URI.'/'.$str;
            }
            $answer = Request::custom($method, $URI, $header, $body);
            $this->app->response->setBody($answer['content']);
            $this->app->response->setStatus($answer['status']);
        }
    }
    /**
     * Funktion to select the right Link from a Linkarray by the prefix
     * Taking two arguments and returning a Link as string
     * @param $arrayOfLinks an array of Link-Objects
     * @param $prefix a string identifies the Component you wants to link to
     */
    public function getLink($arrayOfLinks, $prefix){

        foreach ($arrayOfLinks as $linkObj){
            if ($linkObj->getPrefix() == $prefix){
                return $linkObj->getAddress();
            }
        }
    }
}

/**
 * get new Config-Datas from DB
 */
$com = new CConfig(LController::getPrefix());

/**
 * make a new instance of Course-Class with the Config-Datas
 */
if (!$com->used())
    new LController($com->loadConfig());
?>