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

    private static $_prefix = "getSite";

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
    private $lURL = "";
    private $flag = 0;

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


        //GET TutorAssignmentSiteInfo
        $this->app->get('/tutorassignment/course/:courseid/exercisesheet/:sheetid(/)', array($this, 'tutorAssignmentSiteInfo'));

        //GET StudentSiteInfo
        $this->app->get('/student/user/:userid/course/:courseid(/)', array($this, 'studentSiteInfo'));
        
        //GET AccountSettings
        $this->app->get('/accountsettings/user/:userid(/)', array($this, 'userWithCourses'));
        
        //GET CreateSheet
        $this->app->get('/createsheet/user/:userid(/)', array($this, 'userWithCourses'));
        
        //GET Index
        $this->app->get('/index/user/:userid(/)', array($this, 'userWithCourses'));
        
        //GET RightsManagment
        $this->app->get('/rightsmanagment/user/:userid(/)', array($this, 'userWithCourses'));
        
        //GET Upload
        $this->app->get('/upload/user/:userid(/)', array($this, 'userWithCourses'));
        
        //GET MarkingTool
        $this->app->get('/markingtool/course/:courseid/exercisesheet/:sheetid/user/:userid', array($this, 'markingTool'));
        
        //GET UploadHistory
        $this->app->get('/uploadhistory/user/:userid/exercise/:exerciseid(/)', array($this, 'uploadHistory'));
        
        //GET TutorSite
        $this->app->get('/tutorsite/course/:courseid/user/:userid(/)', array($this, 'tutorDozentAdmin'));
        
        //GET AdminSite
        $this->app->get('/adminsite/course/:courseid/user/:userid(/)', array($this, 'tutorDozentAdmin'));
        
        //GET DozentSite
        $this->app->get('/dozentsite/course/:courseid/user/:userid(/)', array($this, 'tutorDozentAdmin'));
        
        //GET GroupSite
        $this->app->get('/group/course/:courseid/exercisesheet/:sheetid/user/:userid(/)', array($this, 'groupSite'));
        
        //GET Condition
        $this->app->get('/condition/course/:courseid/user/:userid(/)', array($this, 'checkCondition'));
        
        //run Slim
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
        $URL = $this->lURL.'/DB/user/course/'.$courseid.'/status/1'; //status = 1 => Tutor
        $answer = Request::custom('GET', $URL, $header, $body);
        $tutors = json_decode($answer['content'], true);
        foreach ($tutors AS $tutor){
            //benoetigte Attribute waehlen
            $newTutor = array();
            $newTutor['id'] = $tutor['id'];
            $newTutor['userName'] = $tutor['userName'];
            $newTutor['firstName'] = $tutor['firstName'];
            $newTutor['lastName'] = $tutor['lastName'];
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
        $URL = $this->lURL.'/DB/marking/exercisesheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, $body);
        //fuer jedes Marking die zugeordnete Submision im Rueckgabearray dem passenden Tutor zuweisen
        foreach (json_decode($answer['content'], true) as $marking){
            foreach ($response as &$tutorAssignment){
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
                    'id' => null,
                    'userName' => "unassigned",
                    'firstName' => null,
                    'lastName' => null
                    );

        $unassignedSubmissions = array();


        $submissions = json_decode($answer['content'], true);
        foreach ($submissions as $submission){
            if (!in_array($submission['submissionId'], $assignedSubmissionIDs)){
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
    
    public function studentSiteInfo($userid, $courseid){
        
        $response = array(
                        'sheets' => array(),
                        'user' => array()
                        );
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        
        //get Exercisesheets

        $URL = $this->lURL.'/DB/exercisesheet/course/'.$courseid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $sheets = json_decode($answer['content'], true);
        foreach ($sheets as $sheet){
            $newSheet = array(
                        'id' => $sheet['id'],
                        'courseId'=> $sheet['courseId'],
                        'endDate'=> $sheet['endDate'],
                        'startDate'=> $sheet['startDate'],
                        'sampleSolution'=> $sheet['sampleSolution'],
                        'sheetFile'=> $sheet['sheetFile'],
                        'group'=> array()
                        );
            $response['sheets'][] = $newSheet;
        }
        //get UserGroups
        $URL = $this->lURL.'/DB/group/user/'.$userid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $groups = json_decode($answer['content'], true);
        foreach ($groups as $group){
            foreach ($response['sheets'] as &$sheet){
                if ($sheet['id'] == $group['sheetId']){
                    $sheet['group'] = $group;
                    break;
                }
            }
        }
        
        //get User with Courses
        
        $this->flag = 1;
        $response['user'] = $this->userWithCourses($userid);
        
        $this->app->response->setBody(json_encode($response));
        
    }

    public function userWithCourses($userid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/user/user/'.$userid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $user = json_decode($answer['content'], true);
        $response = array(
                'id' =>  $user['id'],
                'userName'=>  $user['userName'],
                'firstName'=>  $user['firstName'],
                'lastName'=>  $user['lastName'],
                'flag'=>  $user['flag'],
                'email'=>  $user['email'],
                'courses'=>  array()
                );
        foreach ($user['courses'] as $course){
            $newCourse = array(
                'status' => $course['status'],
                'statusName' => $this->getStatusName($course['status']),
                'course' => $course['course']
                );
           $response['courses'][] = $newCourse;
        }
        if ($this->flag == 0){
        $this->app->response->setBody(json_encode($response));}
        else{
        $this->flag = 0;
        return $response;}
    }
    
    public function getStatusName($courseStatus){
        if ($courseStatus == 0){
            return "student";}
        elseif ($courseStatus == 1){
            return "tutor";}
        elseif ($courseStatus == 2){
            return "lecturer";}
        elseif ($courseStatus == 3){
            return "administrator";}
        elseif ($courseStatus == 4){
            return "super-administrator";}
    }
    
    public function markingTool($courseid, $sheetid, $userid){

        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/exercisesheet/course/'.$courseid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $sheets = json_decode($answer['content'], true);
        
        foreach ($sheets as $sheet){
            $response['exerciseSheets'][] = $sheet['id'];
        }
        
        $URL = $this->lURL.'/DB/user/course/'.$courseid.'/status/0';
        $answer = Request::custom('GET', $URL, $header, $body);
        $students = json_decode($answer['content'], true);
        
        foreach ($students as $student){
            $response['students'][] = $student;
        }
        
        $URL = $this->lURL.'/DB/exercise/exercisesheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $exercises = json_decode($answer['content'], true);
        
        foreach ($exercises as $exercise){
            foreach ($exercise['submissions'] as $submission){
                foreach ($response['students'] as &$student){
                    $student['exercises'][] = $exercise;
                    if ($student['id'] == $submission['studentId']){
                        $student['exercises']['submissions'][] = $submission;
                    }
                }
            } 
        }
        
        $URL = $this->lURL.'/DB/user/course/'.$courseid.'/status/1';
        $answer = Request::custom('GET', $URL, $header, $body);
        $tutors = json_decode($answer['content'], true);
        
        $response['tutors'][] = $tutors;
        
        $this->flag = 1;
        $response['user'] = $this->userWithCourses($userid);
        
        $this->app->response->setBody(json_encode($response));
        
    }

    public function uploadHistory($userid, $exerciseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/submission/user/'.$userid.'/exercise/'.$exerciseid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $submissions = json_decode($answer['content'], true);
        foreach ($submissions as $submission){
            $response['submissionHistory'][] = $submission;
        }
        
        $this->flag = 1;
        $response['user'] = $this->userWithCourses($userid);
        
        $this->app->response->setBody(json_encode($response));
    }
    
    public function tutorDozentAdmin($courseid, $userid){
        
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/exercisesheet/course/'.$courseid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $sheets = json_decode($answer['content'], true);
        
        foreach ($sheets as $sheet){
            $response['sheets'][] = $sheet;
        }
        
        $this->flag = 1;
        $response['user'] = $this->userWithCourses($userid);
        
        $this->app->response->setBody(json_encode($response));
    }
    
    public function groupSite($courseid, $sheetid, $userid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        
        
        $URL = $this->lURL.'/DB/group/user/'.$userid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $groups = json_decode($answer['content'], true);
        foreach ($groups as $group){
            if ($group['sheetId'] == $sheetid){
                unset($group['sheetId']);
                $response['group'] = $group;
            }
        }
        
        $URL = $this->lURL.'/DB/exercise/exercisesheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $exercises = json_decode($answer['content'], true);
        
        $response['groupSubmissions'] = array();
        
        foreach ( $exercises as $exercise){
            $newGroupExercise = array();
            foreach ($response['group']['members'] as $user){
                $URL = $this->lURL.'/DB/submission/user/'.$user['id'].'/exercise/'.$exercise['id'];
                $answer = Request::custom('GET', $URL, $header, $body);
                $submission = json_decode($answer['content'], true);
                
                if ($submission != NULL){
                    $newGroupSubmission['user'] = $user;
                    $newGroupSubmission['submission'] = $submission;
                    $newGroupExercise[] = $newGroupSubmission;
                }
            }
            $response['groupSubmissions'][] = $newGroupExercise;
        }
        
        $URL = $this->lURL.'/DB/invitation/leader/user/'.$userid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $response['invitations'] = json_decode($answer['content'], true);
        
        
        
        $this->flag = 1;
        $response['user'] = $this->userWithCourses($userid);
        
        $this->app->response->setBody(json_encode($response));
    }

    public function checkCondition($courseid, $userid){
        
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        
        $URL = $this->lURL.'/DB/exercisetype';
        $answer = Request::custom('GET', $URL, $header, $body);
        $possibleExerciseTypes = json_decode($answer['content'], true);
        
        
        $URL = $this->lURL.'/DB/approvalcondition/course/'.$courseid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $approvalconditions = json_decode($answer['content'], true);
        
        foreach ($approvalconditions as $ac){
            $newMinPercentage = array();
            foreach ($possibleExerciseTypes as $eT){
                if($ac['exerciseTypeId'] == $eT['id']){
                    $newMinPercentage['exerciseTypeID'] = $ac['exerciseTypeId'];
                    $newMinPercentage['exerciseType'] = $eT['name'];
                    $newMinPercentage['minimumPercentage'] = $ac['percentage'];
                    
                    $response['minimumPercentages'][] = $newMinPercentage;
                    break;
                }
            }
        }
        $percentages = array();
        $URL = $this->lURL.'/DB/exercise/course/'.$courseid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $exercises = json_decode($answer['content'], true);
        foreach($response['minimumPercentages'] as $condition){
            $maxPoints = 0;
            $percentage['exerciseTypeID'] = $condition['exerciseTypeID'];
            $percentage['exerciseType'] = $condition['exerciseType'];
            $percentage['minimumPercentage'] = $condition['minimumPercentage'];
                        
            foreach($exercises as $exercise){
                if($exercise['type'] == $condition['exerciseTypeID']){
                    $maxPoints = $maxPoints + $exercise['maxPoints'];
                    $percentage['exerciseIds'][] = $exercise['id'];
                }
            }
            
            $percentage['maxPoints'] = $maxPoints;
            $percentage['points'] = "";
            $percentage['isApproved'] = "";
            
            $percentages[] = $percentage;
        }
        
        $allMarkings = array();
        foreach ($exercises as $exercise){
            $URL = $this->lURL.'/DB/marking/exercise/'.$exercise['id'];
            $answer = Request::custom('GET', $URL, $header, $body);
            $markings = json_decode($answer['content'], true);
            
            foreach($markings as $marking){
                $allMarkings[] = $marking;
            }
        }
        
        $URL = $this->lURL.'/DB/user/course/'.$courseid.'/status/0';
        $answer = Request::custom('GET', $URL, $header, $body);
        $students = json_decode($answer['content'], true);
        
        foreach ($students as $student){
            $points = array();
            foreach($percentages as $percentage){
                $int = $percentage['exerciseTypeID'];
                $points[$int] = 0;
            }
            foreach($exercises as $exercise){
                foreach($allMarkings as $marking){
                    if(($marking['submission']['studentId'] == $student['id'])
                        and ($marking['submission']['exerciseId'] == $exercise['id'])){
                        foreach($percentages as $percentage){
                            if(in_array($exercise['id'], $percentage['exerciseIds'])){
                                $int = $percentage['exerciseTypeID'];
                                $points[$int] = $points[$int] + $marking['points'];
                            }
                        }
                        
                    }
                }
            }
            foreach($percentages as &$percentage){
                $int = $percentage['exerciseTypeID'];
                $percentage['points'] = $points[$int];
                
                if (($percentage['points'] / $percentage['maxPoints']) >= $percentage['minimumPercentage']){
                    $percentage['isApproved'] = true;
                }
                else{
                    $percentage['isApproved'] = false;
                }
            }
            $student['percentages'] = $percentages;
            
            $response['users'][] = $student;
        }
        
        $this->flag = 1;
        $response['user'] = $this->userWithCourses($userid);
        
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