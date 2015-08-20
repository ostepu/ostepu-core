<?php
/**
 * @file LGetSite.php
 *
 * contains the LGetSite class.
 * @date 2013-2014
 */
require_once dirname(__FILE__).'/../../Assistants/Slim/Slim.php';
include_once dirname(__FILE__).'/../../Assistants/Request.php';
include_once dirname(__FILE__).'/../../Assistants/CConfig.php';
include_once dirname(__FILE__).'/../../Assistants/Logger.php';
include_once dirname(__FILE__).'/../../Assistants/Structures.php';
include_once dirname(__FILE__).'/../../Assistants/LArraySorter.php';

\Slim\Slim::registerAutoloader();

/**
 * This class gives all information needed to print a Site
 */
class LGetSite
{
    /**
     * Values needed for conversation with other components
     */
    private $_conf=null;

    private static $_prefix = "getSite";

    public static function getPrefix()
    {
        return LGetSite::$_prefix;
    }
    public static function setPrefix($value)
    {
        LGetSite::$_prefix = $value;
    }


    /**
     * Address of the logic controller.
     */
    private $lURL = "";
    
    private $_getUser = array();
    private $_getExercise = array();
    private $_getExerciseType = array();
    private $_getExerciseFileType = array();
    private $_getApprovalCondition = array();
    private $_getMarking = array();
    private $_getSelectedSubmission = array();
    private $_getGroup = array();
    private $_getCourseStatus = array();
    private $_getSubmission = array();
    private $_getCourse = array();
    private $_getInvitation = array();
    
    private $flag = 0;

    public function __construct()
    {
        // runs the CConfig
        $com = new CConfig( LGetSite::getPrefix( ), dirname(__FILE__) );

        // runs the LGetSite
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
            
        // Initialize Slim
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // Set the logic controller URL
        $this->_conf = $conf;

        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        $this->_getUser = CConfig::getLink($conf->getLinks(),"getUser");
        $this->_getExercise = CConfig::getLink($conf->getLinks(),"getExercise");
        $this->_getExerciseType = CConfig::getLink($conf->getLinks(),"getExerciseType");
        $this->_getExerciseFileType = CConfig::getLink($conf->getLinks(),"getExerciseFileType");
        $this->_getApprovalCondition = CConfig::getLink($conf->getLinks(),"getApprovalCondition");
        $this->_getMarking = CConfig::getLink($conf->getLinks(),"getMarking");
        $this->_getSelectedSubmission = CConfig::getLink($conf->getLinks(),"getSelectedSubmission");
        $this->_getGroup = CConfig::getLink($conf->getLinks(),"getGroup");
        $this->_getCourseStatus = CConfig::getLink($conf->getLinks(),"getCourseStatus");
        $this->_getSubmission = CConfig::getLink($conf->getLinks(),"getSubmission");
        $this->_getCourse = CConfig::getLink($conf->getLinks(),"getCourse");
        $this->_getInvitation = CConfig::getLink($conf->getLinks(),"getInvitation");
    
        $this->lURL = $this->query->getAddress();


        //GET TutorAssignmentSiteInfo
        $this->app->get('/tutorassign/user/:userid/course/:courseid/exercisesheet/:sheetid(/)',
                        array($this, 'tutorAssignmentSiteInfo'));

        //GET StudentSiteInfo
        $this->app->get('/student/user/:userid/course/:courseid(/)',
                        array($this, 'studentSiteInfo'));

        //GET AccountSettings
        $this->app->get('/accountsettings/user/:userid(/)',
                        array($this, 'accountsettings'));

        //GET CreateSheet
        $this->app->get('/createsheet/user/:userid/course/:courseid(/)',
                        array($this, 'createSheetInfo'));

        //GET Index
        $this->app->get('/index/user/:userid(/)',
                        array($this, 'userWithAllCourses'));

        //GET CourseManagement
        $this->app->get('/coursemanagement/user/:userid/course/:courseid(/)',
                        array($this, 'courseManagement'));

        //GET MainSettings
        $this->app->get('/mainsettings/user/:userid',
                        array($this, 'mainSettings'));

        //GET Upload
        $this->app->get('/upload/user/:userid/course/:courseid/exercisesheet/:sheetid(/)',
                        array($this, 'upload'));

        //GET TutorUpload
        $this->app->get('/tutorupload/user/:userid/course/:courseid(/)',
                        array($this, 'tutorUpload'));

        //GET MarkingTool
        $this->app->get('/markingtool/user/:userid/course/:courseid/exercisesheet/:sheetid(/)',
                        array($this, 'markingTool'));
        //GET MarkingTool
        $this->app->get('/markingtool/user/:userid/course/:courseid/exercisesheet/:sheetid/tutor/:tutorid',
                        array($this, 'markingToolTutor'));
        //GET MarkingTool
        $this->app->get('/markingtool/user/:userid/course/:courseid/exercisesheet/:sheetid/status/:statusid',
                        array($this, 'markingToolStatus'));
        //GET MarkingTool
        $this->app->get('/markingtool/user/:userid/course/:courseid/exercisesheet/:sheetid/tutor/:tutorid/status/:statusid',
                        array($this, 'markingToolTutorStatus'));

        //GET UploadHistory
        $this->app->get('/uploadhistory/user/:userid/course/:courseid/exercisesheet/:sheetid/uploaduser/:uploaduserid(/)',
                        array($this, 'uploadHistory'));

        //GET UploadHistoryOptions
        $this->app->get('/uploadhistoryoptions/user/:userid/course/:courseid(/)',
                        array($this, 'uploadHistoryOptions'));

        //GET TutorSite
        $this->app->get('/tutor/user/:userid/course/:courseid(/)',
                        array($this, 'tutorDozentAdmin'));

        //GET AdminSite
        $this->app->get('/admin/user/:userid/course/:courseid(/)',
                        array($this, 'tutorDozentAdmin'));

        //GET DozentSite
        $this->app->get('/lecturer/user/:userid/course/:courseid(/)',
                        array($this, 'tutorDozentAdmin'));

        //GET GroupSite
        $this->app->get('/group/user/:userid/course/:courseid/exercisesheet/:sheetid(/)',
                        array($this, 'groupSite'));

        //GET Condition
        $this->app->get('/condition/user/:userid/course/:courseid(/)',
                        array($this, 'checkCondition'));

        //run Slim
        $this->app->run();
    }

