<?php
/**
 * @file LSubmission.php Contains the LSubmission class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

require_once dirname(__FILE__) . '/../../Assistants/vendor/Slim/Slim/Slim.php';
include_once dirname(__FILE__) . '/../../Assistants/Request.php';
include_once dirname(__FILE__) . '/../../Assistants/CConfig.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures.php';
include_once ( dirname(__FILE__). '/../../Assistants/Logger.php' );

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

    private $_file = array( );
    private $_zip = array( );
    private $_submission = array( );
    private $_selectedSubmission = array( );

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
    ///private $lURL = ""; //aus config lesen

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
        $com = new CConfig( LSubmission::getPrefix( ), dirname(__FILE__) );

        // runs the LSubmission
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );

        // initialize slim
        $this->app = new \Slim\Slim( array( 'debug' => true ) );
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        ///$this->query = array();
        $this->_file = CConfig::getLinks($conf->getLinks(),"file");
        $this->_submission = CConfig::getLinks($conf->getLinks(),"submission");
        $this->_selectedSubmission = CConfig::getLinks($conf->getLinks(),"selectedSubmission");
         $this->_zip = CConfig::getLinks($conf->getLinks(),"zip");

        // initialize lURL
        ///$this->lURL = $this->query->getAddress();

        //AddSubmission
        $this->app->post('/'.$this->getPrefix().'(/)', array($this, 'addSubmission'));

        //deleteSubmission
        $this->app->delete('/'.$this->getPrefix().'/submission/:submissionid(/)',
                        array($this, 'deleteSubmission'));

        //LoadSubmissionAsZip
        $this->app->get('/'.$this->getPrefix().'/exercisesheet/:sheetid/user/:userid(/)',
                        array($this, 'loadSubmissionAsZip'));

        $this->app->run();
    }

    /**
     * Adds a user's submission to the database and filesystem
     *
     * Called then this component receives an HTTP POST request to
     * /submission(/)
     * The request body should contain a JSON object representing a submission.
     *
     * @author Till Uhlig
     * @date 2014
     */
    public function addSubmission()
    {

        $body = $this->app->request->getBody();

        $submission = Submission::decodeSubmission($body);
        $file = $submission->getFile();
        if (!isset($file) || $file==array()) $file = new File();
        if ($file->getTimeStamp()===null) $file->setTimeStamp(time());
///echo File::encodeFile($file);return;
        // upload file to filesystem
        $result = Request::routeRequest(
                                        'POST',
                                        '/file',
                                        array(),
                                        File::encodeFile($file),
                                        $this->_file,
                                        'file'
                                        );
///var_dump($result);
///echo $result['content'];return;
        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){
            // file is uploaded
            $newFile = File::decodeFile($result['content']);
            $file->setAddress($newFile->getAddress());
            $file->setHash($newFile->getHash());
            $file->setFileId($newFile->getFileId());
            $file->setBody(null);
            $submission->setFile($file);

            // upload submission to database
            if ($submission->getId()===null){
//echo Submission::encodeSubmission($submission);return;
            $result = Request::routeRequest(
                                    'POST',
                                    '/submission',
                                    array(),
                                    Submission::encodeSubmission($submission),
                                    $this->_submission,
                                    'submission'
                                    );
            }
            else{
                $result['status'] = 201;
                $result['content'] = Submission::encodeSubmission($submission);
            }
//var_dump($result);
            if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){
                // submission is uploaded
                $newsubmission = Submission::decodeSubmission($result['content']);
                $submission->setId($newsubmission->getId());

                // select new submission
                if ($submission->getSelectedForGroup()=='1'){
                    $selectedSubmission = SelectedSubmission::createSelectedSubmission($submission->getLeaderId(),$submission->getId(),$submission->getExerciseId());

                $result = Request::routeRequest(
                                    'POST',
                                    '/selectedsubmission',
                                    array(),
                                    SelectedSubmission::encodeSelectedSubmission($selectedSubmission),
                                    $this->_selectedSubmission,
                                    'selectedsubmission'
                                    );

                if ( $result['status'] >= 200 &&
                    $result['status'] <= 299 ){
                    $this->app->response->setBody(Submission::encodeSubmission($submission));
                    $this->app->response->setStatus( 201 );
                    $this->app->stop( );
                }
                else{
                    $result = Request::routeRequest(
                                    'PUT',
                                    '/selectedsubmission/leader/'.$submission->getLeaderId().'/exercise/'.$submission->getExerciseId(),
                                    $this->app->request->headers->all( ),
                                    SelectedSubmission::encodeSelectedSubmission($selectedSubmission),
                                    $this->_selectedSubmission,
                                    'selectedsubmission'
                                    );

                    if ( $result['status'] >= 200 &&
                    $result['status'] <= 299 ){
                    $this->app->response->setBody(Submission::encodeSubmission($submission));
                    $this->app->response->setStatus( 201 );
                    $this->app->stop( );
                }

                }
                }
                else{
                    $this->app->response->setBody(Submission::encodeSubmission($submission));
                    $this->app->response->setStatus( 201 );
                    $this->app->stop( );
                }
            }
        }

        Logger::Log(
                    'POST AddSubmission failed',
                    LogLevel::ERROR
                    );
        $this->app->response->setBody(Submission::encodeSubmission(new Submission()));
        $this->app->response->setStatus( 409 );
        $this->app->stop( );
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
     *
     * @author Till Uhlig
     * @date 2014
     */
    public function deleteSubmission($submissionid){
        $result = Request::routeRequest(
                                        'DELETE',
                                        '/submission/submission/'.$submissionid,
                                        array(),
                                        '',
                                        $this->_submission,
                                        'submission'
                                        );

        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){

            // submission is deleted
            $this->app->response->setStatus( 201 );
            $this->app->response->setBody( '' );
            if ( isset( $result['headers']['Content-Type'] ) )
                $this->app->response->headers->set(
                                                    'Content-Type',
                                                    $result['headers']['Content-Type']
                                                    );
        } else {
            Logger::Log(
                        'DELETE DeleteSubmission failed',
                        LogLevel::ERROR
                        );
            $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->app->response->setBody( '' );
            $this->app->stop( );
        }
    }

    /**
     * Loads all selected submissions as a zip file.
     *
     * Called when this component receives an HTTP GET request to
     * /submissions/exercisesheet/$sheetid/user/$userid.
     *
     * @param int $sheetid The id of the sheet of which the submissions should
     * be zipped.
     * @param int $userid The id of the user whose submissions should be zipped.
     *
     * @author Till Uhlig
     * @date 2014
     */
    public function loadSubmissionAsZip($sheetid, $userid)
    {
        $result = Request::routeRequest(
                            'GET',
                            '/submission/group/user/'.$userid.'/exercisesheet/'.$sheetid.'/selected',
                            $this->app->request->headers->all( ),
                            '',
                            $this->_submission,
                            'submission'
                            );

        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){
             $submissions = Submission::decodeSubmission($result['content']);

             $files = array();
             foreach ($submissions as $submission){
                $file = $submission->getFile();
                $file->setDisplayName($file->getDisplayName());
                $files[] = $file;
             }


            $result = Request::routeRequest(
                                            'POST',
                                            '/zip/'.$sheetid.'.zip',
                                            $this->app->request->headers->all( ),
                                            File::encodeFile($files),
                                            $this->_zip,
                                            'zip'
                                            );

            if ( $result['status'] >= 200 &&
                 $result['status'] <= 299 ){

                $this->app->response->setBody($result['content']);
                $this->app->response->setStatus( 200 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->app->response->headers->set(
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                if ( isset( $result['headers']['Content-Disposition'] ) )
                    $this->app->response->headers->set(
                                                        'Content-Disposition',
                                                        $result['headers']['Content-Disposition']
                                                        );
                $this->app->stop( );
                }
        }

        $this->app->response->setBody('');
        $this->app->response->setStatus( 404 );
        $this->app->stop( );
    }
}