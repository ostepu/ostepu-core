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
    private $_deleteProcess = array( );
    private $_getProcess = array( );
    
    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct()
    {
        // runs the CConfig
        $com = new CConfig( LOOP::getPrefix( ) . ',course,link', dirname(__FILE__) );

        // runs the LOOP
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
            
        // initialize slim    
        $this->app = new \Slim\Slim(array('debug' => true));
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->_pdf = CConfig::getLinks($conf->getLinks(),"pdf");
        $this->_postProcess = CConfig::getLinks($conf->getLinks(),"postProcess");
        $this->_deleteProcess = CConfig::getLinks($conf->getLinks(),"deleteProcess");
        $this->_getProcess = CConfig::getLinks($conf->getLinks(),"getProcess");

        // POST PostProcess
        $this->app->map('/'.$this->getPrefix().'(/)',
                        array($this, 'postProcess'))->via('POST');
                        
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
                                        
        if (isset($result['status']) && $result['status'] >= 200 && $result['status'] <= 299 && isset($result['content']) && $this->_conf !== null){
        
            $process = Process::decodeProcess($result['content']);
            if (is_array($process)) $process = $process[0];
            $deleteId = $process->getProcessId();
            
            $result = Request::routeRequest( 
                                            'DELETE',
                                            '/process/process/' . $deleteId,
                                            $this->app->request->headers->all( ),
                                            '',
                                            $this->_deleteProcess,
                                            'process'
                                            );
                                            
            if (isset($result['status']) && $result['status'] === 201 && isset($result['content']) && $this->_conf !== null){
                $this->app->response->setStatus( 201 );
                $this->app->stop();
            }
            
            $this->app->response->setStatus( 409 );
            $this->app->stop();
        }
                                        
        $this->app->response->setStatus( 404 );
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
        
        $courses = Course::decodeCourse($body);
        $processes = array();
        if (!is_array($courses)) $courses = array($courses);
        
        foreach ($courses as $course){
            $process = new Process();
            
            $exercise = new Exercise();
            $exercise->setCourseId($course->getId());
            
            $process->setExercise($exercise);
            
            $component = new Component();
            $component->setId($this->_conf->getId());
            
            $process->setTarget($component);
            
            $processes[] = $process;
        }
    
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

                $this->app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
            
               /* if ($courses->getId()!==null){
                    $this->deleteCourse($courses->getId());
                }*/
            
                Logger::Log( 
                            'POST AddCourse failed',
                            LogLevel::ERROR
                            );
                $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->app->response->setBody( Course::encodeCourse( $courses ) );
                $this->app->stop( );
            }
        }
        
        $this->app->response->setBody( Course::encodeCourse( $courses ) );
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
        $result = Request::routeRequest( 
                                        'GET',
                                        '/process/course/'.$courseid.'/component/'.$this->_conf->getId(),
                                        array(),
                                        '',
                                        $this->_getProcess,
                                        'process'
                                        );
                       
        if (isset($result['status']) && $result['status'] >= 200 && $result['status'] <= 299 && isset($result['content']) && $this->_conf !== null && $this->_conf->getId() !== null){
            $this->app->response->setStatus( 200 );
            $this->app->stop();
        }
                                        
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
        $this->app->response->setStatus( 201 );
           
        $body = $this->app->request->getBody();
        $process = Process::decodeProcess($body);
        
        // always been an array
        $arr = true;
        if ( !is_array( $process ) ){
            $process = array( $process );
            $arr = false;
        }

        $res = array( );
        foreach ( $process as $pro ){
            $eid = $pro->getExercise()->getId();

            $file = $pro->getRawSubmission()->getFile();
            $timestamp = $file->getTimeStamp();
            if ($timestamp === null) 
                $timestamp = time();
            
            if ($file !== null){
                $fileName = $file->getDisplayName();
                $file = base64_decode($file->getBody());
              
                if ($file !== null){
                    $fail = false;
                    
                    $fileHash = sha1($file);
                    $filePath = $this->tempdir('/tmp/', $fileHash);
                    file_put_contents($filePath . '/' . $fileName, $file);  

                    $parameter = explode(' ',strtolower($pro->getParameter()));
                    if (count($parameter)>=2){
                        $type = array_shift($parameter);
                        if ($type == 'cx'){
                            $output = array();
                            $return = '';
                            $param = implode(' ',$parameter);
                            if ($param!=''){
                                $param=str_replace('$file',$fileName,$param);
                            } else
                                $param = $fileName;
                             
                            $pathOld = getcwd();
                            chdir($filePath);                             
                            exec('(./start_cx '.$param.') 2>&1', $output, $return);
                            chdir($pathOld);
                           
                            if (count($output)>0 && $output[count($output)-1] === '201'){
                                // nothing
                                $pro->setStatus(201);
                            }
                            else{
                                $pro->setStatus(409);
                                if (count($output)>0){
                                $text = '';
                                    unset($output[count($output)-1]);
                                    foreach($output as $out){
                                        $pos = strpos($out, ',');
                                        $text.=$out."\n";
                                    }
                                    $pro->addMessage($text);
                                }
                                $this->app->response->setStatus( 409 );
                            }
                        } elseif ($type == 'java'){
                            $output = array();
                            $return = '';
                            $param = implode(' ',$parameter);
                            if ($param!=''){
                                $param=str_replace('$file',$fileName,$param);
                            } else
                                $param = $fileName;
                                
                            $pathOld = getcwd();
                            chdir($filePath);
                            exec('(javac '.$param.') 2>&1', $output, $return);
                            chdir($pathOld);
                            
                            /*ob_start();
                            system('("C:/Program Files/Java/jdk1.8.0_05/bin/javac" '.$param.') 2>&1',$return);
                            $output=explode("\n",ob_get_contents());
                            ob_end_clean();*/
                            
                            if ($return == 0){
                                // nothing
                                $pro->setStatus(201);
                            }
                            else{
                                $pro->setStatus(409);
                                if (count($output)>0){
                                    $text = '';
                                    $outputList = array();
                                    foreach($output as $out){
                                        $out = trim(trim($out),'^');
                                        if ($out=='') continue;
                                        $outputList[] = $out;
                                    }
                                    
                                    if (count($outputList)>10){
                                        $outputList[7] = '...';
                                        for ($i=8;$i<count($outputList)-2;$i++)
                                            $outputList[$i]='';
                                    }
                                    
                                    foreach($outputList as $out){
                                        if ($out=='') continue;
                                        $text.=$out."\n";
                                    }
                                    
                                    
                                        
                                    $pro->addMessage($text);
                                }
                                $this->app->response->setStatus( 409 );
                            }
                            
                        } elseif ($type == 'custom'){
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
                                    
                                    
                                        
                                    $pro->addMessage($text);
                                }
                                $this->app->response->setStatus( 409 );
                            }
                        }
                        
                    } else {
                        // no parameter
                    }
                    
                    $this->deleteDir($filePath);
                    
                    $res[] = $pro;          
                    continue;
                }
            }                             
        }

 
        if ( !$arr && 
             count( $res ) == 1 ){
            $this->app->response->setBody( Process::encodeProcess( $res[0] ) );
            
        } else 
            $this->app->response->setBody( Process::encodeProcess( $res ) );
    }
    
    /**
     * Creates the path in the filesystem, if necessary.
     *
     * @param string $path The path which should be created.
     */
    public static function generatepath( $path )
    {
        if (!is_dir($path))          
            mkdir( $path , 0775, true);
    }
    
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