    public function tutorAssignmentSiteInfo($userid, $courseid, $sheetid)
    {
        $response = array();
        $assignedSubmissionIDs = array();

        // get all users with status 1,2,3 (tutor,lecturer,admin)
        $URL = $this->_getUser->getAddress().'/user/course/'.$courseid.'';
        $handler1 = Request_CreateRequest::createGet($URL, array(), '');
        
        // get markings
        $URL = $this->_getMarking->getAddress().'/marking/exercisesheet/'.$sheetid;
        $handler4 = Request_CreateRequest::createGet($URL, array(), '');
        
        // Get SelectedSubmissions
        $URL = $this->_getSelectedSubmission->getAddress().'/selectedsubmission/exercisesheet/'.$sheetid;
        $handler5 = Request_CreateRequest::createGet($URL, array(), '');
        
        $URL = $this->lURL . '/exercisesheet/course/' . $courseid.'/exercise';
        $handler6 = Request_CreateRequest::createGet($URL, array(), '');
        

        $multiRequestHandle = new Request_MultiRequest();
        $multiRequestHandle->addRequest($handler1);
        $multiRequestHandle->addRequest($handler4);
        $multiRequestHandle->addRequest($handler5);
        $multiRequestHandle->addRequest($handler6);

        $answer = $multiRequestHandle->run();

        $users = json_decode($answer[0]['content'], true);
        $markings = json_decode($answer[1]['content'], true);
        $submissions = json_decode($answer[2]['content'], true);
        $exerciseSheets = json_decode($answer[3]['content'], true);
        unset($answer);unset($multiRequestHandle);
        
        $namesOfExercises = array();
        // find the current sheet and it's exercises
        foreach ($exerciseSheets as &$sheet) {
            $thisSheetId = $sheet['id'];

            if ($thisSheetId == $sheetid) {
                $thisExerciseSheet = $sheet;
                        // create exercise names
                //an array to descripe the subtasks
                $alphabet = range('a', 'z');
                $count = 0;
                
                $count=null;
                if (isset($sheet['exercises'])){
                    $exercises = $sheet['exercises'];
                    foreach ($exercises as $key => $exercise){
                        $exerciseId = $exercise['id'];

                        if ($count===null || $exercises[$count]['link'] != $exercise['link']){
                            $count=$key;
                            $namesOfExercises[$exerciseId] = $exercise['link'];
                            $subtask = 0;
                        }else{
                            $subtask++;
                            $namesOfExercises[$exerciseId] = $exercise['link'].$alphabet[$subtask];
                            $namesOfExercises[$exercises[$count]['id']] = $exercises[$count]['link'].$alphabet[0];
                        }
                    }
                }
            }

            unset($sheet['exercises']);
        }

        $students=array();
        $tutors=array();
        foreach ($users as $user){
            if ($user['courses'][0]['status']==0){
                $students[] = $user;
            }elseif ($user['courses'][0]['status']>0){
                $tutors[] = $user;
            }
        }
        unset($users);

        $response['tutorAssignments'] = array();

        if (!empty($tutors)) {
            foreach ($tutors as &$tutor) {
                unset($tutor['salt']);
                unset($tutor['password']);

                // create an empty marking for each tutor
                $response['tutorAssignments'][] = array('tutor' => $tutor, 'submissions' => array());
            }
        }
        $response['tutorAssignments'][] = array('tutor' => json_decode(User::encodeUser(User::createUser(null,'','','','',null,null,null,null,null,null)),true), 'submissions' => array());

        // assign submissions for the markings to the right tutor
        foreach ($markings as $marking ) {

            // ignore marking if submission is not selected for group
            if (isset($marking['submission']) && (!isset($marking['submission']['selectedForGroup']) || !$marking['submission']['selectedForGroup'])) continue;
            
            foreach ($response['tutorAssignments'] as &$tutorAssignment ) {
                if (!isset($tutorAssignment['tutor']['id']) || $marking['tutorId'] == $tutorAssignment['tutor']['id']) {

                    // rename 'id' to 'submissionId'
                    //$marking['submission']['id'] = $marking['submission']['id'];
                    //unset($marking['submission']['id']);

                    // remove unnecessary information
                    unset($marking['submission']['file']);
                    unset($marking['submission']['comment']);
                    unset($marking['submission']['accepted']);
                    unset($marking['submission']['date']);
                    unset($marking['submission']['flag']);
                    unset($marking['submission']['selectedForGroup']);
                    
                    $marking['submission']['user']=null;
                    foreach ($students as $student){
                        if ($student['id']==$marking['submission']['leaderId']){
                            $marking['submission']['user']=$student;
                            break;
                        }
                    }
                    $marking['submission']['markingId'] = $marking['id'];
                    $tutorAssignment['submissions'][] = $marking['submission'];

                    // save ids of all assigned submission
                    $assignedSubmissionIDs[] = $marking['submission']['id'];
                    break;
                }
            }
        }
        
        // remove unknown lecturer if empty
        if (count($response['tutorAssignments'][count($response['tutorAssignments'])-1]['submissions']) == 0)
            unset($response['tutorAssignments'][count($response['tutorAssignments'])-1]);
        
        $virtualTutor = array('id' => null,
                              'userName' => "unassigned",
                              'firstName' => null,
                              'lastName' => null);

        $unassignedSubmissions = array();

        foreach ($submissions as &$submission) {
            $submission['id'] = $submission['submissionId'];
            unset($submission['submissionId']);
            if (!in_array($submission['id'], $assignedSubmissionIDs)) {
                $submission['unassigned'] = true;
                $submission['user']=null;
                    foreach ($students as $student){
                        if ($student['id']==$submission['leaderId']){
                            $submission['user']=$student;
                            break;
                        }
                    }
                $unassignedSubmissions[] = $submission;
            }
        }
        
        // generate proposals for tutors by using the markings from last exercise sheet
        if (count($unassignedSubmissions)>0){
            $lastSid = null;
            for ($i=0; $i<count($exerciseSheets);$i++){
                if ($exerciseSheets[$i]['id'] == $sheetid){
                    if ($i<count($exerciseSheets)-1){
                        $lastSid = $exerciseSheets[$i+1]['id'];
                        break;
                    }
                }
            }

            if ($lastSid!==null){
                $URL = $this->_getMarking->getAddress().'/marking/exercisesheet/'.$lastSid;
                $handler1 = Request_CreateRequest::createGet($URL, array(), '');
                $multiRequestHandle = new Request_MultiRequest();
                $multiRequestHandle->addRequest($handler1);
                $answer = $multiRequestHandle->run();

                $lastMarkings = json_decode($answer[0]['content'], true);
                unset($answer);unset($multiRequestHandle);
                $lastTutorUser = array();
                foreach ($lastMarkings as $marking){
                    ///echo $marking['submission']['id'].' '.$marking['submission']['leaderId'].' '.$marking['tutorId'].' '.(isset($marking['submission']['selectedForGroup'])?$marking['submission']['selectedForGroup']:'')."\n";
                    if (isset($marking['tutorId']) && isset($marking['submission']['leaderId']) && isset($marking['submission']['selectedForGroup']) && $marking['submission']['selectedForGroup']){
                        $tutorId = $marking['tutorId'];
                        $leaderId = $marking['submission']['leaderId'];
                        if (!isset($lastTutorUser[$tutorId])) 
                            $lastTutorUser[$tutorId]=array();
                        if (!in_array($leaderId,$lastTutorUser[$tutorId]))
                            $lastTutorUser[$tutorId][] = $leaderId;
                    }
                }
                
                unset($lastMarkings);
                foreach ($response['tutorAssignments'] as &$tutorAssignment ) {
                    if (!isset($tutorAssignment['tutor']['id'])) continue;
                    if (!isset($lastTutorUser[$tutorAssignment['tutor']['id']])) continue;
                    foreach ($unassignedSubmissions as $submission){
                        if (in_array($submission['leaderId'],$lastTutorUser[$tutorAssignment['tutor']['id']])){
                            if (!isset($tutorAssignment['proposalSubmissions']))$tutorAssignment['proposalSubmissions']=array();
                            $tutorAssignment['proposalSubmissions'][] = $submission;
                            unset($submission);
                        }
                    }
                }
            }
        }
        

        $newTutorAssignment = array('tutor' => $virtualTutor,
                                    'submissions' => $unassignedSubmissions);

        $response['tutorAssignments'][] = $newTutorAssignment;
        $response['namesOfExercises'] = $namesOfExercises;


        $this->flag = 1;
        $response['user'] = $this->userWithCourse($userid, $courseid);

        $this->app->response->setBody(json_encode($response));
    }

