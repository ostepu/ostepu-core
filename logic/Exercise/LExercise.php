<?php
/**
 * @file LExercise.php Contains the LExercise class
 *
 * @author Martin Daute
 * @author Christian Elze
 * @author Peter Koenig
 */

require '../Include/Slim/Slim.php';
include '../Include/Request.php';
include_once( '../Include/CConfig.php' );
include_once '../Include/Structures.php';

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
        $this->query = CConfig::getLink($conf->getLinks(),"controller");

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

        // pointer to the previous subexercise
        $previousSubexercise = 0;
        // counter for current subexercise
        $counter = 1;
        $allright = true;

        if (isset($body) == true && empty($body) == false) {
            foreach ($body as $subexercise) {
                //upload attachement if it exists
                if (isset($subexercise['attachments']) == true && empty($subexercise['attachments']) == false) {
                    // get attachment
                    $attachmentFile = json_encode($subexercise['attachments']);

                    // set URL for requests to filesystem
                    $URL = $this->lURL.'/FS/file';

                    // upload sampleSolution
                    $answer = Request::custom('POST', $URL, $header, $attachmentFile);
                    if($answer['status'] == 201) {
                        $URL = $this->lURL.'/DB/file';
                        $answer2 = Request::custom('POST', $URL, $header, $answer['content']);

                        // if file already exists
                        if($answer2['status'] != 201) {
                            $attachmentFSContent = json_decode($answer['content'], true);
                            $answer2 = Request::custom('GET', $URL.'/hash/'.$attachmentFSContent['hash'], $header, "");
                            if ($answer2['status'] == 200) {
                                $id = json_decode($answer2['content'], true);
                                $subexercise['attachments'] = $id;
                            } else {
                                $allright = false;
                                break;
                            }
                        } elseif ($answer2['status'] == 201) {
                            $id = json_decode($answer2['content'], true);
                            $subexercise['attachments'] = $id;
                        }
                    } else {
                        $allright = false;
                        break;
                    }
                }

                // create exercise in DB
                $subexerciseJSON = json_encode($subexercise);
                $URL = $this->lURL.'/DB/exercise';
                $subexerciseAnswer = Request::custom('POST', $URL, $header, $subexerciseJSON);

                if ($subexerciseAnswer['status'] == 201) {
                    $subexerciseOutput = json_decode($subexerciseAnswer['content'], true);

                    if (isset($subexerciseOutput['id'])) {
                        $linkid = $subexerciseOutput['id'];
                        if ($counter == 1) {
                            $previousSubexercise = $linkid;
                        }
                    }
                    // create attachement in DB
                    if (isset($subexercise['attachments']) == true && empty($subexercise['attachments']) == false) {
                        $AttachmentObj = Attachment::createAttachment(NULL,$linkid,$subexercise['attachments']['fileId']);
                        $AttachmentObjJSON = Attachment::encodeAttachment($AttachmentObj);
                        $URL = $this->lURL."/DB/attachment";
                        $AttachmentAnswer = Request::custom('POST', $URL, $header, $AttachmentObjJSON);

                        if ($AttachmentAnswer['status'] != 201) {
                            $allright = false;
                            break;
                        }
                    }
                    // update link
                    $subexerciseObj = Exercise::createExercise(NULL,NULL,NULL, NULL, NULL, $previousSubexercise, NULL);
                    $subexerciseObjJSON = Exercise::encodeExercise($subexerciseObj);
                    
                    $URL = $this->lURL."/DB/exercise/exercise/".$linkid;
                    $subexercisePutAnswer = Request::custom('PUT', $URL, $header, $subexerciseObjJSON);

                    if ($subexercisePutAnswer['status'] == 201) {
                        if ($counter > 1) {
                            $previousSubexercise = $linkid;
                        }

                        $counter++;
                    } else {
                        $allright = false;
                        break;
                    }
                } else {
                    $allright = false;
                    break;
                }
            }
        }
        if ($allright == true) {
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
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;
        //request to database
        $answer = Request::custom('GET', $URL, $header, $body);
        //set response
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
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
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/exercise/exercise/'.$exerciseid;
        // request to database
        print_r($URL);
        $answer = Request::custom('DELETE', $URL, $header, "");
        $this->app->response->setStatus($answer['status']);
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
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exercise/'.$exerciseid;
        // request to database
        $answer = Request::custom('PUT', $URL, $header, $body);
        // set response
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }
}

// get new config data from DB
$com = new CConfig(LExercise::getPrefix());

// create a new instance of LExercise class with the config data
if (!$com->used())
    new LExercise($com->loadConfig());
?>