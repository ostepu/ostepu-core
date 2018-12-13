<?php
/**
 * @file Submission.php contains the Submission class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 */

include_once ( dirname( __FILE__ ) . '/StructureObject.php' );

/**
 * the submission structure
 */
class Submission extends StructureObject implements JsonSerializable
{

    /**
     * @var string $id The identifier of this submission.
     */
    private $id = null;

    /**
     * the $id getter
     *
     * @return the value of $id
     */
    public function getId( )
    {
        return $this->id;
    }

    /**
     * the $id setter
     *
     * @param string $value the new value for $id
     */
    public function setId( $value = null )
    {
        $this->id = $value;
    }

    /**
     * @var string $studentId The id of the student that submitted his solution.
     */
    private $studentId = null;

    /**
     * the $studentId getter
     *
     * @return the value of $studentId
     */
    public function getStudentId( )
    {
        return $this->studentId;
    }

    /**
     * the $studentId setter
     *
     * @param string $value the new value for $studentId
     */
    public function setStudentId( $value = null )
    {
        $this->studentId = $value;
    }

    /**
     * @var string $exerciseId a string that identifies the exercise this submission belongs to.
     */
    private $exerciseId = null;

    /**
     * the $exerciseId getter
     *
     * @return the value of $exerciseId
     */
    public function getExerciseId( )
    {
        return $this->exerciseId;
    }

    /**
     * the $exerciseId setter
     *
     * @param string $value the new value for $exerciseId
     */
    public function setExerciseId( $value = null )
    {
        $this->exerciseId = $value;
    }

   
    /**
     * @var string $exerciseSheetId a string that identifies the exercise this submission belongs to.
     */
    private $exerciseSheetId = null;

    /**
     * the $exerciseSheetId getter
     *
     * @return the value of $exerciseSheetId
     */
    public function getExerciseSheetId( )
    {
        return $this->exerciseSheetId;
    }

    /**
     * the $exerciseSheetId setter
     *
     * @param string $value the new value for $exerciseSheetId
     */
    public function setExerciseSheetId( $value = null )
    {
        $this->exerciseSheetId = $value;
    }

   
    /**
     * @var string $comment A comment that a student made on his submission.
     */
    private $comment = null;

    /**
     * the $comment getter
     *
     * @return the value of $comment
     */
    public function getComment( )
    {
        return $this->comment;
    }

    /**
     * the $comment setter
     *
     * @param string $value the new value for $comment
     */
    public function setComment( $value = null )
    {
        $this->comment = $value;
    }

    /**
     * @var File $file A students submission.
     */
    private $file = null;

    /**
     * the $file getter
     *
     * @return the value of $file
     */
    public function getFile( )
    {
        return $this->file;
    }

    /**
     * the $file setter
     *
     * @param file $value the new value for $file
     */
    public function setFile( $value = null )
    {
        $this->file = $value;
    }

    /**
     * @var int $hideFile Determines whether a submission file is displayed.
     */
    private $hideFile = null;

    /**
     * the $hideFile getter
     *
     * @return the value of $hideFile
     */
    public function getHideFile( )
    {
        return $this->hideFile;
    }

    /**
     * the $hideFile setter
     *
     * @param hideFile $value the new value for $hideFile
     */
    public function setHideFile( $value = null )
    {
        $this->hideFile = $value;
    }

    /**
     * @var bool $accepted If the submission has been accepted for marking.
     */
    private $accepted = null;

    /**
     * the $accepted getter
     *
     * @return the value of $accepted
     */
    public function getAccepted( )
    {
        return $this->accepted;
    }

    /**
     * the $accepted setter
     *
     * @param bool $value the new value for $accepted
     */
    public function setAccepted( $value = null )
    {
        $this->accepted = $value;
    }

    /**
     * @var bool $selectedForGroup If the submission has been selected as submission for the user's group
     */
    private $selectedForGroup = null;

    /**
     * the $selectedForGroup getter
     *
     * @return the value of $selectedForGroup
     */
    public function getSelectedForGroup( )
    {
        return $this->selectedForGroup;
    }

    /**
     * the $selectedForGroup setter
     *
     * @param string $value the new value for $selectedForGroup
     */
    public function setSelectedForGroup( $value = null )
    {
        $this->selectedForGroup = $value;
    }

    /**
     * @var date $date the date on which the submission was uploaded
     */
    private $date = null;

    /**
     * the $date getter
     *
     * @return the value of $date
     */
    public function getDate( )
    {
        return $this->date;
    }

    /**
     * the $date setter
     *
     * @param date $value the new value for $date
     */
    public function setDate( $value = null )
    {
        $this->date = $value;
    }

    /**
     * @var int $exerciseNumber a human readable exercise number
     */
    private $exerciseNumber = null;

    /**
     * the $exerciseNumber getter
     *
     * @return the value of $exerciseNumber
     */
    public function getExerciseNumber( )
    {
        return $this->exerciseNumber;
    }

    /**
     * the $exerciseNumber setter
     *
     * @param int $value the new value for $exerciseNumber
     */
    public function setExerciseNumber( $value = null )
    {
        $this->exerciseNumber = $value;
    }