    /**
     * Compiles data for the Student page.
     *
     * @todo include markingStatusName
     *
     * @author Florian Lücke
     */
    public function studentSiteInfo($userid, $courseid)
    {
        $response = array('sheets' => array(),
                          'user' => array());

        // load all Requests async
        $URL = $this->lURL . '/exercisesheet/course/' . $courseid . '/exercise';
        $handler1 = Request_CreateRequest::createGet($URL, array(), '');

        $URL = $this->_getSubmission->getAddress().'/submission/group/user/' . $userid .'/course/' . $courseid . '/selected';
        $handler2 = Request_CreateRequest::createGet($URL, array(), '');

        //$URL = $this->_getMarking->getAddress().'/marking/course/' . $courseid;
        $URL = $this->_getMarking->getAddress().'/marking/course/'.$courseid.'/user/'.$userid;
        $handler3 = Request_CreateRequest::createGet($URL, array(), '');

        $URL = $this->_getGroup->getAddress().'/group/user/' . $userid;
        $handler4 = Request_CreateRequest::createGet($URL, array(), '');

        $URL = $this->_getExerciseType->getAddress().'/exercisetype';
        $handler5 = Request_CreateRequest::createGet($URL, array(), '');

        $multiRequestHandle = new Request_MultiRequest();
        $multiRequestHandle->addRequest($handler1);
        $multiRequestHandle->addRequest($handler2);
        $multiRequestHandle->addRequest($handler3);
        $multiRequestHandle->addRequest($handler4);
        $multiRequestHandle->addRequest($handler5);

        $answer = $multiRequestHandle->run();

        //Get neccessary data
        $sheets = json_decode($answer[0]['content'], true);
        $submissions = json_decode($answer[1]['content'], true);
        
        if (!isset($submissions)) {
            $submissions = array();
        }

        $markings = json_decode($answer[2]['content'], true);
        if (!isset($markings)) {
            $markings = array();
        }

        $groups = json_decode($answer[3]['content'], true);

        $possibleExerciseTypes = json_decode($answer[4]['content'], true);

        $markingStatus = Marking::getStatusDefinition();

        // order submissions by exercise
        $submissionsByExercise = array();
        foreach ($submissions as $submission) {
            $exerciseId = $submission['exerciseId'];
            $submissionsByExercise[$exerciseId] = $submission;
        }

        // add markings to the submissions
        foreach ($markings as &$marking) {
            $studentId = $marking['submission']['studentId'];
            $exerciseId = $marking['submission']['exerciseId'];

            if (isset($submissionsByExercise[$exerciseId])) {
                // only check submissions that have the same exercise id
                // as the marking (there should be 1 at most)
                $selectedSubmission = &$submissionsByExercise[$exerciseId];
                $selectedSubmissionStudentId = $selectedSubmission['studentId'];

                if ($selectedSubmissionStudentId == $studentId && (isset($marking['submission']['id']) && isset($selectedSubmission['id']) && $marking['submission']['id'] == $selectedSubmission['id'])) {
                    // the student id of the selected submission and the student
                    // id of the marking match

                    // add marking status to the marking
                    $status = $marking['status'];
                    $marking['statusId'] = $status;

                    // add marking status name to the marking
                    $statusName = $markingStatus[LArraySorter::multidimensional_search($markingStatus, array('id'=>$status))]['longName'];
                    $marking['status'] = $statusName;

                    unset($marking['submission']);
                    $selectedSubmission['marking'] = $marking;
                }
            }
        }

        // order groups by sheet
        $groupsBySheet = array();
        foreach ($groups as $group) {
            if (isset($group['sheetId'])) {
                $groupsBySheet[$group['sheetId']] = $group;
            }
        }

        // order exercise types by id
        $exerciseTypes = array();
        foreach ($possibleExerciseTypes as $exerciseType) {
            $exerciseTypes[$exerciseType['id']] = $exerciseType;
        }

        if (isset($sheets)){
            foreach ($sheets as &$sheet) {
                $sheetPoints = 0;
                $maxSheetPoints = 0;

                $hasAttachments = false;
                $hasMarkings = false;
                $hasSubmissions = false;

                // add group to the sheet
                if (isset($groupsBySheet[$sheet['id']])) {
                    $group = $groupsBySheet[$sheet['id']];
                    $sheet['group'] = $group;
                } else {
                    $sheet['group'] = array();
                }

                // prepare exercises
                foreach ($sheet['exercises'] as &$exercise) {
                    $isBonus = isset($exercise['bonus']) ? $exercise['bonus'] : null;
                    $maxSheetPoints += ($isBonus == null || $isBonus == '0') ? $exercise['maxPoints'] : 0;
                    $exerciseID = $exercise['id'];

                    // add submission to exercise
                    if (isset($submissionsByExercise[$exerciseID])) {
                        $submission = &$submissionsByExercise[$exerciseID];

                        if (!isset($submission['hideFile']) || !$submission['hideFile'])
                            $hasSubmissions=true;
                            
                        if (isset($submission['marking'])) {
                            $marking = $submission['marking'];

                        if (isset($submission['accepted']) && $submission['accepted'] == 1)
                            $sheetPoints += isset($marking['points']) ? $marking['points'] : 0 ;

                            if (!isset($submission['marking']['hideFile']) || !$submission['marking']['hideFile'])
                                $hasMarkings = true;
                        }

                        $exercise['submission'] = $submission;
                    }

                    // add attachments to exercise
                    if (count($exercise['attachments']) > 0) {
                        $exercise['attachment'] = $exercise['attachments'][0];
                        $hasAttachments = true;
                    }

                    unset($exercise['attachments']);

                    // add type name to exercise
                    $typeID = $exercise['type'];
                    if (isset($exerciseTypes[$typeID])) {
                        $exercise['typeName'] = $exerciseTypes[$typeID]['name'];
                    } else {
                        $exercise['typeName'] = "unknown type";
                    }
                }

                $sheet['hasMarkings'] = $hasMarkings;
                $sheet['hasAttachments'] = $hasAttachments;
                $sheet['hasSubmissions'] = $hasSubmissions;
                $sheet['maxPoints'] = $maxSheetPoints;
                $sheet['points'] = $sheetPoints;
                if ($maxSheetPoints != 0) {
                    $percentage = round($sheetPoints / $maxSheetPoints * 100, 2);
                    $sheet['percentage'] = $percentage;
                } else {
                    $sheet['percentage'] = 100;
                }
            }
            $response['sheets'] = $sheets;
        }

        $this->flag = 1;
        $response['user'] = $this->userWithCourse($userid, $courseid);

        $this->app->response->setBody(json_encode($response));
    }

    public function userWithCourse($userid, $courseid)
    {

        $URL = $this->_getCourseStatus->getAddress().'/coursestatus/course/'.$courseid.'/user/'.$userid;
        $answer = Request::custom('GET', $URL, array(), '');
        $user = json_decode($answer['content'], true);

        $response = $user;

        if ($this->flag == 0){
            $this->app->response->setBody(json_encode($response));
        } else{
            $this->flag = 0;
            return $response;
        }
    }

    public function userWithAllCourses($userid)
    {
        $URL = $this->_getUser->getAddress().'/user/user/'.$userid;
        $answer = Request::custom('GET', $URL, array(), '');
        $user = json_decode($answer['content'], true);

        /*$response = array('id' =>  $user['id'],
                          'userName'=>  isset($user['email']) ? $user['userName'] : null,
                          'firstName'=>  isset($user['email']) ? $user['firstName'] : null,
                          'lastName'=>  isset($user['email']) ? $user['lastName'] : null,
                          'flag'=>  isset($user['email']) ? $user['flag'] : null,
                          'email'=>  isset($user['email']) ? $user['email'] : null,
                          'courses'=>  array());*/
                          
        $response = $user;
        $response['courses'] = array();
            

        foreach ($user['courses'] as $course) {
            $newCourse = array('status' => $course['status'],
                               'statusName' => $this->getStatusName($course['status']),
                               'course' => $course['course']);
            $response['courses'][] = $newCourse;
        }

        if ($this->flag == 0) {
            $this->app->response->setBody(json_encode($response));
        } else {
            $this->flag = 0;
            return $response;
        }
    }

    public function accountsettings($userid)
    {
        $URL = $this->_getUser->getAddress().'/user/user/' . $userid;
        $answer = Request::custom('GET', $URL, array(), '');
        $user = json_decode($answer['content'], true);

        $this->app->response->setBody(json_encode($user));
    }

    public function userWithCourseAndHash($userid, $courseid)
    {

        $URL = $this->_getCourseStatus->getAddress().'/coursestatus/course/'.$courseid.'/user/'.$userid;
        $answer = Request::custom('GET', $URL, array(), '');
        $user = json_decode($answer['content'], true);

        $URL = $this->_getUser->getAddress().'/user/user/'.$userid;
        $answer = Request::custom('GET', $URL, array(), '');
        $response['user'] = json_decode($answer['content'], true);

        if (isset($response['user']['courses']))
            unset($response['user']['courses']);

        foreach ($user['courses'] as $course) {
            $newCourse = array('status' => $course['status'],
                               'statusName' => $this->getStatusName($course['status']),
                               'course' => $course['course']);
            $response['courses'][] = $newCourse;
        }
        if ($this->flag == 0){
            $this->app->response->setBody(json_encode($response));
        } else{
            $this->flag = 0;
            return $response;
        }
    }

    /**
    * @todo Receive the names from the database instead of defining it here.
    */
    public function getStatusName($courseStatus)
    {
        $statusNames = CourseStatus::getStatusDefinition();
        return $statusNames[$courseStatus];
    }

