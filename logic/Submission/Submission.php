<?php 

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

class Submission
{    
    private $_conf=null;
    
    private static $_prefix = "submission";
    
    public static function getPrefix()
    {
        return Submission::$_prefix;
    }
    public static function setPrefix($value)
    {
        Submission::$_prefix = $value;
    }
    private $lURL = ""; //aus config lesen
    
    public function __construct($conf)
    {    
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->_conf = $conf;
        $this->query = array();
        
        $this->query = array(CConfig::getLink($conf->getLinks(),"controller"))
        $this->lURL = querry['address'];
        
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
    
    public function addSubmission($data){
        //Parameter abfangen wenn $data "nicht leer"        
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'_file'});      //mit oder ohne "_"?
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS';
        $answer = Request::custom('POST', $URL, $header, $file);
        
        if($answer['status'] == 200){ //nur, wenn file tatsächlich im FS gespeichert wurde
            $body->{'_file'} = $answer['content'];
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB';
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }
    
    public function editSubmissionState($submissionid) {    
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/submission/'.$submissionid;        
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);    
    }

    public function deleteSubmission($submissionid){       
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/submission/'.$submissionid;        
        $answer = Request::custum('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        
        if( $answer['status'] == 200){ //nur, wenn file tatsächlich aus DB gelöscht wurde
            $URL = $this->lURL.'/FS/submission/'.$submissionid; 
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }             
    }
    
    public function loadSubmissionAsZip($sheetid, $userid){       //Annahme: ZipURL in DB abrufbar  
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseSheet/'.$sheetid.'/user/'.$userid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }
    
    public function showSubmissionsHistory($sheetid, $userid){      
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseSheet/'.$sheetid.'/user/'.$userid.'/history';
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }
    
    public function getSubmissionURL($submissionid){        
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/submission/'.$submissionid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }
}

if (!$com->used())
    new Submission($com->loadConfig());
?>