<?php
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
    private $_postMarking = array();
    private $_getMarking = array();
    private $_getExercise = array();

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

        //uploadZip
        $this->app->post('/'.$this->getPrefix().'/user/:userid/exercisesheet/:sheetid(/)', array($this, 'uploadZip'));

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
    public function getZip($userid, $sheetid)
    {
        $multiRequestHandle = new Request_MultiRequest();
        
        //request to database to get the markings
        $handler = Request_CreateRequest::createCustom('GET', $this->_getMarking[0]->getAddress().'/marking/exercisesheet/'.$sheetid.'/tutor/'.$userid, array(),"");
        $multiRequestHandle->addRequest($handler);
        
        //request to database to get the exercise sheets
        $handler = Request_CreateRequest::createCustom('GET', $this->_getExercise[0]->getAddress().'/exercise/exercisesheet/'.$sheetid, array(),"");
        $multiRequestHandle->addRequest($handler);
        
        $answer = $multiRequestHandle->run();
        if (count($answer)< 2 || !isset($answer[0]['status']) || $answer[0]['status']!=200 || !isset($answer[0]['content']) || !isset($answer[1]['status']) || $answer[1]['status']!=200 || !isset($answer[1]['content'])){
            $this->app->response->setStatus(404);
            $this->app->stop();
        }

        $markings = json_decode($answer[0]['content'], true);
        $exercises = json_decode($answer[1]['content'], true);

        
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

        //formating, create the layout of the CSV-file for the tutor
        //first two rows of an exercise are the heads of the table
        foreach ($exercises as $exercise){
            $firstRow = array();
            $secondRow = array();
            $row = array();
            
            if (!isset($exercise['id'])) continue;
            $exerciseId = $exercise['id'];

            $firstRow[] = '--'.$namesOfExercises[$exerciseId];
                
            // $firstRow[] = $exerciseId; /// obsolete???
            $secondRow[] = '--ID';
            $secondRow[] = 'Points';
            $secondRow[] = 'MaxPoints';
            $secondRow[] = 'Outstanding?';
            $secondRow[] = 'Status';
            $secondRow[] = 'TutorComment';
            $secondRow[] = 'StudentComment';
            $secondRow[] = 'File';

            //formating, write known informations of the markings in the CSV-file
            //after the second row to each exercise
            if(in_array($exerciseId, $exerciseIdWithExistingMarkings)){
                $rows[] = $firstRow;
                $rows[] = $secondRow;
                foreach($sortedMarkings[$exerciseId] as $marking){
                    $row = array();
                    //MarkingId
                    if (!isset($marking['id'])) continue;
                    $row[] = $marking['id'];
                    
                    $ExerciseData['markings'][$marking['id']] = array();
                    $ExerciseData['markings'][$marking['id']]['sheetId'] = $exercise['sheetId'];
                    $ExerciseData['markings'][$marking['id']]['courseId'] = $exercise['courseId'];
                    $ExerciseData['markings'][$marking['id']]['exerciseId'] = $exerciseId;                    
                    
                    //Points
                    $row[] = (isset($marking['points']) ? $marking['points'] : '0');

                    //MaxPoints
                    $row[] = (isset($exercise['maxPoints']) ? $exercise['maxPoints'] : '0');
                    $ExerciseData['markings'][$marking['id']]['maxPoints'] = (isset($exercise['maxPoints']) ? $exercise['maxPoints'] : '0');
                    
                    $ExerciseData['markings'][$marking['id']]['submissionId'] = $marking['submission']['id'];
                    
                    //Outstanding
                    $row[] = (isset($marking['outstanding']) ? $marking['outstanding'] : '');

                    //Status
                    $row[] = (isset($marking['status']) ? $marking['status'] : '0');

                    //TutorComment
                    $row[] = (isset($marking['tutorComment']) ? $marking['tutorComment'] : '');

                    //StudentComment
                    if (isset($marking['submission'])){
                        $submission = $marking['submission'];
                        $row[] = (isset($submission['comment']) ? $submission['comment'] : '');
                    }
                    
                    // file
                    $fileInfo = pathinfo($marking['submission']['file']['displayName']);
                    $row[] = $namesOfExercises[$exerciseId].'/'.$marking['id'].($fileInfo['extension']!='' ? '.'.$fileInfo['extension']:'');

                    $rows[] = $row;
                }
                //an empty row after an exercise
                $rows[] = array();
            }

        }

        //request to database to get the user name of the tutor for the
        //name of the CSV-file
        
        // create transaction ticket
        $transaction = Transaction::createTransaction(
                                                      null,
                                                      (time() + (30 * 24 * 60 * 60)),
                                                      'TutorCSV_'.$userid.'_'.$sheetid,
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
            $CSV = fopen($tempDir.'/'.$sheetid.'.csv', 'w'); // $user['lastName'].'_'.

            foreach($rows as $row){
                fputcsv($CSV, $row, ';','"');
            }

            fclose($CSV);

            //Create Zip
            $filesToZip = array();
            //Push all SubmissionFiles to an array in order of exercises
            foreach( $exercises as $exercise){
                $exerciseId = $exercise['id'];
                if(in_array($exercise['id'], $exerciseIdWithExistingMarkings)){
                    foreach($sortedMarkings[$exerciseId] as $marking){
                        if (!isset($marking['submission']['selectedForGroup']) || !$marking['submission']['selectedForGroup'])
                            continue;
                            
                        $submission = $marking['submission'];

                        $newfile = $submission['file'];

                        $fileInfo = pathinfo($newfile['displayName']);
                        $newfile['displayName'] = $namesOfExercises[$exerciseId].'/'.$marking['id'].($fileInfo['extension']!='' ? '.'.$fileInfo['extension']:'');

                        $filesToZip[] = $newfile;
                    }
                }
            }

            //push the .csv-file to the array
            $path = $tempDir.'/'.$sheetid.'.csv';//$user['lastName'].'_'.
            $csvFile = array(
                        'displayName' => $sheetid.'.csv', //$user['lastName'].'_'.
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
                
                //if (isset($result['headers']['Content-Type']))
                //  $this->app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
            
                //if (isset($result['headers']['Content-Disposition']))
                    //$this->app->response->headers->set('Content-Disposition', $result['headers']['Content-Disposition']);
                $this->app->response->setBody(File::encodeFile($ff));
                $this->app->response->setStatus(201);
            } else 
                $this->app->response->setStatus(409);
        } else 
            $this->app->response->setStatus(409);
    }

    public function uploadZip($userid, $sheetid)
    {                        
        // error array of strings
        $errors = array();
        LTutor::generatepath($this->config['DIR']['temp']);
        $tempDir = $this->tempdir($this->config['DIR']['temp'], 'extractZip', $mode=0775);

        $body = json_decode($this->app->request->getBody(), true); //1 file-Object        
        $filename = $tempDir.'/'.$sheetid.'.zip';
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
        if (file_exists($files.'/'.$sheetid.'.csv')){
            $csv = fopen($files.'/'.$sheetid.'.csv', "r");
            
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
                                            '/transaction/authentication/TutorCSV_'.$userid.'_'.$sheetid.'/transaction/'.$transactionId[0],
                                            array(),
                                            '',
                                            $this->_getTransaction,
                                            'transaction'
                                            );

            if (isset($result['status']) && $result['status']==200 && isset($result['content'])){
                $transaction = Transaction::decodeTransaction($result['content']);
                $transaction = json_decode($transaction->getContent(),true);
                unset($result);
                                        
                $markings = array();
                while (($row = fgetcsv($csv,0,';')) !== false){
                    if(count($row)>=8 && $row[0] != "" && substr($row[0],0,2)!='--'){
                    
                        $markingId = $row[0];
                        $points = $row[1];        
                        $markingFile = $row[7];                           
                        
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
                                        'displayName' => $fileInfo['filename'],
                                        'body' => base64_encode($fileBody),
                                        );
                            } else {
                                $file = null;
                            }
                            
                            // create new marking object
                            $marking = array(
                                    'id' => $markingId,
                                    'points' => $points,
                                    'outstanding' => $row[3],
                                    'tutorId' => $userid,
                                    'tutorComment' => $row[5],
                                    'file' => $file,
                                    'status' => $row[4],
                                    'date' => time(),
                                    'hideFile' => ($file==null ? 1 : 0)
                                    );

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

?>