    /**
     * Function that handles all requests for marking tool.
     * Used by the functions that are called by Slim when data for the marking
     * tool is requested
     *
     * @author Florian Lücke
     */
    public function markingToolBase($userid,
                                    $courseid,
                                    $sheetid,
                                    $tutorid,
                                    $statusid,
                                    $shouldfilter,
                                    $selector)
    {
        $response = array();

        //Get neccessary data
        $URL = "{$this->lURL}/exercisesheet/course/{$courseid}/exercise";
        $handler1 = Request_CreateRequest::createGet($URL, array(), '');

        $URL = "{$this->_getMarking->getAddress()}/marking/exercisesheet/{$sheetid}";
        $handler2 = Request_CreateRequest::createGet($URL, array(), '');

        $URL = "{$this->_getUser->getAddress()}/user/course/{$courseid}/status/1";
        $handler3 = Request_CreateRequest::createGet($URL, array(), '');

        $URL = "{$this->_getGroup->getAddress()}/group/exercisesheet/{$sheetid}";
        $handler4 = Request_CreateRequest::createGet($URL, array(), '');

        $URL = "{$this->_getSubmission->getAddress()}/submission/exercisesheet/{$sheetid}/selected";
        $handler5 = Request_CreateRequest::createGet($URL, array(), '');

        $URL = $this->_getExerciseType->getAddress().'/exercisetype';
        $handler6 = Request_CreateRequest::createGet($URL, array(), '');
        
        $URL = "{$this->_getUser->getAddress()}/user/course/{$courseid}/status/2";
        $handler7 = Request_CreateRequest::createGet($URL, array(), '');
        
        $URL = "{$this->_getUser->getAddress()}/user/course/{$courseid}/status/3";
        $handler8 = Request_CreateRequest::createGet($URL, array(), '');
        
        $multiRequestHandle = new Request_MultiRequest();
        $multiRequestHandle->addRequest($handler1);
        $multiRequestHandle->addRequest($handler2);
        $multiRequestHandle->addRequest($handler3);
        $multiRequestHandle->addRequest($handler4);
        $multiRequestHandle->addRequest($handler5);
        $multiRequestHandle->addRequest($handler6);
        $multiRequestHandle->addRequest($handler7);
        $multiRequestHandle->addRequest($handler8);

        $answer = $multiRequestHandle->run();

        $sheets = json_decode($answer[0]['content'], true);
        $markings = json_decode($answer[1]['content'], true);
        $tutors = json_decode($answer[2]['content'], true);
        $groups = json_decode($answer[3]['content'], true);
        $submissions = json_decode($answer[4]['content'], true);
        $possibleExerciseTypes = json_decode($answer[5]['content'], true);
        $tutors = array_merge($tutors,json_decode($answer[6]['content'], true));
        $tutors = array_merge($tutors,json_decode($answer[7]['content'], true));
                
        // order exercise types by id
        $exerciseTypes = array();
        foreach ($possibleExerciseTypes as $exerciseType) {
            $exerciseTypes[$exerciseType['id']] = $exerciseType;
        }

        
        $namesOfExercises = array();
        // find the current sheet and it's exercises
        foreach ($sheets as &$sheet) {
            $thisSheetId = $sheet['id'];

            if ($thisSheetId == $sheetid) {
                $thisExerciseSheet = $sheet;
                        // create exercise names
                //an array to descripe the subtasks
                $alphabet = range('a', 'z');
                $count = 0;
                
                $count=null;
                if (isset($sheet['exercises'])){
                    $exercises = $sheet['exercises'];
                    foreach ($exercises as $key => $exercise){
                        $exerciseId = $exercise['id'];

                        if ($count===null || $exercises[$count]['link'] != $exercise['link']){
                            $count=$key;
                            $namesOfExercises[$exerciseId] = $exercise['link'];
                            $subtask = 0;
                        }else{
                            $subtask++;
                            $namesOfExercises[$exerciseId] = $exercise['link'].$alphabet[$subtask];
                            $namesOfExercises[$exercises[$count]['id']] = $exercises[$count]['link'].$alphabet[0];
                        }
                    }
                }
            }

            unset($sheet['exercises']);
        }

        if (isset($thisExerciseSheet) == false) {
            $this->app->halt(404, '{"code":404,reason":"invalid sheet id"}');
        }

        // save the index of each exercise and add exercise type name
        $exercises = array();
        $exerciseIndices = array();
        foreach ($thisExerciseSheet['exercises'] as $idx => $exercise) {
            $exerciseId = $exercise['id'];
            $typeId = $exercise['type'];

            if (isset($exerciseTypes[$typeId])) {
                $type = $exerciseTypes[$typeId];
                $exercise['typeName'] = $type['name'];
            } else {
                $exercise['typeName'] = "unknown";
            }

            $exerciseIndices[$exerciseId] = $idx;
            $exercises[] = $exercise;
        }

        // save a reference to each user's group and add exercises to each group
        $userGroups = array();
        foreach ($groups as &$group) {
            $leaderId = $group['leader']['id'];
            $userGroups[$leaderId] = &$group;

            if (isset($group['members'])){
                foreach ($group['members'] as $member) {
                    $memberId = $member['id'];
                    $userGroups[$memberId] = &$group;
                }
            }

            $group['exercises'] = $exercises;
        }
        
        foreach ($markings as $key => $marking) {
            $markings[$key]['submissionId'] = $markings[$key]['submission']['id'];
        }

        foreach ($submissions as $submission) {
            $studentId = $submission['studentId'];
            $exerciseId = $submission['exerciseId'];

            $exerciseIndex = $exerciseIndices[$exerciseId];

            $group = &$userGroups[$studentId];
            $group['exercises'][$exerciseIndex]['submission'] = $submission;
        }

        $filteredGroups = array();
        foreach ($submissions as $submission) {
            $marking = LArraySorter::multidimensional_search($markings, array('submissionId'=>$submission['id']));
            if ($marking!==false){
                unset($markings[$marking]['submission']);
                $submission['marking'] = $markings[$marking];
            }

            // filter out markings by the tutor with id $tutorid
            if (($shouldfilter == false) || $selector($submission, $tutorid, $statusid)) {
                //echo json_encode($submission);
                $exerciseId = $submission['exerciseId'];
                $exerciseIndex = $exerciseIndices[$exerciseId];
                $studentId = $submission['studentId'];

                // assign the submission to its group
                $group = &$userGroups[$studentId];
                $groupExercises = &$group['exercises'];
                $groupExercises[$exerciseIndex]['submission'] = $submission;
                $leaderId = &$group['leader']['id'];
                $filteredGroups[$leaderId] = &$group;
            } else {
                $exerciseId = $submission['exerciseId'];
                $exerciseIndex = $exerciseIndices[$exerciseId];
                $studentId = $submission['studentId'];
                $group = &$userGroups[$studentId];
                $groupExercises = &$group['exercises'];
                unset($groupExercises[$exerciseIndex]);
            }
        }
        
        if ($statusid==='0'){
            // remove groups with submissions
            $tempGroups=array();
            foreach ($groups as $key => $group){
                $temp2Groups=array();
                foreach ($group['exercises'] as $key2 => $exercise){
                    if (!isset($exercise['submission']) || (isset($exercise['submission']['marking']['status']) && $exercise['submission']['marking']['status']==='0')){
                        $temp2Groups[] = $exercise;
                    }
                }
                
                if (!empty($temp2Groups)){
                    $group['exercises']=$temp2Groups;
                    $tempGroups[] = $group;
                }
            }
            $groups=$tempGroups;
        }

        $response['groups'] = ($shouldfilter == true && $statusid!=='0') ? array_values($filteredGroups) : $groups;
        $response['tutors'] = $tutors;
        $response['exerciseSheets'] = $sheets;
        $response['markingStatus'] = Marking::getStatusDefinition();
        $response['namesOfExercises'] = $namesOfExercises;

        $this->flag = 1;
        $response['user'] = $this->userWithCourse($userid, $courseid);

        $this->app->response->setBody(json_encode($response));
    }

    /**
     * Compiles data for the marking tool page.
     * This version is used when no additional parameters are given.
     *
     * @author Florian Lücke
     */
    public function markingTool($userid, $courseid, $sheetid)
    {
        $this->markingToolBase($userid,
                               $courseid,
                               $sheetid,
                               NULL,
                               NULL,
                               false,
                               NULL);
    }

    /**
     * Compiles data for the marking tool page.
     * This version is used when we want markings from a specific tutor.
     *
     * @author Florian Lücke
     */
    public function markingToolTutor($userid, $courseid, $sheetid, $tutorid)
    {
        $selector = function ($submission, $tutorid, $statusid) {
            if (isset($submission['marking']['tutorId']) && $submission['marking']['tutorId'] == $tutorid) {
                return true;
            }

            return false;
        };

        $this->markingToolBase($userid,
                               $courseid,
                               $sheetid,
                               $tutorid,
                               NULL,
                               true,
                               $selector);
    }

