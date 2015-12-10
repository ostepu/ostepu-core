<?php 


/**
 * @file Reference.php contains the Reference class
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the reference structure
 *
 * @author Till Uhlig
 * @date 2015
 */
class Reference extends Object implements JsonSerializable
{

    /**
     * @var string $localRef
     */
    private $localRef = null;

    /**
     * the $localRef getter
     *
     * @return the value of $localRef
     */
    private function getLocalRef( )
    {
        return $this->localRef;
    }

    /**
     * the $localRef setter
     *
     * @param string $value the new value for $localRef
     */
    private function setLocalRef( $value = null )
    {
        $this->localRef = $value;
    }

    /**
     * @var string $globalRef
     */
    private $globalRef = null;

    /**
     * the $globalRef getter
     *
     * @return the value of $globalRef
     */
    private function getGlobalRef( )
    {
        return $this->globalRef;
    }

    /**
     * the $globalRef setter
     *
     * @param string $value the new value for $globalRef
     */
    private function setGlobalRef( $value = null )
    {
        $this->globalRef = $value;
    }

    public function getContent( )
    {
        return file_get_contents($this->localRef);
    }

    /**
     * Creates an reference object
     * Not needed attributes can be set to null.
     *
     * @param string $localReference The id of the user.
     * @param string $globalReference The id of the session.
     *
     * @return an reference object
     */
    public static function createReference( 
                                         $localReference,
                                         $globalReference=null
                                         )
    {
        return new Reference( array( 
                                  'localRef' => $localReference,
                                  'globalRef' => $globalReference
                                  ) );
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
    public static function encodeReference( $data )
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
    public static function decodeReference( 
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
                $result[] = new Reference( $value );
            }
            return $result;

        } else 
            return new Reference( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->localRef !== null )
            $list['localRef'] = $this->localRef;
        if ( $this->globalRef !== null )
            $list['globalRef'] = $this->globalRef;
        return array_merge($list,parent::jsonSerialize( ));
    }
}

 