    /**
     * @var int $flag a status flag for submissions, like deleted
     */
    private $flag = null;

    /**
     * the $flag getter
     *
     * @return the value of $flag
     */
    public function getFlag( )
    {
        return $this->flag;
    }

    /**
     * the $flag setter
     *
     * @param int $value the new value for $flag
     */
    public function setFlag( $value = null )
    {
        $this->flag = $value;
    }

    /**
     * @deprecated
     * @var int $leaderId the id of the group leader
     * @description es ist nicht garantiert, dass dieses Feld korrekt ist (NIEMALS NUTZEN)
     */
    private $leaderId = null;

    /**
     * the $leaderId getter
     *
     * @return the value of $flag
     */
    public function getLeaderId( )
    {
        return $this->leaderId;
    }

    /**
     * the $leaderId setter
     *
     * @param int $value the new value for $leaderId
     */
    public function setLeaderId( $value = null )
    {
        $this->leaderId = $value;
    }

    private $exerciseName = null;
    public function getExerciseName( )
    {
        return $this->exerciseName;
    }
    public function setExerciseName( $value = null )
    {
        $this->exerciseName = $value;
    }

    /**
     * Creates an Submission object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $submissionId The id of the submission.
     * @param string $studentId The id of the student(User).
     * @param string $fileId The id of the file.
     * @param string $exerciseId The id of the exercise.
     * @param string $comment The student comment.
     * @param string $accepted The accepted flag.
     * @param string $date The storing date.
     * @param string $flag The submission flag.
     * @param string $leaderId The group leader.
     *
     * @return an submission object
     */
    public static function createSubmission(
                                            $submissionId,
                                            $studentId,
                                            $fileId,
                                            $exerciseId,
                                            $comment,
                                            $accepted,
                                            $date,
                                            $flag,
                                            $leaderId = null,
                                            $hideFile = null
                                            )
    {
        return new Submission( array(
                                     'id' => $submissionId,
                                     'studentId' => $studentId,
                                     'exerciseId' => $exerciseId,
                                     'comment' => $comment,
                                     'accepted' => $accepted,
                                     'date' => $date,
                                     'flag' => $flag,
                                     'leaderId' => $leaderId,
                                     'hideFile' => $hideFile,
                                     'file' => new File( array( 'fileId' => $fileId ) )
                                     ) );
    }

    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert( )
    {
        return array(
                     'S_id' => 'id',
                     'U_id' => 'studentId',
                     'S_file' => 'file',
                     'E_id' => 'exerciseId',
                     'ES_id' => 'exerciseSheetId',
                     'S_comment' => 'comment',
                     'S_accepted' => 'accepted',
                     'S_date' => 'date',
                     'S_flag' => 'flag',
                     'S_leaderId' => 'leaderId',
                     'S_hideFile' => 'hideFile',
                     'S_selected' => 'selectedForGroup'
                     );
    }

    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData( $doubleEscaped=false )
    {
        $values = '';

        if ( $this->id !== null )
            $this->addInsertData(
                                 $values,
                                 'S_id',
                                 DBJson::mysql_real_escape_string( $this->id )
                                 );
        if ( $this->studentId !== null )
            $this->addInsertData(
                                 $values,
                                 'U_id',
                                 DBJson::mysql_real_escape_string( $this->studentId )
                                 );
        if ( $this->file!=array() && $this->file !== null )
            $this->addInsertData(
                                 $values,
                                 'F_id_file',
                                 DBJson::mysql_real_escape_string( $this->file->getFileId( ) )
                                 );
        if ( $this->exerciseId !== null )
            $this->addInsertData(
                                 $values,
                                 'E_id',
                                 DBJson::mysql_real_escape_string( $this->exerciseId )
                                 );
        if ( $this->comment !== null )
            $this->addInsertData(
                                 $values,
                                 'S_comment',
                                 DBJson::mysql_real_escape_string( $this->comment )
                                 );
        if ( $this->accepted !== null )
            $this->addInsertData(
                                 $values,
                                 'S_accepted',
                                 DBJson::mysql_real_escape_string( $this->accepted )
                                 );
        if ( $this->date !== null )
            $this->addInsertData(
                                 $values,
                                 'S_date',
                                 DBJson::mysql_real_escape_string( $this->date )
                                 );

        // if ($this->selectedForGroup != null) $this->addInsertData($values, 'S_selected', DBJson::mysql_real_escape_string($this->selectedForGroup));
        if ( $this->flag !== null )
            $this->addInsertData(
                                 $values,
                                 'S_flag',
                                 DBJson::mysql_real_escape_string( $this->flag )
                                 );
        if ( $this->leaderId !== null )
            $this->addInsertData(
                                 $values,
                                 'S_leaderId',
                                 DBJson::mysql_real_escape_string( $this->leaderId )
                                 );
        if ( $this->hideFile !== null )
            $this->addInsertData(
                                 $values,
                                 'S_hideFile',
                                 DBJson::mysql_real_escape_string( $this->hideFile )
                                 );

        if ( $values != '' ){
            $values = substr(
                             $values,
                             1
                             );
        }
        return ($doubleEscaped ? DBJson::mysql_real_escape_string($values) : $values);
    }

