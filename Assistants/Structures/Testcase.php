<?php 
/**
 * @file Testcase.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2015-2016
 */

// fügt die Objektklasse hinzu, hier sind noch allgemeine Eigenschaften enthalten (Statuscode, Antworttext etc.)
include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * @author Till Uhlig
 * @author Ralf Busch
 * @date 2015
 */
class Testcase extends Object implements JsonSerializable // muss eingebunden werden, damit das Objekt serialisierbar wird
{

    // Attribute sollten stets über getParam und setParam angesprochen werden
    private $testcaseId = null;
    public function getTestcaseId( )
    {
        return $this->testcaseId;
    }
    public function setTestcaseId( $value = null )
    {
        $this->testcaseId = $value;
    }

    // Typ: beschreibt die zu testende Programmiersprache
    private $testcaseType = null;
    public function getTestcaseType( )
    {
        return $this->testcaseType;
    }
    public function setTestcaseType( $value = null )
    {
        $this->testcaseType = $value;
    }

    // Typ: beschreibt die zu testende Programmiersprache
    private $errorsEnabled = null;
    public function getErrorsEnabled( )
    {
        return $this->errorsEnabled;
    }
    public function setErrorsEnabled( $value = null )
    {
        $this->errorsEnabled = $value;
    }

    // input beschreibt die Eingabeparameter
    private $input = array();
    public function getInput( )
    {
        return $this->input;
    }
    public function setInput( $value = array())
    {
        $this->input = $value;
    }

    // Output beschreibt die Muster Ausgabe
    private $output = array();
    public function getOutput( )
    {
        return $this->output;
    }
    public function setOutput( $value = array() )
    {
        $this->output = $value;
    }

     /**
     * @var status $status Der Status zum Testcase:
     * 
     * 0 = nicht bearbeitet
     * 1 = bearbeitet
     * 2 = OK
     * 3 = NO
     */
    private $status = null;
    public function getStatus( )
    {
        return $this->status;
    }
    public function setStatus( $value = null )
    {
        $this->status = $value;
    }

     /**
     * @var Process $process Der Process zum Testcase, entspricht der ID des process.
     */
    private $process = null;
    public function getProcess( )
    {
        return $this->process;
    }
    public function setProcess( $value = null )
    {
        $this->process = $value;
    }

     /**
     * @var Output $runOutput Beschreibt die Ausgabe während des Laufgangs.
     */
    private $runOutput = null;
    public function getRunOutput( )
    {
        return $this->runOutput;
    }
    public function setRunOutput( $value = null )
    {
        $this->runOutput = $value;
    }

     /**
     * @var workDir $workDir  Die auszuführende Datei, entspricht der kompilierten Datei in Submission.
     */
    private $workDir = null;
    public function getWorkDir( )
    {
        return $this->workDir;
    }
    public function setWorkDir( $value = null )
    {
        $this->workDir = $value;
    }

    /**
     * @var file $file  Die auszuführende Datei, entspricht der kompilierten Datei in Submission.
     */
    private $file = null;
    public function getFile( )
    {
        return $this->file;
    }
    public function setFile( $value = null )
    {
        $this->file = $value;
    }


    /**
     * @var Submission $submission Die Einsendung zum jeweiligen Testcase.
     */
    private $submission = null;
    public function getSubmission( )
    {
        return $this->submission;
    }
    public function setSubmission( $value = null )
    {
        $this->submission = $value;
    }

    private $submissionId = null;

     public function getSubmissionId( )
    {
        return $this->submissionId;
    }
    public function setSubmissionId( $value = null )
    {
        $this->submissionId = $value;
    }

    // diese Funktionen sollen das Erstellen neuer Objekte erleichtern, vorallem wenn 
    // die Strukturen aus verschiedenen Strukturen zusammengesetzt wurden und 
    // einzelne Felder für einen Datenbankeintrag benötigt werden
    public static function createTestcase(
                                         $testcaseId = null,
                                         $testcaseType = null,
                                         $input = null,
                                         $output = null,
                                         $status = null,
                                         $process = null,
                                         $runOutput = null,
                                         $workDir = null,
                                         $submissionId = null
                                         )
    {
        return new Testcase( array(
                                    'testcaseId' => $testcaseId,
                                    'testcaseType' => $testcaseType,
                                    'input' => $input,
                                    'output' => $output,
                                    'status' => $status,
                                    'process' => $process,
                                    'runOutput' => $runOutput,
                                    'workDir' => $workDir,
                                    'submission' => new Submission( array( 'id' => $submissionId ) )
                                  ) );
    }

