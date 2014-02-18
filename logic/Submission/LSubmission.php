<?php
/**
 * @file LSubmission.php Contains the LSubmission class
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
 * A class, to handle requests to the LSubmission-Component
 */
class LSubmission
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "submission";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LSubmission::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LSubmission::$_prefix = $value;
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
        // initialize slim 
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        
        // initialize component
        $this->_conf = $conf;
        $this->query = array();
        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        
        // initialize lURL
        $this->lURL = $this->query->getAddress();

        //AddSubmission
        $this->app->post('/'.$this->getPrefix().'(/)', array($this, 'addSubmission'));

        //EditSubmissionState
        $this->app->put('/'.$this->getPrefix().'/submission/:submissionid(/)',
                        array ($this, 'editSubmissionState'));

        //deleteSubmission
        $this->app->delete('/'.$this->getPrefix().'/submission/:submissionid(/)',
                        array($this, 'deleteSubmission'));

        //LoadSubmissionAsZip
        $this->app->get('/'.$this->getPrefix().'/exerciseSheet/:sheetid/user/:userid(/)',
                        array($this, 'loadSubmissionAsZip'));

        //ShowSubmissionsHistory
        $this->app->get('/'.$this->getPrefix().'/exerciseSheet/:sheetid/user/:userid/history(/)',
                        array($this, 'showSubmissionsHistory'));

        //GetSubmissionURL
        $this->app->get('/'.$this->getPrefix().'/submission/:submissionid(/)',
                        array($this, 'getSubmissionURL'));

        $this->app->run();
    }

    /**
     * Adds a user's submission to the database and filesystem
     *
     * Called then this component receives an HTTP POST request to
     * /submissions(/)
     * The request body should contain a JSON object representing a submission.
     */
    public function addSubmission(){
        
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody(), true);
        $file = $body['file'];
        
        //Request to FileSystem
        $URL = $this->lURL.'/FS/file';
        $answer = Request::custom('POST', $URL, $header, json_encode($file));
        
        if(($answer['status'] >= 200) and ($answer['status'] <= 300)){ //if file was succsessfully saved to FS
            $newfile = json_decode($answer['content'], true);
            //Request to DB to save the file
            $file['address'] = $newfile['address'];
            $file['fileSize'] = $newfile['fileSize'];
            $file['hash'] = $newfile['hash'];
            unset($file['body']);
            $body['file'] = $file;
            //check wheter the file allready exists
            $URL = $this->lURL.'/DB/file/hash/'.$file['hash'];
            $answer = Request::custom('GET', $URL, $header, "");
            $answer = json_decode($answer['content'], true);
            if ($answer == "[]"){       //if file does not exists, POST-Request to add it
                $URL = $this->lURL.'/DB/file';
                $answer = Request::custom('POST', $URL, $header, json_encode($file));
                $answer = json_decode($answer['content'], true);
            }
        } else{
            $this->app->response->setStatus($answer['status']);
        }
        
        $submission = $body;
        $submission['file']['fileId'] = $answer['fileId'];
        
        //Get the LeaderId of the Group the submission belongs to        
        $URL = $this->lURL.'/DB/exercise/exercise/'.$submission['exerciseId'];
        $answer = Request::custom('GET', $URL, $header, "");
        $exercise = json_decode($answer['content'], true);        
        
        //Fehlerhafte Ausgabe der DB (ist in Bearbeitung)
        //$URL = $this->lURL.'/DB/group/user/'.$submission['studentId'].'/exercisesheet/'.$exercise['sheetId'];
        //$answer = Request::custom('GET', $URL, $header, "");
        //$group = json_decode($answer['content'], true);
        
        //Alternative Abfrage solange DB-Fehler noch nicht gefixt ist
            $URL = $this->lURL.'/DB/group/exercisesheet/'.$exercise['sheetId'];
            $answer = Request::custom('GET', $URL, $header, "");
            $groups = json_decode($answer['content'], true);
            
        
            foreach ($groups as $group){
                if(isset($group['leader'])){
                    //print_r($group['leader']['id']);
                    if ($submission['studentId'] == $group['leader']['id']){
                        $submission['leaderId'] = $group['leader']['id'];
                        break;
                    }else{
                        foreach($group['members'] as $member){
                            if($submission['studentId'] == $member['id']){
                                $submission['leaderId'] = $group['leader']['id'];
                                break;
                            }
                        }
                    }
                }
            }
            
            
        //$submission['leaderId'] = $group['leader']['id'];
        //Request to DB to add the submission
        $URL = $this->lURL.'/DB/submission';
        $answer = Request::custom('POST', $URL, $header, json_encode($submission));
        
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Edits a submission's state.
     *
     * Called when this component receives an HTTP PUT request to
     * /submissions/$submissionid(/).
     * The request body should contain a JSON object representing a submission.
     *
     * @param int $submissionid The id of the submission that is beeing updated.
     */
    public function editSubmissionState($submissionid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/submission/'.$submissionid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Deletes a submission.
     *
     * Called when this component revceives an HTTP DELETE request to
     * /submissions/$submissionid(/).
     *
     * @param int $submissionid The submission that is beeing deleted.
     *
     * @note Files are completely removed from the system. This is not intended
     * behaviour as this prevents lecturers and admins from seeing them in the
     * user's submission history.
     */
    public function deleteSubmission($submissionid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/submission/'.$submissionid;
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);

        /**
         * if DB-Request was succsessfull the file also gets removed from FS 
         * otherwise returns the Status-Code from DB 
         */
        $fileObject = json_decode($answer['content']);
        //if address-field exists, read it out
        if (isset($fileObject->{'address'})){
            $fileAddress = $fileObject->{'address'};
        }
        
        if( $answer['status'] < 300){
            $URL = $this->lURL.'/FS/'.$fileAddress;
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }
    }

    /**
     * Loads all submissions as a zip file.
     *
     * Called when this component receives an HTTP GET request to
     * /submissions/exercisesheet/$sheetid/user/$userid.
     *
     * @param int $sheetid The id of the sheet of which the submissions should
     * be zipped.
     * @param int $userid The id of the user whose submissions should be zipped.
     */
    public function loadSubmissionAsZip($sheetid, $userid){       //Annahme: ZipURL in DB abrufbar
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseSheet/'.$sheetid.'/user/'.$userid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Loads the submission history of a user.
     *
     * Called when this component receives an HTTP GET request to
     * /submissions/exercisesheet/$sheetid/user/$userid/history.
     *
     * @param int $sheetid The id of the sheet of which the submissions should
     * be loaded.
     * @param int $userid The id of the user whose submissions should be loaded.
     */
    public function showSubmissionsHistory($sheetid, $userid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseSheet/'.$sheetid.'/user/'.$userid.'/history';
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Loads a specific submission.
     *
     * Called when this component receives an HTTP GET request to
     * /submissions/$submissionid(/).
     *
     * @param int $submissionid The id of the requested submission.
     */
    public function getSubmissionURL($submissionid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/submission/'.$submissionid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }
}

/**
 * get new Config-Datas from DB
 */
$com = new CConfig(LSubmission::getPrefix());

/**
 * make a new instance of Submission-Class with the Config-Datas
 */
if (!$com->used())
    new LSubmission($com->loadConfig());
?>