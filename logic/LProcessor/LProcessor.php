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
                        
        // POST AddProcessor
        $this->app->map('/'.$this->getPrefix().'(/)',
                        array($this, 'addProcess'))->via('POST');
                        
        // PUT EditProcessor
        $this->app->map('/'.$this->getPrefix().'(/)',
                        array($this, 'editProcess'))->via('PUT');

        // run Slim
        $this->app->run();
    }

    public function AddProcess(){
    
    
    }
    
    public function EditProcess(){
    
    
    }
    
    public function postSubmission(){
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
            $process = new Process();
            $process->setExerciseId($submission->getExerciseId());
            $process->setRawSubmission($submission);
            
            // process submission
            

            // upload submission
            $uploadSubmission = $process->getSubmission();
            if ($uploadSubmission===null)$uploadSubmission = $process->getRawSubmission();
            
            $result = Request::routeRequest( 
                                'POST',
                                '/submission',
                                $this->app->request->headers->all( ),
                                Submission::encodeSubmission($uploadSubmission),
                                $this->_submission,
                                'submission'
                                );
            
            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Submission::decodeSubmission( $result['content'] );

                $res[] = $queryResult;
                $this->app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
               $res[] = null;
            }
            
            // upload marking
            
            
        }
    }
}

// get new config data from DB
$com = new CConfig(LProcessor::getPrefix() . ',submission');

// create a new instance of LProcessor class with the config data
if (!$com->used())
    new LProcessor($com->loadConfig());
?>