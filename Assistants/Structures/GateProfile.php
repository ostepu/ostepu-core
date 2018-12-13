<?php
include_once ( dirname( __FILE__ ) . '/StructureObject.php' );

/**
 * the gate profile structure
 */
class GateProfile extends StructureObject implements JsonSerializable
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
     * @var string $name the name of the rule
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
     * @param $name (description)
     */
    public function setName( $value = null )
    {
        $this->name = $value;
    }

    /**
     * @var string $name the name of the rule
     */
    private $readonly = null;

    /**
     * the $readonly setter
     *
     * @param string $value the new value for $readonly
     */
    public function getReadonly( )
    {
        return $this->readonly;
    }

    /**
     * (description)
     *
     * @param $readonly (description)
     */
    public function setReadonly( $value = null )
    {
        $this->readonly = $value;
    }

    /**
     * @var string $rules
     */
    private $rules = array();

    /**
     * the $rules getter
     *
     * @return the value of $rules
     */
    public function getRules( )
    {
        return $this->rules;
    }

    /**
     * the $rules setter
     *
     * @param string $value the new value for $rules
     */
    public function setRules( $value = null )
    {
        $this->rules = $value;
    }
    
    public function addRule($newRule){
        $this->rules[] = $newRule;
    }
    
    /**
     * @var string $auths
     */
    private $auths = array();

    /**
     * the $auths getter
     *
     * @return the value of $auths
     */
    public function getAuths( )
    {
        return $this->auths;
    }

    /**
     * the $auths setter
     *
     * @param string $value the new value for $auths
     */
    public function setAuths( $value = null )
    {
        $this->auths = $value;
    }
    
    public function addAuth($newAuth){
        $this->auths[] = $newAuth;
    }
        
    public static function createGateProfile(
                                        $id,
                                        $name,
                                        $readonly=0
                                        )
    {
        return new GateProfile( array(
                                 'id' => $id,
                                 'name' => $name,
                                 'readonly' => $readonly
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
                     'GP_id' => 'id',
                     'GP_name' => 'name',
                     'GP_readonly' => 'readonly',
                     'GP_rules' => 'rules',
                     'GP_auths' => 'auths'
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
                             'GP_id',
                             DBJson::mysql_real_escape_string( $this->id )
                             );
        if ( $this->name !== null )
            $this->addInsertData(
                                 $values,
                                 'GP_name',
                                 DBJson::mysql_real_escape_string( $this->name )
                                 );
        if ( $this->readonly !== null )
            $this->addInsertData(
                                 $values,
                                 'GP_readonly',
                                 DBJson::mysql_real_escape_string( $this->readonly )
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
        return 'GP_id';
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
                if ( $key === 'rules' ){
                    $this->{
                        $key

                    } = GateRule::decodeGateRule(
                                                 $value,
                                                 false
                                                 );

                } elseif ( $key === 'auths' ){
                    $this->{
                        $key

                    } = GateAuth::decodeGateAuth(
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
    public static function encodeGateProfile( $data )
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
    public static function decodeGateProfile(
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
                $result[] = new GateProfile( $value );
            }
            return $result;

        } else
            return new GateProfile( $data );
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
        if ( $this->readonly !== null )
            $list['readonly'] = $this->readonly;
        if ( $this->rules !== array() )
            $list['rules'] = $this->rules;
        if ( $this->auths !== array() )
            $list['auths'] = $this->auths;

        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractGateProfile(
                                         $data,
                                         $singleResult = false,
                                         $ProfileExtension = '',
                                         $RuleExtension = '',
                                         $AuthExtension = '',
                                         $isResult = true
                                         )
    {

        // generates an assoc array of rules by using a defined list of
        // its attributes
        $res = DBJson::getObjectsByAttributes(
                                              $data,
                                              GateProfile::getDBPrimaryKey( ),
                                              GateProfile::getDBConvert( ),
                                              $ProfileExtension
                                              );
                                                      
        $auths = GateAuth::extractGateAuth($data, false, '',false );
        $rules = GateRule::extractGateRule($data, false, '',false );
        
        $res = DBJson::concatObjectListResult(
                                               $data,
                                               $res,
                                               GateProfile::getDBPrimaryKey( ),
                                               GateProfile::getDBConvert( )['GP_rules'],
                                               $rules,
                                               GateRule::getDBPrimaryKey( ),
                                               '',
                                               $ProfileExtension
                                               );

        $res = DBJson::concatObjectListResult(
                                               $data,
                                               $res,
                                               GateProfile::getDBPrimaryKey( ),
                                               GateProfile::getDBConvert( )['GP_auths'],
                                               $auths,
                                               GateAuth::getDBPrimaryKey( ),
                                               '',
                                               $ProfileExtension
                                               );

        if ($isResult){
            $res = GateProfile::decodeGateProfile($res,false);
            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 