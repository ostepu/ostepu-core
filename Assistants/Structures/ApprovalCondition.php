<?php 


/**
 * @file ApprovalCondition.php contains the ApprovalCondition class
 */

/**
 * the ApprovalCondition structure
 *
 * @author Till Uhlig
 * @date 2013-2014
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
    public function getInsertData( )
    {
        $values = '';

        if ( $this->id != null )
            $this->addInsertData( 
                                 $values,
                                 'AC_id',
                                 DBJson::mysql_real_escape_string( $this->id )
                                 );
        if ( $this->courseId != null )
            $this->addInsertData( 
                                 $values,
                                 'C_id',
                                 DBJson::mysql_real_escape_string( $this->courseId )
                                 );
        if ( $this->exerciseTypeId != null )
            $this->addInsertData( 
                                 $values,
                                 'ET_id',
                                 DBJson::mysql_real_escape_string( $this->exerciseTypeId )
                                 );
        if ( $this->percentage != null )
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
        return $values;
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
        if ( is_array( $data ) ){
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

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 
?>

