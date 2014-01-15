<?php 

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * The Exercise class
 *
 * This class handles everything belongs to an Exercise
 */
class LExercise
{
    /**
     * Values that are required for communication with other components
     */
    private $_conf=null;
    private static $_prefix = "exercise";

    public static function getPrefix()
    {
        return LExercise::$_prefix;
    }
    public static function setPrefix($value)
    {
        LExercise::$_prefix = $value;
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
         * When getting a POST and there are no other parameters at the URL,
         * the addExercise function is called
         */
        $this->app->post(':data+', array($this, 'addExercise'));

        /**
         * When getting a GET 
         * and there are the parameters "/exercise/1" for example,
         * the getExercise function is called
         */
        $this->app->get('/exercise/:exerciseid',
                        array ($this, 'getExercise'));

        /**
         * When getting a DELETE
         * and there are the parameters "/exercise/1" for example,
         * the deleteExercise function is called
         */
        $this->app->delete('/exercise/:exerciseid',
                        array($this, 'deleteExercise'));

        /**
         * When getting a PUT
         * and there are the parameters "/exercise/1" for example,
         * the editExercise function is called
         */
        $this->app->put('/exercise/:exerciseid',
                        array($this, 'editExercise'));

        /**
         * runs the application
         */
        $this->app->run();
    }

    /**
     * Function to add a new exercise file to the filesystem and store
     * the exercise information in the database
     * takes one argument and returns a Status-Code
     * @param $data an string-array containing the URI-arguments (shout be empty)
     */
    public function addExercise($data){
        //check that $data is empty
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'file'});      //mit oder ohne "_"?
        //request to the filesystem to save the file
        $URL = $this->lURL.'/FS';
        $answer = Request::custom('POST', $URL, $header, $file);

        /**
         * if the file has been stored, the information
         * belongs to this exercise will stored in the database
         */
        if($answer['status'] < 300){ 
            $body->{'_file'} = json_decode($answer['content']);
            //request to database
            $URL = $this->lURL.'/DB';
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        } else {
            /**
             * if the file has not been stored response the (error-)status-code
             */
            $this->app->response->setStatus($answer['status']);
        }
    }

    /**
     * Function to get the informations belongs to an exercise
     * takes one argument and returns a body and a Status-Code
     * @param $exerciseid an identifier for the exercise
     */
    public function getExercise($exerciseid) {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;
        //request to database
        $answer = Request::custom('GET', $URL, $header, $body);
        //set response
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Function to delete an exercise file from the filesystem and then its
     * informations from the database
     * takes one argument and returns a Status-Code
     * @param $exerciseid an identifier for the exercise
     */
    public function deleteExercise($exerciseid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;
        $answer = Request::custum('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);

        /**
         * if the file information has been deleted, the file
         * will be delet from filesystem
         */
        if( $answer['status'] < 300){
            $URL = $this->lURL.'/FS/exercise/'.$exerciseid; 
            /** ####################################################################################
             * #####################################################################################
             * #####################################################################################
             * maybe there is an error if we send an id to delete a file. 
             * Possibly the database needs the URL to delete the file, 
             * then we have to get them from database, bevor deleting informations
             */
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }
    }

    /**
     * Function to edit the informations belongs to an exercise
     * takes one argument and returns a body and a Status-Code
     * @param $exerciseid an identifier for the exercise
     */
    public function editExercise($exerciseid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;
        //request to database
        $answer = Request::custom('PUT', $URL, $header, $body);
        //set response
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }
}
/**
 * get new Config-Datas from DB
 */
$com = new CConfig(LExercise::getPrefix());

/**
 * make a new instance of the Exercise-Class with the Config-Datas
 */
if (!$com->used())
    new LExercise($com->loadConfig());
?>