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
     * @var Link[] $_process a list of links
     */
    private $_process = array( );
    
    /**
     * @var Link[] $_processorDb a list of links
     */
    private $_processorDb = array( );
    
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
        // initialize slim    
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->_submission = CConfig::getLinks($conf->getLinks(),"submission");
        $this->_marking = CConfig::getLinks($conf->getLinks(),"marking");
        $this->_process = CConfig::getLinks($conf->getLinks(),"process");
        $this->_processorDb = CConfig::getLinks($conf->getLinks(),"processorDb");

        // POST PostSubmission
        $this->app->map('/submission(/)',
                        array($this, 'postSubmission'))->via('POST');
                        
        // POST AddProcess
        $this->app->map('/'.$this->getPrefix().'(/)',
                        array($this, 'addProcess'))->via('POST');
                        
        // PUT EditProcess
        $this->app->map('/'.$this->getPrefix().'(/)',
                        array($this, 'editProcess'))->via('PUT');

        // run Slim
        $this->app->run();
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
 
        if ( !$arr && 
             count( $res ) == 1 ){
            $this->app->response->setBody( Process::encodeProcess( $res[0] ) );
            
        } else 
            $this->app->response->setBody( Process::encodeProcess( $res ) );
    }
    
    public function EditProcess()
    {
    
    
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
               $res[] = null;
               continue;
            }
            
            // process submission
            if ($processors !== null){
                foreach($processors as $pro){
                    $component = $pro->getTarget();
                    
                    if ($process->getExercise()===null)
                        $process->setExercise($pro->getExercise());
                                
//echo Process::encodeProcess($process);
//break;
                    $result = Request::post($component->getAddress().'/process', array(),  Process::encodeProcess($process));
                    
                    if ( $result['status'] >= 200 && 
                         $result['status'] <= 299 ){
                        $process = Process::decodeProcess( $result['content'] );
                       // var_dump($result);
                    } else {
                       $fail = true;
                       break;
                    }
                }
            }
            
            if ($fail){
                $res[] = null;
                continue;
            }

            // upload submission
            $uploadSubmission = $process->getSubmission();
            if ($uploadSubmission===null)$uploadSubmission = $process->getRawSubmission();
            
            if ($uploadSubmission===null){
                // create empty submission? failure?
            }

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
          // var_dump($queryResult);
            } else {
               $res[] = null;
               continue;
            }
            
            // upload marking
            
            
        }
        
      /*  if ( !$arr && count( $res ) == 1 ){
            $this->app->response->setBody( Process::encodeProcess( $res[0] ) );
        } else 
            $this->app->response->setBody( Process::encodeProcess( $res ) );*/
    }
}

// get new config data from DB
$com = new CConfig(LProcessor::getPrefix() . ',submission');

// create a new instance of LProcessor class with the config data
if (!$com->used())
    new LProcessor($com->loadConfig());
?>