    // wandelt Datenbankfelder namentlich in Objektattribute um 
    public static function getDbConvert( )
    {
        return array( 
                     'OOP_id' => 'testcaseId',
                     'OOP_type' => 'testcaseType',
                     'OOP_input' => 'input',
                     'OOP_output' => 'output',
                     'OOP_status' => 'status',
                     'PRO_id' => 'process',
                     'OOP_runOutput' => 'runOutput',
                     'OOP_workDir' => 'workDir',
                     'OOP_submission' => 'submissionId'
                     );
    }

    // wandelt die gesetzten Attribute des Objekts in eine Zusammenstellung
    // für einen UPDATE oder INSERT Befehl einer MySql Anweisung um
    public function getInsertData( )
    {
        $values = '';

        if ( $this->testcaseId !== null )
            $this->addInsertData( 
                                 $values,
                                 'OOP_id',
                                 DBJson::mysql_real_escape_string( $this->testcaseId )
                                 );

        if ( $this->testcaseType !== null )
            $this->addInsertData( 
                                 $values,
                                 'OOP_type',
                                 DBJson::mysql_real_escape_string( $this->testcaseType )
                                 );

        if ( $this->input !== null )
            $this->addInsertData( 
                                 $values,
                                 'OOP_input',
                                 DBJson::mysql_real_escape_string( json_encode( $this->input ) )
                                 );

        if ( $this->output !== null )
            $this->addInsertData( 
                                 $values,
                                 'OOP_output',
                                 DBJson::mysql_real_escape_string( json_encode( $this->output ) )
                                 );

        if ( $this->status !== null )
            $this->addInsertData( 
                                 $values,
                                 'OOP_status',
                                 DBJson::mysql_real_escape_string( $this->status )
                                 );

        if ( $this->process !== null )
            $this->addInsertData( 
                                 $values,
                                 'PRO_id',
                                 DBJson::mysql_real_escape_string( $this->process->getObjectIdFromProcessId() )
                                 );

        if ( $this->runOutput !== null )
            $this->addInsertData( 
                                 $values,
                                 'OOP_runOutput',
                                 DBJson::mysql_real_escape_string(  $this->runOutput  )
                                 );

        if ( $this->workDir != null )
            $this->addInsertData( 
                                 $values,
                                 'OOP_workDir',
                                 DBJson::mysql_real_escape_string( $this->workDir )
                                 );

        if ( $this->submission !== null && 
             $this->submission->getId( ) !== null )
            $this->addInsertData( 
                                 $values,
                                 'OOP_submission',
                                 DBJson::mysql_real_escape_string( $this->submission->getId( ) )
                                 );

        if ( $values != '' ){
            $values = substr( 
                             $values,
                             1
                             );
        }
        return $values;
    }

    // gibt den primären Datenbankschlüssel (eventuell auch ein array) der Struktur zurück
    public static function getDbPrimaryKey( )
    {
        return'OOP_id';
    }

    // ruft passende set() Funktionen des Objekts auf, um dessen Attribute zu belegen
    public function __construct( $data = array( ) )
    {
        if ( $data == null )
            $data = array( );

        foreach ( $data AS $key => $value ){
            if ( isset( $key ) ){
                if ( $key == 'file' ){
                    $this->{$key} = File::decodeFile($value,false);
                } else if ( $key == 'submission' ){
                    $this->{$key} = Submission::decodeSubmission($value,false);   
                } else if ( $key == 'process' ){
                    $this->{$key} = Process::decodeProcess($value,false);   
                }else {
                    $func = 'set' . strtoupper($key[0]).substr($key,1);
                    $methodVariable = array($this, $func);
                    if (is_callable($methodVariable)){
                        $this->$func($value);
                    } else {
                        $this->{$key} = $value;
                    }
                }
            }
            unset($value);
        }
    }

