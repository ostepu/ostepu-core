<?php
/**
 * @file LFormPredecessor.php Contains the LFormPredecessor class
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

require_once dirname(__FILE__) . '/../../Assistants/vendor/Slim/Slim/Slim.php';
include_once dirname(__FILE__) . '/../../Assistants/Request.php';
include_once dirname(__FILE__) . '/../../Assistants/CConfig.php';
include_once dirname(__FILE__) . '/../../Assistants/DBJson.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../../Assistants/DefaultNormalizer.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LFormPredecessor-Component
 */
class LFormPredecessor
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
        return LFormPredecessor::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LFormPredecessor::$_prefix = $value;
    }

    /**
     * @var Link[] $_pdf a list of links
     */
    private $_pdf = array( );

    /**
     * @var Link[] $_formDb a list of links
     */
    private $_formDb = array( );
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
        $com = new CConfig( LFormPredecessor::getPrefix( ) . ',course,link', dirname(__FILE__) );

        // runs the LFormPredecessor
        if ( $com->used( ) ) return;
        $conf = $com->loadConfig( );

        // initialize slim
        $this->app = new \Slim\Slim(array('debug' => true));
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->_pdf = CConfig::getLinks($conf->getLinks(),"pdf");
        $this->_formDb = CConfig::getLinks($conf->getLinks(),"formDb");
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
                                        $this->app->request->headers->all( ),
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

        $header = $this->app->request->headers->all();
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
                                            $header,
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
                                        $this->app->request->headers->all( ),
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
     * Returns the text of a given choice id.
     *
     * @param string $choiceId The id of the choice.
     * @param string[] $Choices An array of choices.
     *
     * @return String The text.
     */
    public function ChoiceIdToText($choiceId, $Choices)
    {
        foreach ($Choices as $choice){
            if ($choiceId === $choice->getChoiceId())
                return $choice->getText();
        }

        return null;
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

        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $process = Process::decodeProcess($body);
        // always been an array
        $arr = true;
        if ( !is_array( $process ) ){
            $process = array( $process );
            $arr = false;
        }

        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $process as $pro ){
            $eid = $pro->getExercise()->getId();

            // loads the form from database
            $result = Request::routeRequest(
                                            'GET',
                                            '/form/exercise/'.$eid,
                                            $this->app->request->headers->all( ),
                                            '',
                                            $this->_formDb,
                                            'form'
                                            );

            // checks the correctness of the query
            if ( $result['status'] >= 200 &&
                 $result['status'] <= 299 ){

                // only one form as result
                $forms = Form::decodeForm($result['content']);
                $forms = $forms[0];

                $formdata = $pro->getRawSubmission()->getFile();

                $timestamp = $formdata->getTimeStamp();
                if ($timestamp === null)
                    $timestamp = time();

                if ($formdata !== null && $forms !== null){
                    $formdata = Form::decodeForm($formdata->getBody( true ));
                    if (is_array($formdata)) $formdata = $formdata[0];

                    if ($formdata !== null){
                        // check the submission
                        $fail = false;
                        $parameter = explode(' ',strtolower($pro->getParameter()));

                        $choices = $formdata->getChoices();

                        if ($forms->getType()==0){
                            foreach ($choices as &$choice){
                                $i=0;
                                for ($i<0;$i<count($parameter);$i++){
                                    $param = $parameter[$i];
                                    if ($param===null || $param==='') continue;

                                    switch($param){
                                        case('isnumeric'):
                                            if (!@preg_match("%^-?([0-9])+([,]([0-9])+)?$%",DefaultNormalizer::normalizeText($choice->getText()))){
                                                $fail = true;
                                                $pro->addMessage('"'.$choice->getText().'" ist keine gültige Zahl. <br>Bsp.: -0,00');
                                            }
                                            break;
                                        case('isdigit'):
                                            if (!ctype_digit(DefaultNormalizer::normalizeText($choice->getText()))){
                                                $fail = true;
                                                $pro->addMessage('"'.$choice->getText().'" ist keine gültige Ziffernfolge.');
                                            }
                                            break;
                                        case('isprintable'):
                                            if (!ctype_print(DefaultNormalizer::normalizeText($choice->getText()))){
                                                $fail = true;
                                                $pro->addMessage('"' . $choice->getText().'" enthält nicht-druckbare Zeichen.');
                                            }
                                            break;
                                        case('isalpha'):
                                            if (!ctype_alpha(DefaultNormalizer::normalizeText($choice->getText()))){
                                                $fail = true;
                                                $pro->addMessage('"' . $choice->getText().'" ist keine gültige Buchstabenfolge.');
                                            }
                                            break;
                                        case('isalphanum'):
                                            if (!ctype_alnum(DefaultNormalizer::normalizeText($choice->getText()))){
                                                $fail = true;
                                                $pro->addMessage('"' . $choice->getText().'" ist nicht alphanumerisch.');
                                            }
                                            break;
                                        case('ishex'):
                                            if (!ctype_xdigit(DefaultNormalizer::normalizeText($choice->getText()))){
                                                $fail = true;
                                                $pro->addMessage('"' . $choice->getText().'" ist keine gültige Hexadezimalzahl.');
                                            }
                                            break;
                                        default:
                                            $test = $parameter[$i];$i++;
                                            while($i<count($parameter)){
                                                if (@preg_match($test, DefaultNormalizer::normalizeText($choice->getText()))!==false)
                                                    break;
                                                $test.=' '.$parameter[$i];
                                                $i++;
                                            };

                                            $match = @preg_match($test, DefaultNormalizer::normalizeText($choice->getText()));
                                            if ($match === false){
                                                $fail = true;
                                                $pro->addMessage('"' . $test . '" ist kein gültiger regulärer Ausdruck.');
                                            } elseif ($match == false){
                                                $fail = true;
                                                $pro->addMessage('"' . $choice->getText().'" entspricht nicht dem regulären Ausdruck "'.$test.'".');
                                            }
                                            break;
                                    }
                                }
                            }
                        }

                        if ($fail){
                            // received submission isn't correct
                            $pro->setStatus(409);
                            $res[] = $pro;
                            $this->app->response->setStatus( 409 );
                            continue;
                        }


                        // save the submission
                        #region Form to PDF
                        if ($pro->getSubmission() === null){
                            $raw = $pro->getRawSubmission();
                            $exerciseName = '';

                            if ( $raw !== null )
                                $exerciseName = $raw->getExerciseName();

                            $answer="";

                            if ($forms->getType()==0) $answer = $formdata->getChoices()[0]->getText();
                            if ($forms->getType()==1) $answer = $this->ChoiceIdToText($formdata->getChoices()[0]->getText(), $forms->getChoices());
                            if ($forms->getType()==2)
                                foreach($formdata->getChoices() as $chosen)
                                    $answer.= $this->ChoiceIdToText($chosen->getText(), $forms->getChoices()).'<br>';

                            $Text =  "<h1>AUFGABE {$exerciseName}</h1>".
                                    "<hr>";

                            if ($forms->getTask()!==null && trim($forms->getTask()) != ''){
                                $Text.= "<p>".
                                        "<h2>Aufgabenstellung:</h2>".
                                        $forms->getTask().
                                        "</p>";
                            }

                            $Text.= "<p>".
                                    "<h2>Antwort:</h2>".
                                    $answer.
                                    "</p>";

                            $pdf = Pdf::createPdf($Text);
                            $pdf->setText($Text);
//echo Pdf::encodePdf($pdf);return;
                            $result = Request::routeRequest(
                                                            'POST',
                                                            '/pdf',
                                                            array(),
                                                            Pdf::encodePdf($pdf),
                                                            $this->_pdf,
                                                            'pdf'
                                                            );

                            // checks the correctness of the query
                            if ( $result['status'] >= 200 &&
                                 $result['status'] <= 299 ){

                                $pdf = File::decodeFile($result['content']);

                                $pdf->setDisplayName($exerciseName.'.pdf');
                                $pdf->setTimeStamp($timestamp);
                                $pdf->setBody(null);

                                if (is_object($pro->getRawSubmission())){
                                    $submission = clone $pro->getRawSubmission();
                                } else
                                    $submission = new Submission();

                                $submission->setFile($pdf);
                                $submission->setExerciseId($eid);
                                $pro->setSubmission($submission);
                            } else {
                                $pro->setStatus(409);
                                $res[] = $pro;
                                $this->app->response->setStatus( 409 );
                                continue;
                            }
                        }
                        #endregion

                        $rawSubmission = $pro->getRawSubmission();
                        $rawFile = $rawSubmission->getFile();
                        $rawFile->setBody(Form::encodeForm($formdata), true);
                        $rawSubmission->setFile($rawFile);
                        $rawSubmission->setExerciseId($eid);
                        $pro->setRawSubmission($rawSubmission);

                        $pro->setStatus(201);
                        $res[] = $pro;
                        continue;
                    }
                }
            }
            $this->app->response->setStatus( 409 );
            $pro->setStatus(409);
            $res[] = $pro;
        }


        if ( !$arr &&
             count( $res ) == 1 ){
            $this->app->response->setBody( Process::encodeProcess( $res[0] ) );

        } else
            $this->app->response->setBody( Process::encodeProcess( $res ) );
    }
}