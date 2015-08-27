<?php 

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
                                         $fileId = null,
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
                                    'file' => new File( array( 'fileId' => $fileId ) ),
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
                     'OOP_file' => 'file',
                     'OOP_submission' => 'submission'
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
                                 DBJson::mysql_real_escape_string( $this->input )
                                 );

        if ( $this->output !== null )
            $this->addInsertData( 
                                 $values,
                                 'OOP_output',
                                 DBJson::mysql_real_escape_string( $this->output )
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
                                 DBJson::mysql_real_escape_string( $this->process )
                                 );

        if ( $this->runOutput !== null )
            $this->addInsertData( 
                                 $values,
                                 'OOP_runOutput',
                                 DBJson::mysql_real_escape_string( $this->runOutput )
                                 );

        if ( $this->file != null && 
             $this->file->getFileId( ) !== null )
            $this->addInsertData( 
                                 $values,
                                 'F_id_file',
                                 DBJson::mysql_real_escape_string( $this->file->getFileId( ) )
                                 );

        if ( $this->submission !== null && 
             $this->submission->getId( ) !== null )
            $this->addInsertData( 
                                 $values,
                                 'S_id',
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
                } else {
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
        if ( is_array( $data ) ){
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
        if ( $this->file !== null )
            $list['file'] = $this->file;
        if ( $this->submission !== null && $this->submission !== array())
            $list['submission'] = $this->submission;
        
        // ruft auch die Serialisierung des darüber liegenden Objekts auf (Object.php)
        return array_merge($list,parent::jsonSerialize( ));
    }

    // wandelt ein assoziatives Array, welches einer Datenbankanfrage entstammt
    // anhand der DBConvert und der Primärschlüssel in Objekte um
    public static function ExtractTestcase( 
                                           $data,
                                           $singleResult = false,
                                           $FileExtension = '',
                                           $TestcaseExtension = '',
                                           $SubmissionExtension = '',
                                           $isResult = true
                                          )
    {
        // generates an assoc array of files by using a defined list of
        // its attributes
        $files = DBJson::getObjectsByAttributes( 
                                                $data,
                                                File::getDBPrimaryKey( ),
                                                File::getDBConvert( ),
                                                $FileExtension
                                                );

        $testcases = DBJson::getResultObjectsByAttributes( 
                                                    $data,
                                                    Testcase::getDBPrimaryKey( ),
                                                    Testcase::getDBConvert( ),
                                                    $TestcaseExtension
                                                    );

        // generates an assoc array of a submission by using a defined
        // list of its attributes
        $submissions = DBJson::getObjectsByAttributes( 
                                                      $data,
                                                      Submission::getDBPrimaryKey( ),
                                                      Submission::getDBConvert( ),
                                                      $SubmissionExtension
                                                      );

        // concatenates the markings and the associated files
        $res = DBJson::concatObjectListsSingleResult( 
                                                     $data,
                                                     $testcases,
                                                     Testcase::getDBPrimaryKey( ),
                                                     Testcase::getDBConvert( )['OOP_file'],
                                                     $files,
                                                     File::getDBPrimaryKey( ),
                                                     $FileExtension,
                                                     $TestcaseExtension       
                                                     );

        // concatenates the markings and the associated submissions
        $res = DBJson::concatObjectListsSingleResult( 
                                                     $data,
                                                     $res,
                                                     Testcase::getDBPrimaryKey( ),
                                                     Testcase::getDBConvert( )['OOP_submission'],
                                                     $submissions,
                                                     Submission::getDBPrimaryKey( ),
                                                     $SubmissionExtension,
                                                     $TestcaseExtension                                                     
                                                     );

        if ($isResult){ 
            // to reindex
            $res = array_values( $res );

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 