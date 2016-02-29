<?php
/**
 * @file ExerciseFileType.php contains the ExerciseFileType class
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2014
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the exercise file type structure
 */
class ExerciseFileType extends Object implements JsonSerializable
{

    /**
     * db id of the exercise file type
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
     * the mime type
     *
     * type: string
     */
    private $text = null;

    /**
     * the $text getter
     *
     * @return the value of $name
     */
    public function getText( )
    {
        return $this->text;
    }

    /**
     * the $text setter
     *
     * @param string $value the new value for $text
     */
    public function setText( $value = null )
    {
        $this->text = $value;
    }

    /**
     * the exercise id
     *
     * type: string
     */
    private $exerciseId = null;

    /**
     * the $exerciseId getter
     *
     * @return the value of $name
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
     * Creates an ExerciseFileType object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $typeid The id of the exercise file type.
     * @param string $text The text which specifies the mime type of the file.
     * @param string $exerciseId The exercise id.
     *
     * @return an exercise type object
     */
    public static function createExerciseFileType(
                                                  $typeid,
                                                  $text,
                                                  $exerciseId
                                                  )
    {
        return new ExerciseFileType( array(
                                           'id' => $typeid,
                                           'text' => $text,
                                           'exerciseId' => $exerciseId
                                           ) );
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
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert( )
    {
        return array(
                     'EFT_id' => 'id',
                     'EFT_text' => 'text',
                     'E_id' => 'exerciseId'
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

        if ( $this->id != null )
            $this->addInsertData(
                                 $values,
                                 'EFT_id',
                                 DBJson::mysql_real_escape_string( $this->id )
                                 );
        if ( $this->text != null )
            $this->addInsertData(
                                 $values,
                                 'EFT_text',
                                 DBJson::mysql_real_escape_string( $this->text )
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
        return'EFT_id';
    }

    /**
     * encodes an object to json
     *
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeExerciseFileType( $data )
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
    public static function decodeExerciseFileType(
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
                $result[] = new ExerciseFileType( $value );
            }
            return $result;

        } else
            return new ExerciseFileType( $data );
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
        if ( $this->text !== null )
            $list['text'] = $this->text;
        if ( $this->exerciseId !== null )
            $list['exerciseId'] = $this->exerciseId;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractExerciseFileType(
                                                   $data,
                                                   $singleResult = false,
                                                   $FileTypeExtension = '',
                                                   $isResult = true
                                                   )
    {

        // generates an assoc array of an exercise file type by using a defined
        // list of its attributes
        $res = DBJson::getResultObjectsByAttributes(
                                                    $data,
                                                    ExerciseFileType::getDBPrimaryKey( ),
                                                    ExerciseFileType::getDBConvert( ),
                                                    $FileTypeExtension
                                                    );
        if ($isResult){
            // to reindex
            $res = array_merge( $res );
            $res = ExerciseFileType::decodeExerciseFileType($res,false);

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 