    // wandelt ein solches Objekt in eine Textdarstellung um (Serialisierung)
    public static function encodeTestcase( $data )
    {
        return json_encode( $data );
    }

    // wandelt die Textdarstellung des Objekts in ein Objekt um (Deserialisierung
    // ,behandelt auch Objektlisten
    public static function decodeTestcase( 
                                                   $data,
                                                   $decode = true
                                                   )
    {
        if ( $decode && 
             $data == null )
            $data = '{}'; // stellt sicher, dass übergebene Daten nicht zu einem Absturz führen

        if ( $decode )
            $data = json_decode( $data );

        $isArray = true;
        if ( !$decode ){
            if ($data !== null){
                reset($data);
                if (current($data)!==false && !is_int(key($data))) {
                    $isArray = false;
                }
            } else {
               $isArray = false; 
            }
        }

        if ( $isArray && is_array( $data ) ){
            $result = array( ); // erzeugt eine Liste von Objekten
            foreach ( $data AS $key => $value ){
                $result[] = new Testcase( $value );
            }
            return $result;
            
        } else // erzeugt ein einzelnes Objekt
            return new Testcase( $data );
    }

    // bereitet die Attribute des Objekts für die 
    // Serialisierung vor (nur belegte Felder sollen übertragen werden)
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->testcaseId !== null )
            $list['testcaseId'] = $this->testcaseId;
        if ( $this->testcaseType !== null )
            $list['testcaseType'] = $this->testcaseType;
        if ( $this->input !== null )
            $list['input'] = $this->input;
        if ( $this->output !== null )
            $list['output'] = $this->output;
        if ( $this->status !== null )
            $list['status'] = $this->status;
        if ( $this->process !== null )
            $list['process'] = $this->process;
        if ( $this->runOutput !== null )
            $list['runOutput'] = $this->runOutput;
        if ( $this->workDir !== null )
            $list['workDir'] = $this->workDir;
        if ( $this->file !== null )
            $list['file'] = $this->file;
        if ( $this->submission !== null && $this->submission !== array())
            $list['submission'] = $this->submission;
        if ( isset($this->submissionId) && $this->submissionId !== null && $this->submissionId !== array())
            $list['submissionId'] = $this->submissionId;
        if ( $this->errorsEnabled !== null )
            $list['errorsEnabled'] = $this->errorsEnabled;
        
        // ruft auch die Serialisierung des darüber liegenden Objekts auf (Object.php)
        return array_merge($list,parent::jsonSerialize( ));
    }

    // wandelt ein assoziatives Array, welches einer Datenbankanfrage entstammt
    // anhand der DBConvert und der Primärschlüssel in Objekte um
    public static function ExtractTestcase( 
                                           $data,
                                           $singleResult = false,
                                           $TestcaseExtension = '',
                                           $SubmissionExtension = '',
                                           $ProcessExtension = '',
                                           $isResult = true
                                          )
    {
        // generates an assoc array of files by using a defined list of
        // its attributes

        $testcases = DBJson::getObjectsByAttributes( 
                                                    $data,
                                                    Testcase::getDBPrimaryKey( ),
                                                    Testcase::getDBConvert( ),
                                                    $TestcaseExtension
                                                    );

        $processes = DBJson::getObjectsByAttributes( 
                                                    $data,
                                                    Process::getDBPrimaryKey( ),
                                                    Process::getDBConvert( ),
                                                    $ProcessExtension.'2'
                                                    );

        foreach ($processes as $key2 => $process) {
            foreach ($process as $key => $value) {
                   if ($key == Process::getDBConvert( )['E_exercise']) {
                        //file_put_contents('php://stderr', print_r("FFFFFFFFFFFFFFFFFFFFFFFFFFFFF\n".Process::getDBConvert( )['E_exercise'].'Id', TRUE));
                        $processes[$key2][Process::getDBConvert( )['E_exercise'].'Id'] = $value;
                        unset($processes[$key2][Process::getDBConvert( )['E_exercise']]);
                   }
                   if ($key == Process::getDBConvert( )['CO_target']) {
                        $processes[$key2][Process::getDBConvert( )['CO_target'].'Id'] = $value;
                        unset($processes[$key2][Process::getDBConvert( )['CO_target']]);
                   }
               }   
        }
        

        // concatenates the testcases and the associated submissions
        $res = DBJson::concatObjectListResult( 
                                                     $data,
                                                     $testcases,
                                                     Testcase::getDBPrimaryKey( ),
                                                     Testcase::getDBConvert( )['PRO_id'],
                                                     $processes,
                                                     Process::getDBPrimaryKey( ),
                                                     $ProcessExtension.'2',
                                                     $TestcaseExtension                                                     
                                                     );

        // concatenates the testcases and the associated submissions
        $res = DBJson::concatObjectListResult( 
                                                     $data,
                                                     $res,
                                                     Testcase::getDBPrimaryKey( ),
                                                     Testcase::getDBConvert( )['PRO_id'],
                                                     $processes,
                                                     Process::getDBPrimaryKey( ),
                                                     $ProcessExtension.'2',
                                                     $TestcaseExtension                                                     
                                                     );
        //file_put_contents('php://stderr', print_r($res, TRUE));
        if ($isResult){ 
            // to reindex
            $res = array_values( $res );
            $res = Testcase::decodeTestcase($res,false);


            /*file_put_contents('php://stderr', print_r(Testcase::decodeTestcase('[{
    "testcaseId": "22",
    "testcaseType": "java",
    "input": "[[\"Text\",\"pups\"]]",
    "output": "[\"Data\",{\"fileId\":\"158\",\"displayName\":\"Hallo.class\",\"address\":\"file\/2\/4\/9\/1a0ce36e5ed8c79802a0450d3a74e3584be12\",\"timeStamp\":\"1453042709\",\"fileSize\":\"412\",\"hash\":\"2491a0ce36e5ed8c79802a0450d3a74e3584be12\",\"mimeType\":\"application\/x-java-applet\"}]",
    "status": "0",
    "process": {
        "processId": "2_3",
        "exerciseId": "1",
        "targetId": "50",
        "parameter": "[{\"testcaseType\":\"compile\",\"input\":[\"java $file\"],\"file\":[{\"fileId\":\"158\",\"displayName\":\"Hallo.class\",\"address\":\"file\\\/2\\\/4\\\/9\\\/1a0ce36e5ed8c79802a0450d3a74e3584be12\",\"timeStamp\":\"1453042709\",\"fileSize\":\"412\",\"hash\":\"2491a0ce36e5ed8c79802a0450d3a74e3584be12\",\"mimeType\":\"application\\\/x-java-applet\"}],\"submission\":{\"file\":[]},\"errorsEnabled\":\"1\"},{\"testcaseType\":\"java\",\"input\":[[\"Text\",\"pups\"]],\"output\":[\"Data\",{\"fileId\":\"158\",\"displayName\":\"Hallo.class\",\"address\":\"file\\\/2\\\/4\\\/9\\\/1a0ce36e5ed8c79802a0450d3a74e3584be12\",\"timeStamp\":\"1453042709\",\"fileSize\":\"412\",\"hash\":\"2491a0ce36e5ed8c79802a0450d3a74e3584be12\",\"mimeType\":\"application\\\/x-java-applet\"}],\"submission\":{\"file\":[]}},{\"testcaseType\":\"java\",\"input\":[[\"Text\",\"ete\"]],\"output\":[\"Data\",\"\"],\"submission\":{\"file\":[]}},{\"testcaseType\":\"java\",\"input\":[[\"Text\",\"sserser\"]],\"output\":[\"Data\",\"\"],\"submission\":{\"file\":[]}}]"
    },
    "workDir": "\/tmp\/30a0c47a0e1196e80239a8f06819a3884ef50a0e9324972",
    "submissionId": "143"
}]'), TRUE));*/
            //$res = Testcase::decodeTestcase($res,false);

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 