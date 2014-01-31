<?php
/**
 * @file LExercisesheet.php Contains the LExercisesheet class
 *
 * @author Christian Elze
 * @author Martin Daute
 * @author Peter Koenig
 */

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LExercisesheet-Component
 */
class LExerciseSheet
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "exercisesheet";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LExerciseSheet::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LExerciseSheet::$_prefix = $value;
    }

    /**
     * @var string $lURL the URL of the logic-controller
     */ 
    private $lURL = ""; // readed out from config below

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct($conf)
    {
        // initialize slim
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->query = CConfig::getLink($conf->getLinks(),"controller");

        // initialize lURL
        $this->lURL = $this->query->getAddress();

        // POST AddExerciseSheet
        $this->app->post('/'.$this->getPrefix().'/course/:courseid(/)',
                        array($this, 'addExerciseSheet'));

        // PUT EditExerciseSheet
        $this->app->put('/'.$this->getPrefix().'/exercisesheet/:sheetid(/)',
                        array ($this, 'editExerciseSheet'));

        // GET GetExerciseSheetURL
        $this->app->get('/'.$this->getPrefix().'/exercisesheet/:sheetid/url(/)',
                        array($this, 'getExerciseSheetURL'));

        // GET GetExerciseSheet
        $this->app->get('/'.$this->getPrefix().'/exercisesheet/:sheetid(/)',
                        array($this, 'getExerciseSheet'));

        // DELETE DeleteExerciseSheet
        $this->app->delete('/'.$this->getPrefix().'/exercisesheet/:sheetid(/)',
                        array($this, 'deleteExerciseSheet'));

        // run Slim
        $this->app->run();
    }

    
    /**
     * Adds an exercise sheet.
     *
     * Called when this component receives an HTTP POST request to
     * /exercisesheet/course/$sourseid(/).
     * The request body should contain a JSON object representing the exercise
     * sheet's attributes.
     * Adds the sheet file, samplesolution file and attachment file to the filesystem first. At success
     * the information belongs to this exercisesheet will be stored in the database.
     * Prerequisite: The Files in the body exists.
     *
     * @param int $courseid The id of the course to which the exercisesheet should be added.
     */
     public function addExerciseSheet($courseid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody(), true);

        // get files from body
        $samplesolutionfile = json_encode($body['sampleSolution']);
        $sheetfile = json_encode($body['sheetFile']);
        $attachments = json_encode($body['attachments']);

        // set URLs for requests to filesystem
        $sampleURL = $this->lURL.'/FS/samplesolution/course/'.$courseid;
        $sheetURL = $this->lURL.'/FS/sheetfile/course/'.$courseid;
        $attachmentsURL = $this->lURL.'/FS/attachments/course/'.$courseid;

        // requests to filesystem
        $sampleanswer = Request::custom('POST', $sampleURL, $header, $samplesolutionfile);
        $sheetanswer = Request::custom('POST', $sheetURL, $header, $sheetfile);
        $attachmentsanswer = Request::custom('POST', $attachmentsURL, $header, $attachments);

        /*
         * if the files has been stored, the information
         * belongs to this exercisesheet will be stored in the database
         */
        if($sampleanswer['status'] >= 200 and $sampleanswer['status'] < 300
            and $sheetanswer['status'] >= 200 and $sheetanswer['status'] < 300
            and $attachmentsanswer['status'] >= 200 
            and $attachmentsanswer['status'] < 300){
                // set the request body for database
                $body['sampleSolution'] = $sampleanswer['content'];
                $body['sheetFile'] = $sheetanswer['content'];
                $body['attachments'] = $attachmentsanswer['content'];
                // request to database
                $URL = $this->lURL.'/DB/course/'.$courseid;
                $answer = Request::custom('POST', $URL, $header, json_encode($body));
                $this->app->response->setStatus($answer['status']);
        }
    }

    /**
     * Edits an exercise sheet.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercisesheet/exercisesheet/$sheetid(/).
     * The request body should contain a JSON object representing the exercise
     * sheet's attributes.
     * Adds the sheet file, samplesolution file and attachment file to the filesystem first. At success
     * the information belongs to this exercisesheet will be stored in the database.
     * Prerequisite: The Files in the body exists.  
     *
     * @param int $sheetid The id of the exercise sheet that is beeing updated.
     */
    public function editExerciseSheet($sheetid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        // get files from body
        $samplesolutionfile = json_encode($body['sampleSolution']);
        $sheetfile = json_encode($body['sheetFile']);
        $attachments = json_encode($body['attachments']);

        // set URLs for requests to filesystem
        $sampleURL = $this->lURL.'/FS/samplesolution/exercisesheet/'.$sheetid;
        $sheetURL = $this->lURL.'/FS/sheetfile/course/exercisesheet/'.$sheetid;
        $attachmentsURL = $this->lURL.'/FS/attachments/exercisesheet/'.$sheetid;

        // requests to filesystem
        $sampleanswer = Request::custom('POST', $sampleURL, $header, $samplesolutionfile);
        $sheetanswer = Request::custom('POST', $sheetURL, $header, $sheetfile);
        $attachmentsanswer = Request::custom('POST', $attachmentsURL, $header, $attachments);

        /*
         * if the files has been stored, the information
         * belongs to this exercisesheet will be stored in the database
         */
        if($sampleanswer['status'] >= 200 and $sampleanswer['status'] < 300
            and $sheetanswer['status'] >= 200 and $sheetanswer['status'] < 300
            and $attachmentsanswer['status'] >= 200
            and $attachmentsanswer['status'] < 300){
                // set the request body for database
                $body['sampleSolution'] = $sampleanswer['content'];
                $body['sheetFile'] = $sheetanswer['content'];
                $body['attachments'] = $attachmentsanswer['content'];
                // request to database
                $URL = $this->lURL.'/DB/exercisesheet/'.$sheetid;
                $answer = Request::custom('PUT', $URL, $header, json_encode($body));
                $this->app->response->setStatus($answer['status']);
        }
    }

    /**
     * Returns the URL to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisesheet/exercisesheet/$sheetid/url(/).
     *
     * @param int $sheetid The id of the exercise sheet the returned URL belongs to.
     */
    public function getExerciseSheetURL($sheetid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisesheet/'.$sheetid.'/url';
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Returns an exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisesheet/exercisesheet/$sheetid(/).
     *
     * @param int $sheetid The id of the exercise sheet that should be returned.
     */
    public function getExerciseSheet($sheetid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisesheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, $body);
        var_dump($answer);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Deletes an exercise sheet. 
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisesheet/exercisesheet/$sheetid(/).
     * Deletes the exercise sheet information from the database first. At success
     * the file belongs to this exercise will be deleted from the filesystem.
     *
     * @param int $esid The id of the exercise sheet that is being deleted.
     */
    public function deleteExerciseSheet($sheetid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercisesheet/'.$sheetid;
        // request to database
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);

        /*
         * if the file information has been deleted, the file
         * will being deleted from filesystem
         */
        $fileObject = json_decode($answer['content'], true);
        // if address-field exists, read it out
        if (isset($fileObject['address']) and $answer['status'] >= 200 and $answer['status'] < 300){
            $fileAddress = $fileObject['address'];
            // request to filesystem
            $URL = $this->lURL.'/FS/file/'.$fileAddress;
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }
    }
}

// get new config data from DB
$com = new CConfig(LExerciseSheet::getPrefix());

// create a new instance of LExercisesheet class with the config data
if (!$com->used())
    new LExerciseSheet($com->loadConfig());
?>