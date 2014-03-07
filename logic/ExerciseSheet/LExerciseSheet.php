<?php
/**
 * @file LExercisesheet.php Contains the LExercisesheet class
 *
 * @author Christian Elze
 * @author Martin Daute
 * @author Peter Koenig
 * @author Ralf Busch
 */

require '../../Assistants/Slim/Slim.php';
include '../../Assistants/Request.php';
include_once '../../Assistants/CConfig.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LExercisesheet-Component
 */
class LExerciseSheet
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "exercisesheet";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LExerciseSheet::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LExerciseSheet::$_prefix = $value;
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

        // POST AddExerciseSheet
        $this->app->post('/'.$this->getPrefix().'(/)',
                        array($this, 'addExerciseSheet'));

        // PUT EditExerciseSheet
        $this->app->put('/'.$this->getPrefix().'/exercisesheet/:sheetid(/)',
                        array ($this, 'editExerciseSheet'));

        // GET GetExerciseSheetURL
        $this->app->get('/'.$this->getPrefix().'/exercisesheet/:sheetid/url(/)',
                        array($this, 'getExerciseSheetURL'));

        // GET GetExerciseSheet
        $this->app->get('/'.$this->getPrefix().'/exercisesheet/:sheetid(/)',
                        array($this, 'getExerciseSheet'));

        // DELETE DeleteExerciseSheet
        $this->app->delete('/'.$this->getPrefix().'/exercisesheet/:sheetid(/)',
                        array($this, 'deleteExerciseSheet'));

        // run Slim
        $this->app->run();
    }


    /**
     * Adds an exercisesheet.
     *
     * Called when this component receives an HTTP POST request to
     * /exercisesheet(/).
     * The request body should contain a JSON object representing the exercise
     * sheet's attributes.
     * Adds the sheet file and samplesolution file to the filesystem first. At success
     * the information belongs to this exercisesheet will be stored in the database.
     */
     public function addExerciseSheet(){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody(), true);
        
        // if sheetFile is given
        if (isset($body['sheetFile']) == true && empty($body['sheetFile']) == false) {
            // get sheetfile
            $sheetfile = json_encode($body['sheetFile']);

            // set URL for requests to filesystem
            $URL = $this->lURL.'/FS/file';

            // upload sheetfile
            $sheetanswer = Request::custom('POST', $URL, $header, $sheetfile);
            if($sheetanswer['status'] == 201) {
                $URL = $this->lURL.'/DB/file';
                $sheetanswer2 = Request::custom('POST', $URL, $header, $sheetanswer['content']);

                // if file already exists
                if($sheetanswer2['status'] != 201) {
                    $sheetFSContent = json_decode($sheetanswer['content'], true);
                    $sheetanswer2 = Request::custom('GET', $URL.'/hash/'.$sheetFSContent['hash'], $header, "");
                    if ($sheetanswer2['status'] == 200) {
                        $id = json_decode($sheetanswer2['content'], true);
                        $body['sheetFile'] = $id;
                    }
                } elseif ($sheetanswer2['status'] == 201) {
                    $id = json_decode($sheetanswer2['content'], true);
                    $body['sheetFile'] = $id;
                }
            } else {
                $this->app->response->setStatus(409);
            } 
        } else {
            $this->app->response->setStatus(409);
        }

        // if sampleSolution is given
        if (isset($body['sampleSolution']) == true && empty($body['sampleSolution']) == false) {
            // get sampleSolution
            $sheetfile = json_encode($body['sampleSolution']);

            // set URL for requests to filesystem
            $URL = $this->lURL.'/FS/file';

            // upload sampleSolution
            $sheetanswer = Request::custom('POST', $URL, $header, $sheetfile);
            if($sheetanswer['status'] == 201) {
                $URL = $this->lURL.'/DB/file';
                $sheetanswer2 = Request::custom('POST', $URL, $header, $sheetanswer['content']);

                // if file already exists
                if($sheetanswer2['status'] != 201) {
                    $sheetFSContent = json_decode($sheetanswer['content'], true);
                    $sheetanswer2 = Request::custom('GET', $URL.'/hash/'.$sheetFSContent['hash'], $header, "");
                    if ($sheetanswer2['status'] == 200) {
                        $id = json_decode($sheetanswer2['content'], true);
                        $body['sampleSolution'] = $id;
                    }
                } elseif ($sheetanswer2['status'] == 201) {
                    $id = json_decode($sheetanswer2['content'], true);
                    $body['sampleSolution'] = $id;
                }
            } else {
                $this->app->response->setStatus(409);
            } 
        } else {
            $this->app->response->setStatus(409);
        }

        // create ExerciseSheet
        $URL = $this->lURL.'/DB/exercisesheet';
        $CreateSheetDB = Request::custom('POST', $URL, $header, json_encode($body));
        $this->app->response->setBody($CreateSheetDB['content']);
        $this->app->response->setStatus($CreateSheetDB['status']);
    }

    /**
     * Edits an exercise sheet.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercisesheet/exercisesheet/$sheetid(/).
     * The request body should contain a JSON object representing the exercise
     * sheet's attributes.
     * Adds the sheet file and samplesolution file to the filesystem first. At success
     * the information belongs to this exercisesheet will be stored in the database.
     *
     * @param int $sheetid The id of the exercise sheet that is beeing updated.
     */
    public function editExerciseSheet($sheetid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody(), true);

        // get files from body
        $samplesolutionfile = json_encode($body['sampleSolution']);
        $sheetfile = json_encode($body['sheetFile']);

        // set URL for requests to filesystem
        $URL = $this->lURL.'/FS/file';

        // requests to filesystem
        $sampleanswer = Request::custom('POST', $URL, $header, $samplesolutionfile);
        $sheetanswer = Request::custom('POST', $URL, $header, $sheetfile);

        /*
         * if the files has been stored, the information
         * belongs to this exercisesheet will be stored in the database
         */
        if($sampleanswer['status'] >= 200 and $sampleanswer['status'] < 300
            and $sheetanswer['status'] >= 200 and $sheetanswer['status'] < 300){
                // first request to store the fileinformation to the DBfile table
                $URL = $this->lURL.'/DB/file';
                $sampleanswer = Request::custom('POST', $URL, $header, $sampleanswer['content']);
                $sheetanswer = Request::custom('POST', $URL, $header, $sheetanswer['content']);
                if($sampleanswer['status'] >= 200 and $sampleanswer['status'] < 300
                    and $sheetanswer['status'] >= 200 and $sheetanswer['status'] < 300){
                        // set the request body for database
                        $body['sampleSolution'] = json_decode($sampleanswer['content'], true);
                        $body['sheetFile'] = json_decode($sheetanswer['content'], true);
                        // request to database
                        $URL = $this->lURL.'/DB/exercisesheet/exercisesheet/'.$sheetid;
                        $answer = Request::custom('PUT', $URL, $header, json_encode($body));
                        $this->app->response->setStatus($answer['status']);
                } else {
                    $this->app->response->setStatus(400);
                }
        } else {
            $this->app->response->setStatus(400);
        }
    }

    /**
     * Returns the URL to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisesheet/exercisesheet/$sheetid/url(/).
     *
     * @param int $sheetid The id of the exercise sheet the returned URL belongs to.
     */
    public function getExerciseSheetURL($sheetid){
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/exercisesheet/exercisesheet/'.$sheetid.'/url';
        $answer = Request::custom('GET', $URL, $header, "");
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Returns an exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisesheet/exercisesheet/$sheetid(/).
     *
     * @param int $sheetid The id of the exercise sheet that should be returned.
     */
    public function getExerciseSheet($sheetid){
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/exercisesheet/exercisesheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, "");
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Deletes an exercise sheet.
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisesheet/exercisesheet/$sheetid(/).
     * Deletes the exercise sheet information from the database first. At success
     * the file belongs to this exercise will be deleted from the filesystem.
     *
     * @param int $sheetid The id of the exercise sheet that is being deleted.
     */
    public function deleteExerciseSheet($sheetid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        // getExerciseSheet
        $URL = $this->lURL.'/DB/exercisesheet/exercisesheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, "");
        $exerciseSheet = json_decode($answer['content'], true);
        $sampleFileAddress = $exerciseSheet['sampleSolution']['address'];
        $sampleFileid = $exerciseSheet['sampleSolution']['fileId'];
        $sheetFileAddress = $exerciseSheet['sheetFile']['address'];
        $sheetFileid = $exerciseSheet['sheetFile']['fileId'];

        $URL = $this->lURL.'/DB/exercisesheet/exercisesheet/'.$sheetid;
        // request to database
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);

        /*
         * if the file information has been deleted, the files
         * will being deleted from filetable and from filesystem
         */
        if ($answer['status'] >= 200 and $answer['status'] < 300){
            // requests to file-table of DB
            $URL = $this->lURL.'/DB/file/'.$sampleFileid;
            $answer = Request::custom('DELETE', $URL, $header, "");
            $URL = $this->lURL.'/DB/file/'.$sheetFileid;
            $answer = Request::custom('DELETE', $URL, $header, "");
            // requests to filesystem
            $URL = $this->lURL.'/FS/'.$sampleFileAddress;
            $answer = Request::custom('DELETE', $URL, $header, $body);
            $URL = $this->lURL.'/FS/'.$sheetFileAddress;
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }
    }
}

// get new config data from DB
$com = new CConfig(LExerciseSheet::getPrefix());

// create a new instance of LExercisesheet class with the config data
if (!$com->used())
    new LExerciseSheet($com->loadConfig());
?>