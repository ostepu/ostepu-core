<?php 


/**
 * @file ExerciseSheet.php contains the ExerciseSheet class
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the exercise sheet structure
 *
 * @author Till Uhlig
 * @author Florian Lücke
 * @date 2013-2014
 */
class ExerciseSheet extends Object implements JsonSerializable
{

    /**
     * a string that identifies the exercise sheet
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
     * The id of the course this exercise belongs to.
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
     * the date and time of the last submission
     *
     * type: date
     */
    private $endDate = null;

    /**
     * the $endDate getter
     *
     * @return the value of $endDate
     */
    public function getEndDate( )
    {
        return $this->endDate;
    }

    /**
     * the $endDate setter
     *
     * @param date $value the new value for $endDate
     */
    public function setEndDate( $value = null )
    {
        $this->endDate = $value;
    }

    /**
     * the date and time the exercise sheet is shown to students
     *
     * type: date
     */
    private $startDate = null;

    /**
     * the $startDate getter
     *
     * @return the value of $startDate
     */
    public function getStartDate( )
    {
        return $this->startDate;
    }

    /**
     * the $startDate setter
     *
     * @param date $value the new value for $startDate
     */
    public function setStartDate( $value = null )
    {
        $this->startDate = $value;
    }

    /**
     * a file that contains student submissions that were previosly
     * assinged to a tutor
     *
     * type: File
     */
    private $zipFile = null;

    /**
     * the $zipFile getter
     *
     * @return the value of $id
     */
    public function getZipFile( )
    {
        return $this->zipFile;
    }

    /**
     * the $zipFile setter
     *
     * @param file $value the new value for $zipFile
     */
    public function setZipFile( $value = null )
    {
        $this->zipFile = $value;
    }

    /**
     * file that contains the sample solution
     *
     * type: File
     */
    private $sampleSolution = null;

    /**
     * the $sampleSolution getter
     *
     * @return the value of $sampleSolution
     */
    public function getSampleSolution( )
    {
        return $this->sampleSolution;
    }

    /**
     * the $sampleSolution setter
     *
     * @param file $value the new value for $sampleSolution
     */
    public function setSampleSolution( $value = null )
    {
        $this->sampleSolution = $value;
    }

    /**
     * file that contains the exercise sheet
     *
     * type: File
     */
    private $sheetFile = null;

    /**
     * the $sheetFile getter
     *
     * @return the value of $sheetFile
     */
    public function getSheetFile( )
    {
        return $this->sheetFile;
    }

    /**
     * the $sheetFile setter
     *
     * @param file $value the new value for $sheetFile
     */
    public function setSheetFile( $value = null )
    {
        $this->sheetFile = $value;
    }

    /**
     * a set of exercises that belong to this sheet
     *
     * type: Exercise[]
     */
    private $exercises = array( );

    /**
     * the $exercises getter
     *
     * @return the value of $exercises
     */
    public function getExercises( )
    {
        return $this->exercises;
    }

    /**
     * the $exercises setter
     *
     * @param Exercise[] $value the new value for $exercises
     */
    public function setExercises( $value = array( ) )
    {
        $this->exercises = $value;
    }

    /**
     * the maximum group size that is allowed for this exercise sheet
     *
     * type: int
     */
    private $groupSize = null;

    /**
     * the $groupSize getter
     *
     * @return the value of $groupSize
     */
    public function getGroupSize( )
    {
        return $this->groupSize;
    }

    /**
     * the $groupSize setter
     *
     * @param int $value the new value for $groupSize
     */
    public function setGroupSize( $value = null )
    {
        $this->groupSize = $value;
    }

    private $sheetName = null;

    /**
     * the $sheetName getter
     *
     * @return the value of $sheetName
     */
    public function getSheetName( )
    {
        return $this->sheetName;
    }

    /**
     * the $sheetName setter
     *
     * @param string $value the new value for $sheetName
     */
    public function setSheetName( $value = null )
    {
        $this->sheetName = $value;
    }

