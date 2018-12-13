<?php
/**
 * @file ExternalId.php contains the ExternalId class
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
 * the external id structure
 */
class ExternalId extends StructureObject implements JsonSerializable
{

    /**
     * the db id of the external id
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
     * the corresponding course
     *
     * type: Course
     */
    private $course = null;

    /**
     * the $course getter
     *
     * @return the value of $course
     */
    public function getCourse( )
    {
        return $this->course;
    }

    /**
     * the $course setter
     *
     * @param Course $value the new value for $course
     */
    public function setCourse( $value = null )
    {
        $this->course = $value;
    }

    /**
     * Creates an ExternalId object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $externalId The id of the external id .
     * @param string $courseId The id of the course.
     *
     * @return an external id object
     */
    public static function createExternalId(
                                            $externalId,
                                            $courseId
                                            )
    {
        return new ExternalId( array(
                                     'id' => $externalId,
                                     'course' => array( 'id' => $courseId )
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
                if ( $key == 'course' ){
                    $this->{
                        $key

                    } = new Course(
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
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert( )
    {
        return array(
                     'EX_id' => 'id',
                     'EX_course' => 'course'
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
                                 'EX_id',
                                 DBJson::mysql_real_escape_string( $this->id )
                                 );
        if ( $this->course != null &&
             $this->course->getId( ) != null )
            $this->addInsertData(
                                 $values,
                                 'C_id',
                                 DBJson::mysql_real_escape_string( $this->course->getId( ) )
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
        return'EX_id';
    }

    /**
     * encodes an object to json
     *
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeExternalId( $data )
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
    public static function decodeExternalId(
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
                $result[] = new ExternalId( $value );
            }
            return $result;

        } else
            return new ExternalId( $data );
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
        if ( $this->course !== null )
            $list['course'] = $this->course;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractExternalId(
                                             $data,
                                             $singleResult = false,
                                             $CourseExtension = '',
                                             $ExternalIdExtension = '',
                                             $isResult = true
                                             )
    {

        // generates an assoc array of courses by using a defined list of
        // its attributes
        $course = DBJson::getObjectsByAttributes(
                                                 $data,
                                                 Course::getDBPrimaryKey( ),
                                                 Course::getDBConvert( ),
                                                 $CourseExtension
                                                 );

        // generates an assoc array of external IDs by using a defined list of
        // its attributes
        $externalIds = DBJson::getObjectsByAttributes(
                                                      $data,
                                                      ExternalId::getDBPrimaryKey( ),
                                                      ExternalId::getDBConvert( ),
                                                      $ExternalIdExtension
                                                      );

        // concatenates the external IDs and the associated courses
        $res = DBJson::concatObjectListsSingleResult(
                                                     $data,
                                                     $externalIds,
                                                     ExternalId::getDBPrimaryKey( ),
                                                     ExternalId::getDBConvert( )['EX_course'],
                                                     $course,
                                                     Course::getDBPrimaryKey( ),
                                                     $CourseExtension,
                                                     $ExternalIdExtension
                                                     );
        if ($isResult){
            // to reindex
            $res = array_values( $res );
            $res = ExternalId::decodeExternalId($res,false);

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 