    /**
     * Compiles data for the marking tool page.
     * This version is used when we want markings with a specific status.
     *
     * @todo male it possible to request unsubmitted exercises.
     *
     * @author Florian Lücke
     */
    public function markingToolStatus($userid, $courseid, $sheetid, $statusid)
    {
        $selector = function ($submission, $tutorid, $statusid) {
            if ((isset($submission['marking']['status']) && $submission['marking']['status'] === $statusid)) {
                return true;
            } elseif (($statusid == -1 && !isset($submission['marking']))) {;
                return true;
            }

            return false;
        };

        $this->markingToolBase($userid,
                               $courseid,
                               $sheetid,
                               NULL,
                               $statusid,
                               true,
                               $selector);
    }

    /**
     * Compiles data for the marking tool page.
     * This version is used when we want markings from a specific tutor and
     * with a specific status.
     *
     * @author Florian Lücke
     */
    public function markingToolTutorStatus($userid, $courseid, $sheetid, $tutorid, $statusid)
    {
        $selector = function ($submission, $tutorid, $statusid) {
            if (isset($submission['marking']) && ($submission['marking']['status'] === $statusid)
                && ($submission['marking']['tutorId'] == $tutorid)) {
                return true;
            }

            return false;
        };

        $this->markingToolBase($userid,
                               $courseid,
                               $sheetid,
                               $tutorid,
                               $statusid,
                               true,
                               $selector);
    }

    public function uploadHistory($userid, $courseid, $sheetid, $uploaduserid)
    {
        // load all exercises of an exercise sheet
        $URL = $this->lURL.'/exercisesheet/exercisesheet/'.$sheetid.'/exercise/';
        $answer = Request::custom('GET', $URL, array(), '');
        $exercisesheet = json_decode($answer['content'], true);

        if(!empty($exercisesheet)) {
            $exercises = $exercisesheet['exercises'];
            $response['exercises'] = $exercises;
        }

        // load all submissions for every exercise of the exerciseSheet
        $submissions = array();
        $markings = array();
        if(!empty($exercises)) {
            $URL = $this->_getSubmission->getAddress().'/submission/group/user/'.$uploaduserid.'/exercisesheet/'.$sheetid;
            $answer = Request::custom('GET', $URL, array(), '');
            $answer = json_decode($answer['content'], true);
            $URL = $this->_getMarking->getAddress().'/marking/exercisesheet/'.$sheetid.'/user/'.$uploaduserid;
            $answer2 = Request::custom('GET', $URL, array(), '');
            $answer2 = json_decode($answer2['content'], true);
            
            if(!empty($answer)) {
                foreach ($answer as $submission){
                    if (isset($submission['exerciseId'])){
                        if (!empty($answer2)){
                            foreach ($answer2 as $key => $marking){
                                if (isset($marking['submission']['id'])){
                                    unset($marking['submission']);
                                    $submission['marking'] = $marking;
                                    unset($answer2[$key]);
                                    break;
                                }
                            }
                        }
                        
                        $submissions[$submission['exerciseId']][] = $submission;
                    }
                }
            }
        }
        
        function compare_submissionTimeStamp($a, $b) {
             return strnatcmp($b['date'], $a['date']);
        }
        
        // sort submissions by time stamp
        foreach ($submissions as $key => $submissionList){
            usort($submissionList, 'compare_submissionTimeStamp');
            $submissions[$key] = $submissionList;
        }
        
        //Get the Group of the User for the given sheet
        $URL = "{$this->_getGroup->getAddress()}/group/user/{$uploaduserid}/exercisesheet/{$sheetid}";
        $answer = Request::custom('GET', $URL, array(), '');
        $group = json_decode($answer['content'], true);
        
        $response['group'] = $group;
        $response['submissionHistory'] = $submissions;

        $this->flag = 1;
        $response['user'] = $this->userWithCourse($userid, $courseid);

        $this->app->response->setBody(json_encode($response));
    }


    public function uploadHistoryOptions($userid, $courseid)
    {
        // load all users of the course
        $URL = $this->_getUser->getAddress().'/user/course/'.$courseid;
        $answer = Request::custom('GET', $URL, array(), '');
        $response['users'] = json_decode($answer['content'], true);

        // load all exercisesheets of the course
        $URL = $this->lURL.'/exercisesheet/course/'.$courseid;
        $answer = Request::custom('GET', $URL, array(), '');
        $response['sheets'] = json_decode($answer['content'], true);

        if(!empty($exercisesheet)) {
            $exercises = $exercisesheet['exercises'];
        }

        $this->flag = 1;
        $response['user'] = $this->userWithCourse($userid, $courseid);

        $this->app->response->setBody(json_encode($response));
    }

    /**
     * Compiles data for the upload page.
     * called whe the component receives an HTTP GET request to
     * /upload/user/$userid/course/$courseid/exercisesheet/$sheetid
     *
     * @author Florian Lücke.
     */
    public function tutorUpload($userid, $courseid)
    {
        $response = array();
        $this->flag = 1;
        $response['user'] = $this->userWithCourse($userid, $courseid);

        $this->app->response->setBody(json_encode($response));
    }
    
    public function upload($userid, $courseid, $sheetid)
    {
        // loads all exercises of an exercise sheet
        $URL = "{$this->lURL}/exercisesheet/exercisesheet/{$sheetid}/exercise";
        $answer = Request::custom('GET', $URL, array(), '');
        $exercisesheet = json_decode($answer['content'], true);

        $URL = "{$this->_getSubmission->getAddress()}/submission/group/user/{$userid}/exercisesheet/{$sheetid}/selected";
        $answer = Request::custom('GET', $URL, array(), '');
        $submissions = json_decode($answer['content'], true);

        $URL = "{$this->_getExerciseType->getAddress()}/exercisetype";
        $answer = Request::custom('GET', $URL, array(), '');
        $possibleExerciseTypes = json_decode($answer['content'], true);
        
        $exerciseTypes = array();
        foreach ($possibleExerciseTypes as $exerciseType) {
            $exerciseTypes[$exerciseType['id']] = $exerciseType;
        }

        if (isset($submissions) == false) {
            $submissions = array();
        }

        $exercises = &$exercisesheet['exercises'];

        $submissionsByExercise = array();
        foreach ($submissions as &$submission) {
            $exerciseId = $submission['exerciseId'];
            $submissionsByExercise[$exerciseId] = &$submission;
        }

        // loads all submissions for every exercise of the exerciseSheet
        if (!empty($exercises)) {
            foreach ($exercises as &$exercise) {
                $exerciseId = $exercise['id'];

                if (isset($submissionsByExercise[$exerciseId])) {
                    $submission = &$submissionsByExercise[$exerciseId];
                    $exercise['selectedSubmission'] = &$submission;
                }
            }
        }

        $response = array();
        $response['exercises'] = $exercises;
        if (isset($exercisesheet['sheetName'])) {
            $response['sheetName'] = $exercisesheet['sheetName'];
        }

        $this->flag = 1;
        $response['user'] = $this->userWithCourse($userid, $courseid);
        $response['exerciseTypes'] = $exerciseTypes;
        
        $exercisesheet['exercises'] = null;
        $response['exerciseSheet'] = $exercisesheet;

        $this->app->response->setBody(json_encode($response));
    }

    public function mainSettings($userid)
    {
        // returns all courses
        $URL = $this->_getCourse->getAddress().'/course';
        $courses = Request::custom('GET', $URL, array(), '');
        $courses = json_decode($courses['content'], true);

        // returns all possible exercisetypes
        $URL = $this->_getExerciseType->getAddress().'/exercisetype';
        $exerciseTypes = Request::custom('GET', $URL, array(), '');
        $response['exerciseTypes'] = json_decode($exerciseTypes['content'], true);

        // returns the user
        $URL = $this->_getUser->getAddress().'/user/user/' . $userid;
        $answer = Request::custom('GET', $URL, array(), '');
        $user = json_decode($answer['content'], true);

        unset($user['courses']);

        // sorts courses by name
        function compare_courseName($a, $b) {
             return strnatcmp($a['name'], $b['name']);
        }
        usort($courses, 'compare_courseName');

        $this->flag = 1;

        $response['courses'] = $courses;
        $response['user'] = $user;

        $this->app->response->setBody(json_encode($response));
    }

