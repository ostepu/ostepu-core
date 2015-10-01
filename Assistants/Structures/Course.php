<?php 


/**
 * @file Course.php contains the Course class
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the course structure
 *
 * @author Till Uhlig
 * @author Florian Lücke
 * @date 2013-2014
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
    public function setId( $value = null )
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
    public function setName( $value = null )
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
    public function setSemester( $value = null )
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
    public function setExerciseSheets( $value = array( ) )
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
    public function setDefaultGroupSize( $value = null )
    {
        $this->defaultGroupSize = $value;
    }

    /**
     * @var int $defaultGroupSize the default size of groups in the course
     */
    private $settings = null;

    /**
     * the $settings getter
     *
     * @return the value of $settings
     */
    public function getSettings( )
    {
        return $this->settings;
    }

    /**
     * the $settings setter
     *
     * @param int $value the new value for $settings
     */
    public function setSettings( $value = null )
    {
        $this->settings = $value;
    }
    
    
    public function containsSetting( $obj, $settingName )
    {
        if ($obj === null) return null;
        $settings = $obj->getSettings();
        if ($settings === null) return null;
        
        $settingName = strtoupper($settingName);
        
        foreach ($settings as $set){
            
            if (strtoupper($set->getName()) == $settingName)
                return $set->getState();
        }
        
        return null;
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
                     'C_exerciseSheets' => 'exerciseSheets',
                     'C_settings' => 'settings'
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
                                 'C_id',
                                 DBJson::mysql_real_escape_string( $this->id )
                                 );
        if ( $this->name !== null )
            $this->addInsertData( 
                                 $values,
                                 'C_name',
                                 DBJson::mysql_real_escape_string( $this->name )
                                 );
        if ( $this->semester !== null )
            $this->addInsertData( 
                                 $values,
                                 'C_semester',
                                 DBJson::mysql_real_escape_string( $this->semester )
                                 );
        if ( $this->defaultGroupSize !== null )
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
        return ($doubleEscaped ? DBJson::mysql_real_escape_string($values) : $values);
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
        if ( $data === null )
            $data = array( );

        foreach ( $data AS $key => $value ){
            if ( isset( $key ) ){
                if ( $key === 'settings' ){
                    $this->{
                        $key
                        
                    } = Setting::decodeSetting( 
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
    public static function encodeCourse( $data )
    {
        /*if (is_array($data))reset($data);
        if (gettype($data) !== 'object' && !(is_array($data) && (current($data)===false || gettype(current($data)) === 'object'))){
            $e = new Exception();
            error_log(__FILE__.':'.__LINE__.' no object, '.gettype($data)." given\n".$e->getTraceAsString());            
            return null;
        }
        if ((is_array($data) && (is_array(current($data)) || (current($data)!==false && get_class(current($data)) !== get_called_class()))) || (!is_array($data) && get_class($data) !== get_called_class())){
            $e = new Exception();
            $class = (is_array($data) && is_array(current($data)) ? 'array' : (is_array($data) ? (current($data)!==false ? get_class(current($data)) : 'array') : get_class($data)));
            error_log(__FILE__.':'.__LINE__.' wrong type, '.$class.' given, '.get_called_class()." expected\n".$e->getTraceAsString());
            return null;
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
        if ( $this->settings !== null && $this->settings !== array() )
            $list['settings'] = $this->settings;
            
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
                                                  
        // generates an assoc array of settings by using a defined list of
        // its attributes
        $settings = DBJson::getObjectsByAttributes( 
                                                  $data,
                                                  Setting::getDBPrimaryKey( ),
                                                  Setting::getDBConvert( )
                                                  );

        // generates an assoc array of exercise sheets by using a defined list of
        // its attributes
        $exerciseSheets = DBJson::getObjectsByAttributes( 
                                                         $data,
                                                         ExerciseSheet::getDBPrimaryKey( ),
                                                         array( ExerciseSheet::getDBPrimaryKey( ) => ExerciseSheet::getDBConvert( )[ExerciseSheet::getDBPrimaryKey( )] ),
                                                         $SheetExtension
                                                         );
                                                         
        // concatenates the courses and the associated settings
        $res = DBJson::concatObjectListResult( 
                                                     $data,
                                                     $courses,
                                                     Course::getDBPrimaryKey( ),
                                                     Course::getDBConvert( )['C_settings'],
                                                     $settings,
                                                     Setting::getDBPrimaryKey( )
                                                     );
                                                     
        // concatenates the courses and the associated exercise sheet IDs
        $res = DBJson::concatResultObjectListAsArray( 
                                                     $data,
                                                     $res,
                                                     Course::getDBPrimaryKey( ),
                                                     Course::getDBConvert( )['C_exerciseSheets'],
                                                     $exerciseSheets,
                                                     ExerciseSheet::getDBPrimaryKey( ),
                                                     $SheetExtension,
                                                     $CourseExtension
                                                     );
        if ($isResult){ 
            $res = Course::decodeCourse($res,false);
            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 