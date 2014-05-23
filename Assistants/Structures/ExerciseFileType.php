<?php 


/**
 * @file ExerciseFileType.php contains the ExerciseFileType class
 */

/**
 * the exercise file type structure
 *
 * @author Till Uhlig
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
        foreach ( $data AS $key => $value ){
            if ( isset( $key ) ){
                 $key = strtoupper($key[0]).substr($key,1);
                $func = "set".$key;
                $this->$func($value);
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
    public function getInsertData( )
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
        return $values;
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
        if ( is_array( $data ) ){
            $result = array( );
            foreach ( $data AS $key => $value ){
                $result[] = new ExerciseFileType( $value = null );
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

