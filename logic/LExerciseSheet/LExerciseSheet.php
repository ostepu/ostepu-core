<?php
/**
 * @file LExercisesheet.php Contains the LExercisesheet class
 *
 * @author Christian Elze
 * @author Martin Daute
 * @author Peter Koenig
 * @author Ralf Busch
 * @date 2013-2014
 */

require '../../Assistants/Slim/Slim.php';
include '../../Assistants/Request.php';
include '../../Assistants/Structures.php';
include_once '../../Assistants/CConfig.php';
include '../Include/LArraySorter.php';

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
    
    private $_postFile = array();
    private $_deleteFile = array();
    private $_postExerciseSheet = array();
    private $_getExerciseSheet = array();
    private $_deleteExerciseSheet = array();
    
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
        $com = new CConfig( LExerciseSheet::getPrefix( ) );

        // runs the LExerciseSheet
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
            
        // initialize slim
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        $this->_postFile = CConfig::getLinks($this->_conf->getLinks( ),'postFile');
        $this->_deleteFile = CConfig::getLinks($this->_conf->getLinks( ),'deleteFile');
        $this->_postExerciseSheet = CConfig::getLinks($this->_conf->getLinks( ),'postExerciseSheet');
        $this->_deleteExerciseSheet = CConfig::getLinks($this->_conf->getLinks( ),'deleteExerciseSheet');
        $this->_getExerciseSheet = CConfig::getLinks($this->_conf->getLinks( ),'getExerciseSheet');

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

        // GET GetExerciseSheet incl exercises
        $this->app->get('/'.$this->getPrefix().'/exercisesheet/:sheetid/exercise(/)',
                        array($this, 'getExerciseSheetExercise'));

        // GET GetExerciseSheet from course incl exercises
        $this->app->get('/'.$this->getPrefix().'/course/:courseid(/)',
                        array($this, 'getExerciseSheetCourse'));

        // GET GetExerciseSheet from course incl exercises
        $this->app->get('/'.$this->getPrefix().'/course/:courseid/exercise(/)',
                        array($this, 'getExerciseSheetCourseExercise'));

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
     * Returns an exercise sheet with exercises.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisesheet/exercisesheet/$sheetid(/).
     *
     * @param int $sheetid The id of the exercise sheet that should be returned.
     */
    public function getExerciseSheetExercise($sheetid){
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/exercisesheet/exercisesheet/'.$sheetid.'/exercise';
        $answer = Request::custom('GET', $URL, $header, "");

        $sheet = json_decode($answer['content'], true);

        // sort exercises by link = exerercises and linkName = subexercise ascendingly
        if (isset($sheet['exercises']) && is_array($sheet['exercises'])) {
            $sheet['exercises'] = LArraySorter::orderBy($sheet['exercises'], 'link', SORT_ASC, 'linkName', SORT_ASC);
        }

        $sheet = json_encode($sheet);

        $this->app->response->setBody($sheet);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Returns an exercise sheet with exercises.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisesheet/exercisesheet/$sheetid(/).
     *
     * @param int $sheetid The id of the exercise sheet that should be returned.
     */
    public function getExerciseSheetCourse($courseid){
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/exercisesheet/course/'.$courseid;
        $answer = Request::custom('GET', $URL, $header, "");

        $sheets = json_decode($answer['content'], true);

        // latest sheets on top
        if (is_array($sheets)) {
            $sheets = LArraySorter::reverse($sheets);
        }

        $sheets = json_encode($sheets);

        $this->app->response->setBody($sheets);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Returns an exercise sheet with exercises.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisesheet/exercisesheet/$sheetid(/).
     *
     * @param int $sheetid The id of the exercise sheet that should be returned.
     */
    public function getExerciseSheetCourseExercise($courseid){
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/exercisesheet/course/'.$courseid.'/exercise';
        $answer = Request::custom('GET', $URL, $header, "");

        $sheets = json_decode($answer['content'], true);
        // latest sheets on top
        if (is_array($sheets)) {
            $sheets = LArraySorter::reverse($sheets);

            // sort exercises by link = exerercises and linkName = subexercise ascendingly
            foreach ($sheets as &$sheet) {
                if (isset($sheet['exercises']) && is_array($sheet['exercises'])) {
                    $sheet['exercises'] = LArraySorter::orderBy($sheet['exercises'], 'link', SORT_ASC, 'linkName', SORT_ASC);
                }
            }
        }

        $sheets = json_encode($sheets);

        $this->app->response->setBody($sheets);
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
        
        $this->app->response->setStatus( 201 );
        Logger::Log( 
                    'starts DELETE DeleteExerciseSheet',
                    LogLevel::DEBUG
                    );
                    
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $res = null;
        
        // getExerciseSheet
        $result = Request::routeRequest( 
                                        'GET',
                                        '/exercisesheet/'.$sheetid,
                                        $this->app->request->headers->all(),
                                        '',
                                        $this->_getExerciseSheet,
                                        'exercisesheet'
                                        );
                                        
        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 && isset($result['content'])){

            $exerciseSheet = ExerciseSheet::decodeExerciseSheet($result['content']);
            $sampleFile = $exerciseSheet->getSampleSolution();
            $sheetFile = $exerciseSheet->getSheetFile();
            
            // delete exerciseSheet
            $result = Request::routeRequest( 
                                            'DELETE',
                                            '/exercisesheet/'.$sheetid,
                                            $this->app->request->headers->all(),
                                            '',
                                            $this->_deleteExerciseSheet,
                                            'exercisesheet'
                                            );
                                            
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299){
                // exerciseSheet is deleted
                
                // delete sampleSolution if exists
                if ($sampleFile !== null){
                    $result = Request::routeRequest( 
                                                    'DELETE',
                                                    '/file/'.$sampleFile->getFileId(),
                                                    array(),
                                                    '',
                                                    $this->_deleteFile,
                                                    'file'
                                                    );
                }
                
                // delete sheetFile if exists
                if ($sheetFile !== null){
                    $result = Request::routeRequest( 
                                                    'DELETE',
                                                    '/file/'.$sheetFile->getFileId(),
                                                    array(),
                                                    '',
                                                    $this->_deleteFile,
                                                    'file'
                                                    );
                }
                
                $res = new ExerciseSheet();
    
            } else {
                $res = null;
                $this->app->response->setStatus( 409 );
            }
        } else {
            $res = null;
            $this->app->response->setStatus( 409 );
        }
        
        if ($this->app->response->getStatus( ) != 201)
            Logger::Log( 
                        'DELETE DeleteExerciseSheet failed',
                        LogLevel::ERROR
                        );
                    
        $this->app->response->setBody( ExerciseSheet::encodeExerciseSheet( $res ) );
    }
}
?>