    /**
     * returns a sting/string[] of the database primary key/keys
     *
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey( )
    {
        return'S_id';
    }

    /**
     * the constructor
     *
     * @param $data an assoc array with the object informations
     */
    public function __construct( $data = array( ) )
    {
        if ( $data === null )
            $data = array( );

        foreach ( $data AS $key => $value ){
            if ( isset( $key ) ){
                if ( $key == 'file' ){
                    $this->{
                        $key

                    } = File::decodeFile(
                                         $value,
                                         false
                                         );

                } else {
                    $func = 'set' . strtoupper($key[0]).substr($key,1);
                    $methodVariable = array($this, $func);
                    if (is_callable($methodVariable)){
                        $this->$func($value);
                    } else
                        $this->{$key} = $value;
                }
            }
        }
    }

    /**
     * encodes an object to json
     *
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeSubmission( $data )
    {
        /*if (is_array($data))reset($data);
        if (gettype($data) !== 'object' && !(is_array($data) && (current($data)===false || gettype(current($data)) === 'object'))){
            $e = new Exception();
            error_log(__FILE__.':'.__LINE__.' no object, '.gettype($data)." given\n".$e->getTraceAsString());           
            ///return null;
        }
        if ((is_array($data) && (is_array(current($data)) || (current($data)!==false && get_class(current($data)) !== get_called_class()))) || (!is_array($data) && get_class($data) !== get_called_class())){
            $e = new Exception();
            $class = (is_array($data) && is_array(current($data)) ? 'array' : (is_array($data) ? (current($data)!==false ? get_class(current($data)) : 'array') : get_class($data)));
            error_log(__FILE__.':'.__LINE__.' wrong type, '.$class.' given, '.get_called_class()." expected\n".$e->getTraceAsString());
            ///return null;
        }*/
        return json_encode( $data );
    }

    /**
     * decodes $data to an object
     *
     * @param string $data json encoded data (decode=true)
     * or json decoded data (decode=false)
     * @param bool $decode specifies whether the data must be decoded
     *
     * @return the object
     */
    public static function decodeSubmission(
                                            $data,
                                            $decode = true
                                            )
    {
        if ( $decode &&
             $data == null )
            $data = '{}';

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
            $result = array( );
            foreach ( $data AS $key => $value ){
                $result[] = new Submission( $value );
            }
            return $result;

        } else
            return new Submission( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->id !== null )
            $list['id'] = $this->id;
        if ( $this->studentId !== null )
            $list['studentId'] = $this->studentId;
        if ( $this->exerciseId !== null )
            $list['exerciseId'] = $this->exerciseId;
        if ( $this->exerciseSheetId !== null )
            $list['exerciseSheetId'] = $this->exerciseSheetId;
        if ( $this->comment !== null )
            $list['comment'] = $this->comment;
        if ( $this->file !== null && $this->file !== array() )
            $list['file'] = $this->file;
        if ( $this->accepted !== null )
            $list['accepted'] = $this->accepted;
        if ( $this->selectedForGroup !== null )
            $list['selectedForGroup'] = $this->selectedForGroup;
        if ( $this->date !== null )
            $list['date'] = $this->date;
        if ( $this->exerciseNumber !== null )
            $list['exerciseNumber'] = $this->exerciseNumber;
        if ( $this->flag !== null )
            $list['flag'] = $this->flag;
        if ( $this->leaderId !== null )
            $list['leaderId'] = $this->leaderId;
        if ( $this->hideFile !== null )
            $list['hideFile'] = $this->hideFile;
        if ( $this->exerciseName !== null )
            $list['exerciseName'] = $this->exerciseName;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractSubmission(
                                             $data,
                                             $singleResult = false,
                                             $FileExtension = '',
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

        // generates an assoc array of submissions by using a defined list of
        // its attributes
        $submissions = DBJson::getObjectsByAttributes(
                                                      $data,
                                                      Submission::getDBPrimaryKey( ),
                                                      Submission::getDBConvert( ),
                                                      $SubmissionExtension
                                                      );

        // sets the selectedForGroup attribute
        foreach ( $submissions as & $submission ){
            if ( isset( $submission['selectedForGroup'] ) ){
                if ( isset( $submission['id'] ) &&
                     $submission['id'] == $submission['selectedForGroup'] ){
                    $submission['selectedForGroup'] = ( string )1;

                } else
                    unset( $submission['selectedForGroup'] );
            }
        }

        // concatenates the submissions and the associated files
        $res = DBJson::concatObjectListsSingleResult(
                                                     $data,
                                                     $submissions,
                                                     Submission::getDBPrimaryKey( ),
                                                     Submission::getDBConvert( )['S_file'],
                                                     $files,
                                                     File::getDBPrimaryKey( ),
                                                     $FileExtension,
                                                     $SubmissionExtension
                                                     );

        if ($isResult){
            // to reindex
            $res = array_values( $res );
            $res = Submission::decodeSubmission($res,false);

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 