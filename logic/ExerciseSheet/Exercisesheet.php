<?php 

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * The ExerciseSheet class
 *
 * This class handles everything belongs to an ExerciseSheet.
 */
class LExerciseSheet
{
    /**
     * Values that are required for communication with other components.
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
     * dynamic set by CConf below.
     */
    private $lURL = "";

    public function __construct($conf)
    {
        /**
         * Initialise the Slim-Framework.
         */
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        /**
         * Get the URL of the Logic-Controller of the CConf.json file and set
         * the $lURL variable.
         */
        $this->_conf = $conf;
        $this->query = array();
        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        $this->lURL = $this->query->getAddress();

        /**
         * When getting a POST
         * and there are the parameters "/course/1" for example,
         * the addExerciseSheet function is called.
         */
        $this->app->post('/'.$this->_prefix.'/course/:courseid(/)', array($this, 'addExerciseSheet'));

        /**
         * When getting a PUT
         * and there are the parameters "/exercisesheet/1" for example,
         * the aditExerciseSheet function is called.
         */
        $this->app->put('/'.$this->_prefix.'/exercisesheet/:sheetid(/)',
                        array ($this, 'editExerciseSheet'));

        /**
         * When getting a GET 
         * and there are the parameters "/exercisesheet/1/url" for example,
         * the getExerciseSheetURL function is called.
         */
        $this->app->get('/'.$this->_prefix.'/exercisesheet/:sheetid/url(/)',
                        array($this, 'getExerciseSheetURL'));

        /**
         * When getting a GET
         * and there are the parameters "/exercisesheet/1" for example,
         * the getExerciseSheet function is called.
         */
        $this->app->get('/'.$this->_prefix.'/exercisesheet/:sheetid(/)',
                        array($this, 'getExerciseSheet'));

        /**
         * When getting a DELETE
         * and there are the parameters "/exercisesheet/1" for example,
         * the deleteExerciseSheet function is called.
         */
        $this->app->delete('/'.$this->_prefix.'/exercisesheet/:sheetid(/)',
                        array($this, 'deleteExerciseSheet'));

        /**
         * Runs the application.
         */
        $this->app->run();
    }

    /**
     * Prerequisite: Files in body.
     */
     public function addExerciseSheet($courseid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $samplesolutionfile = json_encode($body->{'sampleSolution'});
        $sheetfile = json_encode($body->{'sheetFile'});
        $attachments = json_encode($body->{'attachments'});
        /**
         *Request to file system.
         */
        $sampleURL = $this->lURL.'/FS/samplesolution/course/'.$courseid;
        $sheetURL = $this->lURL.'/FS/sheetfile/course/'.$courseid;
        $attachmentsURL = $this->lURL.'/FS/attachments/course/'.$courseid;
        $sampleanswer = Request::custom('POST', $sampleURL, $header, $samplesolutionfile);
        $sheetanswer = Request::custom('POST', $sheetURL, $header, $sheetfile);
        $attachmentsanswer = Request::custom('POST', $attachmentsURL, $header, $attachments);

        /**
         * If the files saved in the file system... .
         */
        if($sampleanswer['status'] == 200 and $sheetanswer['status'] == 200
        and $attachmentsanswer['status'] == 200){
            $body->{'sampleSolution'} = $sampleanswer['content'];
            $body->{'sheetFile'} = $sheetanswer['content'];
            $body->{'attachments'} = $attachmentsanswer['content'];
            /**
            *Request to database.
            */
            $URL = $this->lURL.'/DB/course/'.$courseid;
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }

    /**
     * Prerequisite: Files in body.
     */
    public function editExerciseSheet($sheetid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $samplesolutionfile = json_encode($body->{'sampleSolution'});
        $sheetfile = json_encode($body->{'sheetFile'});
        $attachments = json_encode($body->{'attachments'});
        /**
         *Request to file system.
         */
        $sampleURL = $this->lURL.'/FS/samplesolution/exercisesheet/'.$sheetid;
        $sheetURL = $this->lURL.'/FS/sheetfile/course/exercisesheet/'.$sheetid;
        $attachmentsURL = $this->lURL.'/FS/attachments/exercisesheet/'.$sheetid;
        $sampleanswer = Request::custom('POST', $sampleURL, $header, $samplesolutionfile);
        $sheetanswer = Request::custom('POST', $sheetURL, $header, $sheetfile);
        $attachmentsanswer = Request::custom('POST', $attachmentsURL, $header, $attachments);

        /**
         * If the files saved in the file system... .
         */
        if($sampleanswer['status'] == 200 and $sheetanswer['status'] == 200
        and $attachmentsanswer['status'] == 200){
            $body->{'sampleSolution'} = $sampleanswer['content'];
            $body->{'sheetFile'} = $sheetanswer['content'];
            $body->{'attachments'} = $attachmentsanswer['content'];
            /**
            *Request to database.
            */
            $URL = $this->lURL.'/DB/exercisesheet/'.$sheetid;
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }

    public function getExerciseSheetURL($sheetid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisesheet/'.$sheetid.'/url';
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    public function getExerciseSheet($sheetid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisesheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    public function deleteExerciseSheet($sheetid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisesheet/'.$sheetid;
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);

        /**
         * If the request of the database was successful the file also gets
         * removed from file system otherwise returns the Status-Code from 
         * database.
         */
        $fileObject = json_decode($answer['content']);
        /**
         * If address-field exists, read it out.
         */
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
 * Get new Config-Datas from database.
 */
$com = new CConfig(LExerciseSheet::getPrefix());

/**
 * Make a new instance of ExerciseSheet-class with the config-datas.
 */
if (!$com->used())
    new LExerciseSheet($com->loadConfig());
?>