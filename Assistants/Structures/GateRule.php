<?php
include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the gate rule structure
 */
class GateRule extends Object implements JsonSerializable
{

    /**
     * @var string $id  a string that identifies the gate rule
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
     * @var string $type the type of the rule
     */
    private $type = null;

    /**
     * the $type setter
     *
     * @param string $value the new value for $type
     */
    public function getType( )
    {
        return $this->type;
    }

    /**
     * (description)
     *
     * @param $type (description)
     */
    public function setType( $value = null )
    {
        $this->type = $value;
    }

    /**
     * @var string $component
     */
    private $component = null;

    /**
     * the $component getter
     *
     * @return the value of $component
     */
    public function getComponent( )
    {
        return $this->component;
    }

    /**
     * the $component setter
     *
     * @param string $value the new value for $component
     */
    public function setComponent( $value = null )
    {
        $this->component = $value;
    }

    /**
     * @var string $content
     */
    private $content = null;

    /**
     * the $content getter
     *
     * @return the value of $content
     */
    public function getContent( )
    {
        return $this->content;
    }

    /**
     * the $content setter
     *
     * @param string $value the new value for $content
     */
    public function setContent( $value = array( ) )
    {
        $this->content = $value;
    }

    /**
     * @var string $profileId
     */
    private $profileId = null;

    /**
     * the $profileId getter
     *
     * @return the value of $profileId
     */
    public function getProfileId( )
    {
        return $this->profileId;
    }

    /**
     * the $profileId setter
     *
     * @param string $value the new value for $profileId
     */
    public function setProfileId( $value = array( ) )
    {
        $this->profileId = $value;
    }
    
    public static function createGateRule(
                                        $id,
                                        $type,
                                        $component,
                                        $content,
                                        $profileId
                                        )
    {
        return new GateRule( array(
                                 'id' => $id,
                                 'type' => $type,
                                 'component' => $component,
                                 'content' => $content,
                                 'profileId' => $profileId
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
                     'GR_id' => 'id',
                     'GR_type' => 'type',
                     'GR_component' => 'component',
                     'GR_content' => 'content',
                     'GP_id' => 'profileId'
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

        $this->addInsertData(
                             $values,
                             'GR_id',
                             DBJson::mysql_real_escape_string( $this->id )
                             );
        if ( $this->type !== null )
            $this->addInsertData(
                                 $values,
                                 'GR_type',
                                 DBJson::mysql_real_escape_string( $this->type )
                                 );
        if ( $this->component !== null )
            $this->addInsertData(
                                 $values,
                                 'GR_component',
                                 DBJson::mysql_real_escape_string( $this->component )
                                 );
        if ( $this->content !== null )
            $this->addInsertData(
                                 $values,
                                 'GR_content',
                                 DBJson::mysql_real_escape_string( $this->content )
                                 );
        if ( $this->profileId !== null )
            $this->addInsertData(
                                 $values,
                                 'GP_id',
                                 DBJson::mysql_real_escape_string( $this->profileId )
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
        return 'GR_id';
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
     * encodes an object to json
     *
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeGateRule( $data )
    {
        if (is_array($data))reset($data);
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
        }
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
    public static function decodeGateRule(
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
                $result[] = new GateRule( $value );
            }
            return $result;

        } else
            return new GateRule( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->id !== null )
            $list['id'] = $this->id;
        if ( $this->type !== null )
            $list['type'] = $this->type;
        if ( $this->component !== null )
            $list['component'] = $this->component;
        if ( $this->content !== null )
            $list['content'] = $this->content;
        if ( $this->profileId !== null )
            $list['profileId'] = $this->profileId;

        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractGateRule(
                                         $data,
                                         $singleResult = false,
                                         $RuleExtension = '',
                                         $isResult = true
                                         )
    {

        // generates an assoc array of rules by using a defined list of
        // its attributes
        $res = DBJson::getObjectsByAttributes(
                                              $data,
                                              GateRule::getDBPrimaryKey( ),
                                              GateRule::getDBConvert( ),
                                              $RuleExtension
                                              );

        if ($isResult){
            $res = array_values( $res );
            $res = GateRule::decodeGateRule($res,false);
            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 