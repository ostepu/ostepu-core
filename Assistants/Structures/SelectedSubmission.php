<?php
/**
 * @file SelectedSubmission.php contains the SelectedSubmission class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

include_once ( dirname( __FILE__ ) . '/StructureObject.php' );

/**
 * the selected submission structure
 */
class SelectedSubmission extends StructureObject implements JsonSerializable
{

    /**
     * @var string $leaderId The identifier of the group leader.
     */
    private $leaderId = null;

    /**
     * the $leaderId getter
     *
     * @return the value of $leaderId
     */
    public function getLeaderId( )
    {
        return $this->leaderId;
    }

    /**
     * the $leaderid setter
     *
     * @param string $value the new value for $leaderId
     */
    public function setLeaderId( $value = null )
    {
        $this->leaderId = $value;
    }

    /**
     * @var string $submissionId The id of the selected submission.
     */
    private $submissionId = null;

    /**
     * the $submissionId getter
     *
     * @return the value of $submissionId
     */
    public function getSubmissionId( )
    {
        return $this->submissionId;
    }

    /**
     * the $submissionId setter
     *
     * @param string $value the new value for $submissionId
     */
    public function setSubmissionId( $value = null )
    {
        $this->submissionId = $value;
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
     * @var string $exerciseSheetId a string that identifies the exercisesheet this submission belongs to.
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
     * Creates an SelectedSubmission object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $leaderId The id of the leader(User).
     * @param string $submissionId The id of the submission.
     * @param string $exerciseId The id of the exercise.
     *
     * @return an selected submission object
     */
    public static function createSelectedSubmission(
                                                    $leaderId,
                                                    $submissionId,
                                                    $exerciseId
                                                    )
    {
        return new SelectedSubmission( array(
                                             'leaderId' => $leaderId,
                                             'submissionId' => $submissionId,
                                             'exerciseId' => $exerciseId
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
                     'U_id_leader' => 'leaderId',
                     'S_id_selected' => 'submissionId',
                     'E_id' => 'exerciseId',
                     'ES_id' => 'exerciseSheetId'
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

        if ( $this->leaderId != null )
            $this->addInsertData(
                                 $values,
                                 'U_id_leader',
                                 DBJson::mysql_real_escape_string( $this->leaderId )
                                 );
        if ( $this->submissionId != null )
            $this->addInsertData(
                                 $values,
                                 'S_id_selected',
                                 DBJson::mysql_real_escape_string( $this->submissionId )
                                 );
        if ( $this->exerciseId != null )
            $this->addInsertData(
                                 $values,
                                 'E_id',
                                 DBJson::mysql_real_escape_string( $this->exerciseId )
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
        return array(
                     'U_id_leader',
                     'S_id_selected'
                     );
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
                $func = 'set' . strtoupper($key[0]).substr($key,1);
                $methodVariable = array($this, $func);
                if (is_callable($methodVariable)){
                    $this->$func($value);
                } else
                    $this->{$key} = $value;
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
    public static function encodeSelectedSubmission( $data )
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
    public static function decodeSelectedSubmission(
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
                $result[] = new SelectedSubmission( $value );
            }
            return $result;

        } else
            return new SelectedSubmission( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->leaderId !== null )
            $list['leaderId'] = $this->leaderId;
        if ( $this->submissionId !== null )
            $list['submissionId'] = $this->submissionId;
        if ( $this->exerciseId !== null )
            $list['exerciseId'] = $this->exerciseId;
        if ( $this->exerciseSheetId !== null )
            $list['exerciseSheetId'] = $this->exerciseSheetId;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractSelectedSubmission(
                                                     $data,
                                                     $singleResult = false,
                                                     $SelectedSubmissionExtension = '',
                                                     $isResult = true
                                                     )
    {

        // generates an assoc array of selected entry's by using a defined list of
        // its attributes
        $res = DBJson::getResultObjectsByAttributes(
                                                    $data,
                                                    SelectedSubmission::getDBPrimaryKey( ),
                                                    SelectedSubmission::getDBConvert( ),
                                                    $SelectedSubmissionExtension
                                                    );
        if ($isResult){
            $res = SelectedSubmission::decodeSelectedSubmission($res,false);

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 