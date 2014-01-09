<?php 

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';
include_once( 'include/CConfig.php' ); 

\Slim\Slim::registerAutoloader();

class SampleSolution
{    
    private $_conf=null;
    
    private static $_prefix = "samplesolution";
    
    public static function getPrefix()
    {
        return SampleSolution::$_prefix;
    }
    public static function setPrefix($value)
    {
        SampleSolution::$_prefix = $value;
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
        
        //AddSampleSolution
        $this->app->post('/course/:courseid/exercisesheet/:sheetid',
                            array($this, 'addSampleSolution');
        
        //EditSampleSolustion
        $this->app->put('/file/:fileid', array($this, 'editSampleSolution');
        
        //GetSampleSolutionURL
        $this->app->get('/file/:fileid', array($this, 'getSampleSolutionURL');
        
        //DeleteSampleSolution
        $this->app->delete('file/:fileid', 
                            array($this, 'DeleteSampleSolution');
        
        $this->app->run();
    }
    
    public function addSampleSolution($courseid, $sheetid){
        
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'_file'});      //mit oder ohne "_"?
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS/sampleSolution/course/'.$courseid.'/exercisesheet/'.$sheetid;
        $answer = Request::custom('POST', $URL, $header, $file);
        
        if($answer['status'] == 200){ //nur, wenn file tatsächlich im FS gespeichert wurde
            $body->{'_file'} = $answer['content'];
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB/sampleSolution/course/'.$courseid.'/exercisesheet/'.$sheetid;
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }
    
    public function editSampleSolution($fileid){
    
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'_file'});      //mit oder ohne "_"?
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS/sampleSolution/file'.$fileid;
        $answer = Request::custom('PUT', $URL, $header, $file);
        
        if($answer['status'] == 200){ //nur, wenn file tatsächlich im FS gespeichert wurde
            $body->{'_file'} = $answer['content'];
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB/samplesolution/file'.$fileid;
            $answer = Request::custom('PUT', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }    
    }
    
    public function getSampleSolutionURL($fileid){
    
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/sampleSolution/file/'.$fileid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }
    
    public function deleteSampleSolution($fileid){
    
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/sampleSolution/file/'.$fileid;        
        $answer = Request::custum('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        
        if( $answer['status'] == 200){ //nur, wenn file tatsächlich aus DB gelöscht wurde
            $URL = $this->lURL.'/FS/sampleSolution/file/'.$fileid;
            $answer = Request::custom('DELETE', $URL, $header, $body);
            $this->app->response->setStatus($answer['status']);
        }  
    }
}
if (!$com->used())
    new SampleSolution($com->loadConfig());
?>