    public function tutorDozentAdmin($userid, $courseid)
    {
        // load first pack of Requests
        $multiRequestHandle2 = new Request_MultiRequest();

        $URL = $this->_getExerciseType->getAddress().'/exercisetype';
        $handler1 = Request_CreateRequest::createGet($URL, array(), '');
        $URL = $this->lURL . '/exercisesheet/course/' . $courseid . '/exercise';
        $handler2 = Request_CreateRequest::createGet($URL, array(), '');
        
        // to get all students of the course
        $URL = $this->_getUser->getAddress().'/user/course/' . $courseid. '/status/0'; //$this->_getGroup->getAddress().'/group/'
        $handler3 = Request_CreateRequest::createGet($URL, array(), '');
        $URL = $this->_getMarking->getAddress().'/marking/course/'.$courseid;
        $handler4 = Request_CreateRequest::createGet($URL, array(), '');

        $multiRequestHandle2->addRequest($handler1);
        $multiRequestHandle2->addRequest($handler2);
        $multiRequestHandle2->addRequest($handler3);
        $multiRequestHandle2->addRequest($handler4);

        $answer2 = $multiRequestHandle2->run();
        unset($multiRequestHandle2);

        // decode answers (given in the order which they've been declared)
        $exerciseTypes = json_decode($answer2[0]['content'], true);
        $sheets = json_decode($answer2[1]['content'], true);
        $courseUser = json_decode($answer2[2]['content'], true);
        $markings = json_decode($answer2[3]['content'], true);
        unset($answer2);

        $URL = "{$this->_getSelectedSubmission->getAddress()}/selectedsubmission/course/{$courseid}";
        $answer = Request::custom('GET', $URL, array(), '');
        $selectedSubs = json_decode($answer['content'], true);
        unset($answer);
        $selectedSubmissionsCount = null;
        foreach($selectedSubs as $subs){
            $key = $subs['exerciseSheetId'];
            $eid = $subs['exerciseId'];
            $userId = $subs['leaderId'];
            if (!isset($selectedSubmissionsCount[$key]))
                $selectedSubmissionsCount[$key] = array();
                
            if (!isset($selectedSubmissionsCount[$key]['selected'])){
                $selectedSubmissionsCount[$key]['selected']=1;
            } else {
                $selectedSubmissionsCount[$key]['selected']+=1;
            }
            
            if (!isset($selectedSubmissionsCount[$key]['submissionsCount']))
                $selectedSubmissionsCount[$key]['submissionsCount'] = array();
            
            if (!isset($selectedSubmissionsCount[$key]['submissionsCount'][$eid]))
                $selectedSubmissionsCount[$key]['submissionsCount'][$eid] = array();
                        
            if (!isset($selectedSubmissionsCount[$key]['submissionsCount'][$eid][$userId])){
                $selectedSubmissionsCount[$key]['submissionsCount'][$eid][] = $userId;
            }
        }
        unset($selectedSubs);
        
        foreach ($markings as $marking){
            if (isset($marking['submission']['selectedForGroup']) && $marking['submission']['selectedForGroup']){
                $key = $marking['submission']['exerciseSheetId'];
                
                if (isset($marking['tutorId']) && $marking['tutorId']==$userid){
                    if (!isset($selectedSubmissionsCount[$key]))
                        $selectedSubmissionsCount[$key] = array();
                    
                    if (!isset($selectedSubmissionsCount[$key]['tutorMarkings'])){
                        $selectedSubmissionsCount[$key]['tutorMarkings']=1;
                    } else {
                        $selectedSubmissionsCount[$key]['tutorMarkings']++;
                    }  
                    
                    if (!isset($selectedSubmissionsCount[$key]['status'][$marking['status']])){
                        $selectedSubmissionsCount[$key]['status'][$marking['status']]=1;
                    } else {
                        $selectedSubmissionsCount[$key]['status'][$marking['status']]++;
                    }
                }
                
                {
                    if (!isset($selectedSubmissionsCount[$key]['allMarkings'])){
                        $selectedSubmissionsCount[$key]['allMarkings']=1;
                    } else {
                        $selectedSubmissionsCount[$key]['allMarkings']++;
                    }
                    
                    if (!isset($selectedSubmissionsCount[$key]['allStatus'][$marking['status']])){
                        $selectedSubmissionsCount[$key]['allStatus'][$marking['status']]=1;
                    } else {
                        $selectedSubmissionsCount[$key]['allStatus'][$marking['status']]++;
                    }
                }
            }
        }
        
        if (isset($selectedSubmissionsCount))
            foreach ($selectedSubmissionsCount as $key => $value){
                if (!isset($selectedSubmissionsCount[$key]['allMarkings']))$selectedSubmissionsCount[$key]['allMarkings']=0;
                
                if (isset($selectedSubmissionsCount[$key]['selected']) && isset($selectedSubmissionsCount[$key]['allMarkings'])){
                    $selectedSubmissionsCount[$key]['allStatus']['-1'] = $selectedSubmissionsCount[$key]['selected'] - $selectedSubmissionsCount[$key]['allMarkings'];
                if ($selectedSubmissionsCount[$key]['allStatus']['-1']==0)
                    unset($selectedSubmissionsCount[$key]['allStatus']['-1']);
                }
            }
        unset($markings);

        if (isset($sheets)){
            foreach ($sheets as $key => &$sheet) {

                $hasAttachments = false;
                foreach ($sheet['exercises'] as &$exercise) {
                
                    // add attachments to exercise
                    if (count($exercise['attachments']) > 0) {
                        $exercise['attachment'] = $exercise['attachments'][0];
                        $hasAttachments = true;
                        break;
                    }
                }

                $sheet['hasAttachments'] = $hasAttachments;

                // adds counts for the additional information in the footer
                $sheet['courseUserCount'] = count($courseUser);
                $sheet['submissionStats'] = (isset($selectedSubmissionsCount[$sheet['id']]['submissionsCount']) ? $selectedSubmissionsCount[$sheet['id']]['submissionsCount'] : null);
                $sheet['selectedSubmissions'] = (isset($selectedSubmissionsCount[$sheet['id']]['selected']) ? $selectedSubmissionsCount[$sheet['id']]['selected'] : 0);
                $sheet['tutorMarkings'] = (isset($selectedSubmissionsCount[$sheet['id']]['tutorMarkings']) ? $selectedSubmissionsCount[$sheet['id']]['tutorMarkings'] : 0);
                
                if (isset($selectedSubmissionsCount[$sheet['id']]['status']))
                    foreach ($selectedSubmissionsCount[$sheet['id']]['status'] as $key => $value)
                        $sheet['status'][$key] = $value;
                        
                if (isset($selectedSubmissionsCount[$sheet['id']]['allStatus']))
                    foreach ($selectedSubmissionsCount[$sheet['id']]['allStatus'] as $key => $value)
                        $sheet['allStatus'][$key] = $value;
                        
                foreach ($sheet['exercises'] as &$exercise) {
                    foreach ($exerciseTypes as $exerciseType) {
                        if ($exerciseType['id'] == $exercise['type']) {
                            $exercise['typeName'] = $exerciseType['name'];
                        }
                    }
                }
            }

            $response['sheets'] = $sheets;
        }

        $this->flag = 1;
        $response['user'] = $this->userWithCourse($userid, $courseid);

        $this->app->response->setBody(json_encode($response));
    }

