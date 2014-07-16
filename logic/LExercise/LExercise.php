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

require '../../Assistants/Slim/Slim.php';
include '../../Assistants/Request.php';
include_once '../../Assistants/CConfig.php';
include_once '../../Assistants/Structures.php';

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
        $com = new CConfig( LExercise::getPrefix( ) );

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

        // GET GetExercise
        $this->app->get('/'.$this->getPrefix().'/exercise/:exerciseid(/)',
                        array ($this, 'getExercise'));

        // DELETE DeleteExercise
        $this->app->delete('/'.$this->getPrefix().'/exercise/:exerciseid(/)',
                        array($this, 'deleteExercise'));

        // PUT EditExercise
        $this->app->put('/'.$this->getPrefix().'/exercise/:exerciseid(/)',
                        array($this, 'editExercise'));

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
                //upload attachement if it exists
                /*if (isset($subexercise['attachments']) == true && empty($subexercise['attachments']) == false) {
                    // get attachments
                    $attachmentFiles = Attachment::encodeAttachment($subexercise['attachments']);
                    
                    $result = Request::routeRequest( 
                                                    'POST',
                                                    '/file',
                                                    $header,
                                                    $attachmentFiles,
                                                    $_postFile,
                                                    'file'
                                                    );

                    // checks the correctness of the query
                    if ( $result['status'] >= 200 && 
                         $result['status'] <= 299 ){
                        $queryResult = Course::decodeCourse( $result['content'] );
                        
                        
                    } else {
                        $allright = false;
                        break;
                    }
                }*/

                // create exercise in DB
                if (isset($subexercise['fileTypes'])){
                    $FileTypesArrayTemp = $subexercise['fileTypes'];
                    unset($subexercise['fileTypes']);
                }
                
                $subexerciseJSON = json_encode($subexercise);
                $URL = $this->lURL.'/DB/exercise';
                $subexerciseAnswer = Request::custom('POST', $URL, $header, $subexerciseJSON);

                if ($subexerciseAnswer['status'] == 201) {
                    $subexerciseOutput = json_decode($subexerciseAnswer['content'], true);
                    $result[] = Exercise::decodeExercise($subexerciseAnswer['content']);
                    
                    if (isset($subexerciseOutput['id'])) {
                        $linkid = $subexerciseOutput['id'];
                    }
                    
                    // create attachement in DB and FS
                    if (isset($subexercise['attachments']) && !empty($subexercise['attachments'])) { 
                        foreach($subexercise['attachments'] as &$attachment)
                            $attachment['exerciseId'] = $linkid;
                            
                        $attachments = Attachment::encodeAttachment($subexercise['attachments']);

                        $result = Request::routeRequest( 
                                                        'POST',
                                                        '/attachment',
                                                        $header,
                                                        $attachments,
                                                        $this->_postAttachment,
                                                        'attachment'
                                                        );

                        // checks the correctness of the query
                        if ( $result['status'] >= 200 && 
                             $result['status'] <= 299 ){                          
                            // ...
                        } else {
                            $allright = false;
                            break;
                        }
                    }

                    // create ExerciseFileTypes
                    if (isset($FileTypesArrayTemp) && !empty($FileTypesArrayTemp)){
                        foreach ($FileTypesArrayTemp as $fileType) {
                            $myExerciseFileType = ExerciseFileType::createExerciseFileType(NULL,$fileType,$linkid);
                            $myExerciseFileTypeJSON = ExerciseFileType::encodeExerciseFileType($myExerciseFileType);
                            $URL = $this->lURL."/DB/exercisefiletype";
                            $AttachmentAnswer = Request::custom('POST', $URL, $header, $myExerciseFileTypeJSON);

                            if ($AttachmentAnswer['status'] != 201) {
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

    /**
     * Returns a single exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /exercise/exercise/$exerciseid(/).
     *
     * @param int $exerciseid The id of the exercise that should be returned.
     */
    public function getExercise($exerciseid) {
        /*$header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;
        //request to database
        $answer = Request::custom('GET', $URL, $header, $body);
        //set response
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);*/
    }

    /**
     * Deletes an exercise.
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercise/exercise/$exerciseid(/).
     *
     * @param int $exerciseid The id of the exercise that is beeing deleted.
     */
    public function deleteExercise($exerciseid){
        /*$header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/exercise/exercise/'.$exerciseid;
        // request to database
        
        // ???
        print_r($URL);
        
        $answer = Request::custom('DELETE', $URL, $header, "");
        $this->app->response->setStatus($answer['status']);*/
    }

    /**
     * Edits an exercise.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercise/exercise/$exerciseid(/).
     * The request body should contain a JSON object representing the exercise's new
     * attributes.
     *
     * @param int $exerciseid The id of the exercise that is beeing updated.
     */
    public function editExercise($exerciseid){
        /*$header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;
        // request to database
        $answer = Request::custom('PUT', $URL, $header, $body);
        // set response
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);*/
    }
}
?>