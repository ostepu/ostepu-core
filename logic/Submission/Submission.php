<?php 

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

class LSubmission
{    
    private $_conf=null;
    
    private static $_prefix = "submission";
    
    public static function getPrefix()
    {
        return LSubmission::$_prefix;
    }
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
        
        $this->query = array(CConfig::getLink($conf->getLinks(),"controller"));
        $this->lURL = $querry['address'];
        
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
     * add a new submission
     *
     * This function adds a submission of an exercise.
     * First,the submission will be written in the file system.
     * If the status of this operation is right, then the informations
     * of the submission will be added in the database.
     *
     * @return integer $status the status code
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
     * edit a submission of an exercise
     *
     * This function overwrites a submission of an exercise.
     * First,the submission will be written in the file system.
     * If the status of this operation is right, then the informations
     * of the submission will be overwritten in the database.
     */
    public function editSubmissionState($submissionid){    
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/submission/'.$submissionid;        
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);    
    }

    /**
     * delete a submission
     * 
     * First, this function deletes the informations of a submission
     * in the database. If the status of this operation is right,
     * then the submission will be deleted in the file system.
     */
    public function deleteSubmission($submissionid){       
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/submission/'.$submissionid;        
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        
        if( $answer['status'] == 200){ //nur, wenn file tatsaechlich aus DB geloescht wurde
            $URL = $this->lURL.'/FS/submission/'.$submissionid; 
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }             
    }

    /**
     * get the URL of a zip file with the submission
     * 
     * This function returns the URL of a zip file with the submission
     * for download this.
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
     * get the history of submission uploads
     * 
     * This function returns the history of submission.
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
     * get the URL of a submission
     * 
     * This function returns the URL of a submission for download this.
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