    /**
     * Compiles data for group site.
     * Called when this component receives an HTTP GET request to
     * /upload/user/$userid/course/$courseid/exercisesheet/$sheetid
     *
     * @author Florian Lücke.
     */
    public function groupSite($userid, $courseid, $sheetid)
    {
        $response = array();

        //Get the Group of the User for the given sheet
        $URL = "{$this->_getGroup->getAddress()}/group/user/{$userid}/exercisesheet/{$sheetid}";
        $answer = Request::custom('GET', $URL, array(), '');
        $group = json_decode($answer['content'], true);
        unset($answer);

        //Get the maximum Groupsize of the sheet
        $URL = "{$this->lURL}/exercisesheet/exercisesheet/{$sheetid}/exercise";
        $answer = Request::custom('GET', $URL, array(), '');
        $sheet = json_decode($answer['content'], true);
        unset($answer);

        $exercises = &$sheet['exercises'];
        
        // compute the group from sheet before
        $URL = "{$this->lURL}/exercisesheet/course/{$courseid}";
        $answer = Request::custom('GET', $URL, array(), '');
        $courseSheets = json_decode($answer['content'], true);
        unset($answer);
        
        $lastSheet = null;
        foreach ($courseSheets as $key => $sh){
            if ($sheet['id'] == $sh['id']){
                if (isset($courseSheets[$key+1]))
                    $lastSheet = $courseSheets[$key+1];
                break;
            }
        }
        unset($courseSheets);
        
        $lastGroup = null;
        if ($lastSheet !== null && isset($lastSheet['id'])){
            $URL = "{$this->_getGroup->getAddress()}/group/user/{$userid}/exercisesheet/{$lastSheet['id']}";
            $answer = Request::custom('GET', $URL, array(), '');
            $lastGroup = json_decode($answer['content'], true);
            unset($answer);
        }

        $URL = "{$this->_getSubmission->getAddress()}/submission/group/user/{$userid}/exercisesheet/{$sheetid}";
        $answer = Request::custom('GET', $URL, array(), '');
        $submissions = json_decode($answer['content'], true);
        unset($answer);

        $URL = "{$this->_getInvitation->getAddress()}/invitation/leader/exercisesheet/{$sheetid}/user/{$userid}";
        $answer = Request::custom('GET', $URL, array(), '');
        $invited = json_decode($answer['content'], true);
        unset($answer);
//var_dump($invited);
        $URL = "{$this->_getInvitation->getAddress()}/invitation/member/exercisesheet/{$sheetid}/user/{$userid}";
        $answer = Request::custom('GET', $URL, array(), '');
        $invitations = json_decode($answer['content'], true);
        unset($answer);
///var_dump($invitations);
        // order users by id
        
        $usersById = array();
        if (isset($group['leader']['id'])){
            $leaderId = $group['leader']['id'];
            $usersById[$leaderId] = &$group['leader'];
            if (isset($group['members'])){
                foreach ($group['members'] as &$member) {
                    $uId = $member['id'];
                    $usersById[$uId] = &$member;
                }
            }
        }

        // order submissions by exercise and user, only take latest
        $exerciseUserSubmissions = array();
        foreach ($submissions as $submission) {
            $uId = $submission['studentId'];
            $exerciseId = $submission['exerciseId'];
            
            if (isset($submission['flag']) && $submission['flag']==1){
                if (isset($exerciseUserSubmissions[$exerciseId]) == false) {
                    $exerciseUserSubmissions[$exerciseId] = array();
                }

                if (isset($exerciseUserSubmissions[$exerciseId][$uId]) == false) {
                    $user = &$usersById[$uId];
                    $userSubmission = array('user' => $user,
                                            'submission' => $submission);
                    $exerciseUserSubmissions[$exerciseId][$uId] = $userSubmission;
                } else {
                    $lastUserSubmission = $exerciseUserSubmissions[$exerciseId][$uId];
                    if ($lastUserSubmission['submission']['date'] < $submission['date']) {

                        // smaller date means less seconds since refrence date
                        // so $lastSubmission is older
                        $user = &$usersById[$uId];
                        $userSubmission = array('user' => $user,
                                                'submission' => $submission);
                        $exerciseUserSubmissions[$exerciseId][$uId] = $userSubmission;
                    }
                }
            }
        }

        // insert submissions into the exercises
        foreach ($exercises as &$exercise) {
            $exerciseId = &$exercise['id'];
            if (isset($exerciseUserSubmissions[$exerciseId])) {
                $groupSubmissions = array_values($exerciseUserSubmissions[$exerciseId]);
                $exercise['groupSubmissions'] = $groupSubmissions;
            } else {
                $exercise['groupSubmissions'] = array();
            }

        }

        $response['invitationsFromGroup'] = $invited;
        $response['invitationsToGroup'] = $invitations;
        $response['exercises'] = $exercises;
        
        if (isset($sheet['exercises']))
            unset($sheet['exercises']);
    
        $response['exerciseSheet'] = $sheet;
        $response['group'] = $group;
        $response['groupSize'] = $sheet['groupSize'];
        $response['lastGroup'] = $lastGroup;
        $response['allowApplyGroup'] = 0;
        if ((!isset($group['members']) || count($group['members'])==0) 
                && $lastGroup !== null 
                && $sheet['groupSize'] >= $lastSheet['groupSize'] 
                && (isset($lastGroup['leader']['id']) && $lastGroup['leader']['id'] == $userid)
                && isset($lastGroup['members']) 
                && count($lastGroup['members']) >0 
                && count($invited)==0 
                && count($invitations)==0){
            $response['allowApplyGroup']=1;
        }

        $this->flag = 1;
        $response['user'] = $this->userWithCourse($userid, $courseid);

        $this->app->response->setBody(json_encode($response));
    }


