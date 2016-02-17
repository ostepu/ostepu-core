<?php
/**
 * @file LOOP.php Contains the LOOP class
 * 
 * @author Till Uhlig
 * @date 2014
 */

require_once dirname(__FILE__) . '/../../Assistants/Slim/Slim.php';
include_once dirname(__FILE__) . '/../../Assistants/Request.php';
include_once dirname(__FILE__) . '/../../Assistants/CConfig.php';
include_once dirname(__FILE__) . '/../../Assistants/DBJson.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../../Assistants/Sandbox.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LOOP-Component
 */
class LOOP
{
    /**
     * @var Slim $_app the slim object
     */
    private $app = null;
    
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var ini $iniconfig the ini config data which stores temp and file dirs
     */
    private $iniconfig=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "process";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LOOP::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LOOP::$_prefix = $value;
    }

    /**
     * @var Link[] $_pdf a list of links
     */
    private $_pdf = array( );
    
    /**
     * @var Link[] $_formDb a list of links
     */
    private $_postProcess = array( );
    private $_postCourse = array( );
    private $_deleteProcess = array( );
    private $_deleteCourse = array( );
    private $_getProcess = array( );
    private $_postTestcase = array( );
    private $_selfLink = array( );
    private $_popTestcase = array( );
    private $_submission = array( );
    private $_editTestcase = array( );
    private $_getTestcase = array( );
    private $_getExercise = array( );
    private $_marking = array( );
    
    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct()
    {
        if (file_exists(dirname(__FILE__).'/config.ini')){
            $this->iniconfig = parse_ini_file(
                                           dirname(__FILE__).'/config.ini',
                                           TRUE
                                           );
        }

        // runs the CConfig
        $com = new CConfig( LOOP::getPrefix( ) . ',course,link', dirname(__FILE__) );

        // runs the LOOP
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( ); // lädt die Daten der CConfig.json
            
        // initialize slim    
        $this->app = new \Slim\Slim(array('debug' => true));
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        
        // hier werden die Verknüpfungen aus der CConfig.json ausgelesen (entsprechend ihrem Namen)
        $this->_conf = $conf;
        $this->_pdf = CConfig::getLinks($conf->getLinks(),"pdf"); // wird nicht genutzt, theoretisch koennten hier PDFs erzeugt werden
         // für POST /course zum eintragen als Verarbeitung (wird dann in CreateSheet aufgelistet)
        $this->_postProcess = CConfig::getLinks($conf->getLinks(),"postProcess");
        $this->_postCourse = CConfig::getLinks($conf->getLinks(),"postCourse");
        $this->_deleteProcess = CConfig::getLinks($conf->getLinks(),"deleteProcess"); // für DELETE /course/xyz
        $this->_deleteCourse = CConfig::getLinks($conf->getLinks(),"deleteCourse");
        $this->_getProcess = CConfig::getLinks($conf->getLinks(),"getProcess"); // GET /link/exists/course/:courseid
        $this->_pdf = CConfig::getLinks($conf->getLinks(),"pdf");
        $this->_postTestcase = CConfig::getLinks($conf->getLinks(),"postTestcase");
        $this->_selfLink = CConfig::getLinks($conf->getLinks(),"selfLink");
        $this->_popTestcase = CConfig::getLinks($conf->getLinks(),"popTestcase");
        $this->_submission = CConfig::getLinks($conf->getLinks(),"getSubmission");
        $this->_editTestcase = CConfig::getLinks($conf->getLinks(),"editTestcase");
        $this->_getTestcase = CConfig::getLinks($conf->getLinks(),"getTestcase");
        $this->_getExercise = CConfig::getLinks($conf->getLinks(),"getExercise");
        $this->_marking = CConfig::getLinks($conf->getLinks(),"marking");
        

        // POST PostProcess
        $this->app->map('/'.$this->getPrefix().'(/)',
                        array($this, 'postProcess'))->via('POST');

        // POST saveTestcases
        // erstellt Testcases (Daten kommen im Anfragekörper)
        $this->app->post( 
                         '/postprocess(/)',
                         array( 
                               $this,
                               'saveTestcases'
                               )
                         );
                        
        // POST AddCourse
        // fügt die Komponente der Veranstaltung hinzu (Daten kommen im Anfragekörper)
        $this->app->post( 
                         '/course(/)',
                         array( 
                               $this,
                               'addCourse'
                               )
                         );

        // POST AddPlatform
        // fügt die Komponente der Plattform hinzu (Daten kommen im Anfragekörper)
        $this->app->post( 
                         '/platform(/)',
                         array( 
                               $this,
                               'addPlatform'
                               )
                         );
                         
        // DELETE DeleteCourse
        // entfernt die Komponente aus der Veranstaltung
        $this->app->delete( 
                         '/course/:courseid(/)',
                         array( 
                               $this,
                               'deleteCourse'
                               )
                         );

        // DELETE DeleteCourse
        // entfernt die Komponente aus der Veranstaltung
        $this->app->delete( 
                         '/platform(/)',
                         array( 
                               $this,
                               'deletePlatform'
                               )
                         );
                         
        // GET GetExistsCourse
        // zum Prüfen, ob diese Kompoenten korrekt installiert wurde (existiert Tabelleneintrag, konf-Dateien etc.)
        $this->app->get( 
                         '/link/exists/course/:courseid(/)',
                         array( 
                               $this,
                               'getExistsCourse'
                               )
                        );

        // GET startCompute
        // zum n-maligen Starten der Compute Funktion 
        $this->app->get( 
                         '/start/:count(/)',
                         array( 
                               $this,
                               'startCompute'
                               )
                        );

        // GET startCompute
        // zum n-maligen Starten der Compute Funktion 
        $this->app->get( 
                         '/compute/:count(/)',
                         array( 
                               $this,
                               'compute'
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

        // run Slim
        $this->app->run();
    }

    public function createMarking(&$pro, $text, $file, $status)
    {
        if ($pro->getMarking() === null){
            $timestamp = time();
            $raw = $pro->getRawSubmission();
            $exerciseName = '';
            
            if ( $raw !== null )
                $exerciseName = $raw->getExerciseName();
            
        
            $Text=  "<h1>AUFGABE {$exerciseName}</h1>".
                    "<hr>";
                    
            $Text.= "<p>".
                    "<h2>Fehler:</h2>".
                    "<span style=\"color: 'black'\">".
                    $text.
                    "</span></p><br /><br /><p>".
                    "<span style=\"color: 'red'\">0 Punkte</span>".
                    "</p>";

            
            $pdf = Pdf::createPdf($Text);
//echo Pdf::encodePdf($pdf);return;
            $result = Request::routeRequest( 
                                            'POST',
                                            '/pdf',
                                            array(),
                                            Pdf::encodePdf($pdf),
                                            $this->_pdf,
                                            'pdf'
                                            );
            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                 
                $pdf = File::decodeFile($result['content']);
                
                $pdf->setDisplayName($exerciseName.'.pdf');
                $pdf->setTimeStamp($timestamp);
                $pdf->setBody(null);
                
                $submission = $pro->getSubmission();
                if ($submission === null) $submission = $pro->getRawSubmission();
                
                $studentId = ($pro->getRawSubmission()!==null ? $pro->getRawSubmission()->getStudentId() : null);
                
                if ($studentId===null)
                    $studentId = ($pro->getSubmission()!==null ? $pro->getSubmission()->getStudentId() : null);
                
                $marking = Marking::createMarking( 
                                                 null,
                                                 $studentId,
                                                 null,
                                                 null,
                                                 null,
                                                 null,
                                                 $status,
                                                 0,
                                                 ($submission->getDate()!==null ? $submission->getDate() : time())
                                                 );
                if (is_object($submission))
                    $marking->setSubmission(clone $submission);
                    
                $marking->setFile($pdf);
                $pro->setMarking($marking);
                
            } else {
                $this->app->response->setStatus( 409 );
            }
        }
    }

    public function createMarkingViaPost($testcases, $submission, $status)
    {
            $result = Request::routeRequest( 
                                        'GET',
                                        '/exercise/exercise/'.$submission->getExerciseId().'/nosubmission',
                                        array(),
                                        '',
                                        $this->_getExercise
                                        );
            $myExercise = null;
            if ( $result['status'] >= 200 && $result['status'] <= 299 ){
                $myExercise = Exercise::decodeExercise($result['content']);
            }

            $timestamp = time();
            
        
            $Text=  "<h1>AUSWERTUNG</h1>".
                    "<hr>";
                    
            $Text.= "<p>".
                    "<h2>Testcases:</h2>".
                    "<span style=\"color: 'black'\">";

            $counter = 1;
            foreach ($testcases as $value) {
                if (!empty($value->getRunOutput())) {
                    $Text.= "Durchgang {$counter} Ausgabe: <br />".$value->getRunOutput()."<br />";
                } else {
                    $Text.= "Durchgang {$counter} Ausgabe: <br />leer<br />";
                }
                if ($value->getStatus() == 2) {$Text.= "ist Korrekt.<br /><br />";}
                if ($value->getStatus() == 3) {$Text.= "ist Falsch.<br /><br />";}

                $counter = $counter + 1;
            }

            $Text.= "</span></p>";

            if ($status == 3) {
                $Text.= "<br /><br /><p>".
                    "<span style=\"color: 'red'\">0 Punkte</span>".
                    "</p>";
                } elseif ($status == 2) {
                    $Text.= "<br /><br /><p>".
                    "<span style=\"color: 'red'\">".$myExercise->getMaxPoints()." Punkte</span>".
                    "</p>";
                }
            

            
            $pdf = Pdf::createPdf($Text);
//echo Pdf::encodePdf($pdf);return;
            $result = Request::routeRequest( 
                                            'POST',
                                            '/pdf',
                                            array(),
                                            Pdf::encodePdf($pdf),
                                            $this->_pdf,
                                            'pdf'
                                            );
            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                 
                $pdf = File::decodeFile($result['content']);
                
                $pdf->setDisplayName('Auswertung.pdf');
                $pdf->setTimeStamp($timestamp);
                $pdf->setBody(null);
                
                $studentId = ($submission->getStudentId()!==null ? $submission->getStudentId() : null);
                
                
                $marking = Marking::createMarking( 
                                                 null,
                                                 $studentId,
                                                 null,
                                                 null,
                                                 null,
                                                 null,
                                                 $status,
                                                 0,
                                                 ($submission->getDate()!==null ? $submission->getDate() : time())
                                                 );
                if (is_object($submission))
                    $marking->setSubmission(clone $submission);
                    
                $marking->setFile($pdf);
                
                $result = Request::routeRequest( 
                                            'POST',
                                            '/marking',
                                            array(),
                                            Marking::encodeMarking($marking),
                                            $this->_marking,
                                            'marking'
                                            );

                if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                    return true;
                } else {
                    return false;
                }
                
            } else {
                return false;
            }
    }
   
    /**
     * Removes the component from a given course
     *
     * Called when this component receives an HTTP DELETE request to
     * /course/$courseid(/).
     *
     * @param string $courseid The id of the course.
     */
    public function deleteCourse( $courseid )
    {
        $result = Request::routeRequest( 
                                        'GET',
                                        '/process/course/'.$courseid.'/component/'.$this->_conf->getId(),
                                        array(),
                                        '',
                                        $this->_getProcess,
                                        'process'
                                        );
                                        
        // wenn es einen Eintrag für die Komponente als Verarbeitung gibt, kann diese gelöscht werden
        if (isset($result['status']) && $result['status'] >= 200 && $result['status'] <= 299 && isset($result['content']) && $this->_conf !== null){
        
            // ermittelt die VerarbeitungsID dieser Komponente
            $process = Process::decodeProcess($result['content']);
            if (is_array($process)) $process = $process[0];
            $deleteId = $process->getProcessId();
            
            // nun kann der Eintrag entfernt werden
            $result = Request::routeRequest( 
                                            'DELETE',
                                            '/process/process/' . $deleteId,
                                            $this->app->request->headers->all( ),
                                            '',
                                            $this->_deleteProcess,
                                            'process'
                                            );

            // lösche Testcase Datenbank für course (Prüfung ob sie existiert erfolgt in DBOOB)
            

            $result2 = Request::routeRequest( 
                                            'DELETE',
                                            '/course/' . $courseid,
                                            $this->app->request->headers->all( ),
                                            '',
                                            $this->_deleteCourse,
                                            'course'
                                            );
            
                                              
            if (isset($result['status']) && $result['status'] === 201 && isset($result['content']) && $this->_conf !== null){
                // Eintrag konnte erfolgreich entfernt werden
                $this->app->response->setStatus( 201 );
                $this->app->stop();
            }
            
            // der Eintrag konnte nicht entfernt werden
            $this->app->response->setStatus( 409 );
            $this->app->stop();
        }
                                        
        $this->app->response->setStatus( 404 );
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
    }

    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( )
    {
        $body = $this->app->request->getBody();
        
        $platform = Platform::decodePlatform($body);

        $file = dirname(__FILE__).'/config.ini';
        $text = "[DIR]\n".
                "temp = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$platform->getTempDirectory()))."\"\n".
                "files = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$platform->getFilesDirectory()))."\"\n";
                
        if (!@file_put_contents($file,$text)){

            $this->app->response->setStatus( 409 );
            $this->app->stop();
        }  

        $this->app->response->setStatus( 201 );
    }
   
    /**
     * Adds the component to a course
     *
     * Called when this component receives an HTTP POST request to
     * /course(/).
     */
    public function addCourse( )
    {
         Logger::Log( 
                    'starts POST AddCourse',
                    LogLevel::DEBUG
                    );

        $body = $this->app->request->getBody();
        
        // die Daten der Veranstaltung kommen über den Aufrufkörper rein
        $courses = Course::decodeCourse($body);
        $processes = array();
        if (!is_array($courses)) $courses = array($courses);
        
        // wenn die Komponente direkt in mehrere Veranstaltungen eingetragen werden soll,
        // wird das Eintragen für alle vorgenommen
        foreach ($courses as $course){
            
            // ab hier wird der neue Eintrag zusammengestellt
            $process = new Process();
            
            $exercise = new Exercise();
            $exercise->setCourseId($course->getId());
            
            $process->setExercise($exercise);
            
            $component = new Component();
            $component->setId($this->_conf->getId());
            
            $process->setTarget($component);
            
            // und nun wird gesammelt
            $processes[] = $process;
        }
    
        // die erstellten Einträge können nun an den Zuständigen geschickt werden
        foreach ( $this->_postProcess as $_link ){
            $result = Request::routeRequest( 
                                            'POST',
                                            '/process',
                                            array(),
                                            Process::encodeProcess($processes),
                                            $_link,
                                            'process'
                                            );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){

                foreach ( $this->_postCourse as $_link2 ){
                    $result2 = Request::routeRequest( 
                                            'POST',
                                            '/course',
                                            array(),
                                            Course::encodeCourse($courses),
                                            $_link2,
                                            'course'
                                            );


                    if ( $result2['status'] >= 200 && $result2['status'] <= 299 ){
                        // wenn der Erstellvorgang erfolgreich war, können wir dies melden
                        $this->app->response->setStatus( 201 );
                        if ( isset( $result['headers']['Content-Type'] ) )
                            $this->app->response->headers->set( 
                                                                'Content-Type',
                                                                $result['headers']['Content-Type']
                                                                );
                    } else {
                        $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                        $this->app->response->setBody( Course::encodeCourse( $courses ) );
                        $this->app->stop( );
                    }
                }

                
                
            } else {
                // der Eintrag konnte nicht erstellt werden
                
               /* if ($courses->getId()!==null){
                    $this->deleteCourse($courses->getId());
                }*/
            
                Logger::Log( 
                            'POST AddCourse failed',
                            LogLevel::ERROR
                            );
                            
                // gibt den Status der "Erstellanfrage" zurück oder eine 409 und zusätzlich
                // das eingehende Veranstaltungsobjekt
                $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->app->response->setBody( Course::encodeCourse( $courses ) );
                $this->app->stop( );
            }
        }
        
        // die bearbeiteten Veranstaltungen können nun zur Ausgabe hinzugefügt werden
        $this->app->response->setBody( Course::encodeCourse( $courses ) );
    }

    /**
     * gives count of processes containing the string $name 
     *
     *@param string $name the searchstring 
     *
     * @return int count of processes
     */
    private static function countProcessesContaining($string)
    {
        $returnVal = shell_exec("ps aux|grep '".$string."'|grep -v grep|wc -l");
        return $returnVal;
    }

    /**
     * open website in background
     *
     *@param string $adress the url
     *
     */
    private static function runInBackground($adress)
    {
        $proc_command = "wget ".$adress." -q -O - -b";
        $proc = popen($proc_command, "r");
        pclose($proc);
    }

    /**
     * Starts the compute function.
     *
     * @param int $count Amount of Compute calls.
     */
    public function startCompute($count)
    {
        $adress = $this->_selfLink[0]->getAddress()."/compute/".$count;

        for ($i=0; $i < $count; $i++) { 
            $amountOfProcesses = LOOP::countProcessesContaining("wget ".$adress);

            if ($amountOfProcesses < $count) {
                LOOP::runInBackground($adress);
            } else {
                break;
            }
        }

        $this->app->response->setStatus( 200 );
    }

    /**
     * scans Directory for executable Files
     * @param       string   $source    Source path
     *
     * @return      string   Returns name of executable file
     */
    public function scanForExecutables($source)
    {
        // Loop through the folder
        $dir = dir($source);
        $execfilepath = "";

        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            $returnVal = shell_exec("file --mime-type $source/$entry");
            
            $count = substr_count($returnVal, 'application/x-executable');

            if ($count > 0) {
                $execfilepath = $entry;
                break;
            }

            $count = substr_count($returnVal, 'application/x-java-applet');

            if ($count > 0) {
                $execfilepath = basename($entry,".class");
                break;
            }
        }

        // Clean up
        $dir->close();
        return $execfilepath;
    }

    /**
     * the compute function for testing testcases
     *
     * @param int $count Amount of Compute calls.
     */
    public function compute($count)
    {
        $adress = $this->_selfLink[0]->getAddress()."/start/".$count;

        $result = Request::routeRequest( 
                                        'GET',
                                        '/pop',
                                        array(),
                                        '',
                                        $this->_popTestcase
                                        );

        //submission(/submission)/:suid

        // checks the correctness of the query
        if ( $result['status'] >= 200 && $result['status'] <= 299 ){
            $testcase = Testcase::decodeTestcase($result['content']);
            $submission = null;

            // GET Submission, if no submission attached then end compute
            if (!empty($testcase) && $testcase->getSubmissionId() !== null){
                // get selected submission
                $resultSub = Request::routeRequest( 
                                        'GET',
                                        '/submission/submission/'.$testcase->getSubmissionId(),
                                        array(),
                                        '',
                                        $this->_submission
                                        );

                if ( $resultSub['status'] >= 200 && $resultSub['status'] <= 299 ){
                    $submission = Submission::decodeSubmission($resultSub['content']);

                    if ($submission === null) {
                        $this->app->response->setStatus( 409 );
                        $this->app->stop();
                    }
                }
            } else {
                $this->app->response->setStatus( 409 );
                $this->app->stop();
            }

            // check if workdir is available, if not copy it from portal temp directory
            $myWorkDir = $testcase->getWorkDir();
            $foldername = basename($myWorkDir);
            $myWorkDir = $myWorkDir . "_" . $testcase->getTestcaseId();
            $backupPath = $this->iniconfig['DIR']['temp'].'/'.$foldername;

            if(!empty($myWorkDir) && !is_dir($myWorkDir)){
                if(is_dir($backupPath)){
                    $this->xcopy($backupPath, $myWorkDir,0777);
                } else {
                    $this->app->response->setStatus( 409 );
                    $this->app->stop();
                }
            } elseif (empty($myWorkDir)) {
                $this->app->response->setStatus( 409 );
                $this->app->stop();
            }

            // decode Input and Output Params
            $myInputs = json_decode($testcase->getInput());
            $myOutput = json_decode($testcase->getOutput());

            if (isset($myInputs) && !empty($myInputs) && isset($myOutput) && !empty($myOutput)) {
                if ($testcase->getTestcaseType() == "java" || $testcase->getTestcaseType() == "cx") {
                    $execFilename = $this->scanForExecutables($myWorkDir);

                    $params = "";
                    $inputfiles = array();

                    // parse input parameters
                    foreach ($myInputs as $input) {
                        if (isset($input[0]) && $input[0] == "Text" && isset($input[1])) {
                            // simple Text
                            $params .= escapeshellarg($input[1])." ";
                        } elseif (isset($input[0]) && $input[0] == "Data" && isset($input[1]) && !empty($input[1])) {
                            // copy input files in workdir and set the path in params
                            $inputfile = File::decodeFile($input[1],false);
                            $inputfilePath = $inputfile->getAddress();

                            if(!empty($inputfilePath) && !in_array($inputfilePath, $inputfiles)) {
                                $this->xcopy($this->iniconfig['DIR']['files'].'/'.$inputfilePath, $myWorkDir.'/'.$inputfile->getDisplayName(),0777);
                                $inputfiles[] = $inputfilePath;
                            }

                            if(!empty($inputfilePath)) {
                                $params .= escapeshellarg($myWorkDir.'/'.$inputfile->getDisplayName())." ";
                            }
                        }
                    }

                    $params = trim($params);

                    // execute program in sandbox
                    $compileSandbox = new Sandbox();
                    $compileSandbox->setWorkingDir($myWorkDir);
                    $compileSandbox->loadProfileFromFile(dirname(__FILE__) . '/../../Assistants/mysandbox.profile');

                    $return = 0;
                    $output = "";
                    $ValidationOK = false;

                    if ($testcase->getTestcaseType() == "java") {
                        $return = $compileSandbox->sandbox_exec('java',$execFilename." ".$params,$output);
                    } elseif ($testcase->getTestcaseType() == "cx") {
                        $return = $compileSandbox->sandbox_exec('./'.$execFilename,$params,$output);
                    }

                    $output = trim($output,"\t\n\r\0\x0B");

                    // validate output
                    $correctOutput = "";
                    $mytype = "";

                    if (isset($myOutput[0]) && $myOutput[0] == "Text" && isset($myOutput[1])) {
                        $correctOutput = trim($myOutput[1],"\t\n\r\0\x0B");
                        $mytype = "Text";
                    } elseif (isset($myOutput[0]) && $myOutput[0] == "Data" && isset($myOutput[1]) && !empty($myOutput[1])) {

                        $outputfile = File::decodeFile($myOutput[1],false);
                        $outputfilePath = $outputfile->getAddress();

                        $correctOutput = file_get_contents($this->iniconfig['DIR']['files'].'/'.$outputfilePath);
                        if ($correctOutput !== false) {
                            $correctOutput = trim($correctOutput,"\t\n\r\0\x0B");
                        } else {
                            $correctOutput = "";
                        }
                        $mytype = "Data";

                    } elseif (isset($myOutput[0]) && $myOutput[0] == "Regex" && isset($myOutput[1])) {
                        //$delimiterRegex = substr(stripslashes(trim($myOutput[1])), 0, 1);

                        $correctOutput = preg_quote(stripcslashes(trim($myOutput[1])));
                        $mytype = "Regex";
                    }

                    if ($mytype == "Text" || $mytype == "Data") {
                        if (strcmp($output, $correctOutput) == 0) {
                            $ValidationOK = true;
                        }
                    } elseif ($mytype == "Regex") {
                        if ($correctOutput == "") {
                            $correctOutput = "/.*/";
                        }
                        $rexexMatch = preg_match($correctOutput, $output);

                        if ($rexexMatch === 1) {
                            $ValidationOK = true;
                        }
                    }

                    if (isset($submission) && !empty($submission)){
                        $testcase->setSubmission($submission);

                        if(is_array($testcase->getProcess())) {
                            $testcase->setProcess($testcase->getProcess()[0]);
                        }
                        
                        if ($ValidationOK == true) {
                            $testcase->setStatus(2);
                        } else {
                            $testcase->setStatus(3);
                        }

                        $testcase->setRunOutput($output);

                        // update Testcase
                        $resultPut = Request::routeRequest( 
                                                        'POST',
                                                        '/testcase/testcase/'.$testcase->getTestcaseId(),
                                                        array(),
                                                        Testcase::encodeTestcase($testcase),
                                                        $this->_editTestcase
                                                       );

                        // checks the correctness of the query
                        if ( $resultPut['status'] >= 200 && $resultPut['status'] <= 299 ){
                            // get Testcases
                            $resultGet = Request::routeRequest( 
                                                            'GET',
                                                            '/testcase/submission/'.$testcase->getSubmissionId().'/course/'.$testcase->getProcess()->getObjectCourseFromProcessIdId(),
                                                            array(),
                                                            Testcase::encodeTestcase($testcase),
                                                            $this->_getTestcase
                                                           );

                            // test all testcases if ok then create marking
                            if ( $resultPut['status'] >= 200 && $resultPut['status'] <= 299 ){
                                $allTestcases = Testcase::decodeTestcase($resultGet['content']);

                                if(!empty($allTestcases)) {
                                    $StatusForTestcases = 2;
                                    foreach ($allTestcases as $singleTestcase) {
                                        if ($singleTestcase->getStatus() == 2) {continue;}
                                        if ($singleTestcase->getStatus() == 3) {$StatusForTestcases = 3; continue;}
                                        if ($singleTestcase->getStatus() < 2) {$StatusForTestcases = $singleTestcase->getStatus(); break;}
                                    }

                                    // create Marking
                                    if($StatusForTestcases >= 2) {
                                        $createdResult = $this->createMarkingViaPost($allTestcases, $submission, $StatusForTestcases);

                                        if($createdResult == true) {
                                            //$this->deleteDir($backupPath);
                                        }
                                    }
                                }
                            }

                        } else {
                            $this->deleteDir($myWorkDir);
                            $this->app->response->setStatus( $resultPut['status'] );
                            $this->app->stop();
                        }
                    }
                }
            }
        }
        
        $adress = $this->_selfLink[0]->getAddress()."/start/".$count;
        LOOP::runInBackground($adress);

        
        $this->deleteDir($myWorkDir);
        $this->app->response->setStatus( 200 );
    }
   
    /**
     * Returns whether the component is installed for the given course
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/course/$courseid(/).
     *
     * @param int $courseid A course id.
     */
    public function getExistsCourse($courseid)
    {
        // hier soll geprüft werden, ob ein entsprechender Eintrag für diese Komponente in der referenzierten Prozesstabelle besteht,
        // ob sie also als Verarbeitung registriert ist (dazu wird die ID dieser Komponente verwendet ($this->_conf->getId()))
        $result = Request::routeRequest( 
                                        'GET',
                                        '/process/course/'.$courseid.'/component/'.$this->_conf->getId(),
                                        array(),
                                        '',
                                        $this->_getProcess,
                                        'process'
                                        );
                       
        // wenn etwas für die ID dieser Komponente von der DB geantwortet wird, ist die Installation korrekt bzw. vorhanden.
        if (isset($result['status']) && $result['status'] >= 200 && $result['status'] <= 299 && isset($result['content']) && $this->_conf !== null && $this->_conf->getId() !== null){
            $this->app->response->setStatus( 200 );
            $this->app->stop();
        }
                                        
        // die Datenbank hat keinen Eintrag für diese Komponente geliefert oder ein sonstiger Fehler ist aufgetreten, daher
        // gilt die Installation als nicht vorhanden/korrekt
        $this->app->response->setStatus( 409 );
    }
   
    /**
     * Processes a process
     *
     * Called when this component receives an HTTP POST request to
     * /process(/).
     */
    public function postProcess()
    {
        // hier werden Einsendungen verarbeitet
        
        $this->app->response->setStatus( 201 );
           
        $body = $this->app->request->getBody();
        $process = Process::decodeProcess($body);
        
        // always been an array
        // es ist einfacher, wenn man sicherstellt, dass die Eingabedaten als Liste für foreach verarbeitet
        // werden können, um Abstürze bei übergebenen Einzelobjekten zu vermeiden
        $arr = true;
        if ( !is_array( $process ) ){
            $process = array( $process );
            $arr = false;
        }

        $res = array( );
        
        // behandelt jede eingehende Einsendung
        foreach ( $process as $pro ){
            $eid = $pro->getExercise()->getId();

            $file = $pro->getRawSubmission()->getFile(); // die unverarbeitete Einsendung (Dateiinhalt im body oder Adresse)
            $timestamp = $file->getTimeStamp();

            $showErrorsEnabled = Testcase::decodeTestcase($pro->getParameter())[0]->getErrorsEnabled();
            
            // der Eingangsstempel müsste natürlich schon existieren, ansonsten gilt dieser als Eingangszeitpunkt (wird nicht verwendet)
            if ($timestamp === null) 
                $timestamp = time();
            
            // es muss eine Einsendung vorhanden sein, welche bearbeitet werden kann
            if ($file !== null){
                $fileName = $file->getDisplayName();
                $file = $file->getBody( true ); // gibt den Inhalt der Einsendung an $file
              
                // es muss ein Einsendungsinhalt vorhanden sein
                if ($file !== null){
                    $fail = false;
                    
                    // eindeutigen temporären Ordner für diesen Vorgang erstellen und den Inhalt dort platzieren
                    $fileHash = sha1($file);
                    $filePath = $this->tempdir('/tmp/', $fileHash,0777);
                    file_put_contents($filePath . '/' . $fileName, $file);
                    chmod($filePath . '/' . $fileName, 0777);

                    // Arbeitsverzeichnis in parameter abspeichern
                    $configTestcases = Testcase::decodeTestcase($pro->getParameter());
                    $configTestcases[0]->setWorkDir($filePath);
                    $pro->setParameter(Testcase::encodeTestcase($configTestcases));

                    // der $pro->getParameter() wurden beim Erstellen der Verarbeitung festgelegt und enthält
                    // sowohl den aufzurufenden Compiler als auch weitere Aufrufparameter
                    $parameter = explode(' ',strtolower(Testcase::decodeTestcase($pro->getParameter())[0]->getInput()[0]));

                    if (count($parameter)>=2){
                        $type = array_shift($parameter);
                        
                        if ($type == 'cx'){
                            $this->xcopy(dirname(__FILE__) . '/start_cx', $filePath . '/start_cx',0777);
                            $this->xcopy(dirname(__FILE__) . '/compiler', $filePath . '/compiler',0777);

                            // behandelt Einsendungen für den cx Compiler
                            $output = "";
                            $return = -1;
                            
                            // ersetzt $file durch den Dateinamen der Einsendung und generiert
                            // die zusätzlichen Aufrufparameter für den Compiler
                            $param = implode(' ',$parameter);
                            if ($param!=''){
                                $param=str_replace('$file',$fileName,$param);
                            } else
                                $param = $fileName;
                             
                            // passt das Arbeitsverzeichnis an und führt das Skript für den
                            // cx Compiler aus
                            /*$pathOld = getcwd();
                            chdir($filePath);                             
                            exec('(./start_cx '.$param.') 2>&1', $output, $return);
                            chdir($pathOld);*/

                            $compileSandbox = new Sandbox();
                            $compileSandbox->setWorkingDir($filePath);
                            $compileSandbox->loadProfileFromFile(dirname(__FILE__) . '/../../Assistants/mysandbox.profile');

                            $return = $compileSandbox->sandbox_exec('./start_cx',$param,$output);

                           
                            if ($return == 0){
                                // wenn wir als Antwort eine 201 vom Skript erhalten, konnte alles problemlos 
                                // kompiliert werden
                                $pro->setStatus(201);
                            }
                            else{
                                // ansonsten gab es ein Problem, also einen Fehlerstatus zurückgeben
                                $pro->setStatus(409);
                                
                                // die Antwort des Compilers wird nun noch für die Ausgabe der Fehlermeldung angepasst
                                if (count($output)>0){
                                    $text = '';
                                    $outputList = array();
                                    $output = explode(PHP_EOL, $output);

                                    // entfernt störende Zeichen
                                    foreach($output as $out){
                                        $out = trim(trim($out),'^');
                                        if ($out=='') continue;
                                        $outputList[] = $out;
                                    }

                                    // nur die ersten 7 Fehler und die Zusammenfassung am Ende (2 Zeilen) behalten
                                    if (count($outputList)>10){
                                        $outputList[7] = '...';
                                        for ($i=8;$i<count($outputList)-2;$i++)
                                            $outputList[$i]='';
                                    }

                                    // nur nichtleere Ausgabezeilen werden zur Aufgabe zusammengefasst
                                    foreach($outputList as $out){
                                        if ($out=='') continue;
                                        $text.=$out."\n";
                                    }

                                    $text = trim($text);

                                    if (!is_null($showErrorsEnabled) && $showErrorsEnabled == "1")
                                    {
                                        $pro->addMessage($text);
                                    }
                                    
                                    $this->createMarking($pro, $text, null, 4);
                                }
                                //$this->app->response->setStatus( 409 );
                            }
                        } elseif ($type == 'java'){
                            // behandelt Einsendungen für den Java Compiler
                            
                            $output = "";
                            $return = -1;
                            
                            // ersetzt $file durch den Dateinamen der Einsendung und generiert
                            // die zusätzlichen Aufrufparameter für den Compiler
                            $param = implode(' ',$parameter);
                            if ($param!=''){
                                $param=str_replace('$file',$fileName,$param);
                            } else
                                $param = $fileName;
                               
                            // passt das Arbeitsverzeichnis an und führt das Skript für den
                            // java Compiler (javac) aus. Die Ausgabe erfolgt dabei in $return
                            $compileSandbox = new Sandbox();
                            $compileSandbox->setWorkingDir($filePath);
                            $compileSandbox->loadProfileFromFile(dirname(__FILE__) . '/../../Assistants/mysandbox.profile');


                            //$pathOld = getcwd();
                            //chdir($filePath);
                            $return = $compileSandbox->sandbox_exec('javac',$param,$output);
                            //exec('(javac '.$param.') 2>&1', $output, $return);
                            //chdir($pathOld);
                            
                            if ($return == 0){
                                // wenn wir als Antwort eine 0 erhalten, konnte alles problemlos 
                                // kompiliert werden
                                $pro->setStatus(201);
                            }
                            else{
                                // ansonsten gab es ein Problem, also einen Fehlerstatus zurückgeben
                                $pro->setStatus(409);
                                
                                // die Antwort des Compilers muss nun noch Studentengerecht zusammengebaut werden
                                // für die Fehlermeldung
                                if (count($output)>0){
                                    $text = '';
                                    $outputList = array();
                                    $output = explode(PHP_EOL, $output);

                                    // entfernt störende Zeichen
                                    foreach($output as $out){
                                        $out = trim(trim($out),'^');
                                        if ($out=='') continue;
                                        $outputList[] = $out;
                                    }
                                    
                                    // nur die ersten 7 Fehler und die Zusammenfassung am Ende (2 Zeilen) behalten
                                    if (count($outputList)>10){
                                        $outputList[7] = '...';
                                        for ($i=8;$i<count($outputList)-2;$i++)
                                            $outputList[$i]='';
                                    }
                                    
                                    // nur nichtleere Ausgabezeilen werden zur Aufgabe zusammengefasst
                                    foreach($outputList as $out){
                                        if ($out=='') continue;
                                        $text.=$out."\n";
                                    }
                                    
                                    $text = trim($text);

                                    // die fertige Fehlermeldung dem Prozessobjekt übergeben    
                                    if (!is_null($showErrorsEnabled) && $showErrorsEnabled == "1")
                                    {
                                        $pro->addMessage($text);
                                    }
                                    $this->createMarking($pro, $text, null, 4);
                                }
                                //$this->app->response->setStatus( 409 );
                            }
                            
                        } elseif ($type == 'custom'){
                            // es können natürlich auch individuelle Programmaufrufe damit konfiguriert werden
                            // dabei liefern die Parameter auch das aufzurufende Programm mit (Gefahr???)
                            $output = array();
                            $return = '';
                            $param = implode(' ',$parameter);
                            if ($param!=''){
                                $param=str_replace('$file',$filePath . '/' . $fileName,$param);
                            } else
                                $param = $filePath . '/' . $fileName;
                                
                            exec('('.$param.') 2>&1', $output, $return);
                            
                            if ($return == 0){
                                // nothing
                                $pro->setStatus(201);
                            }
                            else{
                                $pro->setStatus(409);
                                if (count($output)>0){
                                    $text = '';
                                    $outputList = $output;
                                    
                                    if (count($outputList)>10){
                                        $outputList[4] = '...';
                                        for ($i=5;$i<count($outputList)-5;$i++)
                                            $outputList[$i]='';
                                    }
                                    
                                    foreach($outputList as $out){
                                        if ($out=='') continue;
                                        $text.=$out."\n";
                                    }
                                        
                                    if (!is_null($showErrorsEnabled) && $showErrorsEnabled == "1")
                                    {
                                        $pro->addMessage($text);
                                    }
                                    $this->createMarking($pro, $text, null, 4);
                                }
                                //$this->app->response->setStatus( 409 );
                            }
                        }
                        
                    } else {
                        // es wurden womöglich nicht genug Parameter übergeben
                        // er verlangt mindestens den Compiler und einen Parameter
                        // Bsp.: java $file
                    }
                    
                    // nachdem die Einsendung bearbeitet wurde, kann das temporäre
                    // Verzeichnis mit Inhalt entfernt werden
                    //
                    $newfolder = basename($filePath);
                    $this->xcopy($filePath, $this->iniconfig['DIR']['temp'].'/'.$newfolder,0777);
                    $this->deleteDir($filePath);
                    // das Prozessobjekt kann nun zur Ausgabe hinzugefügt werden
                    $res[] = $pro;          
                    continue;
                }
            }                             
        }



        // wenn nur ein einzelnes Objekt als Eingabe kam, wir auch nur ein Einzelobjekt
        // zurückgegeben, ansonsten eine Liste
        if ( !$arr && 
             count( $res ) == 1 ){
            $this->app->response->setBody( Process::encodeProcess( $res[0] ) );
            
        } else 
            $this->app->response->setBody( Process::encodeProcess( $res ) );
    }

    /**
     * PostProcesses a process
     *
     * Called when this component receives an HTTP POST request to
     * /postprocess(/).
     */
    public function saveTestcases()
    {
        // hier werden Einsendungen verarbeitet
        
        $this->app->response->setStatus( 201 );
           
        $body = $this->app->request->getBody();
        $process = Process::decodeProcess($body);
        
        // always been an array
        // es ist einfacher, wenn man sicherstellt, dass die Eingabedaten als Liste für foreach verarbeitet
        // werden können, um Abstürze bei übergebenen Einzelobjekten zu vermeiden
        $arr = true;
        if ( !is_array( $process ) ){
            $process = array( $process );
            $arr = false;
        }

        $res = array( );

        // behandelt jede eingehende Einsendung
        foreach ( $process as $pro ){
            if ($pro->getStatus() != 409){

                $configTestcases = Testcase::decodeTestcase($pro->getParameter());
                $workingDir = $configTestcases[0]->getWorkDir();
                $testcases  = array_slice($configTestcases, 1);

                if (!empty($testcases)) {
                    foreach ($testcases as $test) {
                        $test->setWorkDir($workingDir);
                        $test->setStatus(0);
                        $test->setProcess($pro);

                        //file_put_contents('php://stderr', print_r(Testcase::encodeTestcase($test), TRUE));
                    }

                    $result = Request::routeRequest( 
                                                    'POST',
                                                    '/insert',
                                                    array(),
                                                    Testcase::encodeTestcase($testcases),
                                                    $this->_postTestcase
                                                   );

                    // checks the correctness of the query
                    if ( $result['status'] >= 200 && $result['status'] <= 299 ){
                            
                    }
                }
            }
        }
    }
    
    /**
     * Creates the path in the filesystem, if necessary.
     *
     * @param string $path The path which should be created.
     */
    public static function generatepath( $path )
    {
        // erzeugt einen Ordner
        if (!is_dir($path))          
            mkdir( $path , 0775, true);
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @param       string   $permissions New folder creation permissions
     * @return      bool     Returns true on success, false on failure
     */
    public function xcopy($source, $dest, $permissions = 0755)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            $copyfile = copy($source, $dest);
            chmod($dest, $permissions);
            return $copyfile;
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
            chmod($dest, $permissions);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            $this->xcopy("$source/$entry", "$dest/$entry", $permissions);
        }

        // Clean up
        $dir->close();
        return true;
    }
    
    public function deleteDir($path)
    {
        // entfernt einen Ordner und zuvor alle enthaltenen Dateien
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
    
    public function tempdir($dir, $prefix='', $mode=0775)
    {
        // erzeugt ein eindeutiges Verzeichnis
        if (substr($dir, -1) != '/') $dir .= '/';

        do
        {
            $path = $dir.$prefix.mt_rand(0, 9999999);
        } while (!mkdir($path, $mode));

        if($mode == 0777)
        {
            chmod($path, 0777);
        }

        return $path;
    }
}