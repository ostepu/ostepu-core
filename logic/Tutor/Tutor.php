<?php 

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * The LTutor class
 *
 * This class handles everything belongs to TutorAssignments
 */
class LTutor
{    
    /**
     *Values needed for conversation with other components
     */
    private $_conf=null;
    
    private static $_prefix = "tutor";
    
    public static function getPrefix()
    {
        return LTutor::$_prefix;
    }
    public static function setPrefix($value)
    {
        LTutor::$_prefix = $value;
    }
    /**
     *Address of the Logic-Controller
     *dynamic set by CConf below
     */
    private $lURL = ""; //aus config lesen
    
    public function __construct($conf)
    {    
        /**
         *Initialise the Slim-Framework
         */
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        /**
         *Set the Logiccontroller-URL
         */
        $this->_conf = $conf;
        $this->query = array();
        
        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        $this->lURL = $this->query->getAddress();
        
        //PUT allocate manual by student
        $this->app->put('/'.$this->_prefix.'/exercisesheet/:exercisesheetid/manu/student(/)', array($this, 'allocateManualByStudent'));
        
        //PUT allocate manual by exercise
        $this->app->put('/'.$this->_prefix.'/exercisesheet/:exercisesheetid/manu/exercise(/)', array($this, 'allocateManualByExercise'));
        
        //PUT allocate auto by student
        $this->app->put('/'.$this->_prefix.'/exercisesheet/:exercisesheetid/auto/student(/)', array($this, 'allocateAutoByStudent'));
        
        //PUT allocate manual by exercise
        $this->app->put('/'.$this->_prefix.'/exercisesheet/:exercisesheetid/auto/exercise(/)', array($this, 'allocateAutoByExercise'));       
        
        //run slim
        $this->app->run();
    }
    
    /**
     * Function to manual allocate students to tutors
     * takes one argument and returns a Status-Code
     * @param $exercisesheetid an integer identifies the exercisesheet
     */
    public function allocateManualByStudent($exercisesheetid){   
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());        
        $URL = $this->lURL.'/DB/exercisesheet/'.$exercisesheetid.'/manu/student';
        $status = 200;
        foreach ($body->{'assignments'} AS $assignment){ 
            $answer = Request::custom('PUT', $URL, $header, json_encode($assignment));
            if ($answer['status'] > 300){
                $status = $answer['status'];
            }
        }        
    }
    
    /**
     * Function to manual allocate exercises to tutors
     *
     * takes one argument and returns a Status-Code
     *
     * @param $exercisesheetid an integer identifies the exercisesheet
     */    
    public function allocateManualByExercise($exercisesheetid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $URL = $this->lURL.'/DB/exercisesheet/'.$exercisesheetid.'/manu/exercise';
        $status = 200;
        foreach ($body->{'assignments'} AS $assignment){  
            $answer = Request::custom('PUT', $URL, $header, json_encode($assignment));
            if ($answer['status'] > 300){
                $status = $answer['status'];
            }
        }
        $this->app->response->setStatus($status); 
    }
    
    /**
     * Function to auto allocate students to tutors
     *
     * takes one argument and returns a Status-Code
     *
     * @param $exercisesheetid an integer identifies the exercisesheet
     */   
    public function allocateAutoByStudent($exercisesheetid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $URL = $this->lURL.'/DB/exercisesheet/'.$exercisesheetid.'/auto/student';
        
        //randomized allocation
        shuffle($body->{'unassigned'}); //randomize the order of elements        
        $numberOfTutors = count($body->{'assignments'});
        $i = 0;
        $arrayOfTutors = $body->{'assignments'};
        foreach ($body->{'unassigned'} AS $student){
            array_push($arrayOfTutors[$i]->{'assigned'}, $student); //add a student to the assigned-list of a tutor
            if ($i < $numberOfTutors - 1){
                $i++;
            } else {
                $i = 0;
            }
        }
        
        //requests to DataBase
        $status = 200;        
        foreach ($arrayOfTutors AS $assignment){  
            $answer = Request::custom('PUT', $URL, $header, json_encode($assignment));
            if ($answer['status'] > 300){
                $status = $answer['status'];
            }
        }
        $this->app->response->setStatus($status);
    }
  
    /**
     * Function to auto allocate exercises to tutors
     *
     * takes one argument and returns a Status-Code
     *
     * @param $exercisesheetid an integer identifies the exercisesheet
     */    
    public function allocateAutoByExercise($exercisesheetid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $URL = $this->lURL.'/DB/exercisesheet/'.$exercisesheetid.'/auto/exercise';
        
        //randomized allocation
        shuffle($body->{'unassigned'}); //randomize the order of elements        
        $numberOfTutors = count($body->{'assignments'});
        $i = 0;
        $arrayOfTutors = $body->{'assignments'};
        foreach ($body->{'unassigned'} AS $exercise){
            array_push($arrayOfTutors[$i]->{'assigned'}, $exercise); //add an exercise to the assigned-list of a tutor           
            if ($i < $numberOfTutors - 1){
                $i++;
            } else {
                $i = 0;
            }
        }
        
        //requests to DataBase
        $status = 200;        
        foreach ($arrayOfTutors AS $assignment){  
            $answer = Request::custom('PUT', $URL, $header, json_encode($assignment));
            if ($answer['status'] > 300){
                $status = $answer['status'];
            }
        }
        $this->app->response->setStatus($status);       
    }
}

/**
 * get new Config-Datas from DB 
 */
$com = new CConfig(LTutor::getPrefix());

/**
 * make a new instance of Tutor-Class with the Config-Datas
 */
if (!$com->used())
    new LTutor($com->loadConfig());
?>