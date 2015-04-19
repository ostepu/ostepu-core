<?php
/**
 * @file LExercise.php Contains the LExercise class
 *
 * @author Martin Daute
 * @author Christian Elze
 * @author Peter Koenig
 * @author Ralf Busch
 * @date 2013-2014
 */

require_once dirname(__FILE__) . '/../../Assistants/Slim/Slim.php';
include_once dirname(__FILE__) . '/../../Assistants/Request.php';
include_once dirname(__FILE__) . '/../../Assistants/CConfig.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LExercise-Component
 */
class LExercise
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "exercise";

     /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LExercise::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LExercise::$_prefix = $value;
    }

    /**
     * @var string $lURL the URL of the logic-controller
     */
    private $lURL = ""; // readed out from config below
    private $_postAttachment = array();

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
        $com = new CConfig( LExercise::getPrefix( ), dirname(__FILE__) );

        // runs the LExercise
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
            
        // initialize slim
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        $this->_postAttachment = CConfig::getLinks($conf->getLinks(),"postAttachment");

        // initialize lURL
        $this->lURL = $this->query->getAddress();

        // POST AddExercise
        $this->app->post('/'.$this->getPrefix().'(/)', array($this, 'addExercise'));

        // run Slim
        $this->app->run();
    }

    /**
     * Adds an exercise.
     *
     * Called when this component receives an HTTP POST request to
     * /exercise(/).
     * The request body should contain a JSON object representing an array of exercises
     */
    public function addExercise(){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody(), true);

        $allright = true;
        $result = array();

        if (isset($body) == true && empty($body) == false) {
            foreach ($body as $subexercise) {
                // create exercise in DB
                if (isset($subexercise['fileTypes'])){
                    $FileTypesArrayTemp = $subexercise['fileTypes'];
                    unset($subexercise['fileTypes']);
                }
                
                $subexerciseJSON = json_encode($subexercise);
                $URL = $this->lURL.'/DB/exercise';
                $method='POST';
                if (isset($subexercise['id']) && $subexercise['id'] !== null){
                   $method='PUT'; 
                   $URL = $this->lURL.'/DB/exercise/'.$subexercise['id'];
                }
                $subexerciseAnswer = Request::custom($method, $URL, $header, $subexerciseJSON);

                if ($subexerciseAnswer['status'] == 201) {
                    $subexerciseOutput = json_decode($subexerciseAnswer['content'], true);
                    
                    if (isset($subexercise['id'])){
                        $result[] = $subexercise;
                        $subexerciseOutput = $subexercise;
                    } else {
                        $result[] = Exercise::decodeExercise($subexerciseAnswer['content']);
                    }
                    
                    if (isset($subexerciseOutput['id'])) {
                        $linkid = $subexerciseOutput['id'];
                    }
                    
                    // create attachement in DB and FS
                    if (isset($subexercise['attachments']) && !empty($subexercise['attachments'])) { 
                        foreach($subexercise['attachments'] as &$attachment)
                            $attachment['exerciseId'] = $linkid;
                            
                        $attachments = $subexercise['attachments'];
                        $tempAttachments = array();
                        foreach ($attachments as $attachment){
                            $temp = Attachment::createAttachment(null,$attachment['exerciseId'],null,null);
                            $temp->setFile($attachment);
                            $tempAttachments[] = $temp;
                        }
                        
                        $res = Request::routeRequest( 
                                                        'POST',
                                                        '/attachment',
                                                        $header,
                                                        Attachment::encodeAttachment($tempAttachments),
                                                        $this->_postAttachment,
                                                        'attachment'
                                                        );

                        // checks the correctness of the query
                        if ( $res['status'] >= 200 && 
                             $res['status'] <= 299 ){                          
                            // ...
                        } else {
                            $allright = false;
                            break;
                        }
                    }

                    // create ExerciseFileTypes
                    if (isset($FileTypesArrayTemp) && !empty($FileTypesArrayTemp)){
                        foreach ($FileTypesArrayTemp as $fileType) {
                            $myExerciseFileType = ExerciseFileType::createExerciseFileType(NULL,$fileType['text'],$linkid);
                            $myExerciseFileTypeJSON = ExerciseFileType::encodeExerciseFileType($myExerciseFileType);
                            $URL = $this->lURL."/DB/exercisefiletype";
                            $FileTypesAnswer = Request::custom('POST', $URL, $header, $myExerciseFileTypeJSON);

                            if ($FileTypesAnswer['status'] != 201) {
                                $allright = false;
                                break;
                            }
                        }
                    }
                    
                    if ($allright == false) {
                        break;
                    }
                    
                } else {
                    $allright = false;
                    break;
                }
            }
        }
        if ($allright == true) {
             $this->app->response->setBody(Exercise::encodeExercise($result));
             $this->app->response->setStatus(201);
        } else {
            $this->app->response->setStatus(409);
        }
    }
}