    /**
     * Compiles data for the Condition site.
     *
     * @warning If there is more than one condition assigned to the same
     * exercise type it is undefined which condition will be evaluated. This
     * might even change per user!.
     *
     * @author Florian Lücke
     */
    public function checkCondition($userid, $courseid)
    {
        // load all the data
        $multiRequestHandle = new Request_MultiRequest();
        
        $URL = $this->_getExerciseType->getAddress() . '/exercisetype';
        $handler = Request_CreateRequest::createCustom('GET', $URL, array(),'');
        $multiRequestHandle->addRequest($handler);

        $URL = $this->_getExercise->getAddress() . '/exercise/course/'.$courseid.'/nosubmission';
        $handler = Request_CreateRequest::createCustom('GET', $URL, array(),'');
        $multiRequestHandle->addRequest($handler);

        $URL = $this->_getApprovalCondition->getAddress() . '/approvalcondition/course/'.$courseid;
        $handler = Request_CreateRequest::createCustom('GET', $URL, array(),'');
        $multiRequestHandle->addRequest($handler);

        $URL = $this->_getUser->getAddress() . '/user/course/'.$courseid.'/status/0';
        $handler = Request_CreateRequest::createCustom('GET', $URL, array(),'');
        $multiRequestHandle->addRequest($handler);  

        $URL = $this->_getGroup->getAddress().'/group/course/' . $courseid;
        $handler = Request_CreateRequest::createGet($URL, array(), '');
        $multiRequestHandle->addRequest($handler);        
        
        $answer = $multiRequestHandle->run();
        
        $possibleExerciseTypes = json_decode($answer[0]['content'], true);
        $exercises = json_decode($answer[1]['content'], true);
        $approvalconditions = json_decode($answer[2]['content'], true);
        $students = json_decode($answer[3]['content'], true);
        $groups = json_decode($answer[4]['content'], true);

        // preprocess the data to make it quicker to get specific values
        $exerciseTypes = array();
        foreach ($possibleExerciseTypes as $exerciseType) {
            $exerciseTypes[$exerciseType['id']] = $exerciseType;
        }

        $exercisesById = array();
        foreach ($exercises as $exercise) {
            $exercisesById[$exercise['id']] = $exercise;
        }

        $exercisesByType = array();
        foreach ($exercises as $exercise) {
            if (!isset($exercisesByType[$exercise['type']])) {
                $exercisesByType[$exercise['type']] = array();
            }
            unset($exercise['submissions']);
            $exercisesByType[$exercise['type']][] = $exercise;
        }

        // calculate the maximum number of points that a user could get
        // for each exercise type
        $maxPointsByType = array();
        foreach ($exercisesByType as $type => $exercises) {
            $maxPointsByType[$type] = array_reduce($exercises,
                                                   function ($value, $exercise) {
                if ($exercise['bonus'] == null || $exercise['bonus'] == '0') {
                    // only count the
                    $value += $exercise['maxPoints'];
                }

                return $value;
            }, 0);
        }

        $approvalconditionsByType = array();
        foreach ($approvalconditions as &$condition){
            // add the name of the exercise type to the approvalcondition
            $typeID = $condition['exerciseTypeId'];
            $condition['exerciseType'] = $exerciseTypes[$typeID]['name'];

            // prepare percenteages for the UI
            $condition['minimumPercentage'] = $condition['percentage'] * 100;

            $condition['approvalConditionId'] = $condition['id'];
            unset($condition['id']);
            // sort approvalconditions by exercise type
            /**
              * @warning this implies that there is *only one* approval
              * condition per exercise type!
              */
            $exerciseTypeID = $condition['exerciseTypeId'];

            if (isset($maxPointsByType[$exerciseTypeID])) {
                $condition['maxPoints'] = $maxPointsByType[$exerciseTypeID];
            } else {
                $condition['maxPoints'] = 0;
            }

            $approvalconditionsByType[$exerciseTypeID] = $condition;

        }

        // get all markings
        $allMarkings = array();
        $URL = $this->_getMarking->getAddress() . '/marking/course/'.$courseid;
        $answer = Request::custom('GET', $URL, array(), '');
        $markings = json_decode($answer['content'], true);

        foreach($markings as $marking){
            if (isset($marking['submission']['selectedForGroup']) && $marking['submission']['selectedForGroup'] == 1)
                $allMarkings[] = $marking;
        }
        unset($markings);
        
        $allGroups = array();
        foreach ($groups as $group){
            if (!isset($allGroups[$group['sheetId']]))
                $allGroups[$group['sheetId']] = array();
            $allGroups[$group['sheetId']][$group['leader']['id']] = $group;
        }
        unset($groups);

        // done preprocessing
        // actual computation starts here

        // add up points that each student reached in a specific exercise type
        $studentMarkings = array();
        foreach ($allMarkings as $marking) {
            $studentID = $marking['submission']['studentId'];
            $leaderID = $marking['submission']['leaderId'];
            if (!isset($studentMarkings[$studentID]))
                $studentMarkings[$studentID] = array();
            if (!isset($marking['submission']['accepted']) || $marking['submission']['accepted']==0)
                continue;

            $exerciseID = $marking['submission']['exerciseId'];
            $sheetID = $marking['submission']['exerciseSheetId'];
            $exerciseType = $exercisesById[$exerciseID]['type'];

            if (!isset($studentMarkings[$studentID][$exerciseType]))
                $studentMarkings[$studentID][$exerciseType] = 0;

            $studentMarkings[$studentID][$exerciseType] += isset($marking['points']) ? $marking['points'] : 0;
            
            if (isset($allGroups[$sheetID][$leaderID])){
                $group = $allGroups[$sheetID][$leaderID];
                if (isset($group['members'])){
                    foreach ($group['members'] as $member){
                        if (!isset($studentMarkings[$member['id']]))
                            $studentMarkings[$member['id']] = array();
                        if (!isset($studentMarkings[$member['id']][$exerciseType]))
                            $studentMarkings[$member['id']][$exerciseType] = 0;
                        
                        $studentMarkings[$member['id']][$exerciseType] += isset($marking['points']) ? $marking['points'] : 0;
                    }
                }
            }
        }

        $resultStudents = array();
        foreach ($students as $student) {
            if (!isset($student['id'])) continue;
            
            if (isset($student['courses']))
                unset($student['courses']);
            if (isset($student['attachments']))
                unset($student['attachments']);
            $student['percentages'] = array();

            $allApproved = true;
            // iteraterate over all conditions, this will also filter out the
            // exercisetypes that are not needed for this course
            foreach ($approvalconditionsByType as $typeID => $condition) {

                    $thisPercentage = array();

                    $thisPercentage['exerciseTypeID'] = $typeID;
                    $thisPercentage['exerciseType'] = $exerciseTypes[$typeID]['name'];

                    // check if it was possible to get points for this exercisetype
                    if (!isset($maxPointsByType[$typeID])) {
                        Logger::Log("Unmatchable condition: "
                                    . $condition['approvalConditionId']
                                    . "in course: "
                                    . $courseid, LogLevel::WARNING);

                        $maxPointsByType[$typeID] = 0;
                    }

                    if ($maxPointsByType[$typeID] == 0) {
                        $thisPercentage['percentage'] = '100';
                        $thisPercentage['isApproved'] = true;
                        $thisPercentage['maxPoints'] = 0;

                        if (isset($student['id']) && isset($studentMarkings[$student['id']])
                            && isset($studentMarkings[$student['id']][$typeID])) {
                            $points = $studentMarkings[$student['id']][$typeID];
                            $thisPercentage['points'] = $points;
                        } elseif (isset($student['id'])) {
                            $thisPercentage['points'] = 0;
                        }
                    } else {

                        // check if there are points for this
                        // student-exerciseType combination
                        if (isset($student['id']) && isset($studentMarkings[$student['id']])
                            && isset($studentMarkings[$student['id']][$typeID])) {
                            // the user has points for this exercise type

                            $points = $studentMarkings[$student['id']][$typeID];
                            $maxPoints = $maxPointsByType[$typeID];
                            $percentage = $points / $maxPoints;

                            $percentageNeeded = $condition['percentage'];

                            $thisPercentage['points'] = $points;
                            $thisPercentage['maxPoints'] = $maxPoints;

                            $typeApproved = ($percentage >= $percentageNeeded);
                            $allApproved = $allApproved && $typeApproved;

                            $thisPercentage['isApproved'] = $typeApproved;
                            $thisPercentage['percentage'] = round($percentage * 100, 2);
                        } elseif (isset($student['id']))  {

                            // there are no points for the user for this
                            // exercise type
                            $thisPercentage['percentage'] = 0;
                            $thisPercentage['points'] = 0;

                            $maxPoints = $maxPointsByType[$typeID];
                            $thisPercentage['maxPoints'] = $maxPoints;

                            $typeApproved = ($maxPoints == 0);
                            $thisPercentage['isApproved'] = $typeApproved;

                            $allApproved = $allApproved && $typeApproved;
                        }

                    }

                    $student['percentages'][] = $thisPercentage;
            }

            $student['isApproved'] = $allApproved;
            $resultStudents[] = $student;
        }
        $students = $resultStudents;

        $this->flag = 1;
        $response['user'] = $this->userWithCourse($userid, $courseid);

        if (isset($students))
            $response['users'] = $students;

        $response['minimumPercentages'] = array_values($approvalconditionsByType);

        $this->app->response->setBody(json_encode($response));
    }

    public function courseManagement($userid, $courseid)
    {
        // returns basic course information
        $URL = $this->_getCourse->getAddress() . '/course/'.$courseid;
        $answer = Request::custom('GET', $URL, array(), '');
        $response['course'] = json_decode($answer['content'], true);

        // returns all exerciseTypes
        $URL = $this->_getExerciseType->getAddress() . '/exercisetype';
        $answer = Request::custom('GET', $URL, array(), '');
        $response['exerciseTypes'] = json_decode($answer['content'], true);

        // returns all possible exerciseTypes of the course
        $URL = $this->_getApprovalCondition->getAddress() . '/approvalcondition/course/' . $courseid;
        $answer = Request::custom('GET', $URL, array(), '');
        $approvalConditions = json_decode($answer['content'], true);

        // returns all users of the given course
        $URL = $this->_getUser->getAddress() . '/user/course/'.$courseid;
        $answer = Request::custom('GET', $URL, array(), '');
        $allUsers = json_decode($answer['content'], true);

        // adds an 'inCourse' flag to the exerciseType if there is
        // an approvalCondition with the same id in the same course

        /**
         * @todo Improve running time.
         */
        if(!empty($approvalConditions)) {
            foreach ($approvalConditions as &$approvalCondition) {
                foreach ($response['exerciseTypes'] as &$exerciseType) {
                    if ($approvalCondition['exerciseTypeId'] == $exerciseType['id']) {
                        $exerciseType['inCourse'] = true;
                    }
                }
            }
        }

        // only selects the users whose course-status is student, tutor, lecturer or admin
        if(!empty($allUsers)) {
            foreach($allUsers as $user) {
                if ($user['courses'][0]['status'] >= 0 && $user['courses'][0]['status'] < 4) {

                    // adds the course-status to the user objects in the response
                    $user['statusName'] = $this->getStatusName($user['courses'][0]['status']);

                    // removes unnecessary data from the user object
                    unset($user['password']);
                    unset($user['salt']);
                    unset($user['failedLogins']);
                    unset($user['courses']);

                    // adds the user to the response
                    $response['users'][] = $user;
                }
            }
        }

        $this->flag = 1;

        // adds the user_course_data to the response
        $response['user'] = $this->userWithCourse($userid, $courseid);

        $this->app->response->setBody(json_encode($response));
    }

    public function createSheetInfo($userid, $courseid) 
    {

        // returns all possible exerciseTypes of the course
        $URL = $this->_getApprovalCondition->getAddress() . '/approvalcondition/course/' . $courseid;
        $answer = Request::custom('GET', $URL, array(), '');
        $response['exerciseTypes'] = json_decode($answer['content'], true);

        // returns all exerciseTypes
        $URL = $this->_getExerciseType->getAddress() . '/exercisetype';
        $answer = Request::custom('GET', $URL, array(), '');
        $allexerciseTypes = json_decode($answer['content'], true);

        if(!empty($response['exerciseTypes'])) {
            foreach ($response['exerciseTypes'] as &$exerciseType) {
                foreach ($allexerciseTypes as &$allexerciseType) {
                    if ($exerciseType['exerciseTypeId'] == $allexerciseType['id']) {
                        $exerciseType['name'] = $allexerciseType['name'];
                    }
                }
            }
        } else {
            unset($response['exerciseTypes']);
        }

        $this->flag = 1;

        // adds the user_course_data to the response
        $response['user'] = $this->userWithCourse($userid, $courseid);

        $this->app->response->setBody(json_encode($response));
    }
}
