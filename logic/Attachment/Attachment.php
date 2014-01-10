<?php 

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();
    
class Attachment
{   
    private $_conf=null;
    
    private static $_prefix = "attachment";
    
    public static function getPrefix()
    {
        return Attachment::$_prefix;
    }
    public static function setPrefix($value)
    {
        Attachment::$_prefix = $value;
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
        
        //AddAttachment
        $this->app->post('/course/:courseid/exercisesheet/:exercisesheetid', 
                        array($this, 'addAttachment'));
        
        //GetAttachmentURL
        $this->app->get('/file/:fileid', 
                        array ($this, 'getAttachmentURL'));
        
        //DeleteAttachment
        $this->app->delete('/file/:fileid', 
                        array($this, 'deleteAttachment'));
                        
        //EditAttachment
        $this->app->put('/file/:fileid', 
                        array($this, 'editAttachment'));
        
        $this->app->run();
    }    
    
    public function addAttachment($courseid, $exercisesheetid){     
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS/course/'.$courseid.'/exercisesheet/'.$exercisesheetid;
        $answer = Request::custom('POST', $URL, $header, $body);
        
        if($answer['status'] == 200){ //nur, wenn file tatsächlich im FS gespeichert wurde .. welcher StatusCode-Bereich soll gelten?
            $body = $answer['content'];
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB/course/'.$courseid.'/exercisesheet/'.$exercisesheetid;
            $answer = Request::custom('POST', $URL, $header, $body);
            $this->app->response->setStatus($answer['status']);
        } else {
            $this->app->response->setStatus($answer['status']);
        }
    }
    
    public function getAttachmentURL($fileid) {        
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/file/'.$fileid;        
        $answer = Request::custom('GET', $URL, $header, $body);  
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    public function deleteAttachment($fileid){       
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/file/'.$fileid;        
        $answer = Request::custum('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        
        if( $answer['status'] == 200){ //nur, wenn file tatsächlich aus DB gelöscht wurde
            $URL = $this->lURL.'/FS/file/'.$fileid; 
            /*
             * eigentlich url der zu löschenden datei schicken und nicht die id???????????????
             */
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }             
    }
    
    public function editAttachment($fileid){  
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS/file/'.$fileid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        
        if($answer['status'] == 200){ //nur, wenn file tatsächlich im FS gespeichert wurde .. welcher StatusCode-Bereich soll gelten?
            $body->{'_file'} = $answer['content'];
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB/marking/'.$markingid.'/tutor/'.$tutorid;
            $answer = Request::custom('PUT', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        } else {
            $this->app->response->setStatus($answer['status']);
        }
    }   
}

$com = new CConfig(Attachment::getPrefix());

if (!$com->used())
    new Attachment($com->loadConfig());

?>