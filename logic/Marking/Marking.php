<?php 

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';
include_once( 'include/CConfig.php' ); 

\Slim\Slim::registerAutoloader();
    
class Marking
{    
    private $_conf=null;
    
    private static $_prefix = "marking";
    
    public static function getPrefix()
    {
        return Marking::$_prefix;
    }
    public static function setPrefix($value)
    {
        Marking::$_prefix = $value;
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
        
        //AddMarking
        $this->app->post('/exercise/:exerciseid/tutor/:tutorid', 
                        array($this, 'addMarking'));
        
        //GetMarkingURL
        $this->app->get('/marking/:markingid', 
                        array ($this, 'getMarkingURL'));
        
        //DeleteMarking
        $this->app->delete('/marking/:markingid', 
                        array($this, 'deleteMarking'));
                        
        //EditMarking
        $this->app->put('/marking/:markingid/tutor/:tutorid', 
                        array($this, 'editMarking'));
                        
        //EditMarkingState
        $this->app->put('/marking/:markingid', 
                        array($this, 'editMarkingState'));
        
        $this->app->run();
    }    
    
    public function addMarking($exerciseid, $tutorid){     
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'_file'});      //mit oder ohne "_"?
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS/exercise/'.$exerciseid.'/tutor/'.$tutorid;
        $answer = Request::custom('POST', $URL, $header, $file);
        
        if($answer['status'] == 200){ //nur, wenn file tatsächlich im FS gespeichert wurde .. welcher StatusCode-Bereich soll gelten?
            $body->{'_file'} = json_decode($answer['content']);
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB/exercise/'.$exerciseid.'/tutor/'.$tutorid;
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }
    
    public function getMarkingURL($markingid) {        
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/marking/'.$markingid;        
        $answer = Request::custom('GET', $URL, $header, $body);  
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    public function deleteMarking($markingid){       
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/marking/'.$markingid;        
        $answer = Request::custum('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        
        if( $answer['status'] == 200){ //nur, wenn file tatsächlich aus DB gelöscht wurde
            $URL = $this->lURL.'/FS/marking/'.$markingid; 
            /*
             * eigentlich url der zu löschenden datei schicken und nicht die id???????????????
             */
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }             
    }
    
    public function editMarking($markingid, $tutorid){  
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'_file'});      //mit oder ohne "_"?
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS/marking/'.$markingid.'/tutor/'.$tutorid;
        $answer = Request::custom('PUT', $URL, $header, $file);
        
        if($answer['status'] == 200){ //nur, wenn file tatsächlich im FS gespeichert wurde .. welcher StatusCode-Bereich soll gelten?
            $body->{'_file'} = json_decode($answer['content']);
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB/marking/'.$markingid.'/tutor/'.$tutorid;
            $answer = Request::custom('PUT', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }
    
    public function editMarkingState($markingid){  
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/marking/'.$markingid;        
        $answer = Request::custom('PUT', $URL, $header, $body);  
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }   
}

if (!$com->used())
    new Marking($com->loadConfig());
?>