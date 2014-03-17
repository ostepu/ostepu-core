<?php 


/**
 * @file CourseStatus.php contains the CourseStatus class
 */

/**
 * the course status structure
 *
 * @author Till Uhlig, Florian Lücke
 */
class CourseStatus extends Object implements JsonSerializable
{

    /**
     * @var Course $course A course.
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
     * @param string $value the new value for $course
     */
    public function setCourse( $value )
    {
        $this->course = $value;
    }

    /**
     * @var string $status  a string that defines which status the user has in that course.
     */
    private $status = null;

    /**
     * the $status getter
     *
     * @return the value of $status
     */
    public function getStatus( )
    {
        return $this->status;
    }

    /**
     * the $status setter
     *
     * @param string $value the new value for $status
     */
    public function setStatus( $value )
    {
        $this->status = $value;
    }

    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert( )
    {
        return array( 
                     'CS_course' => 'course',
                     'CS_status' => 'status'
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

        if ( $this->status != null )
            $this->addInsertData( 
                                 $values,
                                 'CS_status',
                                 DBJson::mysql_real_escape_string( $this->status )
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
        return $values;
    }

    /**
     * returns a sting/string[] of the database primary key/keys
     *
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey( )
    {
        return array( 
                     'C_id',
                     'U_id'
                     );
    }

    /**
     * returns an array to get the course status defintions
     */
    public static function getStatusDefinition( )
    {
        return array( 
                     '0' => 'student',
                     '1' => 'tutor',
                     '2' => 'lecturer',
                     '3' => 'administrator',
                     '4' => 'super-administrator'
                     );
    }

    /**
     * the constructor
     *
     * @param $data an assoc array with the object informations
     */
    public function __construct( $data = array( ) )
    {
        if ( $data == null )
            $data = array( );

        foreach ( $data AS $key => $value ){
            if ( isset( $key ) ){
                if ( $key == 'course' ){
                    $this->{
                        $key
                        
                    } = Course::decodeCourse( 
                                             $value,
                                             false
                                             );
                    
                } else {
                    $this->{
                        $key
                        
                    } = $value;
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
    public static function encodeCourseStatus( $data )
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
    public static function decodeCourseStatus( 
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
                $result[] = new CourseStatus( $value );
            }
            return $result;
            
        } else 
            return new CourseStatus( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->course !== null )
            $list['course'] = $this->course;
        if ( $this->status !== null )
            $list['status'] = $this->status;
        return $list;
    }
}

 
?>

