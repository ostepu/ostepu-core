<?php

require 'Slim/Slim.php';
include './Include/Request.php';
include_once( './Include/CConfig.php' );

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
        
        //Set auto allocation by exercise
        $this->app->post('/'.$this->getPrefix().
            '/auto/exercise/course/:courseid/exercisesheet/:sheetid(/)', 
                array($this, 'autoAllocateByExercise'));
        
        //Set auto allocation by group
        $this->app->post('/'.$this->getPrefix().
            '/auto/group/course/:courseid/exercisesheet/:sheetid(/)', 
                array($this, 'autoAllocateByGroup'));
        
        //Get zip
        $this->app->get('/'.$this->getPrefix().'/user/:userid/exercisesheet/:sheetid(/)',
                array($this, 'getZip'));
        
        //run Slim
        $this->app->run();
    }
    
    /**
     * Function to auto allocate exercises to tutors
     *
     * This function takes two arguments and returns a status code.
     *
     * @param $courseid an integer identifies the course
     * @param $sheetid an integer identifies the exercisesheet
     */
    public function autoAllocateByExercise($courseid, $sheetid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());        
        $URL = $this->lURL.'/DB/marking';
        
        $tutors = $body['tutors'];
        $submissions = array();
        foreach($body['unassigned'] as $submission){
            $exerciseId = $submission['exerciseId'];
            $submissions[$exerciseId][] = $submission;
        }

        //randomized allocation
        shuffle($tutors);
        shuffle($submissions);

        $i = 0;
        $numberOfTutors = count($tutors);
        $markings = array();
        foreach ($submissions as $submissionsByExercise){
            foreach($submissionsByExercise as $submission){
                $newMarking = array(
                    'submission' => $submission,
                    'status' => 0,
                    'tutorId' => $tutors[$i]['tutorId'],
                );
                //adds a submission to a tutor
                $markings[] = $newMarking;
            }
            if ($i < $numberOfTutors - 1){
                $i++;
            } else {
                $i = 0;
            }

        }
        
        //requests to database
        foreach($markings as $marking){
            $answer = Request::custom('POST', $URL, $header,
                    json_encode($marking));
        }
        
        $URL = $this->lURL.'/getsite/tutorassignment/course/'
                        .$courseid.'/exercisesheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, "");
        
        $this->app->response->setBody($answer['content']);
    }

    /**
     * Function to auto allocate groups to tutors
     *
     * It takes two argument and returns a Status-Code.
     *
     * @param $courseid an integer identifies the course
     * @param $sheetid an integer identifies the exercisesheet
     */
    public function autoAllocateByGroup($courseid, $sheetid){
        
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());        
        $URL = $this->lURL.'/DB/marking';
        
        $tutors = $body['tutors'];
        $submissions = array();
        foreach($body['unassigned'] as $submission){
            $leaderId = $submission['leaderId'];
            $submissions[$leaderId][] = $submission;
        }

        //randomized allocation
        shuffle($tutors);

        $i = 0;
        $numberOfTutors = count($tutors);
        $markings = array();
        foreach ($submissions as $submissionsByGroup){
            foreach($submissionsByGroup as $submission){
                $newMarking = array(
                    'submission' => $submission,
                    'status' => 0,
                    'tutorId' => $tutors[$i]['tutorId']
                );
                //adds a submission to a tutor
                $markings[] = $newMarking;
            }
            if ($i < $numberOfTutors - 1){
                $i++;
            } else {
                $i = 0;
            }
            
        }
        
        //requests to database
        foreach($markings as $marking){
            $answer = Request::custom('POST', $URL, $header, 
                    json_encode($marking));
        }
        
        $URL = $this->lURL.'/getsite/tutorassignment/course/'
                    .$courseid.'/exercisesheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, "");
        
        $this->app->response->setBody($answer['content']);
    }
    
    /**
     * Function to get a zip with csv
     *
     * It takes two arguments and returns a zip with folders named a
     * exercise-ID and contains PDF's named the marking-ID. Informations
     * for each marking is written in a CSV-file in the root of the zip.
     *
     * @param $userid an integer identifies the user (tutor)
     * @param $sheetid an integer identifies the exercisesheet
     */
    public function getZip($userid, $sheetid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
       
        $URL = $this->lURL.'/DB/marking/exercisesheet/'.$sheetid.'/tutor/'.$userid;
        //request to database to get the markings
        $answer = Request::custom('GET', $URL, $header,"");
        $markings = json_decode($answer['content'], true);

        $URL = $this->lURL.'/DB/exercise/exercisesheet/'.$sheetid;
        //request to database to get the exercise sheets
        $answer = Request::custom('GET', $URL, $header,"");
        $exercises = json_decode($answer['content'], true);

        $count = 0;
        //an array to descripe the subtasks
        $alphabet = range('a', 'z');
        $secondRow = array();
        $sortedMarkings = array();
        $rows = array();
        $exerciseIdWithExistingMarkings = array();
        
        //exercises with informations of marking and submissions
        //sorted by exercise ID and checked of existence
        foreach( $markings as $marking){
            $submission = $marking['submission'];
            $id = $submission['exerciseId'];
            $sortedMarkings[$id][] = $marking;
            if(!in_array($id, $exerciseIdWithExistingMarkings)){
                $exerciseIdWithExistingMarkings[] = $id;
            }
        }
        
        //formating, create the layout of the CSV-file for the tutor
        //first two rows of an exercise are the heads of the table
        foreach ($exercises as $exercise){
            $firstRow = array();
            $secondRow = array();
            $row = array();
            
            if ($exercise != $exercise['link']){
                $count++;
                $firstRow[] = 'Aufgabe '.$count;
                $subtask = 0;
            }else{
                $firstRow[] = 'Aufgabe '.$count.$alphabet[$subtask];
                $subtask++;
            }
            $firstRow[] = $exercise['id'];
            $secondRow[] = 'ID';
            $secondRow[] = 'Points';
            $secondRow[] = 'MaxPoints';
            $secondRow[] = 'Outstanding?';
            $secondRow[] = 'Status';
            $secondRow[] = 'TutorComment';
            $secondRow[] = 'StudentComment';

            $rows[] = $firstRow;
            $rows[] = $secondRow;
            
            //formating, write known informations of the markings in the CSV-file
            //after the second row to each exercise
            if(in_array($exercise['id'], $exerciseIdWithExistingMarkings)){
                foreach($sortedMarkings[$exercise['id']] as $marking){
                    $row[] = $marking['id'];
                    $row[] = "";
                    $row[] = $exercise['maxPoints'];
                    $row[] = "";
                    $row[] = 0;
                    $row[] = "";
                    $submission = $marking['submission'];
                    $row[] = $submission['comment'];
                    $rows[] = $row;
                }
            }
            //an empty row after an exercise
            $rows[] = array();
        }

        //request to database to get the user name of the tutor for the
        //name of the CSV-file
        $URL = $this->lURL.'/DB/user/user/'.$userid;
        $answer = Request::custom('GET', $URL, $header, "");
        $user = json_decode($answer['content'], true);
        
        //this is the true writing of the CSV-file named [tutorname]_[sheetid].csv
        $CSV = fopen($user['lastName'].'_'.$sheetid.'.csv', 'w');
        
        foreach($rows as $row){
            fputcsv($CSV, $row, ';');
        }
        
        fclose($CSV);
        
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