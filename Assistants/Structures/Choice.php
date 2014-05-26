<?php


/**
 * @file Choice.php contains the Choice class
 */

/**
 * the choice structure
 *
 * @author Till Uhlig
 */
class Choice extends Object implements JsonSerializable
{
    /**
     * @var string $choiceId
     */
    private $choiceId = null;

    /**
     * the $choiceId getter
     *
     * @return the value of $choiceId
     */
    public function getChoiceId( )
    {
        return $this->choiceId;
    }

    /**
     * the $choiceId setter
     *
     * @param string $value the new value for $choiceId
     */
    public function setChoiceId( $value = null )
    {
        $this->choiceId = $value;
    }
    
    public static function getCourseFromChoiceId($id)
    {
        $arr = explode('_',$id);
        if (count($arr)==2){
            return $arr[0];
        }
        else
        return '';
    }
    
    public static function getIdFromChoiceId($id)
    {
        $arr = explode('_',$id);
        if (count($arr)==2){
            return $arr[1];
        }
        else
        return $id;
    }
    
    public function getObjectCourseFromChoiceId()
    {
        return Choice::getCourseFromChoiceId($this->choiceId);
    }
    
    public function getObjectIdFromChoiceId()
    {
        return Choice::getIdFromChoiceId($this->choiceId);
    }
    
    /**
     * @var string $formId
     */
    private $formId = null;

    /**
     * the $formId getter
     *
     * @return the value of $formId
     */
    public function getFormId( )
    {
        return $this->formId;
    }

    /**
     * the $formId setter
     *
     * @param string $value the new value for $formId
     */
    public function setFormId( $value = null )
    {
        $this->formId = $value;
    }

    /**
     * @var string $text
     */
    private $text = null;

    /**
     * the $text getter
     *
     * @return the value of $text
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
     * @var string $correct
     */
    private $correct = null;

    /**
     * the $correct getter
     *
     * @return the value of $correct
     */
    public function getCorrect( )
    {
        return $this->correct;
    }

    /**
     * the $correct setter
     *
     * @param string $value the new value for $correct
     */
    public function setCorrect( $value = null )
    {
        $this->correct = $value;
    }
    
    private $submissionId = null;
    public function getSubmissionId( )
    {
        return $this->submissionId;
    }
    public function setSubmissionId( $value = null )
    {
        $this->submissionId = $value;
    }

    
    /**
     * Creates an Exercise object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $exerciseId The id of the exercise.
     * @param string $courseId The id of the course. (do not use!)
     * @param string $sheetId The id of the exercise sheet.
     * @param string $maxPoints the max points
     * @param string $type the id of the exercise type
     * @param string $link the id of the exercise, this exercise belongs to
     * @param string $linkName the name of the sub exercise.
     * @param string $bonus the bonus flag
     *
     * @return an exercise object
     */
    public static function createChoice(
                                          $formId,
                                          $choiceId,
                                          $text,
                                          $correct,
                                          $submissionId = null
                                          )
    {
        return new Choice( array(
                                   'formId' => $formId,
                                   'choiceId' => $choiceId,
                                   'text' => $text,
                                   'correct' => $correct,
                                   'submissionId' => $submissionId
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
                     'FO_id' => 'formId',
                     'CH_id' => 'choiceId',
                     'CH_text' => 'text',
                     'CH_correct' => 'correct',
                     'S_id' => 'submissionId'
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

        if ( $this->choiceId != null )
            $this->addInsertData(
                                 $values,
                                 'CH_id',
                                 DBJson::mysql_real_escape_string( Choice::getIdFromChoiceId($this->choiceId) )
                                 );
        if ( $this->formId != null )
            $this->addInsertData(
                                 $values,
                                 'FO_id',
                                 DBJson::mysql_real_escape_string( Form::getIdFromFormId($this->formId) )
                                 );
        if ( $this->text != null )
            $this->addInsertData(
                                 $values,
                                 'CH_text',
                                 DBJson::mysql_real_escape_string( $this->text )
                                 );
        if ( $this->correct != null )
            $this->addInsertData(
                                 $values,
                                 'CH_correct',
                                 DBJson::mysql_real_escape_string( $this->correct )
                                 );
        if ( $this->submissionId != null )
            $this->addInsertData(
                                 $values,
                                 'S_id',
                                 DBJson::mysql_real_escape_string( $this->submissionId )
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
        return 'CH_id';
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
    public static function encodeChoice( $data )
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
    public static function decodeChoice(
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
                $result[] = new Choice( $value );
            }
            return $result;

        } else
            return new Choice( $data );
    }

    /**
     * the json serialize function
     *
     * @return an array to serialize the object
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->choiceId !== null )
            $list['choiceId'] = $this->choiceId;
        if ( $this->formId !== null )
            $list['formId'] = $this->formId;
        if ( $this->text !== null )
            $list['text'] = $this->text;
        if ( $this->correct !== null )
            $list['correct'] = $this->correct;
        if ( $this->submissionId !== null )
            $list['submissionId'] = $this->submissionId;
            
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractChoice(
                                           $data,
                                           $singleResult = false,
                                           $ChoiceExtension = '',
                                           $isResult = true
                                           )
    {

        // generates an assoc array of choices by using a defined list of
        // its attributes
        $res = DBJson::getResultObjectsByAttributes( 
                                                    $data,
                                                    Choice::getDBPrimaryKey( ),
                                                    Choice::getDBConvert( ),
                                                    $ChoiceExtension
                                                    );
                                                    
        if ($isResult){                                             
            // to reindex
            $res = array_values( $res );

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
