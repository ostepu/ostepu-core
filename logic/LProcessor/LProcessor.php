<?php
/**
 * @file LProcessor.php Contains the LProcessor class
 * 
 * @author Till Uhlig
 */

require_once '../../Assistants/Slim/Slim.php';
include_once '../../Assistants/Request.php';
include_once '../../Assistants/CConfig.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LProcessor-Component
 */
class LProcessor
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
        return LProcessor::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LProcessor::$_prefix = $value;
    }

    /**
     * @var Link[] $_submission a list of links
     */
    private $_submission = array( );
    
    /**
     * @var Link[] $_marking a list of links
     */
    private $_marking = array( );
    
    /**
     * @var Link[] $_processorDb a list of links
     */
    private $_processorDb = array( );
    
    /**
     * @var Link[] $_attachment a list of links
     */
    private $_attachment = array( );
    
    /**
     * @var Link[] $_workFiles a list of links
     */
    private $_workFiles = array( );
    private $_createCourse = array( );
    private $_file = array( );
    
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
        $com = new CConfig( LProcessor::getPrefix( ) . ',submission,course,link' );

        // runs the LProcessor
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
            
        // initialize slim    
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->_submission = CConfig::getLinks($conf->getLinks(),"submission");
        $this->_marking = CConfig::getLinks($conf->getLinks(),"marking");
        $this->_processorDb = CConfig::getLinks($conf->getLinks(),"processorDb");
        $this->_attachment = CConfig::getLinks($conf->getLinks(),"attachment");
        $this->_workFiles = CConfig::getLinks($conf->getLinks(),"workFiles");
        $this->_file = CConfig::getLinks($conf->getLinks(),"file");
        $this->_createCourse = CConfig::getLinks($conf->getLinks(),"postCourse");
        
        // POST PostSubmission
        $this->app->map('/submission(/)',
                        array($this, 'postSubmission'))->via('POST');
                        
        // POST AddProcess
        $this->app->map('/'.$this->getPrefix().'(/)',
                        array($this, 'addProcess'))->via('POST');
                        
        // POST AddCourse
        $this->app->post( 
                         '/course(/)',
                         array( 
                               $this,
                               'addCourse'
                               )
                         );
                         
        // POST DeleteCourse
        $this->app->delete( 
                         '/course/:courseid(/)',
                         array( 
                               $this,
                               'deleteCourse'
                               )
                         );
                         
        // GET GetExistsCourse
        $this->app->get( 
                         '/link/exists/course/:courseid(/)',
                         array( 
                               $this,
                               'getExistsCourse'
                               )
                        );

        // run Slim
        $this->app->run();
    }
    
    public function getExistsCourse($courseid)
    {
         Logger::Log( 
                    'starts GET GetExistsCourse',
                    LogLevel::DEBUG
                    );

        foreach ( $this->_createCourse as $_link ){
            $result = Request::routeRequest( 
                                            'GET',
                                            '/link/exists/course/'.$courseid,
                                            $this->app->request->headers->all(),
                                            '',
                                            $_link,
                                            'link'
                                            );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                // nothing
            } else {
                $this->app->response->setStatus( 409 );
                $this->app->response->setBody( null );
                $this->app->stop( );
            }
        }
        
        $this->app->response->setStatus( 200 );
        $this->app->response->setBody( null );
    }

    public function addCourse()
    {
         Logger::Log( 
                    'starts POST AddCourse',
                    LogLevel::DEBUG
                    );
                    
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        
        $course = Course::decodeCourse($body);
    
        foreach ( $this->_createCourse as $_link ){
            $result = Request::routeRequest( 
                                            'POST',
                                            '/course',
                                            $header,
                                            Course::encodeCourse($course),
                                            $_link,
                                            'course'
                                            );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){

                $this->app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
            
               /* if ($course->getId()!==null){
                    $this->deleteCourse($course->getId());
                }*/
            
                Logger::Log( 
                            'POST AddCourse failed',
                            LogLevel::ERROR
                            );
                $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->app->response->setBody( Course::encodeCourse( $course ) );
                $this->app->stop( );
            }
        }
        
        $this->app->response->setBody( Course::encodeCourse( $course ) );
    }

    public function deleteCourse($courseid){
        Logger::Log( 
                    'starts DELETE DeleteCourse',
                    LogLevel::DEBUG
                    );
                    
        $header = $this->app->request->headers->all();
        $courseid = DBJson::mysql_real_escape_string( $courseid ); 
        
        foreach ( $this->_createCourse as $_link ){
            $result = Request::routeRequest( 
                                            'DELETE',
                                            '/course/'.$courseid,
                                            $header,
                                            '',
                                            $_link,
                                            'course'
                                            );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){

                $this->app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'POST DeleteCourse failed',
                            LogLevel::ERROR
                            );
                $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->app->stop( );
            }
        }
    }
    
    public function AddProcess()
    {
        $this->app->response->setStatus( 201 );
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $processes = Process::decodeProcess($body);
        
        // always been an array
        $arr = true;
        if ( !is_array( $processes ) ){
            $processes = array( $processes );
            $arr = false;
        }
        
        $res = array( );
        foreach ( $processes as $process ){
            // create process
            if ($process->getProcessId() === null){
                $result = Request::routeRequest( 
                                                'POST',
                                                '/process',
                                                array(),
                                                Process::encodeProcess($process),
                                                $this->_processorDb,
                                                'process'
                                                );
                                                
                if ( $result['status'] >= 200 && 
                     $result['status'] <= 299 ){
                     
                    $queryResult = Process::decodeProcess($result['content']);
                    $process->setProcessId($queryResult->getProcessId());
                    $res[] = $process;
                }
                else{
                    $res[] = null;
                    $this->app->response->setStatus( 409 );
                    continue;
                }
            }
            
            // create attachment
            $attachments = $process->getAttachment();
            $process->setAttachment(array());
            foreach ( $attachments as $attachment ){
                if ($attachment->getId() === null){
                    $attachment->setExerciseId($process->getExercise()->getId());
                    $attachment->setProcessId($process->getProcessId());
                    
                    // upload file
                    $result = Request::routeRequest( 
                                                'POST',
                                                '/file',
                                                array(),
                                                File::encodeFile($attachment->getFile()),
                                                $this->_file,
                                                'file'
                                                );
                                                
                    if ( $result['status'] >= 200 && 
                         $result['status'] <= 299 ){
                        $queryResult = File::decodeFile($result['content']);
                        $attachment->setFile($queryResult);
                    }
                    else{
                        $attachment->setFile(null);
                        $this->app->response->setStatus( 409 );
                        continue;
                    }

                    // upload attachment   
                    $attachment->setProcessId($process->getProcessId());   
                    $result = Request::routeRequest( 
                                                'POST',
                                                '/attachment',
                                                array(),
                                                Attachment::encodeAttachment($attachment),
                                                $this->_attachment,
                                                'attachment'
                                                );
                                                
                    if ( $result['status'] >= 200 && 
                         $result['status'] <= 299 ){
                         
                        $queryResult = Attachment::decodeAttachment($result['content']);
                        $attachment->setId($queryResult->getId());
                        $process->getAttachment()[] = $attachment;
                    }
                    else{
                        $process->getAttachment()[] = null;
                        $this->app->response->setStatus( 409 );
                        continue;
                    }
                }
            }
            
            // create workFiles
            $workFiles = $process->getWorkFiles();
            $process->setWorkFiles(array());
            foreach ( $workFiles as $workFile ){
                if ($workFile->getId() === null){
                $workFile->setExerciseId($process->getExercise()->getId());
                $workFile->setProcessId($process->getProcessId());
                
                    // upload file
                    $result = Request::routeRequest( 
                                                'POST',
                                                '/file',
                                                array(),
                                                File::encodeFile($workFile->getFile()),
                                                $this->_file,
                                                'file'
                                                );
                                                
                    if ( $result['status'] >= 200 && 
                         $result['status'] <= 299 ){
                         
                        $queryResult = File::decodeFile($result['content']);
                        $workFile->setFile($queryResult);
                    }
                    else{
                        $workFile->setFile(null);
                        $this->app->response->setStatus( 409 );
                        continue;
                    }
                    
                    // upload attachment
                    $workFile->setProcessId($process->getProcessId()); 
                    $result = Request::routeRequest( 
                                                'POST',
                                                '/attachment',
                                                array(),
                                                Attachment::encodeAttachment($workFile),
                                                $this->_workFiles,
                                                'attachment'
                                                );
                                                
                    if ( $result['status'] >= 200 && 
                         $result['status'] <= 299 ){
                         
                        $queryResult = Attachment::decodeAttachment($result['content']);
                        $workFile->setId($queryResult->getId());
                        $process->getWorkFiles()[] = $workFile;
                    }
                    else{
                        $process->getWorkFiles()[] = null;
                        $this->app->response->setStatus( 409 );
                        continue;
                    }
                }
            }
            
        }
 
        if ( !$arr && 
             count( $res ) == 1 ){
            $this->app->response->setBody( Process::encodeProcess( $res[0] ) );
            
        } else 
            $this->app->response->setBody( Process::encodeProcess( $res ) );
    }
    
    public function postSubmission()
    {
        $this->app->response->setStatus( 201 );
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        
        $submissions = Submission::decodeSubmission($body);
        
        // always been an array
        $arr = true;
        if ( !is_array( $submissions ) ){
            $submissions = array( $submissions );
            $arr = false;
        }


        $res = array( );
        foreach ( $submissions as $submission ){
            $fail = false;
        
            $process = new Process();
            $process->setRawSubmission($submission);

            $eid = $submission->getExerciseId();
            // load processor data from database
            $result = Request::routeRequest( 
                                'GET',
                                '/process/exercise/'.$eid,
                                $this->app->request->headers->all( ),
                                '',
                                $this->_processorDb,
                                'process'
                                );
            $processors = null;
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $processors = Process::decodeProcess( $result['content'] );
            } else {
               if ($result['status'] != 404){
                    $submission->addMessage("Interner Fehler");
                    $res[] = $submission;
                    $this->app->response->setStatus( 409 );
                    continue;
               }
            }
            
            // process submission
            if ($processors !== null){
                if (!is_array($processors)) $processors = array($processors);
                
                foreach($processors as $pro){
                    $component = $pro->getTarget();
                    
                    if ($process->getExercise()===null)
                        $process->setExercise($pro->getExercise());
                     
                    $process->setParameter($pro->getParameter());
                    $process->setAttachment($pro->getAttachment());
                    $process->setTarget($pro->getTarget());
                    $process->setWorkFiles($pro->getWorkFiles());
                        
//echo Process::encodeProcess($process); return;

                    $result = Request::post($component->getAddress().'/process', array(),  Process::encodeProcess($process));
                    
                    if ( $result['status'] >= 200 && 
                         $result['status'] <= 299 ){
                         $process = Process::decodeProcess( $result['content'] ); 
                    } else {
                        $fail = true;
                        $submission->addMessage("Beim Verarbeiten der Einsendung ist ein Fehler aufgetreten");

                        if (isset($result['content'])){
                            $content = Process::decodeProcess($result['content']); 
                            $submission->setStatus($content->getStatus());  
                            $submission->addMessages($content->getMessages());
                        }
                       break;
                    }
                }
            }
            
            if ($fail){
                if (isset($submission))
                $submission->setFile(null);

                $res[] = $submission;
                $this->app->response->setStatus( 409 );
                continue;
            }

            // upload submission
            $uploadSubmission = $process->getSubmission();
            if ($uploadSubmission===null)$uploadSubmission = $process->getRawSubmission();
            
            if ($uploadSubmission!==null){
//echo Submission::encodeSubmission($uploadSubmission);return;
                $result = Request::routeRequest( 
                                                'POST',
                                                '/submission',
                                                array(),
                                                Submission::encodeSubmission($uploadSubmission),
                                                $this->_submission,
                                                'submission'
                                               );

                // checks the correctness of the query
                if ( $result['status'] >= 200 && 
                     $result['status'] <= 299 ){
                    $queryResult = Submission::decodeSubmission( $result['content'] );
                    $uploadSubmission->setId($queryResult->getId());
                    if ($process->getMarking()!==null){
                        $process->getMarking()->setSubmission($queryResult);
                    }
                   
                } else {
                    $uploadSubmission->addMessage("Beim Speichern der Einsendung ist ein Fehler aufgetreten.");
                
                    if (isset($result['content'])){
                        $content = Submission::decodeSubmission($result['content']);
                        $uploadSubmission->setStatus($content->getStatus());  
                        $uploadSubmission->addMessages($content->getMessages()); 
                   }
            
                   //$res[] = $uploadSubmission;
                   $this->app->response->setStatus( 409 );
                   continue;
                }
            }
            
            // upload marking
            if ($process->getMarking()!==null){
                $result = Request::routeRequest( 
                                                'POST',
                                                '/marking',
                                                array(),
                                                Marking::encodeMarking($process->getMarking()),
                                                $this->_marking,
                                                'marking'
                                               );

                // checks the correctness of the query
                if ( $result['status'] >= 200 && 
                     $result['status'] <= 299 ){
                    $queryResult = Marking::decodeMarking( $result['content'] );
                } else {
                    $uploadSubmission->addMessage("Beim Speichern der Korrektur ist ein Fehler aufgetreten");
                    if (isset($result['content'])){
                        $content = Marking::decodeMarking($result['content']); 
                        $uploadSubmission->addMessages($content->getMessages()); 
                   }
                    
                   $this->app->response->setStatus( 409 );
                   continue;
                }
            }

            $rr = $process->getSubmission();
            if ($rr===null)$rr = $process->getRawSubmission();
            $res[] = $rr;
            
        }
        
        if ( !$arr && count( $res ) == 1 ){
            $this->app->response->setBody( Submission::encodeSubmission( $res[0] ) );
        } else 
            $this->app->response->setBody( Submission::encodeSubmission( $res ) );
    }
}
?>