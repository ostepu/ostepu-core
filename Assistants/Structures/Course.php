<?php 


/**
 * @file Course.php contains the Course class
 */

/**
 * the course structure
 *
 * @author Till Uhlig, Florian Lücke
 */
class Course extends Object implements JsonSerializable
{

    /**
     * @var string $id  a string that identifies the course
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
    public function setId( $value )
    {
        $this->id = $value;
    }

    /**
     * @var string $name the name of the course
     */
    private $name = null;

    /**
     * the $name setter
     *
     * @param string $value the new value for $name
     */
    public function getName( )
    {
        return $this->name;
    }

    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function setName( $value )
    {
        $this->name = $value;
    }

    /**
     * @var string $semester the semester in which the course is offered
     */
    private $semester = null;

    /**
     * the $semester getter
     *
     * @return the value of $semester
     */
    public function getSemester( )
    {
        return $this->semester;
    }

    /**
     * the $semester setter
     *
     * @param string $value the new value for $semester
     */
    public function setSemester( $value )
    {
        $this->semester = $value;
    }

    /**
     * @var string[] $exerciseSheets  a set of ids of exercise sheets that belong to this course
     */
    private $exerciseSheets = array( );

    /**
     * the $exerciseSheets getter
     *
     * @return the value of $exerciseSheets
     */
    public function getExerciseSheets( )
    {
        return $this->exerciseSheets;
    }

    /**
     * the $exerciseSheets setter
     *
     * @param string $value the new value for $exerciseSheets
     */
    public function setExerciseSheets( $value )
    {
        $this->exerciseSheets = $value;
    }

    /**
     * @var int $defaultGroupSize the default size of groups in the course
     */
    private $defaultGroupSize = null;

    /**
     * the $defaultGroupSize getter
     *
     * @return the value of $defaultGroupSize
     */
    public function getDefaultGroupSize( )
    {
        return $this->defaultGroupSize;
    }

    /**
     * the $defaultGroupSize setter
     *
     * @param int $value the new value for $defaultGroupSize
     */
    public function setDefaultGroupSize( $value )
    {
        $this->defaultGroupSize = $value;
    }

    /**
     * Creates an Course object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $courseId The id of the course.
     * @param string $name The course name.
     * @param string $semester The semester.
     * @param string $defaultGroupSize The default group size.
     *
     * @return an course object
     */
    public static function createCourse( 
                                        $courseId,
                                        $name,
                                        $semester,
                                        $defaultGroupSize
                                        )
    {
        return new Course( array( 
                                 'id' => $courseId,
                                 'name' => $name,
                                 'semester' => $semester,
                                 'defaultGroupSize' => $defaultGroupSize
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
                     'C_id' => 'id',
                     'C_name' => 'name',
                     'C_semester' => 'semester',
                     'C_defaultGroupSize' => 'defaultGroupSize',
                     'C_exerciseSheets' => 'exerciseSheets'
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
                                 'C_id',
                                 DBJson::mysql_real_escape_string( $this->id )
                                 );
        if ( $this->name != null )
            $this->addInsertData( 
                                 $values,
                                 'C_name',
                                 DBJson::mysql_real_escape_string( $this->name )
                                 );
        if ( $this->semester != null )
            $this->addInsertData( 
                                 $values,
                                 'C_semester',
                                 DBJson::mysql_real_escape_string( $this->semester )
                                 );
        if ( $this->defaultGroupSize != null )
            $this->addInsertData( 
                                 $values,
                                 'C_defaultGroupSize',
                                 DBJson::mysql_real_escape_string( $this->defaultGroupSize )
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
        return'C_id';
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
                if ( $key == 'exerciseSheets' ){
                    $this->{
                        $key
                        
                    } = ExerciseSheet::decodeExerciseSheet( 
                                                           $value,
                                                           false
                                                           );
                    
                } else 
                    $this->{
                    $key
                    
                } = $value;
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
    public static function encodeCourse( $data )
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
    public static function decodeCourse( 
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
                $result[] = new Course( $value );
            }
            return $result;
            
        } else 
            return new Course( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->id !== null )
            $list['id'] = $this->id;
        if ( $this->name !== null )
            $list['name'] = $this->name;
        if ( $this->semester !== null )
            $list['semester'] = $this->semester;
        if ( $this->exerciseSheets !== array( ) )
            $list['exerciseSheets'] = $this->exerciseSheets;
        if ( $this->defaultGroupSize !== null )
            $list['defaultGroupSize'] = $this->defaultGroupSize;
            
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractCourse( 
                                         $data,
                                         $singleResult = false,
                                         $CourseExtension = '',
                                         $SheetExtension = '',
                                         $isResult = true
                                         )
    {

        // generates an assoc array of courses by using a defined list of
        // its attributes
        $courses = DBJson::getObjectsByAttributes( 
                                                  $data,
                                                  Course::getDBPrimaryKey( ),
                                                  Course::getDBConvert( ),
                                                  $CourseExtension
                                                  );

        // generates an assoc array of exercise sheets by using a defined list of
        // its attributes
        $exerciseSheets = DBJson::getObjectsByAttributes( 
                                                         $data,
                                                         ExerciseSheet::getDBPrimaryKey( ),
                                                         array( ExerciseSheet::getDBPrimaryKey( ) => ExerciseSheet::getDBConvert( )[ExerciseSheet::getDBPrimaryKey( )] ),
                                                         $SheetExtension
                                                         );

        // concatenates the courses and the associated exercise sheet IDs
        $res = DBJson::concatResultObjectListAsArray( 
                                                     $data,
                                                     $courses,
                                                     Course::getDBPrimaryKey( ),
                                                     Course::getDBConvert( )['C_exerciseSheets'],
                                                     $exerciseSheets,
                                                     ExerciseSheet::getDBPrimaryKey( ),
                                                     $SheetExtension,
                                                     $CourseExtension
                                                     );
        if ($isResult){ 
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

