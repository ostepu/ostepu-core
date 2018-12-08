<?php
include_once ( dirname( __FILE__ ) . '/StructureObject.php' );

/**
 * the gate auth structure
 */
class GateAuth extends StructureObject implements JsonSerializable
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
     * @var string $params (muss ein String sein, kein Array)
     */
    private $params = null;

    /**
     * the $params getter
     *
     * @return the value of $params
     */
    public function getParams( )
    {
        return $this->params;
    }

    /**
     * the $params setter
     *
     * @param string $value the new value for $params
     */
    public function setParams( $value = null )
    {
        $this->params = $value;
    }
    
    /**
     * @var string $login
     */
    private $login = null;

    /**
     * the $login getter
     *
     * @return the value of $login
     */
    public function getLogin( )
    {
        return $this->login;
    }

    /**
     * the $login setter
     *
     * @param string $value the new value for $login
     */
    public function setLogin( $value = null )
    {
        $this->login = $value;
    }
    
    /**
     * @var string $passwd
     */
    private $passwd = null;

    /**
     * the $passwd getter
     *
     * @return the value of $passwd
     */
    public function getPasswd( )
    {
        return $this->passwd;
    }

    /**
     * the $passwd setter
     *
     * @param string $value the new value for $passwd
     */
    public function setPasswd( $value = null )
    {
        $this->passwd = $value;
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
    
    public static function createGateAuth(
                                        $id,
                                        $type,
                                        $params,
                                        $login,
                                        $passwd,
                                        $profileId
                                        )
    {
        return new GateAuth( array(
                                 'id' => $id,
                                 'type' => $type,
                                 'params' => $params,
                                 'login' => $login,
                                 'passwd' => $passwd,
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
                     'GA_id' => 'id',
                     'GA_type' => 'type',
                     'GA_params' => 'params',
                     'GA_login' => 'login',
                     'GA_passwd' => 'passwd',
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
                             'GA_id',
                             DBJson::mysql_real_escape_string( $this->id )
                             );
        if ( $this->type !== null )
            $this->addInsertData(
                                 $values,
                                 'GA_type',
                                 DBJson::mysql_real_escape_string( $this->type )
                                 );
        if ( $this->params !== null )
            $this->addInsertData(
                                 $values,
                                 'GA_params',
                                 DBJson::mysql_real_escape_string( $this->params )
                                 );
        if ( $this->login !== null )
            $this->addInsertData(
                                 $values,
                                 'GA_login',
                                 DBJson::mysql_real_escape_string( $this->login )
                                 );
        if ( $this->passwd !== null )
            $this->addInsertData(
                                 $values,
                                 'GA_passwd',
                                 DBJson::mysql_real_escape_string( $this->passwd )
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
        return 'GA_id';
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
    public static function encodeGateAuth( $data )
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
    public static function decodeGateAuth(
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
                $result[] = new GateAuth( $value );
            }
            return $result;

        } else
            return new GateAuth( $data );
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
        if ( $this->params !== null)
            $list['params'] = $this->params;
        if ( $this->login !== null )
            $list['login'] = $this->login;
        if ( $this->passwd !== null )
            $list['passwd'] = $this->passwd;
        if ( $this->profileId !== null )
            $list['profileId'] = $this->profileId;

        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractGateAuth(
                                         $data,
                                         $singleResult = false,
                                         $AuthExtension = '',
                                         $isResult = true
                                         )
    {

        // generates an assoc array of rules by using a defined list of
        // its attributes
        $res = DBJson::getObjectsByAttributes(
                                              $data,
                                              GateAuth::getDBPrimaryKey( ),
                                              GateAuth::getDBConvert( ),
                                              $AuthExtension
                                              );

        if ($isResult){
            $res = array_values( $res );
            $res = GateAuth::decodeGateAuth($res,false);
            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 