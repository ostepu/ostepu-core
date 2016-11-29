<?php
set_time_limit(180);
/**
 * @file LTutor.php Contains the LTutor class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2014
 * @author Martin Daute <martin.daute@student.uni-halle.de>
 * @date 2014
 * @author Peter König <upbkgs20@arcor.de>
 * @date 2014
 * @author Christian Elze <christian.elze@gmail.com>
 * @date 2014
 */

require_once dirname(__FILE__) . '/../../Assistants/vendor/Slim/Slim/Slim.php';
include_once dirname(__FILE__) . '/../../Assistants/Request.php';
include_once dirname(__FILE__) . '/../../Assistants/CConfig.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures/Transaction.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures/Platform.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures/File.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures/Course.php';
include_once dirname(__FILE__) . '/../../Assistants/Language.php';
include_once dirname(__FILE__) . '/../../Assistants/LArraySorter.php';

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


    private static $langTemplate='LTutor';

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
    private $_out = array();
    private $_getCourse = array();

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
        $com = new CConfig( LTutor::getPrefix( ).',course', dirname(__FILE__) );

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

        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');

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
        $this->_out = array( CConfig::getLink(
                                                        $this->_conf->getLinks( ),
                                                        'out2'
                                                        ) );
        $this->_getCourse = array( CConfig::getLink(
                                                        $this->_conf->getLinks( ),
                                                        'getCourse'
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

        // POST AddCourse
        $this->app->post( 
                          '/course(/)',
                          array( 
                                $this,
                                'addCourse'
                                )
                          );
                          
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

        // initialisiere die Eingabedaten
        $body['unassigned'] = isset($body['unassigned']) ? $body['unassigned'] : array();
        if (!is_array($body['unassigned'])){
            $this->app->response->setStatus(500);
            $this->app->response->setBody("Error: Invalid Input (var=unassigned)!");
            $this->app->stop();
        }
        $body['assigned'] = isset($body['assigned']) ? $body['assigned'] : array();
        if (!is_array($body['assigned'])){
            $this->app->response->setStatus(500);
            $this->app->response->setBody("Error: Invalid Input (var=assigned)!");
            $this->app->stop();
        }
        $body['tutors'] = isset($body['tutors']) ? $body['tutors'] : array();
        if (!is_array($body['tutors'])){
            $this->app->response->setStatus(500);
            $this->app->response->setBody("Error: Invalid Input (var=tutors)!");
            $this->app->stop();
        }

        // eine Liste von Tutoren, welchen die Einsendungen bzw. Korrekturen zugewiesen werden sollen
        $targetTutors = $body['tutors'];

        // die Einsendungen sollen entsprechend ihrer Gruppenführer gruppiert werden
        $selectedMarkings = array();
        foreach($body['unassigned'] as $submission){
            if (!isset($submission['leaderId']) || !isset($submission['id'])){
                // wenn das Feld nicht existiert, wird der Datensatz ignoriert, er ist beschädigt
                // Todo: eventuell wird eine Fehlermeldung zurückgegeben
                continue;
            }
            $leaderId = $submission['leaderId'];
            $selectedMarkings[$leaderId][] = array('submission' => $submission);
        }

        // die bereits zugewiesenen Einsendungen sollen entsprechend ihrer Gruppenführer gruppiert werden
        foreach($body['assigned'] as $marking){
            if (!isset($marking['submission']['leaderId']) || !isset($marking['submission']['id']) || !isset($marking['id'])){
                // wenn das Feld nicht existiert, wird der Datensatz ignoriert, er ist beschädigt
                // Todo: eventuell wird eine Fehlermeldung zurückgegeben
                continue;
            }
            $leaderId = $marking['submission']['leaderId'];
            $selectedMarkings[$leaderId][] = $marking;
        }

        // randomized allocation
        shuffle($targetTutors);

        $i = 0;
        $numberOfTutors = count($targetTutors);
        $markings = array();
        foreach ($selectedMarkings as $markingsByGroup){
            foreach($markingsByGroup as $marking){

                // erzeugt eine neue "unkorrigierte" Korrektur (oder behält den bisherigen Zustand)
                // und weist sie dem Tutor zu
                $newMarking = array(
                    'submission' => $marking['submission'],
                    'id' => (isset($marking['id']) ? $marking['id'] : null),
                    'status' => (isset($marking['id']) ? null : 1),
                    'tutorId' => $targetTutors[$i]['tutorId']
                );
                //adds a submission to a tutor
                $markings[] = $newMarking;
            }

            // die Auswahl der Tutoren dreht sich im Kreis
            if ($i < $numberOfTutors - 1){
                $i++;
            } else {
                $i = 0;
            }
        }

        //requests to database
        $URL = $this->_postMarking[0]->getAddress().'/marking';

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

                    if (isset($submission['comment']) && trim($submission['comment']) != '')
                        $data.="Kommentar: {$submission['comment']}\n";

                    $data.="<pre>";
                    $newFileData->setBody($data, true);
                    $newFileSend[] = $newFileData;
                    $newFileSend[] = $newFile;
                    $newFileData = new File();
                    $newFileData->setBody("</pre>", true);
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
        unset($answer);
        unset($multiRequestHandle);
        
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
        
        // die Aufgaben müssen entsprechend sortiert sein, sonst werden die Namen falsch erzeugt,
        // falls eine Aufgabe später hinzugefügt wurde
        $exercises = LArraySorter::orderBy($exercises, 'link', SORT_ASC, 'linkName', SORT_ASC);
        
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
        
        $multiRequestHandle = new Request_MultiRequest();
        $handler = Request_CreateRequest::createCustom('GET', $this->_getCourse[0]->getAddress().'/course/'.$courseid, array(),"");
        $multiRequestHandle->addRequest($handler);
        $answer = $multiRequestHandle->run();
        if (count($answer)< 1 || !isset($answer[0]['status']) || $answer[0]['status']!=200 || !isset($answer[0]['content'])){
            return array('status'=>404,'content'=>'');
        }
        $course = Course::decodeCourse($answer[0]['content'], true);
        unset($answer);
        unset($multiRequestHandle);
        
        // Hilfe einfügen
        $rows[] = array();
        $tmpHelp = Language::Get('main','csvDescription', self::$langTemplate);
        $tmpHelp = explode("\n",$tmpHelp);
        foreach($tmpHelp as $line){
            if (!empty($line)){
                $rows[] = array('--'.$line);
            } else {
                $rows[] = array($line);
            }
        }
        $rows[] = array();

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
                    $selectedFile = null;
                    if (isset($marking['submission']['file']['displayName'])){
                        $fileInfo = pathinfo($marking['submission']['file']['displayName']);
                        $newFile = array_merge(array(),$marking['submission']['file']);
                        $selectedFile='submission';
                    }
                    $converted=false;

                    if (isset($marking['file']) && $marking['file']!==array()) {
                        $newFile = array_merge(array(),$marking['file']);
                        $selectedFile='marking';
                    }

                    $generateDummyForAllMimeTypes = Course::containsSetting($course,'GenerateDummyCorrectionsForTutorArchives');
                    if (!isset($generateDummyForAllMimeTypes)) $generateDummyForAllMimeTypes = 0;
                    
                    if ($selectedFile == 'submission')
                    if (!isset($newFile['mimeType']) || strpos($newFile['mimeType'],'text/')!==false || $generateDummyForAllMimeTypes){

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

                        if (isset($marking['submission']['comment']) && trim($marking['submission']['comment']) != '')
                            $data.="Kommentar: {$marking['submission']['comment']}\n";

                        $data.="<pre>";
                        $newFileData->setBody($data, true);
                        $newFileSend[] = $newFileData;

                        if (isset($newFile)){
                            if (strpos($newFile['mimeType'],'text/')!==false){
                                $newFileSend[] = $newFile;
                            }
                            $newFileData = new File();
                            $newFileData->setBody("</pre>", true);
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
                                $selectedFile='converted';
                                $sortedMarkings[$exerciseId][$key]['submission']['file']['conv'] = $newFile;
                                $converted=true;
                            }
                            unset($answer);
                        }
                    }

                    //$row[] = $namesOfExercises[$exerciseId].'/'.($converted ? 'K_' :'').$marking['id'].($fileInfo['extension']!='' ? '.'.$fileInfo['extension']:'');
                    //if (!$converted)
                    if (isset($newFile['displayName'])){
                        if (isset($selectedFile) && isset($marking['submission']['file']['displayName']) &&
                            ($selectedFile == 'marking' || $selectedFile == 'converted') &&
                              $newFile['displayName'] == $marking['submission']['file']['displayName']
                           ){

                                $newFile['displayName'] = 'K_'.$newFile['displayName'];

                            }

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
            $csvFile = new File();
            $csvFile->setDisplayName('Liste.csv');
            $csvFile->setBody( Reference::createReference($path) );
            $filesToZip[] = $csvFile;

            ///unlink($path);
            ///$this->deleteDir(dirname($path));

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
        
        // nun werden die übrigen (erlaubten Korrekturen) noch aussortiert, sodass eine Einsendung
        // nur eine Korrektur hat (wähle die letzte Korrektur)
        $computedSubmissions=array();
        $markings = LArraySorter::orderby($markings, 'id', SORT_DESC);
        foreach($markings as $key => $marking){
            $sid = $marking['submission']['id'];
            if (isset($computedSubmissions[$sid])){
                unset($markings[$key]);
            } else {
                $computedSubmissions[$sid] = $sid;
            }
        }
        unset($computedSubmissions);

        // sortiere die Korrekturen innerhalb dieser Liste
        $markings = LArraySorter::orderby($markings, 'id', SORT_ASC);

        $answer = $this->generateTutorArchive($userid, $sheetid, $markings);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }

    public function uploadZip($userid, $courseid)
    {
        $csvFile = 'Liste.csv';

        // error array of strings
        $errors = array();
        LTutor::generatepath($this->config['DIR']['temp']);
        $tempDir = $this->tempdir($this->config['DIR']['temp'], 'extractZip', $mode=0775);

        $body = File::decodeFile($this->app->request->getBody()); //1 file-Object
        $filename = $tempDir.'/'.$courseid.'.zip';
        file_put_contents($filename, $body->getBody( true ));
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
        if (file_exists($files.'/'.$csvFile)){
            // UTF8 is required
            $text = file_get_contents($files.'/'.$csvFile);
            if (mb_detect_encoding($text, 'UTF-8', true)=== false){
                $errors[] = Language::Get('main','utf8Required', self::$langTemplate, array('csvFile'=>$csvFile));
                $this->deleteDir($tempDir);
                $this->app->response->setStatus(409);
                $this->app->response->setBody(json_encode($errors));
                $this->app->stop();
            }

            $csv = fopen($files.'/Liste.csv', "r");

            if (($transactionId = fgetcsv($csv,0,';')) === false){
                fclose($csv);
                $this->deleteDir($tempDir);
                $this->app->response->setStatus(409);
                $errors[] = Language::Get('main','emptyCSV', self::$langTemplate, array('csvFile'=>$csvFile));
                $this->app->response->setBody(json_encode($errors));
                $this->app->stop();
            }
            
            if (!preg_match("%^([a-z0-9_]+)$%", $transactionId[0])){
                fclose($csv);
                $this->deleteDir($tempDir);
                $this->app->response->setStatus(409);
                $errors[] = Language::Get('main','invalidTransactionId', self::$langTemplate, array('transactionId'=>$transactionId[0]));
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

                        if ((isset($currectOrder['ID']) && !isset($row[$currectOrder['ID']])) ||
                            (isset($currectOrder['POINTS']) && !isset($row[$currectOrder['POINTS']])) ||
                            (isset($currectOrder['FILE']) && !isset($row[$currectOrder['FILE']])) ||
                            (isset($currectOrder['TUTORCOMMENT']) && !isset($row[$currectOrder['TUTORCOMMENT']])) ||
                            (isset($currectOrder['OUTSTANDING']) && !isset($row[$currectOrder['OUTSTANDING']])) ||
                            (isset($currectOrder['STATUS']) && !isset($row[$currectOrder['STATUS']]))){
                            $errors[] = Language::Get('main','invalidCSV', self::$langTemplate, array('csvFile'=>$csvFile));
                            fclose($csv);
                            $this->deleteDir($tempDir);
                            $this->app->response->setStatus(409);
                            $this->app->response->setBody(json_encode($errors));
                            $this->app->stop();
                        }

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
                            $errors[] = Language::Get('main','unknownMarkingID', self::$langTemplate, array('csvFile'=>$csvFile,'markingId'=>htmlspecialchars(trim(($markingId)),ENT_QUOTES, 'UTF-8')));
                            $this->app->response->setBody(json_encode($errors));
                            $this->app->stop();
                        }
                        $markingData = $transaction['markings'][$markingId];

                        // checks whether the points are less or equal to the maximum points
                        if ($points > $markingData['maxPoints'] || $points<0){
                            // too much points
                            ///fclose($csv);
                            ///$this->deleteDir($tempDir);
                            ///$this->app->response->setStatus(409);
                            ///$errors[] = "incorrect points in marking: {$markingId}";
                            ///$this->app->response->setBody(json_encode($errors));
                            ///$this->app->stop();
                        }

                        // checks if file with this markingid exists
                        if ($markingFile == null || $markingFile == '' || file_exists($files.'/'.$markingFile)) {

                            if ($markingFile!='' && $markingFile!=null){
                                $fileAddress = $files.'/'.$markingFile; ///file_get_contents($files.'/'.$markingFile);
                                // file
                                $fileInfo = pathinfo($markingFile);
                                $file = new File();
                                $file->setDisplayName($fileInfo['basename']);
                                $file->setBody( Reference::createReference($fileAddress) );
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

                            $markings[] = $marking;

                        } else { //if file with this markingid not exists
                            $errors[] = Language::Get('main','unknownMarkingPath', self::$langTemplate, array('csvFile'=>$csvFile, 'markingFile'=>htmlspecialchars(trim(($markingFile)),ENT_QUOTES, 'UTF-8')));
                            fclose($csv);
                            $this->deleteDir($tempDir);
                            $this->app->response->setStatus(409);
                            $this->app->response->setBody(json_encode($errors));
                            $this->app->stop();
                        }
                    }
                }

                $mark = @json_encode($markings);

                if ($mark !== false){
                    //request to database to edit the markings
                    $result = Request::routeRequest(
                                                    'POST',
                                                    '/marking',
                                                    array(),
                                                    $mark,
                                                    $this->_postMarking,
                                                    'marking'
                                                    );

                    /// TODO: prüfen ob jede hochgeladen wurde
                    if ($result['status'] != 201) {
                        $errors[] = Language::Get('main','errorSendMarkings', self::$langTemplate, array('csvFile'=>$csvFile));
                    }
                } else {
                    $errors[] = Language::Get('main','encodeMarkingsError', self::$langTemplate, array('csvFile'=>$csvFile));
                }

            } else {
                $errors[] = Language::Get('main','noTransactionData', self::$langTemplate, array('csvFile'=>$csvFile));
            }

            fclose($csv);
        } else { // if csv file does not exist
            $errors[] = Language::Get('main','missingCSV', self::$langTemplate, array('csvFile'=>$csvFile));
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
    
    public function addCourse( )
    {
        Logger::Log( 
                    'starts POST AddCourse',
                    LogLevel::DEBUG
                    );
        // decode the received course data, as an object
        $insert = Course::decodeCourse( $this->app->request->getBody( ) );
        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }
        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $insert as $in ){           
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->_out,
                                                  dirname(__FILE__) . '/Sql/AddCourse.sql',
                                                  array( 'in' => $in )
                                                  );
            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $res[] = $in;
                $this->app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'POST AddCourse failed',
                            LogLevel::ERROR
                            );
                $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->app->response->setBody( Course::encodeCourse( $insert ) );
                $this->app->stop( );
            }
        }
        if ( !$arr && 
             count( $res ) == 1 ){
            $this->app->response->setBody( Course::encodeCourse( $res[0] ) );
            
        } else 
            $this->app->response->setBody( Course::encodeCourse( $res ) );
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
            return @rmdir($path);
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
