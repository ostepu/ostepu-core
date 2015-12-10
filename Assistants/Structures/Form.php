<?php


/**
 * @file Form.php contains the Form class
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the form structure
 *
 * @author Till Uhlig
 * @date 2014
 */
class Form extends Object implements JsonSerializable
{

    /**
     * @var string $formId a string that identifies the form.
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

    public static function getCourseFromFormId($id)
    {
        $arr = explode('_',$id);
        if (count($arr)==2){
            return $arr[0];
        }
        else
        return '';
    }

    public static function getIdFromFormId($id)
    {
        $arr = explode('_',$id);
        if (count($arr)==2){
            return $arr[1];
        }
        else
        return $id;
    }

    public function getObjectCourseFromFormId()
    {
        return Form::getCourseFromFormId($this->formId);
    }

    public function getObjectIdFromFormId()
    {
        return Form::getIdFromFormId($this->formId);
    }

    /**
     * @var string $exerciseId a string that identifies the exercise.
     */
    private $exerciseId = null;

    /**
     * the $exerciseId getter
     *
     * @return the value of $exerciseId
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
     * @var string $type
     */
    private $type = null;

    /**
     * the $type getter
     *
     * @return the value of $type
     */
    public function getType( )
    {
        return $this->type;
    }

    /**
     * the $type setter
     *
     * @param string $value the new value for $type
     */
    public function setType( $value = null )
    {
        $this->type = $value;
    }

    /**
     * @var string $solution
     */
    private $solution = null;

    /**
     * the $solution getter
     *
     * @return the value of $solution
     */
    public function getSolution( )
    {
        return $this->solution;
    }

    /**
     * the $solution setter
     *
     * @param string $value the new value for $solution
     */
    public function setSolution( $value = null )
    {
        $this->solution = $value;
    }

    /**
     * @var int $task
     */
    private $task = null;

    /**
     * the $task getter
     *
     * @return the value of $task
     */
    public function getTask( )
    {
        return $this->task;
    }

    /**
     * the $task setter
     *
     * @param int $value the new value for $task
     */
    public function setTask( $value = null )
    {
        $this->task = $value;
    }

    /**
     * @var int $choices
     */
    private $choices = array();

    /**
     * the $choices getter
     *
     * @return the value of $choices
     */
    public function getChoices( )
    {
        return $this->choices;
    }

    /**
     * the $choices setter
     *
     * @param string $value the new value for $choices
     */
    public function setChoices( $value = array( ) )
    {
        $this->choices = $value;
    }

    public static function getTypeDefinition( )
    {
        return array( 
                     '0' => 'Eingabezeile',

                     '1' => 'Einfachauswahl',

                     '2' => 'Mehrfachauswahl'

                     );
    }

    public static $INPUT = 0;
    public static $RADIO = 1;
    public static $CHECKBOX = 2;

    /**
     * Creates an Form object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $formId The id of the form.
     * @param string $exerciseId The id of the exercise.
     * @param string $solution The solution text.
     * @param string $task The task text.
     * @param string $type The form type.
     *
     * @return an form object
     */
    public static function createForm(
                                          $formId,
                                          $exerciseId,
                                          $solution,
                                          $task,
                                          $type
                                          )
    {
        return new Form( array(
                                   'formId' => $formId,
                                   'exerciseId' => $exerciseId,
                                   'solution' => $solution,
                                   'task' => $task,
                                   'type' => $type
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
                     'E_id' => 'exerciseId',
                     'FO_solution' => 'solution',
                     'FO_task' => 'task',
                     'FO_type' => 'type',
                     'FO_choices' => 'choices'
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

        if ( $this->formId != null )
            $this->addInsertData(
                                 $values,
                                 'FO_id',
                                 DBJson::mysql_real_escape_string( Form::getIdFromFormId($this->formId) )
                                 );
        if ( $this->exerciseId != null )
            $this->addInsertData(
                                 $values,
                                 'E_id',
                                 DBJson::mysql_real_escape_string( $this->exerciseId )
                                 );
        if ( $this->type != null )
            $this->addInsertData(
                                 $values,
                                 'FO_type',
                                 DBJson::mysql_real_escape_string( $this->type )
                                 );
        if ( $this->solution != null )
            $this->addInsertData(
                                 $values,
                                 'FO_solution',
                                 DBJson::mysql_real_escape_string( $this->solution )
                                 );
        if ( $this->task != null )
            $this->addInsertData(
                                 $values,
                                 'FO_task',
                                 DBJson::mysql_real_escape_string( $this->task )
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
        return 'FO_id';
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
                if ( $key == 'choices' ){
                    $this->{
                        $key

                    } = Choice::decodeChoice(
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
    public static function encodeForm( $data )
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
    public static function decodeForm(
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
                $result[] = new Form( $value );
            }
            return $result;

        } else
            return new Form( $data );
    }

    /**
     * the json serialize function
     *
     * @return an array to serialize the object
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->formId !== null )
            $list['formId'] = $this->formId;
        if ( $this->exerciseId !== null )
            $list['exerciseId'] = $this->exerciseId;
        if ( $this->type !== null )
            $list['type'] = $this->type;
        if ( $this->solution !== null )
            $list['solution'] = $this->solution;
        if ( $this->task !== null )
            $list['task'] = $this->task;
        if ( $this->choices !== array( ) &&
             $this->choices !== null )
             $list['choices'] = $this->choices;

        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractForm(
                                           $data,
                                           $singleResult = false,
                                           $FormsExtension = '',
                                           $ChoiceExtension = '',
                                           $isResult = true
                                           )
    {

        // generates an assoc array of an forms by using a defined
        // list of its attributes
        $forms = DBJson::getObjectsByAttributes(
                                                   $data,
                                                   Form::getDBPrimaryKey( ),
                                                   Form::getDBConvert( ),
                                                   $FormsExtension
                                                   );


        // generates an assoc array of choices by using a defined
        // list of its attributes
        $choices = DBJson::getObjectsByAttributes(
                                                      $data,
                                                      Choice::getDBPrimaryKey( ),
                                                      Choice::getDBConvert( ),
                                                      $ChoiceExtension
                                                      );

        // concatenates the forms and the associated choices
        $res =         DBJson::concatObjectListResult(
                                                   $data,
                                                   $forms,
                                                   Form::getDBPrimaryKey( ),
                                                   Form::getDBConvert( )['FO_choices'],
                                                   $choices,
                                                   Choice::getDBPrimaryKey( ),
                                                   $ChoiceExtension,
                                                   $FormsExtension
                                                   );
        if ($isResult){ 
            // to reindex
            $res = array_values( $res );
            $res = Form::decodeForm($res,false);

            if ( $singleResult ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}
