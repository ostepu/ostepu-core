<?php

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * The ExerciseType class
 *
 * This class handles everything belongs to an ExerciseType
 */
class LExerciseType
{
    /**
     * Values that are required for communication with other components
     */
    private $_conf=null;
    private static $_prefix = "exercisetype";

    public static function getPrefix()
    {
        return LExerciseType::$_prefix;
    }
    public static function setPrefix($value)
    {
        LExerciseType::$_prefix = $value;
    }

    /**
     * Address of the Logic-Controller
     * dynamic set by CConf below
     */
    private $lURL = "";

    public function __construct($conf)
    {
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
         * When getting a POST
         * and there are the parameters "/course/1" for example,
         * the setPossibleTypes function is called
         */
        $this->app->post('/'.$this->getPrefix().'/course/:courseid(/)' ,
                            array($this, 'setPossibleTypes'));

        /**
         * When getting a DELETE
         * and there are the parameters "/course/1" for example,
         * the deletePossibleTypes function is called
         */
        $this->app->delete('/'.$this->getPrefix().'/course/:courseid(/)' ,
                            array($this, 'deletePossibleTypes'));

        /**
         * When getting a PUT
         * and there are the parameters "/course/1" for example,
         * the editExerciseType function is called
         */
        $this->app->put('/'.$this->getPrefix().'/course/:courseid(/)' ,
                            array($this, 'editPossibleTypes'));

        /**
         * runs the application
         */
        $this->app->run();
    }

    /**
     * Function to set the possible types of points to a course
     *
     * takes one argument and returns a Status-Code
     * @param $courseid an identifier of the course
     *        for which the types should be set
     */
    public function setPossibleTypes($courseid)
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseType/course/'.$courseid;
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Function to delete the possible types of points to a course
     *
     * takes one argument and returns a Status-Code
     * @param $courseid an identifier of the course
     *        for which the types should be set
     */
    public function deletePossibleTypes($courseid)
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseType/course/'.$courseid;
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Function to edit the possible types of points to a course
     *
     * takes one argument and returns a Status-Code
     * @param $courseid an identifier of the course
     *        for which the types should be set
     */
    public function editPossibleTypes($courseid)
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseType/course/'.$courseid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }
}

/**
 * get new Config-Datas from DB
 */
$com = new CConfig(LExerciseType::getPrefix());

/**
 * make a new instance of ExerciseType-Class with the Config-Datas
 */
if (!$com->used())
    new LExerciseType($com->loadConfig());
?>