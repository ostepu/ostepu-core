<?php
/**
 * @file LTutor.php Contains the LTutor class
 * 
 * @author Peter Koenig
 * @author Christian Elze
 * @author Martin Daute 
 */
 
require '../Include/Slim/Slim.php';
include '../Include/Request.php';
include_once( '../Include/CConfig.php' );

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

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to 
     * the functions.
     *
     * @param Component $conf component data
     */
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
        
        // initialize lURL
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
        $namesOfExercises = array();
        
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
                $int = $exercise['id'];
                $namesOfExercises[$int] = 'Aufgabe '.$count;
                $subtask = 0;
            }else{
                $firstRow[] = 'Aufgabe '.$count.$alphabet[$subtask];
                $int = $exercise['id'];
                $namesOfExercises[$int] = 'Aufgabe '.$count.$alphabet[$subtask];
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
                    $row = array();
                    //MarkingId
                    $row[] = $marking['id'];
                    //Points
                    if(array_key_exists('points', $marking)) { 
                        $row[] = $marking['points'];
                    }else {
                        $row[] = "";
                    }
                    //MaxPoints
                    $row[] = $exercise['maxPoints'];
                    //Outstanding
                    if(array_key_exists('outstanding', $marking)) { 
                        $row[] = $marking['outstanding'];
                    }else {
                        $row[] = "";
                    }
                    //Status
                    if(array_key_exists('status', $marking)) { 
                        $row[] = $marking['status'];
                    }else {
                        $row[] = 0;
                    }
                    //TutorComment
                    if(array_key_exists('tutorComment', $marking)) { 
                        $row[] = $marking['tutorComment'];
                    }else {
                        $row[] = "";
                    }
                    //StudentComment
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
        
        //if(file_exists("./csv")){
        //    $dir = "./csv";
        //    $files = glob($dir.'/*.*');
        //    if ( !empty($files) ) {
        //        foreach ($files as $file) {
        //            unlink($file);
        //        }
        //    }
        //    rmdir($dir);  
        //}
        mkdir("./csv");
        //this is the true writing of the CSV-file named [tutorname]_[sheetid].csv
        $CSV = fopen('csv/'.$user['lastName'].'_'.$sheetid.'.csv', 'w');
        
        foreach($rows as $row){
            fputcsv($CSV, $row, ';');
        }
        
        fclose($CSV);
        
        //Create Zip
        $filesToZip = array();
        //Push all SubmissionFiles to an array in order of exercises
        foreach( $exercises as $exercise){
            $exerciseId = $exercise['id'];
            if(in_array($exercise['id'], $exerciseIdWithExistingMarkings)){
                foreach($sortedMarkings[$exerciseId] as $marking){
                    $URL = $this->lURL.'/DB/submission/submission/'.
                                            $marking['submission']['id'];
                    //request to database to get the submission file
                    $answer = Request::custom('GET', $URL, $header,"");
                    $submission = json_decode($answer['content'], true);                
                    
                    //$submission['file'] = array(
                    //            'fileId' => 8,
                    //            'displayName' => "test.pdf",
                    //            'address' => "test/abc",
                    //            'timeStamp' => 123456789,
                    //            'fileSize' => 158,
                    //            'hash' => 'AFD1S65G4F1A34FWEA',
                    //            );
                    $newfile = $submission['file'];
    
                    $newfile['displayName'] = 
                        $namesOfExercises[$exerciseId].'/'.$marking['id'];
                    if ($newfile['fileId'] > 2){            //inkonsistente DB-FS-Verlinkungen
                        $filesToZip[] = $newfile;}
                }
            }
        }
        
        //push the .csv-file to the array
        $path = './csv/'.$user['lastName'].'_'.$sheetid.'.csv';
        $csvFile = array(
                    'displayName' => $user['lastName'].'_'.$sheetid.'.csv',
                    'body' => base64_encode(file_get_contents($path))
                );
        $filesToZip[] = $csvFile;
        
        
        $URL = $this->lURL.'/FS/zip';
        //request to filesystem to create the Zip-File
        $answer = Request::custom('POST', $URL, $header,json_encode($filesToZip));
        $zipFile = json_decode($answer['content'], true); 
                   // print_r(json_encode($filesToZip));
        
        $URL = $this->lURL.'/FS/'.$zipFile['address'].'/'.$userid.'_'.$sheetid.'.zip';
        //request to filesystem to get the created Zip-File
        $answer = Request::custom('GET', $URL, $header,json_encode($filesToZip));
        
        //ToDo: get Zip-File
        $this->app->response->headers->set('Content-Type', 'application/zip');
        $this->app->response->headers->set('Content-Disposition', $answer['headers']['Content-Disposition']);
        $this->app->response->setBody($answer['content']);
    }

    public function uploadZip($userid, $sheetid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody(), true); //1 file-Object
        
        $URL = 'http://141.48.9.92/uebungsplattform/DB/DBUser/user/'.$userid;
        //request to database to get the tutor
        $answer = Request::custom('GET', $URL, $header,"");
        $user = json_decode($answer['content'], true); 
        
        $filename = $user['userName'].'.zip';
        file_put_contents($filename, base64_decode($body['body']));
        
        $zip = new ZipArchive();
        $zip->open($filename);
        $zip->extractTo('./'.$userid.'/');
        $zip->close();
        $csv = fopen('./'.$userid.'/'.$user['lastName'].'_'.$sheetid.'.csv', "r");
        while (($row = fgetcsv($csv)) !== false){
            $row = explode(";", $row[0]);
            if($row[0][0] == "A"){
                $exerciseName = $row[0];
            }elseif(!($row[0] == "ID" or Count($row) == 1)){
                $fileBody = file_get_contents('./'.$userid.'/'.$exerciseName.'/'.$row[0].'.pdf');
                $file = array(
                        'displayName' => $exerciseName.'_'.$row[0],
                        'body' => base64_encode($fileBody),
                        );
                
                $URL = $this->lURL.'/FS/file';
                //request to filesystem to save the marking file
                $answer = Request::custom('POST', $URL, $header,json_encode($file));
                $markingFile = json_decode($answer['content'], true);
                
                $marking = array(
                        'id' => $row[0],
                        'points' => $row[1],
                        'outstanding' => $row[3],
                        'tutorId' => $userid,
                        'tutorComment' => $row[5],
                        'file' => $markingFile['address'],
                        'status' => $row[4],
                        );
                        
                 $URL = $this->lURL.'/DB/marking/'.$marking['id'];
                //request to database to edit the marking
                $answer = Request::custom('PUT', $URL, $header,json_encode($marking));
            }
        }
        fclose($csv);
        
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