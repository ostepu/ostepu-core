<?php 

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * The ExerciseSheet class
 *
 * This class handles everything belongs to an ExerciseSheet
 */
class LExerciseSheet
{
    /**
     * Values that are required for communication with other components
     */
    private $_conf=null;
    private static $_prefix = "exercisesheet";

    public static function getPrefix()
    {
        return LExerciseSheet::$_prefix;
    }
    public static function setPrefix($value)
    {
        LExerciseSheet::$_prefix = $value;
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
         * the addExerciseSheet function is called
         */
        $this->app->post('/course/:courseid', array($this, 'addExerciseSheet'));        //Adressen noch anpassen (Parameter mit Compo-Namen)

        /**
         * When getting a PUT
         * and there are the parameters "/exercisesheet/1" for example,
         * the aditExerciseSheet function is called
         */
        $this->app->put('/exercisesheet/:sheetid',
                        array ($this, 'editExerciseSheet'));

        /**
         * When getting a GET 
         * and there are the parameters "/exercisesheet/1/url" for example,
         * the getExerciseSheetURL function is called
         */
        $this->app->get('/exercisesheet/:sheetid/url',
                        array($this, 'getExerciseSheetURL'));

        /**
         * When getting a GET
         * and there are the parameters "/exercisesheet/1" for example,
         * the getExerciseSheet function is called
         */
        $this->app->get('/exercisesheet/:sheetid',
                        array($this, 'getExerciseSheet'));

        /**
         * When getting a DELETE
         * and there are the parameters "/exercisesheet/1" for example,
         * the deleteExerciseSheet function is called
         */
        $this->app->delete('/exercisesheet/:sheetid',
                        array($this, 'deleteExerciseSheet'));

        /**
         * runs the application
         */
        $this->app->run();
    }

    //!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Zwei Files im Body
    public function addExerciseSheet($courseid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $samplesolutionfile = json_encode($body->{'_sampleSolution'});      //SampleSolutionfile; mit oder ohne "_"?
        $sheetfile = json_encode($body->{'_sheetFile'});      //SheetFile; mit oder ohne "_"?
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS';
        $sampleanswer = Request::custom('POST', $URL, $header, $samplesolutionfile);
        $sheetanswer = Request::custom('POST', $URL, $header, $sheetfile);

        if($sampleanswer['status'] == 200 and $sheetanswer['status'] == 200){ //nur, wenn Files tatsächlich im FS gespeichert wurden
            $body->{'_file'} = $answer['content'];      //hier zwei Files
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB';
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }

    //!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Zwei Files im Body
    public function editExerciseSheet($sheetid){
    
    }

    public function getExerciseSheetURL($sheetid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisesheet/'.$exercisesheetid.'/url';
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    public function getExerciseSheet($sheetid, $userid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseSheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    public function deleteExerciseSheet($sheetid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisesheet/'.$sheetid;
        $answer = Request::custum('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);

        /**
         * if DB-Request was succsessfull the file also gets removed from FS 
         * otherwise returns the Status-Code from DB 
         */
        $fileObject = json_decode($answer['content']);
        //if address-field exists, read it out
        if (isset($fileObject->{'address'})){
            $fileAddress = $fileObject->{'address'};
        }
        if( $answer['status'] < 300){
            $URL = $this->lURL.'/FS/'.$fileAddress;
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }
    }
}
/**
 * get new Config-Datas from DB
 */
$com = new CConfig(LExerciseSheet::getPrefix());

/**
 * make a new instance of ExerciseSheet-Class with the Config-Datas
 */
if (!$com->used())
    new LExerciseSheet($com->loadConfig());
?>