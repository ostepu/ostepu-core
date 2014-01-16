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
        return LCourse::$_prefix;
    }
    public static function setPrefix($value)
    {
        LCourse::$_prefix = $value;
    }
    
    
    /**
     *Address of the Logic-Controller
     *dynamic set by CConf below
     */ 
    private $lURL = "";
        
    public function __construct($conf)
    {
        /**
         *Initialise the Sli-Framework
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
        
        //GET TutorAssignmentSideInfo
        $this->app->get('/tutorassignment/course/:courseid/exercisesheet/:sheetid', array($this, 'tutorAssignmentSiteInfo'));
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
        
        $tutors = $answer['content'];
        foreach ($tutors as $tutor){
            //benoetigte Attribute wählen
            $newTutor = array();
            $newTutor['id'] = $tutor['id'];
            $newTutor['userName'] = $tutor['userName'];
            $newTutor['firstName'] = $tutor['firstName'];
            $newTutor['lastName'] = $tutor['lastName'];
            //im Rueckgabe-Array für jeden Tutor ein Marking (ohne Submissions) anlegen
            array_push($response,('tutor' => $newTutor, 'submissions' => array()));
        }
        /**
         * Get Markings
         */
        $URL = $this->lURL.'/DB/exercisesheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, $body);
        //fuer jedes Marking die zugeordnete Submision im Rueckgabearray dem passenden Tutor zuweisen
        foreach ($answer['content'] as $marking){
            foreach ($response as $tutorAssignment){
                if ($marking['tutorId'] == $tutorAssignment['tutor']['id']){
                    array_push($tutorAssignment['submissions'], $marking['submission']);
                    //ID's aller bereits zugeordneten Submissions speicher
                    array_push($assignedSubmissionIDs, $marking['submission']['id']);
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
                    'id' = null;
                    'userName' = "unassigned";
                    'firstName' = null;
                    'lastName' = null;
        
        $unassignedSubmissions = array();
        
        
        
        foreach ($answer['content'] as $submission){
            if (!in_array($submission['id'], $assignedSubmissionIDs)){
                array_push($unassignedSubmissions, $submission);
            }
        }
        array_push($response, ('tutor' => $virtualTutor, 'submissions' => $unassignedSubmissions));
        
        $this->app->response->setBody($response);
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