<?php

require 'Slim/Slim.php';
include 'include/Request.php';
include_once( 'include/CConfig.php' );
    
\Slim\Slim::registerAutoloader();
/**
 * The GetSite class
 *
 * This class gives all informations needed to print a Site
 */
class LgetSite
{
    /**
     *Values needed for conversation with other components
     */
    private $_conf=null;
    
    private static $_prefix = "course";
    
    public static function getPrefix()
    {
        return LgetSite::$_prefix;
    }
    public static function setPrefix($value)
    {
        LgetSite::$_prefix = $value;
    }
    
    
    /**
     *Address of the Logic-Controller
     *dynamic set by CConf below
     */ 
    private $lURL = "http://localhost/uebungsplattform/SiteTest.php";
        
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
        //$this->_conf = $conf;
        //$this->query = array();
        //
        //$this->query = CConfig::getLink($conf->getLinks(),"controller");
        //$this->lURL = $this->query->getAddress();
        
        //GET TutorAssignmentSiteInfo
        $this->app->get('/tutorassignment/course/:courseid/exercisesheet/:sheetid', array($this, 'tutorAssignmentSiteInfo'));
        
        
        $this->app->run();
    }
    
    public function tutorAssignmentSiteInfo($courseid, $sheetid){
        
        $response = array();
        $assignedSubmissionIDs = array();
        /**
         * Get Tutors
         */
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/coursestatus/course/'.$courseid.'/status/1'; //status = 1 => Tutor
        $answer = Request::custom('GET', $URL, $header, $body);
        $tutors = '[{"id":"0","userName":"tut0","firstName":"tut0First","lastName":"tut0last"},{"id":"1","userName":"tut1","firstName":"tut1First","lastName":"tut1last"},{"id":"2","userName":"tut2","firstName":"tut2First","lastName":"tut2last"},{"id":"3","userName":"tut3","firstName":"tut3First","lastName":"tut3last"},{"id":"4","userName":"tut4","firstName":"tut4First","lastName":"tut4last"},{"id":"5","userName":"tut5","firstName":"tut5First","lastName":"tut5last"},{"id":"6","userName":"tut6","firstName":"tut6First","lastName":"tut6last"},{"id":"7","userName":"tut7","firstName":"tut7First","lastName":"tut7last"},{"id":"8","userName":"tut8","firstName":"tut8First","lastName":"tut8last"},{"id":"9","userName":"tut9","firstName":"tut9First","lastName":"tut9last"}]';
        $tutors = json_decode($tutors);
        foreach ($tutors AS $tutor){
            //benoetigte Attribute waehlen
            $newTutor = array();
            $newTutor['id'] = $tutor->{'id'};
            $newTutor['userName'] = $tutor->{'userName'};
            $newTutor['firstName'] = $tutor->{'firstName'};
            $newTutor['lastName'] = $tutor->{'lastName'};
            //im Rueckgabe-Array für jeden Tutor ein Marking (ohne Submissions) anlegen
            $tutorAssignment = array(
                    'tutor' => $newTutor,
                    'submissions' => array()
                    );
            array_push($response,$tutorAssignment);
        }
        /**
         * Get Markings
         */
        $URL = $this->lURL.'/DB/exercisesheet/'.$sheetid;
        //$answer = Request::custom('GET', $URL, $header, $body);
        $answer = '[{"id":0,"submission":{"id":5},"tutorId":"0"},{"id":10,"submission":{"id":15},"tutorId":"1"},{"id":20,"submission":{"id":25},"tutorId":"2"},{"id":30,"submission":{"id":35},"tutorId":"3"},{"id":40,"submission":{"id":45},"tutorId":"4"},{"id":50,"submission":{"id":55},"tutorId":"5"},{"id":60,"submission":{"id":65},"tutorId":"6"},{"id":70,"submission":{"id":75},"tutorId":"7"},{"id":80,"submission":{"id":85},"tutorId":"8"},{"id":90,"submission":{"id":95},"tutorId":"9"}]';
        //fuer jedes Marking die zugeordnete Submision im Rueckgabearray dem passenden Tutor zuweisen
        foreach (json_decode($answer) as $marking){
            foreach ($response as &$tutorAssignment){
                if ($marking->{'tutorId'} == $tutorAssignment['tutor']['id']){
                    array_push($tutorAssignment['submissions'], $marking->{'submission'});
                    //ID's aller bereits zugeordneten Submissions speicher
                    array_push($assignedSubmissionIDs, $marking->{'submission'}->{'id'});
                    break;
                }
            }
        }
        
        /**
         * Get SelectedSubmissions
         */
        $URL = $this->lURL.'/DB/selectedsubmission/exercisesheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, $body);
        
        $virtualTutor = array(
                    'id' => null,
                    'userName' => "unassigned",
                    'firstName' => null,
                    'lastName' => null
                    );
        
        $unassignedSubmissions = array();
        
        
        $submissions = json_decode('[{"id":"5"},{"id":"15"},{"id":"25"},{"id":"35"},{"id":"45"},{"id":"55"},{"id":"65"},{"id":"75"},{"id":"85"},{"id":"95"},{"id":"96"},{"id":"97"},{"id":"98"},{"id":"99"}]');
        foreach ($submissions as $submission){
            if (!in_array($submission->{'id'}, $assignedSubmissionIDs)){
                array_push($unassignedSubmissions, $submission);
            }
        }
        $newTutorAssignment = array(
            'tutor' => $virtualTutor,
            'submissions' => $unassignedSubmissions
                    );           
        array_push($response, $newTutorAssignment);
        
        $this->app->response->setBody(json_encode($response));
    }
}

/**
 * get new Config-Datas from DB 
 */
$com = new CConfig(LgetSite::getPrefix());

/**
 * make a new instance of LgetSite-Class with the Config-Datas
 */
if (!$com->used())
    new LgetSite($com->loadConfig());
?>   