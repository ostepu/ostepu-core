<?php
set_time_limit(180);
/**
 * @file LTutor.php Contains the LTutor class
 *
 * @author Peter Koenig
 * @author Christian Elze
 * @author Martin Daute
 * @date 2013-2014
 */
require_once dirname(__FILE__) . '/../../Assistants/Slim/Slim.php';
include_once dirname(__FILE__) . '/../../Assistants/Request.php';
include_once dirname(__FILE__) . '/../../Assistants/CConfig.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures/Transaction.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures/Platform.php';

\Slim\Slim::registerAutoloader();

/**
 * The LTutor class
 *
 * This class handles everything belongs to TutorAssignments
 */
class LTutor
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;
    private $config = array();

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "tutor";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LTutor::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LTutor::$_prefix = $value;
    }

    /**
     * @var string $lURL the URL of the logic-controller
     */
    private $lURL = ""; //aus config lesen
    
    private $_postTransaction = array();
    private $_getTransaction = array();
    private $_postZip = array();
    private $_postPdf = array();
    private $_postMarking = array();
    private $_getMarking = array();
    private $_getExercise = array();
    private $_getGroup = array();
    private $_getSubmission = array();
    private $_postSubmission = array();

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct()
    {
        // runs the CConfig
        $com = new CConfig( LTutor::getPrefix( ), dirname(__FILE__) );

        // runs the LTutor
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
            
        /**
         *Initialise the Slim-Framework
         */
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        
        if (file_exists(dirname(__FILE__).'/config.ini'))
            $this->config = parse_ini_file( 
                                           dirname(__FILE__).'/config.ini',
                                           TRUE
                                           ); 

        /**
         *Set the Logiccontroller-URL
         */
        $this->_conf = $conf;
        $this->query = array();
        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        
        $this->_postTransaction = array( CConfig::getLink( 
                                                        $this->_conf->getLinks( ),
                                                        'postTransaction'
                                                        ) );
        $this->_getTransaction = array( CConfig::getLink( 
                                                        $this->_conf->getLinks( ),
                                                        'getTransaction'
                                                        ) );
        $this->_postZip = array( CConfig::getLink( 
                                                    $this->_conf->getLinks( ),
                                                    'postZip'
                                                    ) );
        $this->_postPdf = array( CConfig::getLink( 
                                                    $this->_conf->getLinks( ),
                                                    'postPdf'
                                                    ) );
        $this->_postMarking = array( CConfig::getLink( 
                                                    $this->_conf->getLinks( ),
                                                    'postMarking'
                                                    ) );
        $this->_getMarking = array( CConfig::getLink( 
                                                        $this->_conf->getLinks( ),
                                                        'getMarking'
                                                        ) );
        $this->_getExercise = array( CConfig::getLink( 
                                                        $this->_conf->getLinks( ),
                                                        'getExercise'
                                                        ) );
        $this->_getGroup = array( CConfig::getLink( 
                                                        $this->_conf->getLinks( ),
                                                        'getGroup'
                                                        ) );
        $this->_getSubmission = array( CConfig::getLink( 
                                                        $this->_conf->getLinks( ),
                                                        'getSubmission'
                                                        ) );
        $this->_postSubmission = array( CConfig::getLink( 
                                                        $this->_conf->getLinks( ),
                                                        'postSubmission'
                                                        ) );
                                                        
        // initialize lURL
        $this->lURL = $this->query->getAddress();
        
        // POST AddPlatform
        $this->app->post( 
                         '/platform',
                         array( 
                               $this,
                               'addPlatform'
                               )
                         );
                         
        // DELETE DeletePlatform
        $this->app->delete( 
                         '/platform',
                         array( 
                               $this,
                               'deletePlatform'
                               )
                         );
                         
        // GET GetExistsPlatform
        $this->app->get( 
                         '/link/exists/platform',
                         array( 
                               $this,
                               'getExistsPlatform'
                               )
                         );
                         
        // POST PostSubmissionConvert
        $this->app->post( 
                         '/submission/convert(/timestamp/begin/:begin/end/:end)',
                         array( 
                               $this,
                               'postSubmissionConvert'
                               )
                         );

        //Set auto allocation by exercise
        $this->app->post('/'.$this->getPrefix().
            '/auto/exercise/course/:courseid/exercisesheet/:sheetid(/)',
                array($this, 'autoAllocateByExercise'));

        //Set auto allocation by group
        $this->app->post('/'.$this->getPrefix().
            '/auto/group/course/:courseid/exercisesheet/:sheetid(/)',
                array($this, 'autoAllocateByGroup'));

        //Get zip
        $this->app->get('/'.$this->getPrefix().'/user/:userid/exercisesheet/:sheetid(/status/:status)(/)',
                array($this, 'getZip'));
                
        //Post zip
        $this->app->post('/'.$this->getPrefix().'/archive/user/:userid/exercisesheet/:sheetid(/)',
                array($this, 'postTutorArchive'));

        //Post zip
        $this->app->post('/'.$this->getPrefix().'/archive/user/:userid/exercisesheet/:sheetid/withnames(/)',
                array($this, 'postTutorArchiveWithNames'));
                
        //uploadZip
        $this->app->post('/'.$this->getPrefix().'/user/:userid/course/:courseid(/)', array($this, 'uploadZip'));

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
        $body = json_decode($this->app->request->getBody(), true);

        $error = false;

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
        $URL = $this->lURL.'/DB/marking';
        /*foreach($markings as $marking){
            $answer = Request::custom('POST', $URL, $header,
                    json_encode($marking));
            if ($answer['status'] >= 300){
                $error = true;
                $errorstatus = $answer['status'];
            }
        }*/
        $answer = Request::custom('POST', $URL, array(),
            json_encode($markings));
        if ($answer['status'] >= 300){
            $error = true;
            $errorstatus = $answer['status'];
        }
        
        // response
        if ($error == false){
            $this->app->response->setStatus(201);
            $this->app->response->setBody("");
        } else {
            $this->app->response->setStatus($errorstatus);
            $this->app->response->setBody("Warning: At least one exercise was not being allocated!");
        }

      //  $URL = $this->lURL.'/getSite/tutorassign/user/3/course/'
      //                  .$courseid.'/exercisesheet/'.$sheetid;
      //  $answer = Request::custom('GET', $URL, $header, "");
      //  $this->app->response->setBody($answer['content']);
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
        $body = json_decode($this->app->request->getBody(), true);

        $error = false;

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
                    'status' => 1,
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
        $URL = $this->lURL.'/DB/marking';
        /*foreach($markings as $marking){
            $answer = Request::custom('POST', $URL, array(),
                    json_encode($marking));
            if ($answer['status'] >= 300){
                $error = true;
                $errorstatus = $answer['status'];
            }
        }*/
        $answer = Request::custom('POST', $URL, array(),
                    json_encode($markings));
        if ($answer['status'] >= 300){
            $error = true;
            $errorstatus = $answer['status'];
        }
        
        // response
        if ($error == false){
            $this->app->response->setStatus(201);
            $this->app->response->setBody("");
        } else {
            $this->app->response->setStatus($errorstatus);
            $this->app->response->setBody("Warning: At least one group was not being allocated!");
        }

       // $URL = $this->lURL.'/getsite/tutorassignment/course/'
       //             .$courseid.'/exercisesheet/'.$sheetid;
       // $answer = Request::custom('GET', $URL, $header, "");
       //
       // $this->app->response->setBody($answer['content']);
    }
    
    
    public function postSubmissionConvert($begin=null, $end=null)
    {
        $multiRequestHandle = new Request_MultiRequest();
        
        //request to database to get the markings
        $handler = Request_CreateRequest::createCustom('GET', $this->_getSubmission[0]->getAddress().'/submission/selected/date/begin/'.$begin.'/end/'.$end, array(),"");
        $multiRequestHandle->addRequest($handler);
                
        $answer = $multiRequestHandle->run();

        $submissions = json_decode($answer[0]['content'], true);
        //var_dump($submissions);//exerciseSheetId
        $sortedExerciseSheets=array();
        foreach($submissions as $submission){
            $sheetid = $submission['exerciseSheetId'];
            if (!isset($sortedExerciseSheets[$sheetid]))
                $sortedExerciseSheets[$sheetid] = array();
            $sortedExerciseSheets[$sheetid][] = $submission;
        }
        
        $createdFiles=array();
        
        foreach ($sortedExerciseSheets as $sheetid => $list){
            $multiRequestHandle2 = new Request_MultiRequest();
            //request to database to get the exercise sheets
            $handler = Request_CreateRequest::createCustom('GET', $this->_getExercise[0]->getAddress().'/exercise/exercisesheet/'.$sheetid, array(),"");
            $multiRequestHandle2->addRequest($handler);
            
            $handler = Request_CreateRequest::createCustom('GET', $this->_getGroup[0]->getAddress().'/group/exercisesheet/'.$sheetid, array(),"");
            $multiRequestHandle2->addRequest($handler);
            $answer2 = $multiRequestHandle2->run();
            
            $exercises = json_decode($answer2[0]['content'], true);
            $groups = json_decode($answer2[1]['content'], true);
            
            $count = 0;
            //an array to descripe the subtasks
            $alphabet = range('a', 'z');
            $secondRow = array();
            $sortedMarkings = array();
            $rows = array();
            //$exerciseIdWithExistingMarkings = array();
            $namesOfExercises = array();
           /* $ExerciseData = array();
            $ExerciseData['userId'] = $userid;
            $ExerciseData['markings'] = array();*/
            
            $count=null;
            foreach ($exercises as $key => $exercise){
                $exerciseId = $exercise['id'];

                if ($count===null || $exercises[$count]['link'] != $exercise['link']){
                    $count=$key;
                    $namesOfExercises[$exerciseId] = 'Aufgabe_'.$exercise['link'];
                    $subtask = 0;
                }else{
                    $subtask++;
                    $namesOfExercises[$exerciseId] = 'Aufgabe_'.$exercise['link'].$alphabet[$subtask];
                    $namesOfExercises[$exercises[$count]['id']] = 'Aufgabe_'.$exercises[$count]['link'].$alphabet[0];
                }
            }
            
            // file
            foreach ($list as $submission){
                $fileInfo = pathinfo($submission['file']['displayName']);
                $newFile = array_merge(array(),$submission['file']);
                $converted=false;
                $exerciseId = $submission['exerciseId'];
                
                ///echo $submission['id'].'_'.$newFile['fileId'].'__'.(isset($newFile['mimeType'])?$newFile['mimeType']:'');
                if (!isset($newFile['mimeType']) || strpos($newFile['mimeType'],'text/')!==false){
                ///echo "_C";
                    // convert file to pdf
                    $newFileSend = array();
                    $newFileData = new File();
                    $data="<h1>".str_replace('_',' ',strtoupper($namesOfExercises[$exerciseId]))."</h1><hr><p></p>";
                    
                    if (isset($submission['id']))
                        $data.="Einsendungsnummer: {$submission['id']}\n";
                    
                    foreach ($groups as $group){
                        $user = array_merge(array($group['leader']),isset($group['members']) ? $group['members'] : array());
                        $found=false;
                        foreach ($user as $us){
                            if ($us['id'] == $submission['studentId']){
                                $namen=array();
                                foreach ($user as $member){
                                    $namen[] = (isset($member['firstName']) ? $member['firstName'] : '-').' '.(isset($member['lastName']) ? $member['lastName'] : '' ).' ('.(isset($member['userName']) ? $member['userName'] : '').')';
                                }
                                $namen=implode(', ',$namen);
                                $data.="Studenten: {$namen}\n"; 
                                $found=true;
                                break;
                            }
                        }
                        if ($found) break;
                    }
                    
                    if (isset($submission['comment']))
                        $data.="Kommentar: {$submission['comment']}\n";
                        
                    $data.="<pre>";
                    $newFileData->setBody(base64_encode($data));
                    $newFileSend[] = $newFileData;
                    $newFileSend[] = $newFile;
                    $newFileData = new File();
                    $newFileData->setBody(base64_encode("</pre>"));
                    $newFileSend[] = $newFileData;
                    //echo File::encodeFile($newFileSend);
                    $answer = Request::routeRequest(
                                                    'POST',
                                                    '/temppdf/file/merge',
                                                    array(),
                                                    File::encodeFile($newFileSend),
                                                    $this->_postPdf,
                                                    'pdf'
                                                    );

                    if ($answer['status'] == 201 && isset($answer['content'])){
                        $createdFiles[] = File::decodeFile($answer['content']);
                        /*$a = json_decode($answer['content'],true);
                        $a['inp'] = htmlspecialchars($data);
                        $a['submissionId'] = $submission['id']; 
                        $createdFiles[] = $a;*/
                    }  
                }
                ///echo "\n";
            }
        }
        
        $this->app->response->setBody(File::encodeFile($createdFiles));
        ///$this->app->response->setBody(json_encode($createdFiles));
        $this->app->response->setStatus(201);
        
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
    public function postTutorArchive($userid, $sheetid)
    {
        $answer = $this->generateTutorArchive($userid, $sheetid, json_decode($this->app->request->getBody(), true));
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }
    
    public function postTutorArchiveWithNames($userid, $sheetid)
    {
        $answer = $this->generateTutorArchive($userid, $sheetid, json_decode($this->app->request->getBody(), true), true);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }
    
    public function generateTutorArchive($userid, $sheetid, $markings, $withNames=false)
    {
        $multiRequestHandle = new Request_MultiRequest();
        //request to database to get the exercise sheets
        $handler = Request_CreateRequest::createCustom('GET', $this->_getExercise[0]->getAddress().'/exercise/exercisesheet/'.$sheetid, array(),"");
        $multiRequestHandle->addRequest($handler);
        $handler = Request_CreateRequest::createCustom('GET', $this->_getGroup[0]->getAddress().'/group/exercisesheet/'.$sheetid, array(),"");
        $multiRequestHandle->addRequest($handler);
        
        $answer = $multiRequestHandle->run();
        if (count($answer)< 2 || !isset($answer[0]['status']) || $answer[0]['status']!=200 || !isset($answer[0]['content']) || !isset($answer[1]['status']) || $answer[1]['status']!=200 || !isset($answer[1]['content'])){
            return array('status'=>404,'content'=>'');
        }
        $exercises = json_decode($answer[0]['content'], true);
        $groups = json_decode($answer[1]['content'], true);
        
        $count = 0;
        //an array to descripe the subtasks
        $alphabet = range('a', 'z');
        $secondRow = array();
        $sortedMarkings = array();
        $rows = array();
        $exerciseIdWithExistingMarkings = array();
        $namesOfExercises = array();
        $ExerciseData = array();
        $ExerciseData['userId'] = $userid;
        $ExerciseData['markings'] = array();

        //exercises with informations of marking and submissions
        //sorted by exercise ID and checked of existence
        foreach( $markings as $marking){
            if (!isset($marking['submission']['selectedForGroup']) || !$marking['submission']['selectedForGroup'])
                continue;
                
            $submission = $marking['submission'];
            $id = $submission['exerciseId'];
            $sortedMarkings[$id][] = $marking;
            if(!in_array($id, $exerciseIdWithExistingMarkings)){
                $exerciseIdWithExistingMarkings[] = $id;
            }
        }
        
        $defaultOrder = array('ID','NAME','USERNAME','POINTS','MAXPOINTS','OUTSTANDING','STATUS','TUTORCOMMENT','STUDENTCOMMENT','FILE');

        $courseid=null;
        $count=null;
        foreach ($exercises as $key => $exercise){
            if ($courseid===null){
                $courseid = $exercise['courseId'];
            }
            
            $exerciseId = $exercise['id'];

            if ($count===null || $exercises[$count]['link'] != $exercise['link']){
                $count=$key;
                $namesOfExercises[$exerciseId] = 'Aufgabe_'.$exercise['link'];
                $subtask = 0;
            }else{
                $subtask++;
                $namesOfExercises[$exerciseId] = 'Aufgabe_'.$exercise['link'].$alphabet[$subtask];
                $namesOfExercises[$exercises[$count]['id']] = 'Aufgabe_'.$exercises[$count]['link'].$alphabet[0];
            }
        }

        //formating, create the layout of the CSV-file for the tutor
        //first two rows of an exercise are the heads of the table
        foreach ($exercises as $exercise){
            
            if (!isset($exercise['id'])) continue;
            $exerciseId = $exercise['id'];

            // adds the exercise name
            $firstRow = array();
            $firstRow[] = '--'.$namesOfExercises[$exerciseId];

            //formating, write known informations of the markings in the CSV-file
            //after the second row to each exercise
            if(in_array($exerciseId, $exerciseIdWithExistingMarkings)){
                $tempRows = array();
                $collumns = array('ID','POINTS','MAXPOINTS','OUTSTANDING','STATUS','TUTORCOMMENT','STUDENTCOMMENT','FILE');
                foreach($sortedMarkings[$exerciseId] as $key => $marking){
                    $row = array();
                    //MarkingId
                    if (!isset($marking['id'])) continue;
                    $row['ID'] = isset($marking['id']) ? $marking['id'] : null;
                    
                    $ExerciseData['markings'][$marking['id']] = array();
                    $ExerciseData['markings'][$marking['id']]['sheetId'] = $exercise['sheetId'];
                    $ExerciseData['markings'][$marking['id']]['courseId'] = $exercise['courseId'];
                    $ExerciseData['markings'][$marking['id']]['exerciseId'] = $exerciseId; 
                    
                    // Username + Name
                    if ($withNames && isset($marking['submission']['studentId'])){
                        foreach ($groups as $group){
                            $user = array_merge(array($group['leader']),isset($group['members']) ? $group['members'] : array());
                            $found=false;
                            foreach ($user as $us){
                                if ($us['id'] == $marking['submission']['studentId']){
                                    $member = $us;
                                    $row['NAME']=(isset($member['firstName']) ? $member['firstName'] : '-').' '.(isset($member['lastName']) ? $member['lastName'] : '' );
                                    $collumns[] = 'NAME';
                                    $row['USERNAME']=(isset($member['userName']) ? $member['userName'] : '');
                                    $collumns[] = 'USERNAME';
                                    $row['STUDENTID']=(isset($member['id']) ? $member['id'] : null);
                                    $ExerciseData['markings'][$marking['id']]['studentId'] = (isset($member['id']) ? $member['id'] : null);
                                    $ExerciseData['markings'][$marking['id']]['leaderId'] = (isset($group['leader']['id']) ? $group['leader']['id'] : null);
                                    $found=true;
                                    break;
                                }
                            }
                            if ($found) break;
                        }
                    }                 
                    
                    //Points
                    $row['POINTS'] = (isset($marking['points']) ? $marking['points'] : '0');

                    //MaxPoints
                    $row['MAXPOINTS'] = (isset($exercise['maxPoints']) ? $exercise['maxPoints'] : '0');
                    $ExerciseData['markings'][$marking['id']]['maxPoints'] = (isset($exercise['maxPoints']) ? $exercise['maxPoints'] : '0');
                    
                    $ExerciseData['markings'][$marking['id']]['submissionId'] = $marking['submission']['id'];
                    
                    //Outstanding
                    $row['OUTSTANDING'] = (isset($marking['outstanding']) ? $marking['outstanding'] : '');
                    
                    //Status
                    $row['STATUS'] = (isset($marking['status']) ? $marking['status'] : '0');

                    //TutorComment
                    $row['TUTORCOMMENT'] = (isset($marking['tutorComment']) ? $marking['tutorComment'] : '');
                    
                    //StudentComment
                    if (isset($marking['submission'])){
                        $submission = $marking['submission'];
                        $row['STUDENTCOMMENT'] = (isset($submission['comment']) ? $submission['comment'] : '');
                    }
                                        
                    // file
                    $newFile = null;
                    if (isset($marking['submission']['file']['displayName'])){
                        $fileInfo = pathinfo($marking['submission']['file']['displayName']);
                        $newFile = array_merge(array(),$marking['submission']['file']);
                    }
                    $converted=false;
                    
                    if (isset($marking['file']))
                        $newFile = array_merge(array(),$marking['file']);

                    if (!isset($marking['file']))
                    if (!isset($newFile['mimeType']) || strpos($newFile['mimeType'],'text/')!==false){
                   
                        // convert file to pdf
                        $newFileSend = array();
                        $newFileData = new File();
                        $data="<h1>".str_replace('_',' ',strtoupper($namesOfExercises[$exerciseId]))."</h1><hr><p></p>";
                        
                        if (isset($marking['submission']['id']))
                            $data.="Einsendungsnummer: {$marking['submission']['id']}\n";
                        /*if (isset($marking['id']))
                            $data.="Korrekturnummer: {$marking['id']}\n";*/
                        
                        foreach ($groups as $group){
                            $user = array_merge(array($group['leader']),isset($group['members']) ? $group['members'] : array());
                            $found=false;
                            foreach ($user as $us){
                                if ($us['id'] == $marking['submission']['studentId']){
                                    $namen=array();
                                    foreach ($user as $member){
                                        $namen[] = (isset($member['firstName']) ? $member['firstName'] : '-').' '.(isset($member['lastName']) ? $member['lastName'] : '' ).' ('.(isset($member['userName']) ? $member['userName'] : '').')';
                                    }
                                    $namen=implode(', ',$namen);
                                    $data.="Studenten: {$namen}\n"; 
                                    $found=true;
                                    break;
                                }
                            }
                            if ($found) break;
                        }
                        
                        if (isset($marking['submission']['comment']))
                            $data.="Kommentar: {$marking['submission']['comment']}\n";
                            
                        $data.="<pre>";
                        $newFileData->setBody(base64_encode($data));
                        $newFileSend[] = $newFileData;
                        
                        if (isset($newFile)){
                            $newFileSend[] = $newFile;
                            $newFileData = new File();
                            $newFileData->setBody(base64_encode("</pre>"));
                            $newFileSend[] = $newFileData;
//echo File::encodeFile($newFileSend);//return;
                            $answer = Request::routeRequest(
                                                            'POST',
                                                            '/temppdf/file/merge',
                                                            array(),
                                                            File::encodeFile($newFileSend),
                                                            $this->_postPdf,
                                                            'pdf'
                                                            );
                            unset($newFileSend);
                            if ($answer['status'] == 201 && isset($answer['content'])){
                                $file = json_decode($answer['content'],true);
                                /*$a = json_decode($answer['content'],true);
                                $a['inp'] = htmlspecialchars($data);
                                $a['submissionId'] = $marking['submission']['id'];
                                $filesList[]=$a;*/
                                $address = $file['address'];
                                $newFile['address'] = $address;
                                $newFile['displayName'] = $fileInfo['filename'].'.pdf';
                                $sortedMarkings[$exerciseId][$key]['submission']['file']['conv'] = $newFile;
                                $converted=true;
                            }
                            unset($answer);
                        }
                    }
                    
                    //$row[] = $namesOfExercises[$exerciseId].'/'.($converted ? 'K_' :'').$marking['id'].($fileInfo['extension']!='' ? '.'.$fileInfo['extension']:'');
                    //if (!$converted)
                    if (isset($newFile['displayName'])){
                        if (isset($marking['submission']['file']['displayName']) && $newFile['displayName'] == $marking['submission']['file']['displayName'])
                            $newFile['displayName'] = 'K_'.$newFile['displayName'];
                        $row['FILE'] = $namesOfExercises[$exerciseId].'/'.$marking['id'].'/'.$newFile['displayName'];
                    }
                    unset($newFile);

                    $tempRows[] = $row;
                }
                
                //an empty row after an exercise
                $rows[] = $firstRow;
                $secondRow=array();
                $i=0;
                foreach ($defaultOrder as $coll){
                    if (in_array($coll, $collumns)){
                        $secondRow[] = ($i==0?'--':'').strtoupper($coll);
                        $i++;
                    }
                }
                    
                $rows[] = $secondRow;
                foreach ($tempRows as $rr){
                    $row=array();
                    foreach ($defaultOrder as $coll){
                        if (in_array($coll, $collumns) && isset($rr[$coll])){
                            $row[] = $rr[$coll];
                        } elseif (in_array($coll, $collumns)){
                            $row[] = '';
                        }
                    }
                    $rows[] = $row;
                }
                
                $rows[] = array();
            }

        }

        //request to database to get the user name of the tutor for the
        //name of the CSV-file
        
        // create transaction ticket
        $transaction = Transaction::createTransaction(
                                                      null,
                                                      (time() + (30 * 24 * 60 * 60)),
                                                      'TutorCSV_'.$userid.'_'.$courseid,
                                                      json_encode($ExerciseData)
                                                      );
        $result = Request::routeRequest(
                                        'POST',
                                        '/transaction/exercisesheet/'.$sheetid,
                                        array(),
                                        Transaction::encodeTransaction($transaction),
                                        $this->_postTransaction,
                                        'transaction'
                                        );

        // checks the correctness of the query
        if ( isset($result['status']) && isset($result['content']) && $result['status'] == 201){
            $transaction = Transaction::decodeTransaction($result['content']);
             
            LTutor::generatepath($this->config['DIR']['temp']);
            $tempDir = $this->tempdir($this->config['DIR']['temp'], 'createCSV', $mode=0775);
        
            ///$this->deleteDir($tempDir);
            $transactionRow = array($transaction->getTransactionId());
            array_unshift($rows,$transactionRow);

            //this is the true writing of the CSV-file named [tutorname]_[sheetid].csv
            $CSV = fopen($tempDir.'/Liste.csv', 'w'); // $user['lastName'].'_'.

            foreach($rows as $row){
                fputcsv($CSV, $row, ';','"');
            }

            fclose($CSV);
            //Create Zip
            $filesToZip = array();
            //Push all SubmissionFiles to an array in order of exercises
            foreach( $exercises as $exercise){
                $exerciseId = $exercise['id'];
                
                if(in_array($exerciseId, $exerciseIdWithExistingMarkings)){
                    $markings = $sortedMarkings[$exerciseId];
                    foreach($markings as $marking){
                        if (!isset($marking['submission']['selectedForGroup']) || !$marking['submission']['selectedForGroup'])
                            continue;
                            
                        $submission = $marking['submission'];

                        $newfile = (isset($submission['file']) ? $submission['file'] : null);
                        
                        if (isset($marking['file']['displayName'])){
                            $newfile3 = $marking['file'];
                            $fileInfo = pathinfo($newfile3['displayName']);
                            
                            if (isset($newfile['displayName']) && $newfile['displayName'] == $newfile3['displayName'])
                                $newfile3['displayName'] = 'K_'.$newfile3['displayName'];
                            
                            //$newfile3['displayName'] = $namesOfExercises[$exerciseId].'/K_'.$marking['id'].($fileInfo['extension']!='' ? '.'.$fileInfo['extension']:'');
                            $newfile3['displayName'] = $namesOfExercises[$exerciseId].'/'.$marking['id'].'/'.$newfile3['displayName'];
                            $filesToZip[] = $newfile3;
                        } elseif (isset($newfile['conv']['displayName'])){
                            $newfile2 = $newfile['conv'];
                            $fileInfo = pathinfo($newfile2['displayName']);
                            
                            if (isset($newfile['displayName']) && $newfile['displayName'] == $newfile2['displayName'])
                                $newfile2['displayName'] = 'K_'.$newfile2['displayName'];
                            
                            //$newfile2['displayName'] = $namesOfExercises[$exerciseId].'/K_'.$marking['id'].($fileInfo['extension']!='' ? '.'.$fileInfo['extension']:'');
                            $newfile2['displayName'] = $namesOfExercises[$exerciseId].'/'.$marking['id'].'/'.$newfile2['displayName'];
                            $filesToZip[] = $newfile2;
                        }
                        
                        if (isset($newfile['displayName'])){
                            $fileInfo = pathinfo($newfile['displayName']);
                            //$newfile['displayName'] = $namesOfExercises[$exerciseId].'/'.$marking['id'].($fileInfo['extension']!='' ? '.'.$fileInfo['extension']:'');
                            $newfile['displayName'] = $namesOfExercises[$exerciseId].'/'.$marking['id'].'/'.$newfile['displayName'];
                            $filesToZip[] = $newfile;
                        }
                    }
                }
            }

            //push the .csv-file to the array
            $path = $tempDir.'/Liste.csv';//$user['lastName'].'_'.
            $csvFile = array(
                        'displayName' => 'Liste.csv', //$user['lastName'].'_'.
                        'body' => base64_encode(file_get_contents($path))
                    );
            $filesToZip[] = $csvFile;
            
            unlink($path);
            $this->deleteDir(dirname($path));

            //request to filesystem to create the Zip-File
            $result = Request::routeRequest( 
                                            'POST',
                                            '/zip',
                                            array(),
                                            json_encode($filesToZip),
                                            $this->_postZip,
                                            'zip'
                                            );

            // checks the correctness of the query
            if ( $result['status'] == 201){
                $ff = File::decodeFile($result['content']);
                $ff->setDisplayName($transaction->getTransactionId().'.zip');
                return array('status'=>201,'content'=>File::encodeFile($ff));
            } else 
                return array('status'=>409,'content'=>'');
        } else 
             return array('status'=>409,'content'=>'');
        
    }
    
    public function getZip($userid, $sheetid, $status=null)
    {
        $multiRequestHandle = new Request_MultiRequest();
        $filesList=array();
        
        //request to database to get the markings
        $handler = Request_CreateRequest::createCustom('GET', $this->_getMarking[0]->getAddress().'/marking/exercisesheet/'.$sheetid.'/tutor/'.$userid, array(),"");
        $multiRequestHandle->addRequest($handler);
        
        $answer = $multiRequestHandle->run();
        if (count($answer)< 1 || !isset($answer[0]['status']) || $answer[0]['status']!=200 || !isset($answer[0]['content'])){
            $this->app->response->setStatus(404);
            $this->app->response->setBody('');
            $this->app->stop();
        }

        $markings = json_decode($answer[0]['content'], true);
        if (isset($status)){
            $marks=array();
            foreach($markings as $marking)
                if (isset($marking['status']) && $marking['status'] == $status)
                    $marks[] = $marking;
            $markings=$marks;
        }            

        $answer = $this->generateTutorArchive($userid, $sheetid, $markings);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }

    public function uploadZip($userid, $courseid)
    {                        
        // error array of strings
        $errors = array();
        LTutor::generatepath($this->config['DIR']['temp']);
        $tempDir = $this->tempdir($this->config['DIR']['temp'], 'extractZip', $mode=0775);

        $body = json_decode($this->app->request->getBody(), true); //1 file-Object        
        $filename = $tempDir.'/'.$courseid.'.zip';
        file_put_contents($filename, base64_decode($body['body']));
        unset($body);
        
        $zip = new ZipArchive();
        $zip->open($filename);
        $zip->extractTo($tempDir.'/files');
        $zip->close();        
        unlink($filename);
        ///$this->deleteDir(dirname($filename));
        unset($zip);
        
        $files = $tempDir.'/files';
        
        // check if csv file exists
        if (file_exists($files.'/Liste.csv')){
            $csv = fopen($files.'/Liste.csv', "r");
            
            if (($transactionId = fgetcsv($csv,0,';')) === false){
                fclose($csv);
                $this->deleteDir($tempDir);
                $this->app->response->setStatus(409);
                $errors[] = 'empty .csv file';
                $this->app->response->setBody(json_encode($errors));
                $this->app->stop();
            }

            $result = Request::routeRequest(
                                            'GET',
                                            '/transaction/authentication/TutorCSV_'.$userid.'_'.$courseid.'/transaction/'.$transactionId[0],
                                            array(),
                                            '',
                                            $this->_getTransaction,
                                            'transaction'
                                            );

            if (isset($result['status']) && $result['status']==200 && isset($result['content'])){
                $transaction = Transaction::decodeTransaction($result['content']);
                $transaction = json_decode($transaction->getContent(),true);
                unset($result);
                
                $defaultOrder = array('ID','NAME','USERNAME','POINTS','MAXPOINTS','OUTSTANDING','STATUS','TUTORCOMMENT','STUDENTCOMMENT','FILE');
                $currectOrder = $defaultOrder;
                
                $markings = array();
                while (($row = fgetcsv($csv,0,';')) !== false){
                    if (substr($row[0],0,2)=='--'){
                        $row[0] = substr($row[0],2);
                        if (in_array(strtoupper($row[0]),$defaultOrder)){
                            $currectOrder = array();
                            foreach ($row as $ro){
                                $currectOrder[strtoupper($ro)] = count($currectOrder);
                            }
                        }                    
                    } elseif(implode('',$row) != '' && substr($row[0],0,2)!='--'){
                    
                        $markingId = isset($currectOrder['ID']) ? $row[$currectOrder['ID']] : null;
                        $points = isset($currectOrder['POINTS']) ? $row[$currectOrder['POINTS']] : null;       
                        $points = str_replace(',','.',$points);
                        $markingFile = isset($currectOrder['FILE']) ? $row[$currectOrder['FILE']] : null;                         
                        
                        // check if markingId exists in transaction
                        if (!isset($transaction['markings'][$markingId])){
                            // unknown markingId
                            fclose($csv);
                            $this->deleteDir($tempDir);
                            $this->app->response->setStatus(409);
                            $errors[] = "unknown ID: {$markingId}";
                            $this->app->response->setBody(json_encode($errors));
                            $this->app->stop();
                        }
                        ///var_dump($transaction['markings'][$markingId]);
                        $markingData = $transaction['markings'][$markingId];
                        
                        // checks whether the points are less or equal to the maximum points
                        if ($points > $markingData['maxPoints'] || $points<0){
                            // too much points
                            fclose($csv);
                            $this->deleteDir($tempDir);
                            $this->app->response->setStatus(409);
                            $errors[] = "incorrect points in marking: {$markingId}";
                            $this->app->response->setBody(json_encode($errors));
                            $this->app->stop();
                        }
                        
                        // checks if file with this markingid exists
                        if ($markingFile == '' || file_exists($files.'/'.$markingFile)) {
                        
                            if ($markingFile!=''){
                                $fileBody = file_get_contents($files.'/'.$markingFile);
                                // file
                                $fileInfo = pathinfo($markingFile);
                                $file = array(
                                        'displayName' => $fileInfo['basename'],
                                        'body' => base64_encode($fileBody),
                                        );
                            } else {
                                $file = null;
                            }
                            
                            if (isset($transaction['markings'][$markingId]['submissionId']) && $transaction['markings'][$markingId]['submissionId']<0){
                                // create new submission object
                                $submissionId = $transaction['markings'][$markingId]['submissionId'];
                                $studentId = $transaction['markings'][$markingId]['studentId'];
                                $exerciseId = $transaction['markings'][$markingId]['exerciseId'];
                                $submission = Submission::createSubmission( 
                                                                            null,
                                                                            $studentId,
                                                                            null,
                                                                            $exerciseId,
                                                                            null,
                                                                            1,
                                                                            time(),
                                                                            null,
                                                                            $leaderId,
                                                                            1
                                                                            );
                                $submission->setSelectedForGroup('1');
                                ///echo json_encode($submission);return;
                                $result = Request::routeRequest(
                                                                'POST',
                                                                '/submission',
                                                                array(),
                                                                json_encode($submission),
                                                                $this->_postSubmission,
                                                                'submission'
                                                                );

                                if ($result['status'] == 201) {
                                    $transaction['markings'][$markingId]['submissionId'] = json_decode($result['content'],true)['id'];
                                }
                            }
                            
                            // create new marking object
                            $marking = Marking::createMarking( 
                                                             $markingId<0 ? null : $markingId,
                                                             $userid,
                                                             null,
                                                             $transaction['markings'][$markingId]['submissionId'],
                                                             isset($currectOrder['TUTORCOMMENT']) ? $row[$currectOrder['TUTORCOMMENT']] : null,
                                                             isset($currectOrder['OUTSTANDING']) ? $row[$currectOrder['OUTSTANDING']] : null,
                                                             isset($currectOrder['STATUS']) ? $row[$currectOrder['STATUS']] : null,
                                                             $points,
                                                             time(),
                                                             $file==null ? 1 : 0
                                                             );
                            $marking->setFile($file);
                            
                            /*array(
                                    'id' => ($markingId<0 ? null : $markingId),
                                    'points' => $points,
                                    'outstanding' => isset($currectOrder['OUTSTANDING']) ? $row[$currectOrder['OUTSTANDING']] : null,
                                    'tutorId' => $userid,
                                    'tutorComment' => isset($currectOrder['TUTORCOMMENT']) ? $row[$currectOrder['TUTORCOMMENT']] : null,
                                    'file' => $file,
                                    'status' => isset($currectOrder['STATUS']) ? $row[$currectOrder['STATUS']] : null,
                                    'date' => time(),
                                    'hideFile' => 
                                    );*/

                            $markings[] = $marking;

                        } else { //if file with this markingid not exists
                            $errors[] = 'File does not exist: '.$markingFile;
                            fclose($csv);
                            $this->deleteDir($tempDir);
                            $this->app->response->setStatus(409);
                            $this->app->response->setBody(json_encode($errors));
                            $this->app->stop();
                        }
                    }
                }
                ///echo json_encode($markings); return;
                //request to database to edit the markings
                $result = Request::routeRequest(
                                                'POST',
                                                '/marking',
                                                array(),
                                                json_encode($markings),
                                                $this->_postMarking,
                                                'marking'
                                                );
                                            
                /// TODO: prüfen ob jede hochgeladen wurde
                if ($result['status'] != 201) {
                    $errors[] = 'send markings failed';
                }
                
            } else {
                $errors[] = 'no transaction data';
            }
            
            fclose($csv);
        } else { // if csv file does not exist
            $errors[] = '.csv file does not exist in uploaded zip-Archiv';
        }
        $this->deleteDir($tempDir);

        $this->app->response->setBody(json_encode($errors));
        if (!($errors == array())){
            $this->app->response->setStatus(409);
        }
    }

    /**
     * Returns status code 200, if this component is correctly installed for the platform
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/platform.
     */
    public function getExistsPlatform( )
    {
        Logger::Log( 
                    'starts GET GetExistsPlatform',
                    LogLevel::DEBUG
                    );
                    
        if (!file_exists(dirname(__FILE__).'/config.ini')){
            $this->app->response->setStatus( 409 );
            $this->app->stop();
        }
       
        $this->app->response->setStatus( 200 );
        $this->app->response->setBody( '' );  
    }
    
    /**
     * Removes the component from the platform
     *
     * Called when this component receives an HTTP DELETE request to
     * /platform.
     */
    public function deletePlatform( )
    {
        Logger::Log( 
                    'starts DELETE DeletePlatform',
                    LogLevel::DEBUG
                    );
        if (file_exists(dirname(__FILE__).'/config.ini') && !unlink(dirname(__FILE__).'/config.ini')){
            $this->app->response->setStatus( 409 );
            $this->app->stop();
        }
        
        $this->app->response->setStatus( 201 );
        $this->app->response->setBody( '' );
    }
    
    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( )
    {
        Logger::Log( 
                    'starts POST AddPlatform',
                    LogLevel::DEBUG
                    );

        // decode the received course data, as an object
        $insert = Platform::decodePlatform( $this->app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $insert as $in ){
        
            $file = dirname(__FILE__).'/config.ini';
            $text = "[DIR]\n".
                    "temp = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$in->getTempDirectory()))."\"\n".
                    "files = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$in->getFilesDirectory()))."\"\n";
                    
            if (!@file_put_contents($file,$text)){
                Logger::Log( 
                            'POST AddPlatform failed, config.ini no access',
                            LogLevel::ERROR
                            );

                $this->app->response->setStatus( 409 );
                $this->app->stop();
            }   

            $platform = new Platform();
            $platform->setStatus(201);
            $res[] = $platform;
            $this->app->response->setStatus( 201 );
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->app->response->setBody( Platform::encodePlatform( $res[0] ) );
            
        } else 
            $this->app->response->setBody( Platform::encodePlatform( $res ) );
    }
    
    /**
    * Delete hole directory inclusiv files and dirs
    *
    * @param string $path
    * @return boolean
    */
    public function deleteDir($path)
    {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file) {
                $this->deleteDir(realpath($path) . '/' . $file);
            }
            return rmdir($path);
        }

        // Datei entfernen
        else if (is_file($path) === true) {
            return unlink($path);
        }
        return false;
    }
    
    /**
     * Creates the path in the filesystem, if necessary.
     *
     * @param string $path The path which should be created.
     * @see http://php.net/manual/de/function.mkdir.php#83265
     */
    public static function generatepath( $path, $mode = 0755 )
    {
        $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
        $e = explode("/", ltrim($path, "/"));
        if(substr($path, 0, 1) == "/") {
            $e[0] = "/".$e[0];
        }
        $c = count($e);
        $cp = $e[0];
        for($i = 1; $i < $c; $i++) {
            if(!is_dir($cp) && !@mkdir($cp, $mode)) {
                return false;
            }
            $cp .= "/".$e[$i];
        }
        return @mkdir($path, $mode);
    }
    
    public function tempdir($dir, $prefix='', $mode=0775)
    {
        if (substr($dir, -1) != '/') $dir .= '/';

        do
        {
            $path = $dir.$prefix.mt_rand(0, 9999999);
        } while (!mkdir($path, $mode));

        return $path;
    }

}
