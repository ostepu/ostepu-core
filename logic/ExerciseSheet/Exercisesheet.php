<?php 

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' ); 

\Slim\Slim::registerAutoloader();

class LExerciseSheet
{       
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
    private $lURL = ""; //aus config lesen
    
    public function __construct($conf)
    {    
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->_conf = $conf;
        $this->query = array();
        
        $this->query = array(CConfig::getLink($conf->getLinks(),"controller"));
        $this->lURL = $querry['address'];
        
        //AddExerciseSheet
        $this->app->post('/course/:courseid', array($this, 'addExerciseSheet'));        //Adressen noch anpassen (Parameter mit Compo-Namen)

        //EditExerciseSheet
        $this->app->put('/exercisesheet/:sheetid',
                        array ($this, 'editExerciseSheet'));
        
        //GetExerciseSheetURL
        $this->app->get('/exercisesheet/:sheetid/url', 
                        array($this, 'getExerciseSheetURL'));

        //GetExerciseSheet
        $this->app->get('/exercisesheet/:sheetid', 
                        array($this, 'getExerciseSheet'));

        //DeleteExerciseSheet
        $this->app->delete('/exercisesheet/:sheetid', 
                        array($this, 'deleteExerciseSheet'));
                        
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
        
        if( $answer['status'] == 200){ //nur, wenn File tatsächlich aus DB gelöscht wurde
            $URL = $this->lURL.'/FS/exercisesheet/'.$sheetid; 
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