    /**
     * Creates an ExerciseSheet object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $sheetId The id of the exercise sheet.
     * @param string $courseId The id of the course.
     * @param string $endDate The end date.
     * @param string $startDate the start date.
     * @param string $groupSize the max group size.
     * @param string $sampleSolutionId the file id of the sample solution.
     * @param string $sheetFileId the file id of the sheet.
     * @param string $sheetName a optional sheet name.
     *
     * @return an exercise sheet object
     */
    public static function createExerciseSheet( 
                                               $sheetId,
                                               $courseId,
                                               $endDate,
                                               $startDate,
                                               $groupSize,
                                               $sampleSolutionId,
                                               $sheetFileId,
                                               $sheetName
                                               )
    {
        return new ExerciseSheet( array( 
                                        'id' => $sheetId,
                                        'courseId' => $courseId,
                                        'endDate' => $endDate,
                                        'startDate' => $startDate,
                                        'groupSize' => $groupSize,
                                        'sheetName' => $sheetName,
                                        'sampleSolution' => new File( array( 'fileId' => $sampleSolutionId ) ),
                                        'sheetFile' => new File( array( 'fileId' => $sheetFileId ) )
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
                     'ES_id' => 'id',
                     'C_id' => 'courseId',
                     'ES_startDate' => 'startDate',
                     'ES_endDate' => 'endDate',
                     'F_id_zip' => 'zipFile',
                     'ES_groupSize' => 'groupSize',
                     'F_id_sampleSolution' => 'sampleSolution',
                     'F_id_file' => 'sheetFile',
                     'ES_exercises' => 'exercises',
                     'ES_name' => 'sheetName'
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
                                 'ES_id',
                                 DBJson::mysql_real_escape_string( $this->id )
                                 );
        if ( $this->courseId !== null )
            $this->addInsertData( 
                                 $values,
                                 'C_id',
                                 DBJson::mysql_real_escape_string( $this->courseId )
                                 );
        if ( $this->endDate !== null )
            $this->addInsertData( 
                                 $values,
                                 'ES_endDate',
                                 DBJson::mysql_real_escape_string( $this->endDate )
                                 );
        if ( $this->startDate !== null )
            $this->addInsertData( 
                                 $values,
                                 'ES_startDate',
                                 DBJson::mysql_real_escape_string( $this->startDate )
                                 );
        if ( $this->groupSize !== null )
            $this->addInsertData( 
                                 $values,
                                 'ES_groupSize',
                                 DBJson::mysql_real_escape_string( $this->groupSize )
                                 );
        if ( $this->sheetName !== null )
            $this->addInsertData( 
                                 $values,
                                 'ES_name',
                                 DBJson::mysql_real_escape_string( $this->sheetName )
                                 );
                                 
        $sFId = null;
        if ( $this->sheetFile!==array() && $this->sheetFile !== null && 
             $this->sheetFile->getFileId( ) !== null )
            $sFId = $this->sheetFile->getFileId( );
            
        $this->addInsertData( 
                             $values,
                             'F_id_file',
                             DBJson::mysql_real_escape_string( $sFId )
                             );
                             
        $sSFId = null;       
        if ( $this->sampleSolution!==array() && $this->sampleSolution !== null && 
             $this->sampleSolution->getFileId( ) !== null )
            $sSFId = $this->sampleSolution->getFileId( );
            
        $this->addInsertData( 
                             $values,
                             'F_id_sampleSolution',
                             DBJson::mysql_real_escape_string( $sSFId )
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
        return'ES_id';
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
                if ( $key == 'exercises' ){
                    $this->{
                        $key
                        
                    } = Exercise::decodeExercise( 
                                                 $value,
                                                 false
                                                 );
                    
                }elseif ( $key == 'sheetFile' || 
                          $key == 'sampleSolution' ){
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
    public static function encodeExerciseSheet( $data )
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
    public static function decodeExerciseSheet( 
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
                $result[] = new ExerciseSheet( $value );
            }
            return $result;
            
        } else 
            return new ExerciseSheet( $data );
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
        if ( $this->endDate !== null )
            $list['endDate'] = $this->endDate;
        if ( $this->startDate !== null )
            $list['startDate'] = $this->startDate;
        if ( $this->zipFile !== null )
            $list['zipFile'] = $this->zipFile;
        if ( $this->sampleSolution !== null )
            $list['sampleSolution'] = $this->sampleSolution;
        if ( $this->sheetFile !== null )
            $list['sheetFile'] = $this->sheetFile;
        if ( $this->exercises !== array( ) )
            $list['exercises'] = $this->exercises;
        if ( $this->groupSize !== null )
            $list['groupSize'] = $this->groupSize;
        if ( $this->sheetName !== null )
            $list['sheetName'] = $this->sheetName;
        return array_merge($list,parent::jsonSerialize( ));
    }
    
    public static function ExtractExerciseSheet( 
                                                 $data,
                                                 $singleResult = false,
                                                 $SheetExtension = '',
                                                 $SheetFileExtension = '',
                                                 $SheetSolutionExtension = '',
                                                 $isResult = true
                                                 )
    {

        // generates an assoc array of an exercise sheet by using a defined list of its attributes
        $exerciseSheet = DBJson::getObjectsByAttributes( 
                                                        $data,
                                                        ExerciseSheet::getDBPrimaryKey( ),
                                                        ExerciseSheet::getDBConvert( ),
                                                        $SheetExtension
                                                        );

        // generates an assoc array of an file by using a defined list of its attributes
        $exerciseSheetFile = DBJson::getObjectsByAttributes( 
                                                            $data,
                                                            File::getDBPrimaryKey( ),
                                                            File::getDBConvert( ),
                                                            $SheetFileExtension
                                                            );

        // generates an assoc array of an file by using a defined list of its attributes
        $sampleSolutions = DBJson::getObjectsByAttributes( 
                                                          $data,
                                                          File::getDBPrimaryKey( ),
                                                          File::getDBConvert( ),
                                                          $SheetSolutionExtension.'2'
                                                          );

        // concatenates the exercise sheet and the associated sample solution
        $res = DBJson::concatObjectListsSingleResult( 
                                                     $data,
                                                     $exerciseSheet,
                                                     ExerciseSheet::getDBPrimaryKey( ),
                                                     ExerciseSheet::getDBConvert( )['F_id_file'],
                                                     $exerciseSheetFile,
                                                     File::getDBPrimaryKey( ),
                                                     $SheetFileExtension,
                                                     $SheetExtension
                                                     );

        // concatenates the exercise sheet and the associated exercise sheet file
        $res = DBJson::concatObjectListsSingleResult( 
                                                     $data,
                                                     $res,
                                                     ExerciseSheet::getDBPrimaryKey( ),
                                                     ExerciseSheet::getDBConvert( )['F_id_sampleSolution'],
                                                     $sampleSolutions,
                                                     File::getDBPrimaryKey( ),
                                                     $SheetSolutionExtension.'2',
                                                     $SheetExtension
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

 
