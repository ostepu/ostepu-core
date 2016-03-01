<?php
/**
 * @file ApprovalCondition.php contains the ApprovalCondition class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the ApprovalCondition structure
 */
class ApprovalCondition extends Object implements JsonSerializable
{

    /**
     * db id of the approval condition
     *
     * type: string
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

     * The id of the course this condition belongs to.
     *
     * type: string
     */
    private $courseId = null;

    /**
     * the $courseId getter
     *
     * @return the value of $courseId
     */
    public function getCourseId( )
    {
        return $this->courseId;
    }

    /**
     * the $courseId setter
     *
     * @param string $value the new value for $courseId
     */
    public function setCourseId( $value = null )
    {
        $this->courseId = $value;
    }

    /**
     * The id of the exercise type this condition belongs to.
     *
     * type: string
     */
    private $exerciseTypeId = null;

    /**
     * the $exerciseTypeId getter
     *
     * @return the value of $exerciseTypeid
     */
    public function getExerciseTypeId( )
    {
        return $this->exerciseTypeId;
    }

    /**
     * the $exerciseTypeid setter
     *
     * @param string $value the new value for $exerciseTypeId
     */
    public function setExerciseTypeId( $value = null )
    {
        $this->exerciseTypeId = $value;
    }

    /**
     * (description)
     *
     * type: string
     */
    private $percentage = null;

    /**
     * the $percentage getter
     *
     * @return the value of $percentage
     */
    public function getPercentage( )
    {
        return $this->percentage;
    }

    /**
     * the $percentage setter
     *
     * @param string $value the new value for $percentage
     */
    public function setPercentage( $value = null )
    {
        $this->percentage = $value;
    }

    /**
     * Creates an ApprovalCondition object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $approvalConditionId The id of the approval condition.
     * @param string $courseId The id of the course.
     * @param string $exerciseTypeId The id of the exercise type.
     * @param string $percentage the percentage
     *
     * @return an approval condition object.
     */
    public static function createApprovalCondition(
                                                   $approvalConditionId,
                                                   $courseId,
                                                   $exerciseTypeId,
                                                   $percentage
                                                   )
    {
        return new ApprovalCondition( array(
                                            'id' => $approvalConditionId,
                                            'courseId' => $courseId,
                                            'exerciseTypeId' => $exerciseTypeId,
                                            'percentage' => $percentage
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
                     'AC_id' => 'id',
                     'C_id' => 'courseId',
                     'ET_id' => 'exerciseTypeId',
                     'AC_percentage' => 'percentage'
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
                                 'AC_id',
                                 DBJson::mysql_real_escape_string( $this->id )
                                 );
        if ( $this->courseId !== null )
            $this->addInsertData(
                                 $values,
                                 'C_id',
                                 DBJson::mysql_real_escape_string( $this->courseId )
                                 );
        if ( $this->exerciseTypeId !== null )
            $this->addInsertData(
                                 $values,
                                 'ET_id',
                                 DBJson::mysql_real_escape_string( $this->exerciseTypeId )
                                 );
        if ( $this->percentage !== null )
            $this->addInsertData(
                                 $values,
                                 'AC_percentage',
                                 DBJson::mysql_real_escape_string( $this->percentage )
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
        return'AC_id';
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
    public static function encodeApprovalCondition( $data )
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
    public static function decodeApprovalCondition(
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
                $result[] = new ApprovalCondition( $value );
            }
            return $result;

        } else
            return new ApprovalCondition( $data );
    }

    /**
     * the json serialize function
     *
     * @return an array to serialize the object
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->id !== null )
            $list['id'] = $this->id;
        if ( $this->courseId !== null )
            $list['courseId'] = $this->courseId;
        if ( $this->exerciseTypeId !== null )
            $list['exerciseTypeId'] = $this->exerciseTypeId;
        if ( $this->percentage !== null )
            $list['percentage'] = $this->percentage;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractApprovalCondition(
                                                    $data,
                                                    $singleResult = false,
                                                    $ApprovalExtension = '',
                                                    $isResult = true
                                                    )
    {

        // generates an assoc array of an approval condition by using a
        // defined list of its attributes
        $res = DBJson::getResultObjectsByAttributes(
                                                    $data,
                                                    ApprovalCondition::getDBPrimaryKey( ),
                                                    ApprovalCondition::getDBConvert( ),
                                                    $ApprovalExtension
                                                    );

        if ($isResult){
            // to reindex
            $res = array_merge( $res );
            $res = ApprovalCondition::decodeApprovalCondition($res,false);

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 