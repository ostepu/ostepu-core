<?php

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

class LSubmission
{
    private $_conf=null;

    /**
     * Prefix of this component
     */
    private static $_prefix = "submission";

    /**
     * Gets the Prefix of this component.
     *
     * @return mixed
     */
    public static function getPrefix()
    {
        return LSubmission::$_prefix;
    }

    /**
     * Sets the Prefix of this component.
     *
     * @param mixed $_prefix the _prefix
     */
    public static function setPrefix($value)
    {
        LSubmission::$_prefix = $value;
    }
    private $lURL = ""; //aus config lesen

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct($conf)
    {
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->_conf = $conf;
        $this->query = array();

        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        $this->lURL = $this->query->getAddress();

        //AddSubmission
        $this->app->post(':data+', array($this, 'addSubmission'));

        //EditSubmissionState
        $this->app->put('/submission/:submissionid',
                        array ($this, 'editSubmissionState'));

        //deleteSubmission
        $this->app->delete('/submission/:submissionid',
                        array($this, 'deleteSubmission'));

        //LoadSubmissionAsZip
        $this->app->get('/exerciseSheet/:sheetid/user/:userid',
                        array($this, 'loadSubmissionAsZip'));

        //ShowSubmissionsHistory
        $this->app->get('/exerciseSheet/:sheetid/user/:userid/history',
                        array($this, 'showSubmissionsHistory'));

        //GetSubmissionURL
        $this->app->get('/submission/:submissionid',
                        array($this, 'getSubmissionURL'));

        $this->app->run();
    }

    /**
     * Adds a user's submission to the database and filesystem
     *
     * Called then this component receives an HTTP POST request to
     * /submissions(/)
     * The request body should contain a JSON object representing a submission.
     */
    public function addSubmission($data){
        //Parameter abfangen wenn $data "nicht leer"
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'_file'});      //mit oder ohne "_"?
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS';
        $answer = Request::custom('POST', $URL, $header, $file);

        if($answer['status'] == 200){ //nur, wenn file tatsaechlich im FS gespeichert wurde
            $body->{'_file'} = $answer['content'];
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB';
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }

    /**
     * Edits a submission's state.
     *
     * Called when this component receives an HTTP PUT request to
     * /submissions/$submissionid(/).
     * The request body should contain a JSON object representing a submission.
     *
     * @param int $submissionid The id of the submission that is beeing updated.
     */
    public function editSubmissionState($submissionid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/submission/'.$submissionid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Deletes a submission.
     *
     * Called when this component revceives an HTTP DELETE request to
     * /submissions/$submissionid(/).
     *
     * @param int $submissionid The submission that is beeing deleted.
     *
     * @note Files are completely removed from the system. This is not intended
     * behaviour as this prevents lecturers and admins from seeing them in the
     * user's submission history.
     */
    public function deleteSubmission($submissionid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/submission/'.$submissionid;
        $answer = Request::custom('DELETE', $URL, $header, $body);
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

    /**
     * Loads all submissions as a zip file.
     *
     * Called when this component receives an HTTP GET request to
     * /submissions/exercisesheet/$sheetid/user/$userid.
     *
     * @param int $sheetid The id of the sheet of which the submissions should
     * be zipped.
     * @param int $userid The id of the user whose submissions should be zipped.
     */
    public function loadSubmissionAsZip($sheetid, $userid){       //Annahme: ZipURL in DB abrufbar
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseSheet/'.$sheetid.'/user/'.$userid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Loads the submission history of a user.
     *
     * Called when this component receives an HTTP GET request to
     * /submissions/exercisesheet/$sheetid/user/$userid/history.
     *
     * @param int $sheetid The id of the sheet of which the submissions should
     * be loaded.
     * @param int $userid The id of the user whose submissions should be loaded.
     */
    public function showSubmissionsHistory($sheetid, $userid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseSheet/'.$sheetid.'/user/'.$userid.'/history';
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Loads a specific submission.
     *
     * Called when this component receives an HTTP GET request to
     * /submissions/$submissionid(/).
     *
     * @param int $submissionid The id of the requested submission.
     */
    public function getSubmissionURL($submissionid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/submission/'.$submissionid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }
}

/**
 * get new Config-Datas from DB
 */
$com = new CConfig(LSubmission::getPrefix());

/**
 * make a new instance of Submission-Class with the Config-Datas
 */
if (!$com->used())
    new LSubmission($com->loadConfig());
?>