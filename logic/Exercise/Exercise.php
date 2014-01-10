<?php 

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';
include_once( 'include/CConfig.php' ); 

\Slim\Slim::registerAutoloader();
    
class Exercise
{    

    private $_conf=null;
    
    private static $_prefix = "exercise";
    
    public static function getPrefix()
    {
        return Exercise::$_prefix;
    }
    public static function setPrefix($value)
    {
        Exercise::$_prefix = $value;
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
        
        //AddExercise
        $this->app->post(':data+', array($this, 'addExercise'));
        
        //GetExercise
        $this->app->get('/exercise/:exerciseid', 
                        array ($this, 'getExercise'));
        
        //DeleteExercise
        $this->app->delete('/exercise/:exerciseid', 
                        array($this, 'deleteExercise'));
                        
        //EditExercise
        $this->app->put('/exercise/:exerciseid', 
                        array($this, 'editExercise'));
        
        $this->app->run();
    }    
    
    public function addExercise($data){
        //Parameter abfangen wenn $data "nicht leer"        
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'_file'});      //mit oder ohne "_"?
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS';
        $answer = Request::custom('POST', $URL, $header, $file);
        
        if($answer['status'] == 200){ //nur, wenn file tatsächlich im FS gespeichert wurde .. welcher StatusCode-Bereich soll gelten?
            $body->{'_file'} = json_decode($answer['content']);
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB';
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        } else {
            $this->app->response->setStatus($answer['status']);
        }
    }
    
    public function getExercise($exerciseid) {        
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;        
        $answer = Request::custom('GET', $URL, $header, $body);  
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    public function deleteExercise($exerciseid){       
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;        
        $answer = Request::custum('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        
        if( $answer['status'] == 200){ //nur, wenn file tatsächlich aus DB gelöscht wurde
            $URL = $this->lURL.'/FS/exercise/'.$exerciseid; 
            /*
             * eigentlich url der zu löschenden datei schicken und nicht die id???????????????
             */
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }             
    }
    
    public function editExercise($exerciseid){  
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }
}

if (!$com->used())
    new Exercise($com->loadConfig());
?>