<?php 

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

class LSampleSolution
{    
    private $_conf=null;
    
    private static $_prefix = "samplesolution";
    
    public static function getPrefix()
    {
        return LSampleSolution::$_prefix;
    }
    public static function setPrefix($value)
    {
        LSampleSolution::$_prefix = $value;
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
        
        //AddSampleSolution
        $this->app->post('/'.$this->getPrefix().'/course/:courseid/exercisesheet/:sheetid(/)',
                            array($this, 'addSampleSolution');
        
        //EditSampleSolustion
        $this->app->put('/'.$this->getPrefix().'/file/:fileid(/)', array($this, 'editSampleSolution');
        
        //GetSampleSolutionURL
        $this->app->get('/'.$this->getPrefix().'/file/:fileid(/)', array($this, 'getSampleSolutionURL');
        
        //DeleteSampleSolution
        $this->app->delete('/'.$this->getPrefix().'file/:fileid(/)', 
                            array($this, 'DeleteSampleSolution');
        
        $this->app->run();
    }

    /**
     * add a new sample solution
     *
     * This function adds a solution of an exercise.
     * First,the marking will be written in the file system.
     * If the status of this operation is right, then the informations
     * of the solution will be added in the database.
     *
     * @return integer $status the status code
     */
    public function addSampleSolution($courseid, $sheetid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'_file'});      //mit oder ohne "_"?
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS/sampleSolution/course/'.$courseid.'/exercisesheet/'.$sheetid;
        $answer = Request::custom('POST', $URL, $header, $file);
        
        if($answer['status'] == 200){ //nur, wenn file tatsaechlich im FS gespeichert wurde
            $body->{'_file'} = $answer['content'];
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB/sampleSolution/course/'.$courseid.'/exercisesheet/'.$sheetid;
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }

    /**
     * edit a solution of an exercise
     *
     * This function overwrites a solution of an exercise.
     * First,the solution will be written in the file system.
     * If the status of this operation is right, then the informations
     * of the solution will be overwritten in the database.
     */
    public function editSampleSolution($fileid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'_file'});      //mit oder ohne "_"?
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS/sampleSolution/file'.$fileid;
        $answer = Request::custom('PUT', $URL, $header, $file);
        
        if($answer['status'] == 200){ //nur, wenn file tatsaechlich im FS gespeichert wurde
            $body->{'_file'} = $answer['content'];
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB/samplesolution/file'.$fileid;
            $answer = Request::custom('PUT', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }    
    }

    /**
     * get the URL of a sample solution
     * 
     * This function returns the URL of a sample solution for download this.
     */
    public function getSampleSolutionURL($fileid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/sampleSolution/file/'.$fileid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * delete a sample solution
     * 
     * First, this function deletes the informations of a solution
     * in the database. If the status of this operation is right,
     * then the solution will be deleted in the file system.
     */
    public function deleteSampleSolution($fileid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/sampleSolution/file/'.$fileid;        
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
}
/**
 * get new Config-Datas from DB 
 */
$com = new CConfig(LSampleSolution::getPrefix());

/**
 * make a new instance of SampleSolution-Class with the Config-Datas
 */
if (!$com->used())
    new LSampleSolution($com->loadConfig());
?>