<?php 


/**
 * @file Session.php contains the Session class
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the session structure
 *
 * @author Till Uhlig
 * @date 2013-2014
 */
class Session extends Object implements JsonSerializable
{

    /**
     * @var string $user the db id of an user
     */
    private $user = null;

    /**
     * the $user getter
     *
     * @return the value of $user
     */
    public function getUser( )
    {
        return $this->user;
    }

    /**
     * the $user setter
     *
     * @param string $value the new value for $user
     */
    public function setUser( $value = null )
    {
        $this->user = $value;
    }

    /**
     * @var string $session a string that defines which session the user has in that course.
     */
    private $session = null;

    /**
     * the $session getter
     *
     * @return the value of $session
     */
    public function getSession( )
    {
        return $this->session;
    }

    /**
     * the $session setter
     *
     * @param string $value the new value for $session
     */
    public function setSession( $value = null )
    {
        $this->session = $value;
    }

    /**
     * Creates an Session object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $userId The id of the user.
     * @param string $sessionId The id of the session.
     *
     * @return an session object
     */
    public static function createSession( 
                                         $userId,
                                         $sessionId
                                         )
    {
        return new Session( array( 
                                  'user' => $userId,
                                  'session' => $sessionId
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
                     'U_id' => 'user',
                     'SE_sessionID' => 'session'
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

        if ( $this->user != null )
            $this->addInsertData( 
                                 $values,
                                 'U_id',
                                 DBJson::mysql_real_escape_string( $this->user )
                                 );
        if ( $this->session != null )
            $this->addInsertData( 
                                 $values,
                                 'SE_sessionID',
                                 DBJson::mysql_real_escape_string( $this->session )
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
     * @todo hier fehlt noch der primary key/keys
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey( )
    {
        return'SE_id';
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

                /*if ($key == "user"){
                 $this->{$key} = new User($value,false);
                 }
                else */
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
    public static function encodeSession( $data )
    {
        if (is_array($data))reset($data);
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
    public static function decodeSession( 
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
            reset($data);
            if (current($data)!==false && !is_int(key($data))) {
                $isArray = false;
            }
        }
        
        if ( $isArray && is_array( $data ) ){
            $result = array( );
            foreach ( $data AS $key => $value ){
                $result[] = new Session( $value );
            }
            return $result;
            
        } else 
            return new Session( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->user !== null )
            $list['user'] = $this->user;
        if ( $this->session !== null )
            $list['session'] = $this->session;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractSession( 
                                          $data,
                                          $singleResult = false,
                                          $SessionExtension = '',
                                          $isResult = true
                                          )
    {

        // generates an assoc array of sessions by using a defined list
        // of its attributes
        $res = DBJson::getResultObjectsByAttributes( 
                                                    $data,
                                                    Session::getDBPrimaryKey( ),
                                                    Session::getDBConvert( ),
                                                    $SessionExtension
                                                    );
        if ($isResult){ 
            $res = Session::decodeSession($res,false);
            
            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 