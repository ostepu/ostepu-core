<?php 

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();
    
class LMarking
{    
    private $_conf=null;
    
    private static $_prefix = "marking";

    public static function getPrefix()
    {
        return LMarking::$_prefix;
    }
    public static function setPrefix($value)
    {
        LMarking::$_prefix = $value;
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
        
        //AddMarking
        $this->app->post('/'.$this->getPrefix().'/exercise/:exerciseid/tutor/:tutorid(/)', 
                        array($this, 'addMarking'));
        
        //GetMarkingURL
        $this->app->get('/'.$this->getPrefix().'/marking/:markingid(/)', 
                        array ($this, 'getMarkingURL'));
        
        //DeleteMarking
        $this->app->delete('/'.$this->getPrefix().'/marking/:markingid(/)', 
                        array($this, 'deleteMarking'));
                        
        //EditMarking
        $this->app->put('/'.$this->getPrefix().'/marking/:markingid/tutor/:tutorid(/)', 
                        array($this, 'editMarking'));
                        
        //EditMarkingState
        $this->app->put('/'.$this->getPrefix().'/marking/:markingid(/)', 
                        array($this, 'editMarkingState'));
        
        $this->app->run();
    }    
    
    /**
     * add a new marking
     *
     * This function is for tutors who once to add a marking of an exercise.
     * First,the marking will be written in the file system.
     * If the status of this operation is right, then the informations
     * of the marking will be added in the database.
     *
     * @return integer $status the status code
     */
    public function addMarking($exerciseid, $tutorid){     
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'_file'});      //mit oder ohne "_"?
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS/exercise/'.$exerciseid.'/tutor/'.$tutorid;
        $answer = Request::custom('POST', $URL, $header, $file);
        
        if($answer['status'] == 200){ //nur, wenn file tatsaechlich im FS gespeichert wurde .. welcher StatusCode-Bereich soll gelten?
            $body->{'_file'} = json_decode($answer['content']);
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB/exercise/'.$exerciseid.'/tutor/'.$tutorid;
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }

    /**
     * get the URL of a marking
     * 
     * This function returns the URL of a marking for download this.
     */
    public function getMarkingURL($markingid) {        
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/marking/'.$markingid;        
        $answer = Request::custom('GET', $URL, $header, $body);  
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * delete a sample solution
     * 
     * First, this function deletes the informations of a marking
     * in the database. If the status of this operation is right,
     * then the marking will be deleted in the file system.
     */
    public function deleteMarking($markingid){       
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/marking/'.$markingid;        
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
     * edit a marking
     *
     * This function overwrites a marking of an exercise.
     * First,the marking will be written in the file system.
     * If the status of this operation is right, then the informations of 
     * the marking will be overwritten in the database.
     */
    public function editMarking($markingid, $tutorid){  
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'_file'});      //mit oder ohne "_"?
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS/marking/'.$markingid.'/tutor/'.$tutorid;
        $answer = Request::custom('PUT', $URL, $header, $file);
        
        if($answer['status'] == 200){ //nur, wenn file tatsaechlich im FS gespeichert wurde .. welcher StatusCode-Bereich soll gelten?
            $body->{'_file'} = json_decode($answer['content']);
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB/marking/'.$markingid.'/tutor/'.$tutorid;
            $answer = Request::custom('PUT', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }

    /**
     * edit a marking state of an exercise
     * 
     * This function changes the state of a marking.
     */
    public function editMarkingState($markingid){  
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/marking/'.$markingid;        
        $answer = Request::custom('PUT', $URL, $header, $body);  
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }   
}

/**
 * get new Config-Datas from DB 
 */
$com = new CConfig(LMarking::getPrefix());

/**
 * make a new instance of Marking-Class with the Config-Datas
 */
if (!$com->used())
    new LMarking($com->loadConfig());
?>