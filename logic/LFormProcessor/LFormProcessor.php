<?php
/**
 * @file LFormProcessor.php Contains the LFormProcessor class
 * 
 * @author Till Uhlig
 */

require_once '../../Assistants/Slim/Slim.php';
include_once '../../Assistants/Request.php';
include_once '../../Assistants/CConfig.php';
include_once '../../Assistants/DBJson.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LFormProcessor-Component
 */
class LFormProcessor
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
        return LFormProcessor::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LFormProcessor::$_prefix = $value;
    }

    /**
     * @var Link[] $_pdf a list of links
     */
    private $_pdf = array( );
    
    /**
     * @var Link[] $_formDb a list of links
     */
    private $_formDb = array( );
    
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
        $this->_formDb = CConfig::getLinks($conf->getLinks(),"formDb");
        $this->_pdf = CConfig::getLinks($conf->getLinks(),"pdf");

        // POST PostProcess
        $this->app->map('/'.$this->getPrefix().'(/)',
                        array($this, 'postProcess'))->via('POST');

        // run Slim
        $this->app->run();
    }

    public function ChoiceIdToText($choiceId, $Choices)
    {
        foreach ($Choices as $choice){
            if ($choiceId === $choice->getChoiceId())
                return $choice->getText();
        }
        
        return null;
    }
    
    public function postProcess(){
          
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
                    $formdata = Form::decodeForm(base64_decode($formdata->getBody()));
                    if (is_array($formdata)) $formdata = $formdata[0];

                    if ($formdata !== null){
                        // check the submission
                        $fail = false;
                        $parameter = explode(' ',strtolower($pro->getParameter()));

                        $choices = $formdata->getChoices();
                        
                        if ($forms->getType()==0){
                            foreach ($choices as &$choice){
                                foreach ($parameter as $param){
                                    switch($param){
                                        case('isnumeric'):
                                            if (!is_numeric($choice->getText()))
                                                $fail = true;
                                            break;
                                        case('isdigit'):
                                            if (!ctype_digit($choice->getText()))
                                                $fail = true;
                                            break;
                                        case('isprintable'):
                                            if (!ctype_print($choice->getText()))
                                                $fail = true;
                                            break;
                                        case('isalpha'):
                                            if (!ctype_alpha($choice->getText()))
                                                $fail = true;
                                            break;
                                        case('isalphanum'):
                                            if (!ctype_alnum($choice->getText()))
                                                $fail = true;
                                            break;
                                    }
                                    if ($fail) break;
                                }
                                if ($fail) break;
                            }
                        }
                        
                        if ($fail){
                            // received submission isn't correct
                            $res[] = null;
                            $this->app->response->setStatus( 409 );
                            continue;
                        } 

                        // preprocess the submission
                        if ($forms->getType()==0){
                            $choices = $formdata->getChoices();
                            foreach ($choices as &$choice){
                                foreach ($parameter as $param){
                                    switch($param){
                                        case('lowercase'):
                                            $choice->setText(strtolower($choice->getText()));
                                            break;
                                        case('uppercase'):
                                            $choice->setText(strtoupper($choice->getText()));
                                            break;
                                        case('trim'):
                                            $choice->setText(trim($choice->getText()));
                                            break;
                                    }
                                }
                            }
                            $formdata->setChoices($choices);
                        }
                        
                        // evaluate the formdata
                        $points = 0;
                        $answers = $formdata->getChoices();
                        $correctAnswers = $forms->getChoices();
                        $allcorrect = true;
                        
                        if ($forms->getType()==0){
                            if ($correctAnswers[0]->getText() != $answers[0]->getText())
                                $allcorrect = false;
                        
                        }elseif ($forms->getType()==1){
                            foreach ($correctAnswers as $mask){
                            
                                $foundInStudentsAnswer = false;
                                foreach($answers as $answer){
                                    if ($answer->getText() === $mask->getChoiceId()){
                                        $foundInStudentsAnswer = true;
                                        break;
                                    }
                                }
                            
                                if ($mask->getCorrect()==='1' && !$foundInStudentsAnswer){
                                    $allcorrect = false;
                                    break;
                                } elseif ($mask->getCorrect()==='0' && $foundInStudentsAnswer) {
                                    $allcorrect = false;
                                    break;
                                }
                            }
                        
                        }elseif ($forms->getType()==2){
                            foreach ($correctAnswers as $mask){
                            
                                $foundInStudentsAnswer = false;
                                foreach($answers as $answer){
                                    if ($answer->getText() === $mask->getChoiceId()){
                                        $foundInStudentsAnswer = true;
                                        break;
                                    }
                                }
                            
                                if ($mask->getCorrect()==='1' && !$foundInStudentsAnswer){
                                    $allcorrect = false;
                                    break;
                                } elseif ($mask->getCorrect()==='0' && $foundInStudentsAnswer) {
                                    $allcorrect = false;
                                    break;
                                }
                            }
                        }
                        
                        if ($allcorrect)
                            $points = $pro->getExercise()->getMaxPoints();
                        
                        // save the marking
                        #region Form to PDF
                        if ($pro->getMarking() === null){
                            $raw = $pro->getRawSubmission();
                            $exerciseName = '';
                            
                            if ( $raw !== null )
                                $exerciseName = $raw->getExerciseName();
                            
                            $answer=""; 
                            if ($forms->getType()==0) $answer = DBJson::mysql_real_escape_string($formdata->getChoices()[0]->getText());
                            if ($forms->getType()==1) $answer = $this->ChoiceIdToText(DBJson::mysql_real_escape_string($formdata->getChoices()[0]->getText()), $forms->getChoices());
                            if ($forms->getType()==2)
                                foreach($formdata->getChoices() as $chosen)
                                    $answer.= $this->ChoiceIdToText(DBJson::mysql_real_escape_string($chosen->getText()), $forms->getChoices()).'<br>';
                                 
                            $answer2="";
                                foreach($forms->getChoices() as $chosen)
                                    if ($chosen->getCorrect()==='1')
                                    $answer2.= $chosen->getText().'<br>';
                        
                            $Text=  "<h1>AUFGABE {$exerciseName}</h1>".
                                    "<hr>".
                                    "<p>".
                                    "<h2>Aufgabenstellung:</h2>".
                                    $forms->getTask().
                                    "</p>".
                                    "<p>".
                                    "<h2>Antwort:</h2>".
                                    "<font color='".($points===0 ? 'red' : '')."'>".
                                    $answer.
                                    "</font></p>";
                            if ($points===0){
                                $Text.= "<p>".
                                        "<h2>Lösung:</h2><font color='green'>".
                                        $answer2.
                                        "</font></p>";
                            }
                                    
                            $Text.= "<p>".
                                    "<h2>Lösungsbegründung:</h2>".
                                    $forms->getSolution().
                                    "</p>".
                                    "<p align='right'>".
                                    "<h2><font color='red'>{$points}P</font></h2>".
                                    "</p>";
                                    
                            $pdf = Pdf::createPdf($Text);
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
                                
                                $submission = $pro->getSubmission();
                                if ($submission === null) $submission = $pro->getRawSubmission();
                                
                                $studentId = ($pro->getRawSubmission()!==null ? $pro->getRawSubmission()->getStudentId() : null);
                                
                                if ($studentId===null)
                                    $studentId = ($pro->getSubmission()!==null ? $pro->getSubmission()->getStudentId() : null);
                                
                                $marking = Marking::createMarking( 
                                                                 null,
                                                                 $studentId,
                                                                 null,
                                                                 null,
                                                                 null,
                                                                 null,
                                                                 Marking::getStatusDefinition()[3]['id'],
                                                                 $points,
                                                                 ($submission->getDate()!==null ? $submission->getDate() : time())
                                                                 );
                                if (is_object($submission))
                                    $marking->setSubmission(clone $submission);
                                    
                                $marking->setFile($pdf);
                                $pro->setMarking($marking);
                                
                            } else {
                                $res[] = null;
                                $this->app->response->setStatus( 409 );
                                continue;
                            }
                        }
                        #endregion

                        $rawSubmission = $pro->getRawSubmission();
                        $rawFile = $rawSubmission->getFile();
                        $rawFile->setBody(base64_encode(Form::encodeForm($formdata)));
                        $rawSubmission->setFile($rawFile);
                        $rawSubmission->setExerciseId($eid);
                        $pro->setRawSubmission($rawSubmission);
                        
                        $res[] = $pro;          
                        continue;
                    }
                }                             
            }
            $this->app->response->setStatus( 409 );
            $res[] = null;
        }

 
        if ( !$arr && 
             count( $res ) == 1 ){
            $this->app->response->setBody( Process::encodeProcess( $res[0] ) );
            
        } else 
            $this->app->response->setBody( Process::encodeProcess( $res ) );
    }
}

// get new config data from DB
$com = new CConfig(LFormProcessor::getPrefix());

// create a new instance of LFormProcessor class with the config data
if (!$com->used())
    new LFormProcessor($com->loadConfig());
?>