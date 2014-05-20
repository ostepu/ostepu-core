<?php 


/**
 * @file Session.php contains the Session class
 */

/**
 * the session structure
 *
 * @author Till Uhlig
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
    public function setUser( $value )
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
    public function setSession( $value )
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
    public function getInsertData( )
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
        return $values;
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
        if ( $data == null )
            $data = array( );

        foreach ( $data AS $key => $value ){
            if ( isset( $key ) ){

                /*if ($key == "user"){
                 $this->{$key} = new User($value,false);
                 }
                else */
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
    public static function encodeSession( $data )
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

        if ( is_array( $data ) ){
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
        return $list;
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

